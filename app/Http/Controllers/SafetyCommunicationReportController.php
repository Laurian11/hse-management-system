<?php

namespace App\Http\Controllers;

use App\Models\SafetyCommunication;
use App\Models\Department;
use App\Models\User;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SafetyCommunicationReportController extends Controller
{
    use UsesCompanyGroup;

    public function index()
    {
        return view('safety-communications.reports.index');
    }

    public function departmentReport(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $query = SafetyCommunication::whereIn('company_id', $companyGroupIds)
            ->with(['company', 'creator']);
        
        // Filters
        if ($request->filled('department_id')) {
            $departmentId = $request->department_id;
            $query->where(function($q) use ($departmentId) {
                $q->where('target_audience', 'all_employees')
                  ->orWhereJsonContains('target_departments', $departmentId);
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $communications = $query->get();
        
        // Group by department
        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        $departmentStats = [];
        
        foreach ($departments as $department) {
            $deptCommunications = $communications->filter(function($comm) use ($department) {
                return $comm->target_audience === 'all_employees' || 
                       (is_array($comm->target_departments) && in_array($department->id, $comm->target_departments));
            });
            
            $departmentStats[] = [
                'department' => $department,
                'total' => $deptCommunications->count(),
                'sent' => $deptCommunications->where('status', 'sent')->count(),
                'scheduled' => $deptCommunications->where('status', 'scheduled')->count(),
                'draft' => $deptCommunications->where('status', 'draft')->count(),
                'avg_ack_rate' => $deptCommunications->where('status', 'sent')->avg('acknowledgment_rate') ?? 0,
            ];
        }
        
        if ($request->get('format') === 'pdf') {
            return $this->exportDepartmentPDF($departmentStats, $request);
        }
        
        if ($request->get('format') === 'excel') {
            return $this->exportDepartmentExcel($departmentStats, $request);
        }
        
        return view('safety-communications.reports.department', compact('departmentStats', 'departments'));
    }

    public function employeeReport(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $query = SafetyCommunication::whereIn('company_id', $companyGroupIds)
            ->with(['company', 'creator']);
        
        // Filters
        if ($request->filled('employee_id')) {
            $employee = User::find($request->employee_id);
            if ($employee) {
                $query->where(function($q) use ($employee) {
                    $q->where('target_audience', 'all_employees')
                      ->orWhere(function($q2) use ($employee) {
                          if ($employee->department_id) {
                              $q2->whereJsonContains('target_departments', $employee->department_id);
                          }
                          if ($employee->role) {
                              $q2->orWhereJsonContains('target_roles', $employee->role->name ?? '');
                          }
                      });
                });
            }
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $communications = $query->get();
        
        // Get employees
        $employees = User::whereIn('company_id', $companyGroupIds)
            ->whereNotNull('employee_id')
            ->with(['employee', 'role'])
            ->get();
        
        $employeeStats = [];
        
        foreach ($employees as $employee) {
            $empCommunications = $communications->filter(function($comm) use ($employee) {
                if ($comm->target_audience === 'all_employees') return true;
                if ($comm->target_audience === 'specific_departments' && $employee->department_id) {
                    return in_array($employee->department_id, $comm->target_departments ?? []);
                }
                if ($comm->target_audience === 'specific_roles' && $employee->role) {
                    return in_array($employee->role->name, $comm->target_roles ?? []);
                }
                return false;
            });
            
            $employeeStats[] = [
                'employee' => $employee,
                'total' => $empCommunications->count(),
                'sent' => $empCommunications->where('status', 'sent')->count(),
                'acknowledged' => 0, // Would need acknowledgment tracking table
            ];
        }
        
        if ($request->get('format') === 'pdf') {
            return $this->exportEmployeePDF($employeeStats, $request);
        }
        
        if ($request->get('format') === 'excel') {
            return $this->exportEmployeeExcel($employeeStats, $request);
        }
        
        return view('safety-communications.reports.employee', compact('employeeStats', 'employees'));
    }

    public function periodReport(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $period = $request->get('period', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));
        
        $query = SafetyCommunication::whereIn('company_id', $companyGroupIds)
            ->with(['company', 'creator']);
        
        switch ($period) {
            case 'day':
                $startDate = Carbon::parse($date)->startOfDay();
                $endDate = Carbon::parse($date)->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::parse($date)->startOfWeek();
                $endDate = Carbon::parse($date)->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::parse($date)->startOfMonth();
                $endDate = Carbon::parse($date)->endOfMonth();
                break;
            case 'annual':
                $startDate = Carbon::parse($date)->startOfYear();
                $endDate = Carbon::parse($date)->endOfYear();
                break;
            default:
                $startDate = Carbon::parse($date)->startOfMonth();
                $endDate = Carbon::parse($date)->endOfMonth();
        }
        
        $query->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $communications = $query->get();
        
        // Statistics
        $stats = [
            'total' => $communications->count(),
            'sent' => $communications->where('status', 'sent')->count(),
            'scheduled' => $communications->where('status', 'scheduled')->count(),
            'draft' => $communications->where('status', 'draft')->count(),
            'avg_ack_rate' => $communications->where('status', 'sent')->avg('acknowledgment_rate') ?? 0,
            'by_type' => $communications->groupBy('communication_type')->map->count(),
            'by_priority' => $communications->groupBy('priority_level')->map->count(),
        ];
        
        if ($request->get('format') === 'pdf') {
            return $this->exportPeriodPDF($communications, $stats, $period, $date, $request);
        }
        
        if ($request->get('format') === 'excel') {
            return $this->exportPeriodExcel($communications, $stats, $period, $date, $request);
        }
        
        return view('safety-communications.reports.period', compact('communications', 'stats', 'period', 'date'));
    }

    public function companiesReport(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $query = SafetyCommunication::whereIn('company_id', $companyGroupIds)
            ->with(['company', 'creator']);
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $communications = $query->get();
        
        // Group by company
        $companies = \App\Models\Company::whereIn('id', $companyGroupIds)->get();
        $companyStats = [];
        
        foreach ($companies as $company) {
            $companyComms = $communications->where('company_id', $company->id);
            
            $companyStats[] = [
                'company' => $company,
                'total' => $companyComms->count(),
                'sent' => $companyComms->where('status', 'sent')->count(),
                'scheduled' => $companyComms->where('status', 'scheduled')->count(),
                'draft' => $companyComms->where('status', 'draft')->count(),
                'avg_ack_rate' => $companyComms->where('status', 'sent')->avg('acknowledgment_rate') ?? 0,
                'by_type' => $companyComms->groupBy('communication_type')->map->count(),
            ];
        }
        
        if ($request->get('format') === 'pdf') {
            return $this->exportCompaniesPDF($companyStats, $request);
        }
        
        if ($request->get('format') === 'excel') {
            return $this->exportCompaniesExcel($companyStats, $request);
        }
        
        return view('safety-communications.reports.companies', compact('companyStats', 'companies'));
    }

    // Export methods
    private function exportDepartmentExcel($departmentStats, $request)
    {
        $filename = 'safety_communications_department_report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($departmentStats) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Department', 'Total', 'Sent', 'Scheduled', 'Draft', 'Avg. Acknowledgment Rate']);
            
            foreach ($departmentStats as $stat) {
                fputcsv($file, [
                    $stat['department']->name,
                    $stat['total'],
                    $stat['sent'],
                    $stat['scheduled'],
                    $stat['draft'],
                    number_format($stat['avg_ack_rate'], 2) . '%',
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportDepartmentPDF($departmentStats, $request)
    {
        $pdf = Pdf::loadView('safety-communications.reports.exports.department-pdf', [
            'departmentStats' => $departmentStats,
            'filters' => $request->all(),
        ]);
        return $pdf->download('safety_communications_department_report_' . date('Y-m-d_His') . '.pdf');
    }

    private function exportEmployeeExcel($employeeStats, $request)
    {
        $filename = 'safety_communications_employee_report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($employeeStats) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employee', 'Department', 'Total', 'Sent', 'Acknowledged']);
            
            foreach ($employeeStats as $stat) {
                fputcsv($file, [
                    $stat['employee']->name,
                    $stat['employee']->employee->department->name ?? 'N/A',
                    $stat['total'],
                    $stat['sent'],
                    $stat['acknowledged'],
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportEmployeePDF($employeeStats, $request)
    {
        $pdf = Pdf::loadView('safety-communications.reports.exports.employee-pdf', [
            'employeeStats' => $employeeStats,
            'filters' => $request->all(),
        ]);
        return $pdf->download('safety_communications_employee_report_' . date('Y-m-d_His') . '.pdf');
    }

    private function exportPeriodExcel($communications, $stats, $period, $date, $request)
    {
        $filename = 'safety_communications_period_report_' . $period . '_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($communications) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Reference', 'Title', 'Type', 'Priority', 'Status', 'Recipients', 'Acknowledged', 'Created']);
            
            foreach ($communications as $comm) {
                fputcsv($file, [
                    $comm->reference_number,
                    $comm->title,
                    ucfirst(str_replace('_', ' ', $comm->communication_type)),
                    ucfirst($comm->priority_level),
                    ucfirst($comm->status),
                    $comm->total_recipients ?? 0,
                    $comm->acknowledged_count ?? 0,
                    $comm->created_at->format('Y-m-d'),
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportPeriodPDF($communications, $stats, $period, $date, $request)
    {
        $pdf = Pdf::loadView('safety-communications.reports.exports.period-pdf', [
            'communications' => $communications,
            'stats' => $stats,
            'period' => $period,
            'date' => $date,
            'filters' => $request->all(),
        ]);
        return $pdf->download('safety_communications_period_report_' . $period . '_' . date('Y-m-d_His') . '.pdf');
    }

    private function exportCompaniesExcel($companyStats, $request)
    {
        $filename = 'safety_communications_companies_report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($companyStats) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Company', 'Total', 'Sent', 'Scheduled', 'Draft', 'Avg. Acknowledgment Rate']);
            
            foreach ($companyStats as $stat) {
                fputcsv($file, [
                    $stat['company']->name,
                    $stat['total'],
                    $stat['sent'],
                    $stat['scheduled'],
                    $stat['draft'],
                    number_format($stat['avg_ack_rate'], 2) . '%',
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function exportCompaniesPDF($companyStats, $request)
    {
        $pdf = Pdf::loadView('safety-communications.reports.exports.companies-pdf', [
            'companyStats' => $companyStats,
            'filters' => $request->all(),
        ]);
        return $pdf->download('safety_communications_companies_report_' . date('Y-m-d_His') . '.pdf');
    }
}

