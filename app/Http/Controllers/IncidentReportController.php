<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Department;
use App\Models\User;
use App\Models\Company;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidentReportController extends Controller
{
    use UsesCompanyGroup;

    /**
     * Show reporting dashboard
     */
    public function index()
    {
        return view('incidents.reports.index');
    }

    /**
     * Department Incident Report
     */
    public function departmentReport(Request $request)
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
        $companyIds = $this->getCompanyGroupIds($companyId);
        
        // Get departments
        $departments = Department::whereIn('company_id', $companyIds)->get();
        
        // Get incidents in date range
        $incidents = Incident::whereIn('company_id', $companyIds)
            ->whereBetween('incident_date', [$startDate, $endDate])
            ->with(['department', 'investigation'])
            ->get();
        
        // Calculate department statistics
        $departmentStats = $departments->map(function($dept) use ($incidents) {
            $deptIncidents = $incidents->where('department_id', $dept->id);
            $totalIncidents = $deptIncidents->count();
            $openIncidents = $deptIncidents->whereIn('status', ['reported', 'open'])->count();
            $investigatingIncidents = $deptIncidents->where('status', 'investigating')->count();
            $closedIncidents = $deptIncidents->whereIn('status', ['closed', 'resolved'])->count();
            $criticalIncidents = $deptIncidents->where('severity', 'critical')->count();
            $injuryIncidents = $deptIncidents->where('event_type', 'injury_illness')->count();
            
            return [
                'id' => $dept->id,
                'name' => $dept->name,
                'total_incidents' => $totalIncidents,
                'open' => $openIncidents,
                'investigating' => $investigatingIncidents,
                'closed' => $closedIncidents,
                'critical' => $criticalIncidents,
                'injury' => $injuryIncidents,
                'incidents' => $deptIncidents->values(),
            ];
        })->filter(function($dept) {
            return $dept['total_incidents'] > 0;
        })->sortByDesc('total_incidents');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportDepartmentExcel($departmentStats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportDepartmentPDF($departmentStats, $startDate, $endDate, $period);
        }
        
        return view('incidents.reports.department', compact(
            'departmentStats',
            'startDate',
            'endDate',
            'period',
            'date'
        ));
    }

    /**
     * Employee Incident Report
     */
    public function employeeReport(Request $request)
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
        
        // Get incidents in date range
        $incidents = Incident::whereIn('company_id', $companyIds)
            ->whereBetween('incident_date', [$startDate, $endDate])
            ->with(['reporter', 'assignedTo', 'department'])
            ->get();
        
        // Get employees
        $employees = User::whereIn('company_id', $companyIds)
            ->where('is_active', true)
            ->when($employeeId, function($query) use ($employeeId) {
                return $query->where('id', $employeeId);
            })
            ->with(['employee.department'])
            ->get();
        
        // Calculate employee statistics
        $employeeStats = $employees->map(function($employee) use ($incidents) {
            $reportedIncidents = $incidents->where('reported_by', $employee->id);
            $assignedIncidents = $incidents->where('assigned_to', $employee->id);
            $totalReported = $reportedIncidents->count();
            $totalAssigned = $assignedIncidents->count();
            $criticalReported = $reportedIncidents->where('severity', 'critical')->count();
            $injuryReported = $reportedIncidents->where('event_type', 'injury_illness')->count();
            
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'employee_id' => $employee->employee->employee_id_number ?? 'N/A',
                'department' => $employee->employee && $employee->employee->department ? $employee->employee->department->name : 'N/A',
                'total_reported' => $totalReported,
                'total_assigned' => $totalAssigned,
                'critical_reported' => $criticalReported,
                'injury_reported' => $injuryReported,
                'reported_incidents' => $reportedIncidents->values(),
                'assigned_incidents' => $assignedIncidents->values(),
            ];
        })->filter(function($emp) {
            return $emp['total_reported'] > 0 || $emp['total_assigned'] > 0;
        })->sortByDesc('total_reported');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportEmployeeExcel($employeeStats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportEmployeePDF($employeeStats, $startDate, $endDate, $period);
        }
        
        return view('incidents.reports.employee', compact(
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
     * Period Report (Day, Week, Month, Annual)
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
        
        // Get incidents in date range
        $incidents = Incident::whereIn('company_id', $companyIds)
            ->whereBetween('incident_date', [$startDate, $endDate])
            ->with(['department', 'reporter', 'assignedTo', 'investigation', 'rootCauseAnalysis'])
            ->orderBy('incident_date', 'desc')
            ->get();
        
        // Calculate statistics
        $stats = [
            'total_incidents' => $incidents->count(),
            'open' => $incidents->whereIn('status', ['reported', 'open'])->count(),
            'investigating' => $incidents->where('status', 'investigating')->count(),
            'closed' => $incidents->whereIn('status', ['closed', 'resolved'])->count(),
            'critical' => $incidents->where('severity', 'critical')->count(),
            'high' => $incidents->where('severity', 'high')->count(),
            'medium' => $incidents->where('severity', 'medium')->count(),
            'low' => $incidents->where('severity', 'low')->count(),
            'injury_illness' => $incidents->where('event_type', 'injury_illness')->count(),
            'property_damage' => $incidents->where('event_type', 'property_damage')->count(),
            'near_miss' => $incidents->where('event_type', 'near_miss')->count(),
        ];
        
        // Group by department
        $departmentBreakdown = $incidents->groupBy('department_id')->map(function($deptIncidents, $deptId) {
            $dept = $deptIncidents->first()->department;
            return [
                'name' => $dept ? $dept->name : 'No Department',
                'count' => $deptIncidents->count(),
                'critical' => $deptIncidents->where('severity', 'critical')->count(),
            ];
        })->values();
        
        // Group by event type
        $eventTypeBreakdown = $incidents->groupBy('event_type')->map(function($typeIncidents, $type) {
            return [
                'name' => ucfirst(str_replace('_', ' ', $type ?: 'Unknown')),
                'count' => $typeIncidents->count(),
                'critical' => $typeIncidents->where('severity', 'critical')->count(),
            ];
        })->values()->sortByDesc('count');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportPeriodExcel($incidents, $stats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportPeriodPDF($incidents, $stats, $startDate, $endDate, $period);
        }
        
        return view('incidents.reports.period', compact(
            'incidents',
            'stats',
            'departmentBreakdown',
            'eventTypeBreakdown',
            'startDate',
            'endDate',
            'period',
            'date'
        ));
    }

    /**
     * Companies Report
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
        
        // Get incidents for all companies
        $incidents = Incident::whereIn('company_id', $companyIds)
            ->whereBetween('incident_date', [$startDate, $endDate])
            ->with(['company', 'department', 'investigation'])
            ->get();
        
        // Calculate company statistics
        $companyStats = $companies->map(function($company) use ($incidents) {
            $companyIncidents = $incidents->where('company_id', $company->id);
            $totalIncidents = $companyIncidents->count();
            $openIncidents = $companyIncidents->whereIn('status', ['reported', 'open'])->count();
            $closedIncidents = $companyIncidents->whereIn('status', ['closed', 'resolved'])->count();
            $criticalIncidents = $companyIncidents->where('severity', 'critical')->count();
            $injuryIncidents = $companyIncidents->where('event_type', 'injury_illness')->count();
            
            return [
                'id' => $company->id,
                'name' => $company->name,
                'total_incidents' => $totalIncidents,
                'open' => $openIncidents,
                'closed' => $closedIncidents,
                'critical' => $criticalIncidents,
                'injury' => $injuryIncidents,
                'incidents' => $companyIncidents->values(),
            ];
        })->filter(function($comp) {
            return $comp['total_incidents'] > 0;
        })->sortByDesc('total_incidents');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportCompaniesExcel($companyStats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportCompaniesPDF($companyStats, $startDate, $endDate, $period);
        }
        
        return view('incidents.reports.companies', compact(
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

    // Excel Export Methods
    private function exportDepartmentExcel($departmentStats, $startDate, $endDate, $period)
    {
        $filename = "incidents-department-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($departmentStats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            // Header rows
            fputcsv($file, ['Department Incident Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, ['Department', 'Total Incidents', 'Open', 'Investigating', 'Closed', 'Critical', 'Injury/Illness']);
            
            // Data rows
            foreach ($departmentStats as $dept) {
                fputcsv($file, [
                    $dept['name'],
                    $dept['total_incidents'],
                    $dept['open'],
                    $dept['investigating'],
                    $dept['closed'],
                    $dept['critical'],
                    $dept['injury'],
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportEmployeeExcel($employeeStats, $startDate, $endDate, $period)
    {
        $filename = "incidents-employee-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($employeeStats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            // Header rows
            fputcsv($file, ['Employee Incident Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, ['Employee Name', 'Employee ID', 'Department', 'Total Reported', 'Total Assigned', 'Critical Reported', 'Injury Reported']);
            
            // Data rows
            foreach ($employeeStats as $emp) {
                fputcsv($file, [
                    $emp['name'],
                    $emp['employee_id'],
                    $emp['department'],
                    $emp['total_reported'],
                    $emp['total_assigned'],
                    $emp['critical_reported'],
                    $emp['injury_reported'],
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPeriodExcel($incidents, $stats, $startDate, $endDate, $period)
    {
        $filename = "incidents-period-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($incidents, $stats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            // Header rows
            fputcsv($file, ['Period Incident Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            // Statistics summary
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Incidents', $stats['total_incidents']]);
            fputcsv($file, ['Open', $stats['open']]);
            fputcsv($file, ['Investigating', $stats['investigating']]);
            fputcsv($file, ['Closed', $stats['closed']]);
            fputcsv($file, ['Critical', $stats['critical']]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, ['Reference', 'Title', 'Date', 'Status', 'Severity', 'Event Type', 'Department', 'Reporter', 'Assigned To', 'Location']);
            
            // Data rows
            foreach ($incidents as $incident) {
                fputcsv($file, [
                    $incident->reference_number,
                    $incident->title ?? $incident->incident_type,
                    $incident->incident_date->format('Y-m-d'),
                    ucfirst($incident->status),
                    ucfirst($incident->severity),
                    ucfirst(str_replace('_', ' ', $incident->event_type ?? 'N/A')),
                    $incident->department?->name ?? 'N/A',
                    $incident->reporter?->name ?? 'N/A',
                    $incident->assignedTo?->name ?? 'N/A',
                    $incident->location ?? 'N/A',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCompaniesExcel($companyStats, $startDate, $endDate, $period)
    {
        $filename = "incidents-companies-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($companyStats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            // Header rows
            fputcsv($file, ['Companies Incident Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, ['Company', 'Total Incidents', 'Open', 'Closed', 'Critical', 'Injury/Illness']);
            
            // Data rows
            foreach ($companyStats as $comp) {
                fputcsv($file, [
                    $comp['name'],
                    $comp['total_incidents'],
                    $comp['open'],
                    $comp['closed'],
                    $comp['critical'],
                    $comp['injury'],
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // PDF Export Methods
    private function exportDepartmentPDF($departmentStats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('incidents.reports.exports.department-pdf', [
            'departmentStats' => $departmentStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("incidents-department-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportEmployeePDF($employeeStats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('incidents.reports.exports.employee-pdf', [
            'employeeStats' => $employeeStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("incidents-employee-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportPeriodPDF($incidents, $stats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('incidents.reports.exports.period-pdf', [
            'incidents' => $incidents,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("incidents-period-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportCompaniesPDF($companyStats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('incidents.reports.exports.companies-pdf', [
            'companyStats' => $companyStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("incidents-companies-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }
}

