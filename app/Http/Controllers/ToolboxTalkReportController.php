<?php

namespace App\Http\Controllers;

use App\Models\ToolboxTalk;
use App\Models\ToolboxTalkAttendance;
use App\Models\Department;
use App\Models\User;
use App\Models\Company;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ToolboxTalkReportController extends Controller
{
    use UsesCompanyGroup;

    /**
     * Show reporting dashboard with all report options
     */
    public function index()
    {
        return view('toolbox-talks.reports.index');
    }

    /**
     * Department Attendance Report
     */
    public function departmentAttendance(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        
        // Get date range
        $period = $request->get('period', 'month'); // day, week, month, annual
        $date = $request->get('date', now()->format('Y-m-d'));
        $dateObj = Carbon::parse($date);
        
        $startDate = $this->getStartDate($dateObj, $period);
        $endDate = $this->getEndDate($dateObj, $period);
        
        // Get company group IDs if applicable
        $companyIds = $this->getCompanyGroupIds();
        
        // Get departments
        $departments = Department::whereIn('company_id', $companyIds)->get();
        
        // Get talks in date range
        $talks = ToolboxTalk::whereIn('company_id', $companyIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->with(['department', 'attendances'])
            ->get();
        
        // Calculate department statistics
        $departmentStats = $departments->map(function($dept) use ($talks) {
            $deptTalks = $talks->where('department_id', $dept->id);
            $totalTalks = $deptTalks->count();
            $totalAttendances = $deptTalks->sum(function($talk) {
                return $talk->attendances->where('attendance_status', 'present')->count();
            });
            $totalExpected = $deptTalks->sum('total_attendees');
            $attendanceRate = $totalExpected > 0 ? ($totalAttendances / $totalExpected) * 100 : 0;
            
            return [
                'id' => $dept->id,
                'name' => $dept->name,
                'total_talks' => $totalTalks,
                'total_attendances' => $totalAttendances,
                'total_expected' => $totalExpected,
                'attendance_rate' => round($attendanceRate, 2),
                'talks' => $deptTalks->values(),
            ];
        })->filter(function($dept) {
            return $dept['total_talks'] > 0;
        })->sortByDesc('attendance_rate');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportDepartmentAttendanceExcel($departmentStats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportDepartmentAttendancePDF($departmentStats, $startDate, $endDate, $period);
        }
        
        return view('toolbox-talks.reports.department-attendance', compact(
            'departmentStats',
            'startDate',
            'endDate',
            'period',
            'date'
        ));
    }

    /**
     * Employee Attendance Report
     */
    public function employeeAttendance(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        
        // Get date range
        $period = $request->get('period', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));
        $dateObj = Carbon::parse($date);
        $employeeId = $request->get('employee_id');
        
        $startDate = $this->getStartDate($dateObj, $period);
        $endDate = $this->getEndDate($dateObj, $period);
        
        // Get company group IDs
        $companyIds = $this->getCompanyGroupIds();
        
        // Get talks in date range
        $talks = ToolboxTalk::whereIn('company_id', $companyIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->with(['attendances'])
            ->get();
        
        // Get employees
        $employees = User::whereIn('company_id', $companyIds)
            ->where('is_active', true)
            ->when($employeeId, function($query) use ($employeeId) {
                return $query->where('id', $employeeId);
            })
            ->with('employee')
            ->get();
        
        // Calculate employee statistics
        $employeeStats = $employees->map(function($employee) use ($talks) {
            $attendances = ToolboxTalkAttendance::where('employee_id', $employee->id)
                ->whereIn('toolbox_talk_id', $talks->pluck('id'))
                ->with('toolboxTalk')
                ->get();
            
            $presentCount = $attendances->where('attendance_status', 'present')->count();
            $absentCount = $attendances->where('attendance_status', 'absent')->count();
            $lateCount = $attendances->where('attendance_status', 'late')->count();
            $totalTalks = $talks->count();
            $attendanceRate = $totalTalks > 0 ? ($presentCount / $totalTalks) * 100 : 0;
            
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'employee_id' => $employee->employee->employee_id_number ?? 'N/A',
                'department' => $employee->employee && $employee->employee->department ? $employee->employee->department->name : 'N/A',
                'total_talks' => $totalTalks,
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'attendance_rate' => round($attendanceRate, 2),
                'attendances' => $attendances,
            ];
        })->filter(function($emp) {
            return $emp['total_talks'] > 0;
        })->sortByDesc('attendance_rate');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportEmployeeAttendanceExcel($employeeStats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportEmployeeAttendancePDF($employeeStats, $startDate, $endDate, $period);
        }
        
        return view('toolbox-talks.reports.employee-attendance', compact(
            'employeeStats',
            'startDate',
            'endDate',
            'period',
            'date',
            'employees',
            'employeeId'
        ));
    }

    /**
     * Period-based Report (Day, Week, Month, Annual)
     */
    public function periodReport(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        
        // Get date range
        $period = $request->get('period', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));
        $dateObj = Carbon::parse($date);
        
        $startDate = $this->getStartDate($dateObj, $period);
        $endDate = $this->getEndDate($dateObj, $period);
        
        // Get company group IDs
        $companyIds = $this->getCompanyGroupIds();
        
        // Get talks in date range
        $talks = ToolboxTalk::whereIn('company_id', $companyIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->with(['department', 'supervisor', 'topic', 'attendances'])
            ->orderBy('scheduled_date', 'desc')
            ->get();
        
        // Calculate statistics
        $stats = [
            'total_talks' => $talks->count(),
            'completed' => $talks->where('status', 'completed')->count(),
            'scheduled' => $talks->where('status', 'scheduled')->count(),
            'in_progress' => $talks->where('status', 'in_progress')->count(),
            'cancelled' => $talks->where('status', 'cancelled')->count(),
            'overdue' => $talks->where('status', 'overdue')->count(),
            'total_attendees' => $talks->sum('total_attendees'),
            'present_attendees' => $talks->sum('present_attendees'),
            'avg_attendance_rate' => $talks->avg('attendance_rate') ?? 0,
            'avg_feedback_score' => $talks->avg('average_feedback_score') ?? 0,
        ];
        
        // Group by department
        $departmentBreakdown = $talks->groupBy('department_id')->map(function($deptTalks, $deptId) {
            $dept = $deptTalks->first()->department;
            return [
                'name' => $dept ? $dept->name : 'No Department',
                'count' => $deptTalks->count(),
                'avg_attendance' => $deptTalks->avg('attendance_rate') ?? 0,
            ];
        })->values();
        
        // Group by topic
        $topicBreakdown = $talks->whereNotNull('topic_id')->groupBy('topic_id')->map(function($topicTalks, $topicId) {
            $topic = $topicTalks->first()->topic;
            return [
                'name' => $topic ? $topic->title : 'Unknown',
                'count' => $topicTalks->count(),
                'avg_attendance' => $topicTalks->avg('attendance_rate') ?? 0,
            ];
        })->values()->sortByDesc('count')->take(10);
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportPeriodReportExcel($talks, $stats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportPeriodReportPDF($talks, $stats, $startDate, $endDate, $period);
        }
        
        return view('toolbox-talks.reports.period-report', compact(
            'talks',
            'stats',
            'departmentBreakdown',
            'topicBreakdown',
            'startDate',
            'endDate',
            'period',
            'date'
        ));
    }

    /**
     * Companies Report (for parent companies)
     */
    public function companiesReport(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        
        // Get date range
        $period = $request->get('period', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));
        $dateObj = Carbon::parse($date);
        
        $startDate = $this->getStartDate($dateObj, $period);
        $endDate = $this->getEndDate($dateObj, $period);
        
        // Get company group
        $companyIds = $this->getCompanyGroupIds();
        $companies = Company::whereIn('id', $companyIds)->get();
        
        // Get talks for all companies
        $talks = ToolboxTalk::whereIn('company_id', $companyIds)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->with(['company', 'department', 'attendances'])
            ->get();
        
        // Calculate company statistics
        $companyStats = $companies->map(function($company) use ($talks) {
            $companyTalks = $talks->where('company_id', $company->id);
            $totalTalks = $companyTalks->count();
            $completedTalks = $companyTalks->where('status', 'completed')->count();
            $totalAttendees = $companyTalks->sum('total_attendees');
            $presentAttendees = $companyTalks->sum('present_attendees');
            $attendanceRate = $totalAttendees > 0 ? ($presentAttendees / $totalAttendees) * 100 : 0;
            $avgFeedback = $companyTalks->avg('average_feedback_score') ?? 0;
            
            return [
                'id' => $company->id,
                'name' => $company->name,
                'total_talks' => $totalTalks,
                'completed_talks' => $completedTalks,
                'completion_rate' => $totalTalks > 0 ? ($completedTalks / $totalTalks) * 100 : 0,
                'total_attendees' => $totalAttendees,
                'present_attendees' => $presentAttendees,
                'attendance_rate' => round($attendanceRate, 2),
                'avg_feedback' => round($avgFeedback, 2),
                'talks' => $companyTalks->values(),
            ];
        })->filter(function($comp) {
            return $comp['total_talks'] > 0;
        })->sortByDesc('total_talks');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportCompaniesReportExcel($companyStats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportCompaniesReportPDF($companyStats, $startDate, $endDate, $period);
        }
        
        return view('toolbox-talks.reports.companies', compact(
            'companyStats',
            'startDate',
            'endDate',
            'period',
            'date'
        ));
    }

    // Helper methods for date ranges
    private function getStartDate(Carbon $date, string $period): Carbon
    {
        return match($period) {
            'day' => $date->copy()->startOfDay(),
            'week' => $date->copy()->startOfWeek(),
            'month' => $date->copy()->startOfMonth(),
            'annual' => $date->copy()->startOfYear(),
            default => $date->copy()->startOfMonth(),
        };
    }

    private function getEndDate(Carbon $date, string $period): Carbon
    {
        return match($period) {
            'day' => $date->copy()->endOfDay(),
            'week' => $date->copy()->endOfWeek(),
            'month' => $date->copy()->endOfMonth(),
            'annual' => $date->copy()->endOfYear(),
            default => $date->copy()->endOfMonth(),
        };
    }

    // Excel Export Methods (using CSV for compatibility)
    private function exportDepartmentAttendanceExcel($departmentStats, $startDate, $endDate, $period)
    {
        $filename = "department-attendance-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($departmentStats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            // Header rows
            fputcsv($file, ['Department Attendance Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, ['Department', 'Total Talks', 'Total Attendances', 'Total Expected', 'Attendance Rate (%)']);
            
            // Data rows
            foreach ($departmentStats as $dept) {
                fputcsv($file, [
                    $dept['name'],
                    $dept['total_talks'],
                    $dept['total_attendances'],
                    $dept['total_expected'],
                    number_format($dept['attendance_rate'], 2),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportEmployeeAttendanceExcel($employeeStats, $startDate, $endDate, $period)
    {
        $filename = "employee-attendance-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($employeeStats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            // Header rows
            fputcsv($file, ['Employee Attendance Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, ['Employee Name', 'Employee ID', 'Department', 'Total Talks', 'Present', 'Absent', 'Late', 'Attendance Rate (%)']);
            
            // Data rows
            foreach ($employeeStats as $emp) {
                fputcsv($file, [
                    $emp['name'],
                    $emp['employee_id'],
                    $emp['department'],
                    $emp['total_talks'],
                    $emp['present'],
                    $emp['absent'],
                    $emp['late'],
                    number_format($emp['attendance_rate'], 2),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPeriodReportExcel($talks, $stats, $startDate, $endDate, $period)
    {
        $filename = "period-report-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($talks, $stats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            // Header rows
            fputcsv($file, ['Period Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            // Statistics summary
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Talks', $stats['total_talks']]);
            fputcsv($file, ['Completed', $stats['completed']]);
            fputcsv($file, ['Average Attendance Rate', number_format($stats['avg_attendance_rate'], 2) . '%']);
            fputcsv($file, ['Average Feedback Score', number_format($stats['avg_feedback_score'], 2)]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, ['Reference', 'Title', 'Date', 'Status', 'Department', 'Supervisor', 'Topic', 'Total Attendees', 'Present', 'Attendance Rate (%)', 'Feedback Score']);
            
            // Data rows
            foreach ($talks as $talk) {
                fputcsv($file, [
                    $talk->reference_number,
                    $talk->title,
                    $talk->scheduled_date->format('Y-m-d'),
                    ucfirst($talk->status),
                    $talk->department?->name ?? 'N/A',
                    $talk->supervisor?->name ?? 'N/A',
                    $talk->topic?->title ?? 'N/A',
                    $talk->total_attendees,
                    $talk->present_attendees,
                    number_format($talk->attendance_rate, 2),
                    $talk->average_feedback_score ? number_format($talk->average_feedback_score, 2) : 'N/A',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCompaniesReportExcel($companyStats, $startDate, $endDate, $period)
    {
        $filename = "companies-report-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($companyStats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            // Header rows
            fputcsv($file, ['Companies Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, ['Company', 'Total Talks', 'Completed Talks', 'Completion Rate (%)', 'Total Attendees', 'Present Attendees', 'Attendance Rate (%)', 'Avg Feedback Score']);
            
            // Data rows
            foreach ($companyStats as $comp) {
                fputcsv($file, [
                    $comp['name'],
                    $comp['total_talks'],
                    $comp['completed_talks'],
                    number_format($comp['completion_rate'], 2),
                    $comp['total_attendees'],
                    $comp['present_attendees'],
                    number_format($comp['attendance_rate'], 2),
                    number_format($comp['avg_feedback'], 2),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // PDF Export Methods
    private function exportDepartmentAttendancePDF($departmentStats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('toolbox-talks.reports.exports.department-attendance-pdf', [
            'departmentStats' => $departmentStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("department-attendance-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportEmployeeAttendancePDF($employeeStats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('toolbox-talks.reports.exports.employee-attendance-pdf', [
            'employeeStats' => $employeeStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("employee-attendance-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportPeriodReportPDF($talks, $stats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('toolbox-talks.reports.exports.period-report-pdf', [
            'talks' => $talks,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("period-report-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportCompaniesReportPDF($companyStats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('toolbox-talks.reports.exports.companies-pdf', [
            'companyStats' => $companyStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("companies-report-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }
}

