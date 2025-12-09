<?php

namespace App\Http\Controllers;

use App\Models\DailyAttendance;
use App\Models\BiometricDevice;
use App\Models\Employee;
use App\Models\Department;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ManpowerReportController extends Controller
{
    use UsesCompanyGroup;

    /**
     * Show manpower reports dashboard
     */
    public function index()
    {
        return view('manpower-reports.index');
    }

    /**
     * Daily manpower report
     */
    public function dailyReport(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $date = $request->filled('date') ? Carbon::parse($request->date) : today();
        
        // Get all active employees
        $totalEmployees = Employee::whereIn('company_id', $companyGroupIds)
            ->where('is_active', true)
            ->where('employment_status', 'active')
            ->count();
        
        // Get present employees
        $presentEmployees = DailyAttendance::whereIn('company_id', $companyGroupIds)
            ->whereDate('attendance_date', $date)
            ->where('attendance_status', 'present')
            ->distinct('employee_id')
            ->count('employee_id');
        
        // Get absent employees
        $absentCount = $totalEmployees - $presentEmployees;
        
        // Device-wise breakdown
        $devices = BiometricDevice::whereIn('company_id', $companyGroupIds)
            ->where('daily_attendance_enabled', true)
            ->get();
        
        $deviceBreakdown = $devices->map(function($device) use ($date) {
            $attendances = DailyAttendance::where('biometric_device_id', $device->id)
                ->whereDate('attendance_date', $date)
                ->where('attendance_status', 'present')
                ->get();
            
            return [
                'device' => $device,
                'present' => $attendances->count(),
                'late' => $attendances->where('is_late', true)->count(),
                'on_time' => $attendances->where('is_late', false)->count(),
            ];
        });
        
        // Department breakdown
        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        $departmentBreakdown = $departments->map(function($dept) use ($date, $companyGroupIds) {
            $totalDeptEmployees = Employee::where('department_id', $dept->id)
                ->whereIn('company_id', $companyGroupIds)
                ->where('is_active', true)
                ->count();
            
            $presentDeptEmployees = DailyAttendance::where('department_id', $dept->id)
                ->whereIn('company_id', $companyGroupIds)
                ->whereDate('attendance_date', $date)
                ->where('attendance_status', 'present')
                ->distinct('employee_id')
                ->count('employee_id');
            
            return [
                'department' => $dept,
                'total' => $totalDeptEmployees,
                'present' => $presentDeptEmployees,
                'absent' => $totalDeptEmployees - $presentDeptEmployees,
                'attendance_rate' => $totalDeptEmployees > 0 
                    ? round(($presentDeptEmployees / $totalDeptEmployees) * 100, 1) 
                    : 0,
            ];
        });
        
        $stats = [
            'total_employees' => $totalEmployees,
            'present' => $presentEmployees,
            'absent' => $absentCount,
            'attendance_rate' => $totalEmployees > 0 
                ? round(($presentEmployees / $totalEmployees) * 100, 1) 
                : 0,
        ];
        
        if ($request->get('format') === 'pdf') {
            return $this->exportDailyPDF($stats, $deviceBreakdown, $departmentBreakdown, $date, $request);
        }
        
        if ($request->get('format') === 'excel') {
            return $this->exportDailyExcel($stats, $deviceBreakdown, $departmentBreakdown, $date, $request);
        }
        
        return view('manpower-reports.daily', compact('stats', 'deviceBreakdown', 'departmentBreakdown', 'date'));
    }

    /**
     * Weekly manpower report
     */
    public function weeklyReport(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $weekStart = $request->filled('week_start') 
            ? Carbon::parse($request->week_start)->startOfWeek() 
            : now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        // Get all active employees
        $totalEmployees = Employee::whereIn('company_id', $companyGroupIds)
            ->where('is_active', true)
            ->count();
        
        // Daily breakdown for the week
        $dailyBreakdown = [];
        for ($date = $weekStart->copy(); $date <= $weekEnd; $date->addDay()) {
            $present = DailyAttendance::whereIn('company_id', $companyGroupIds)
                ->whereDate('attendance_date', $date)
                ->where('attendance_status', 'present')
                ->distinct('employee_id')
                ->count('employee_id');
            
            $late = DailyAttendance::whereIn('company_id', $companyGroupIds)
                ->whereDate('attendance_date', $date)
                ->where('is_late', true)
                ->distinct('employee_id')
                ->count('employee_id');
            
            $dailyBreakdown[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('l'),
                'present' => $present,
                'late' => $late,
                'absent' => $totalEmployees - $present,
                'attendance_rate' => $totalEmployees > 0 ? round(($present / $totalEmployees) * 100, 1) : 0,
            ];
        }
        
        // Average stats
        $avgPresent = collect($dailyBreakdown)->avg('present');
        $avgAttendanceRate = collect($dailyBreakdown)->avg('attendance_rate');
        
        $stats = [
            'total_employees' => $totalEmployees,
            'avg_present' => round($avgPresent, 1),
            'avg_attendance_rate' => round($avgAttendanceRate, 1),
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
        ];
        
        if ($request->get('format') === 'pdf') {
            return $this->exportWeeklyPDF($stats, $dailyBreakdown, $request);
        }
        
        if ($request->get('format') === 'excel') {
            return $this->exportWeeklyExcel($stats, $dailyBreakdown, $request);
        }
        
        return view('manpower-reports.weekly', compact('stats', 'dailyBreakdown'));
    }

    /**
     * Monthly manpower report
     */
    public function monthlyReport(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $month = $request->filled('month') 
            ? Carbon::parse($request->month) 
            : now();
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        
        // Get all active employees
        $totalEmployees = Employee::whereIn('company_id', $companyGroupIds)
            ->where('is_active', true)
            ->count();
        
        // Daily breakdown for the month
        $dailyBreakdown = [];
        for ($date = $monthStart->copy(); $date <= $monthEnd; $date->addDay()) {
            $present = DailyAttendance::whereIn('company_id', $companyGroupIds)
                ->whereDate('attendance_date', $date)
                ->where('attendance_status', 'present')
                ->distinct('employee_id')
                ->count('employee_id');
            
            $dailyBreakdown[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'present' => $present,
                'absent' => $totalEmployees - $present,
                'attendance_rate' => $totalEmployees > 0 ? round(($present / $totalEmployees) * 100, 1) : 0,
            ];
        }
        
        // Weekly summary
        $weeklySummary = [];
        $currentWeek = $monthStart->copy()->startOfWeek();
        while ($currentWeek <= $monthEnd) {
            $weekEnd = $currentWeek->copy()->endOfWeek();
            if ($weekEnd > $monthEnd) {
                $weekEnd = $monthEnd->copy();
            }
            
            $weekPresent = DailyAttendance::whereIn('company_id', $companyGroupIds)
                ->whereBetween('attendance_date', [$currentWeek, $weekEnd])
                ->where('attendance_status', 'present')
                ->distinct('employee_id')
                ->count('employee_id');
            
            $weeklySummary[] = [
                'week' => "Week " . $currentWeek->format('W'),
                'start' => $currentWeek->format('M j'),
                'end' => $weekEnd->format('M j'),
                'avg_present' => round($weekPresent / 7, 1),
                'attendance_rate' => $totalEmployees > 0 ? round(($weekPresent / ($totalEmployees * 7)) * 100, 1) : 0,
            ];
            
            $currentWeek->addWeek();
        }
        
        $stats = [
            'total_employees' => $totalEmployees,
            'month' => $month->format('F Y'),
            'avg_present' => round(collect($dailyBreakdown)->avg('present'), 1),
            'avg_attendance_rate' => round(collect($dailyBreakdown)->avg('attendance_rate'), 1),
        ];
        
        if ($request->get('format') === 'pdf') {
            return $this->exportMonthlyPDF($stats, $dailyBreakdown, $weeklySummary, $request);
        }
        
        if ($request->get('format') === 'excel') {
            return $this->exportMonthlyExcel($stats, $dailyBreakdown, $weeklySummary, $request);
        }
        
        return view('manpower-reports.monthly', compact('stats', 'dailyBreakdown', 'weeklySummary'));
    }

    /**
     * Location/Device comparison report
     */
    public function locationReport(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $date = $request->filled('date') ? Carbon::parse($request->date) : today();
        
        $devices = BiometricDevice::whereIn('company_id', $companyGroupIds)
            ->where('daily_attendance_enabled', true)
            ->get();
        
        $locationStats = $devices->map(function($device) use ($date) {
            $attendances = DailyAttendance::where('biometric_device_id', $device->id)
                ->whereDate('attendance_date', $date)
                ->get();
            
            return [
                'device' => $device,
                'location' => $device->location_name,
                'company' => $device->company->name,
                'total' => $attendances->count(),
                'present' => $attendances->where('attendance_status', 'present')->count(),
                'late' => $attendances->where('is_late', true)->count(),
                'absent' => 0, // Would need total expected employees per location
            ];
        });
        
        if ($request->get('format') === 'pdf') {
            return $this->exportLocationPDF($locationStats, $date, $request);
        }
        
        if ($request->get('format') === 'excel') {
            return $this->exportLocationExcel($locationStats, $date, $request);
        }
        
        return view('manpower-reports.location', compact('locationStats', 'date'));
    }

    // Export methods
    private function exportDailyExcel($stats, $deviceBreakdown, $departmentBreakdown, $date, $request)
    {
        $filename = 'daily_manpower_report_' . $date->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($stats, $deviceBreakdown, $departmentBreakdown, $date) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Daily Manpower Report - ' . $date->format('F j, Y')]);
            fputcsv($file, []);
            fputcsv($file, ['Total Employees', $stats['total_employees']]);
            fputcsv($file, ['Present', $stats['present']]);
            fputcsv($file, ['Absent', $stats['absent']]);
            fputcsv($file, ['Attendance Rate', $stats['attendance_rate'] . '%']);
            fputcsv($file, []);
            fputcsv($file, ['Device/Location Breakdown']);
            fputcsv($file, ['Location', 'Present', 'Late', 'On Time']);
            
            foreach ($deviceBreakdown as $item) {
                fputcsv($file, [
                    $item['device']->location_name,
                    $item['present'],
                    $item['late'],
                    $item['on_time'],
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['Department Breakdown']);
            fputcsv($file, ['Department', 'Total', 'Present', 'Absent', 'Attendance Rate']);
            
            foreach ($departmentBreakdown as $item) {
                fputcsv($file, [
                    $item['department']->name,
                    $item['total'],
                    $item['present'],
                    $item['absent'],
                    $item['attendance_rate'] . '%',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportDailyPDF($stats, $deviceBreakdown, $departmentBreakdown, $date, $request)
    {
        $pdf = Pdf::loadView('manpower-reports.exports.daily-pdf', [
            'stats' => $stats,
            'deviceBreakdown' => $deviceBreakdown,
            'departmentBreakdown' => $departmentBreakdown,
            'date' => $date,
        ]);
        return $pdf->download('daily_manpower_report_' . $date->format('Y-m-d') . '.pdf');
    }

    private function exportWeeklyExcel($stats, $dailyBreakdown, $request)
    {
        $filename = 'weekly_manpower_report_' . $stats['week_start']->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($stats, $dailyBreakdown) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Weekly Manpower Report']);
            fputcsv($file, ['Week: ' . $stats['week_start']->format('M j') . ' - ' . $stats['week_end']->format('M j, Y')]);
            fputcsv($file, []);
            fputcsv($file, ['Date', 'Day', 'Present', 'Late', 'Absent', 'Attendance Rate']);
            
            foreach ($dailyBreakdown as $day) {
                fputcsv($file, [
                    $day['date'],
                    $day['day'],
                    $day['present'],
                    $day['late'],
                    $day['absent'],
                    $day['attendance_rate'] . '%',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportWeeklyPDF($stats, $dailyBreakdown, $request)
    {
        $pdf = Pdf::loadView('manpower-reports.exports.weekly-pdf', [
            'stats' => $stats,
            'dailyBreakdown' => $dailyBreakdown,
        ]);
        return $pdf->download('weekly_manpower_report_' . $stats['week_start']->format('Y-m-d') . '.pdf');
    }

    private function exportMonthlyExcel($stats, $dailyBreakdown, $weeklySummary, $request)
    {
        $filename = 'monthly_manpower_report_' . date('Y-m') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($stats, $dailyBreakdown, $weeklySummary) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Monthly Manpower Report - ' . $stats['month']]);
            fputcsv($file, []);
            fputcsv($file, ['Date', 'Day', 'Present', 'Absent', 'Attendance Rate']);
            
            foreach ($dailyBreakdown as $day) {
                fputcsv($file, [
                    $day['date'],
                    $day['day'],
                    $day['present'],
                    $day['absent'],
                    $day['attendance_rate'] . '%',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportMonthlyPDF($stats, $dailyBreakdown, $weeklySummary, $request)
    {
        $pdf = Pdf::loadView('manpower-reports.exports.monthly-pdf', [
            'stats' => $stats,
            'dailyBreakdown' => $dailyBreakdown,
            'weeklySummary' => $weeklySummary,
        ]);
        return $pdf->download('monthly_manpower_report_' . date('Y-m') . '.pdf');
    }

    private function exportLocationExcel($locationStats, $date, $request)
    {
        $filename = 'location_manpower_report_' . $date->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($locationStats) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Location', 'Company', 'Present', 'Late', 'Total']);
            
            foreach ($locationStats as $stat) {
                fputcsv($file, [
                    $stat['location'],
                    $stat['company'],
                    $stat['present'],
                    $stat['late'],
                    $stat['total'],
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportLocationPDF($locationStats, $date, $request)
    {
        $pdf = Pdf::loadView('manpower-reports.exports.location-pdf', [
            'locationStats' => $locationStats,
            'date' => $date,
        ]);
        return $pdf->download('location_manpower_report_' . $date->format('Y-m-d') . '.pdf');
    }
}
