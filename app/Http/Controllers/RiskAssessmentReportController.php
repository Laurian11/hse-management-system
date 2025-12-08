<?php

namespace App\Http\Controllers;

use App\Models\RiskAssessment;
use App\Models\Department;
use App\Models\User;
use App\Models\Company;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RiskAssessmentReportController extends Controller
{
    use UsesCompanyGroup;

    /**
     * Show reporting dashboard
     */
    public function index()
    {
        return view('risk-assessment.reports.index');
    }

    /**
     * Department Risk Assessment Report
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
        $companyIds = $this->getCompanyGroupIds();
        
        // Get departments
        $departments = Department::whereIn('company_id', $companyIds)->get();
        
        // Get assessments in date range
        $assessments = RiskAssessment::whereIn('company_id', $companyIds)
            ->whereBetween('assessment_date', [$startDate, $endDate])
            ->with(['department', 'hazard'])
            ->get();
        
        // Calculate department statistics
        $departmentStats = $departments->map(function($dept) use ($assessments) {
            $deptAssessments = $assessments->where('department_id', $dept->id);
            $totalAssessments = $deptAssessments->count();
            $highRiskAssessments = $deptAssessments->whereIn('risk_level', ['high', 'critical', 'extreme'])->count();
            $approvedAssessments = $deptAssessments->where('status', 'approved')->count();
            $dueForReview = $deptAssessments->filter(function($a) {
                return $a->next_review_date && $a->next_review_date->isPast();
            })->count();
            
            return [
                'id' => $dept->id,
                'name' => $dept->name,
                'total_assessments' => $totalAssessments,
                'high_risk' => $highRiskAssessments,
                'approved' => $approvedAssessments,
                'due_for_review' => $dueForReview,
                'assessments' => $deptAssessments->values(),
            ];
        })->filter(function($dept) {
            return $dept['total_assessments'] > 0;
        })->sortByDesc('total_assessments');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportDepartmentExcel($departmentStats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportDepartmentPDF($departmentStats, $startDate, $endDate, $period);
        }
        
        return view('risk-assessment.reports.department', compact(
            'departmentStats',
            'startDate',
            'endDate',
            'period',
            'date'
        ));
    }

    /**
     * Employee Risk Assessment Report
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
        
        // Get assessments in date range
        $assessments = RiskAssessment::whereIn('company_id', $companyIds)
            ->whereBetween('assessment_date', [$startDate, $endDate])
            ->with(['creator', 'assignedTo', 'department'])
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
        $employeeStats = $employees->map(function($employee) use ($assessments) {
            $createdAssessments = $assessments->where('created_by', $employee->id);
            $assignedAssessments = $assessments->where('assigned_to', $employee->id);
            $totalCreated = $createdAssessments->count();
            $totalAssigned = $assignedAssessments->count();
            $highRiskCreated = $createdAssessments->whereIn('risk_level', ['high', 'critical', 'extreme'])->count();
            
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'employee_id' => $employee->employee->employee_id_number ?? 'N/A',
                'department' => $employee->employee && $employee->employee->department ? $employee->employee->department->name : 'N/A',
                'total_created' => $totalCreated,
                'total_assigned' => $totalAssigned,
                'high_risk_created' => $highRiskCreated,
                'created_assessments' => $createdAssessments->values(),
                'assigned_assessments' => $assignedAssessments->values(),
            ];
        })->filter(function($emp) {
            return $emp['total_created'] > 0 || $emp['total_assigned'] > 0;
        })->sortByDesc('total_created');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportEmployeeExcel($employeeStats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportEmployeePDF($employeeStats, $startDate, $endDate, $period);
        }
        
        return view('risk-assessment.reports.employee', compact(
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
        
        // Get assessments in date range
        $assessments = RiskAssessment::whereIn('company_id', $companyIds)
            ->whereBetween('assessment_date', [$startDate, $endDate])
            ->with(['department', 'creator', 'assignedTo', 'hazard'])
            ->orderBy('assessment_date', 'desc')
            ->get();
        
        // Calculate statistics
        $stats = [
            'total_assessments' => $assessments->count(),
            'high_risk' => $assessments->whereIn('risk_level', ['high', 'critical', 'extreme'])->count(),
            'approved' => $assessments->where('status', 'approved')->count(),
            'due_for_review' => $assessments->filter(function($a) {
                return $a->next_review_date && $a->next_review_date->isPast();
            })->count(),
            'extreme' => $assessments->where('risk_level', 'extreme')->count(),
            'critical' => $assessments->where('risk_level', 'critical')->count(),
            'high' => $assessments->where('risk_level', 'high')->count(),
            'medium' => $assessments->where('risk_level', 'medium')->count(),
            'low' => $assessments->where('risk_level', 'low')->count(),
        ];
        
        // Group by department
        $departmentBreakdown = $assessments->groupBy('department_id')->map(function($deptAssessments, $deptId) {
            $dept = $deptAssessments->first()->department;
            return [
                'name' => $dept ? $dept->name : 'No Department',
                'count' => $deptAssessments->count(),
                'high_risk' => $deptAssessments->whereIn('risk_level', ['high', 'critical', 'extreme'])->count(),
            ];
        })->values();
        
        // Group by risk level
        $riskLevelBreakdown = $assessments->groupBy('risk_level')->map(function($levelAssessments, $level) {
            return [
                'name' => ucfirst($level ?: 'Unknown'),
                'count' => $levelAssessments->count(),
            ];
        })->values()->sortByDesc('count');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportPeriodExcel($assessments, $stats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportPeriodPDF($assessments, $stats, $startDate, $endDate, $period);
        }
        
        return view('risk-assessment.reports.period', compact(
            'assessments',
            'stats',
            'departmentBreakdown',
            'riskLevelBreakdown',
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
        // Get date range
        $period = $request->get('period', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));
        $dateObj = Carbon::parse($date);
        
        $startDate = $this->getStartDate($dateObj, $period);
        $endDate = $this->getEndDate($dateObj, $period);
        
        // Get company group
        $companyIds = $this->getCompanyGroupIds();
        $companies = Company::whereIn('id', $companyIds)->get();
        
        // Get assessments for all companies
        $assessments = RiskAssessment::whereIn('company_id', $companyIds)
            ->whereBetween('assessment_date', [$startDate, $endDate])
            ->with(['company', 'department', 'hazard'])
            ->get();
        
        // Calculate company statistics
        $companyStats = $companies->map(function($company) use ($assessments) {
            $companyAssessments = $assessments->where('company_id', $company->id);
            $totalAssessments = $companyAssessments->count();
            $highRiskAssessments = $companyAssessments->whereIn('risk_level', ['high', 'critical', 'extreme'])->count();
            $approvedAssessments = $companyAssessments->where('status', 'approved')->count();
            $dueForReview = $companyAssessments->filter(function($a) {
                return $a->next_review_date && $a->next_review_date->isPast();
            })->count();
            
            return [
                'id' => $company->id,
                'name' => $company->name,
                'total_assessments' => $totalAssessments,
                'high_risk' => $highRiskAssessments,
                'approved' => $approvedAssessments,
                'due_for_review' => $dueForReview,
                'assessments' => $companyAssessments->values(),
            ];
        })->filter(function($comp) {
            return $comp['total_assessments'] > 0;
        })->sortByDesc('total_assessments');
        
        $format = $request->get('format', 'html');
        
        if ($format === 'excel') {
            return $this->exportCompaniesExcel($companyStats, $startDate, $endDate, $period);
        } elseif ($format === 'pdf') {
            return $this->exportCompaniesPDF($companyStats, $startDate, $endDate, $period);
        }
        
        return view('risk-assessment.reports.companies', compact(
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
        $filename = "risk-assessments-department-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($departmentStats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Department Risk Assessment Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            fputcsv($file, ['Department', 'Total Assessments', 'High Risk', 'Approved', 'Due for Review']);
            
            foreach ($departmentStats as $dept) {
                fputcsv($file, [
                    $dept['name'],
                    $dept['total_assessments'],
                    $dept['high_risk'],
                    $dept['approved'],
                    $dept['due_for_review'],
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportEmployeeExcel($employeeStats, $startDate, $endDate, $period)
    {
        $filename = "risk-assessments-employee-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($employeeStats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Employee Risk Assessment Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            fputcsv($file, ['Employee Name', 'Employee ID', 'Department', 'Total Created', 'Total Assigned', 'High Risk Created']);
            
            foreach ($employeeStats as $emp) {
                fputcsv($file, [
                    $emp['name'],
                    $emp['employee_id'],
                    $emp['department'],
                    $emp['total_created'],
                    $emp['total_assigned'],
                    $emp['high_risk_created'],
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPeriodExcel($assessments, $stats, $startDate, $endDate, $period)
    {
        $filename = "risk-assessments-period-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($assessments, $stats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Period Risk Assessment Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            fputcsv($file, ['Summary Statistics']);
            fputcsv($file, ['Total Assessments', $stats['total_assessments']]);
            fputcsv($file, ['High Risk', $stats['high_risk']]);
            fputcsv($file, ['Approved', $stats['approved']]);
            fputcsv($file, ['Due for Review', $stats['due_for_review']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Reference', 'Title', 'Date', 'Status', 'Risk Level', 'Risk Score', 'Department', 'Creator', 'Assigned To']);
            
            foreach ($assessments as $assessment) {
                fputcsv($file, [
                    $assessment->reference_number,
                    $assessment->title,
                    $assessment->assessment_date->format('Y-m-d'),
                    ucfirst($assessment->status),
                    ucfirst($assessment->risk_level),
                    $assessment->risk_score,
                    $assessment->department?->name ?? 'N/A',
                    $assessment->creator?->name ?? 'N/A',
                    $assessment->assignedTo?->name ?? 'N/A',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCompaniesExcel($companyStats, $startDate, $endDate, $period)
    {
        $filename = "risk-assessments-companies-{$period}-" . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($companyStats, $startDate, $endDate, $period) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Companies Risk Assessment Report']);
            fputcsv($file, ['Period: ' . ucfirst($period)]);
            fputcsv($file, ['Date Range: ' . $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')]);
            fputcsv($file, []);
            
            fputcsv($file, ['Company', 'Total Assessments', 'High Risk', 'Approved', 'Due for Review']);
            
            foreach ($companyStats as $comp) {
                fputcsv($file, [
                    $comp['name'],
                    $comp['total_assessments'],
                    $comp['high_risk'],
                    $comp['approved'],
                    $comp['due_for_review'],
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // PDF Export Methods
    private function exportDepartmentPDF($departmentStats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('risk-assessment.reports.exports.department-pdf', [
            'departmentStats' => $departmentStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("risk-assessments-department-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportEmployeePDF($employeeStats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('risk-assessment.reports.exports.employee-pdf', [
            'employeeStats' => $employeeStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("risk-assessments-employee-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportPeriodPDF($assessments, $stats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('risk-assessment.reports.exports.period-pdf', [
            'assessments' => $assessments,
            'stats' => $stats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("risk-assessments-period-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }

    private function exportCompaniesPDF($companyStats, $startDate, $endDate, $period)
    {
        $pdf = Pdf::loadView('risk-assessment.reports.exports.companies-pdf', [
            'companyStats' => $companyStats,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'period' => $period,
        ]);
        
        return $pdf->download("risk-assessments-companies-{$period}-" . now()->format('Y-m-d') . '.pdf');
    }
}

