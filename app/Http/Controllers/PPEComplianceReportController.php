<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PPEComplianceReport;
use App\Models\PPEIssuance;
use App\Models\PPEInspection;
use App\Models\Department;
use App\Models\User;
use App\Models\PPEItem;
use Carbon\Carbon;

class PPEComplianceReportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = PPEComplianceReport::forCompany($companyId);
        
        // Filters
        if ($request->filled('report_type')) {
            $query->where('report_type', $request->report_type);
        }
        
        if ($request->filled('scope')) {
            $query->where('scope', $request->scope);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $reports = $query->with(['generatedBy', 'department', 'user', 'ppeItem'])->latest()->paginate(20);
        
        return view('ppe.reports.index', compact('reports'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::where('company_id', $companyId)->active()->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        $items = PPEItem::forCompany($companyId)->active()->get();
        
        return view('ppe.reports.create', compact('departments', 'users', 'items'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'report_type' => 'required|string|max:255',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after:report_period_start',
            'scope' => 'required|in:company,department,user,item',
            'department_id' => 'nullable|exists:departments,id',
            'user_id' => 'nullable|exists:users,id',
            'ppe_item_id' => 'nullable|exists:ppe_items,id',
        ]);
        
        // Generate report data
        $reportData = $this->generateReportData($companyId, $validated);
        
        $validated['company_id'] = $companyId;
        $validated['generated_by'] = Auth::id();
        $validated['status'] = 'generated';
        $validated = array_merge($validated, $reportData);
        
        $report = PPEComplianceReport::create($validated);
        
        return redirect()->route('ppe.reports.show', $report)
            ->with('success', 'Compliance report generated successfully.');
    }

    public function show(PPEComplianceReport $report)
    {
        $report->load(['generatedBy', 'department', 'user', 'ppeItem']);
        
        return view('ppe.reports.show', compact('report'));
    }

    private function generateReportData($companyId, $filters)
    {
        $startDate = Carbon::parse($filters['report_period_start']);
        $endDate = Carbon::parse($filters['report_period_end']);
        
        $query = PPEIssuance::forCompany($companyId)
            ->whereBetween('issue_date', [$startDate, $endDate]);
        
        $inspectionQuery = PPEInspection::forCompany($companyId)
            ->whereBetween('inspection_date', [$startDate, $endDate]);
        
        // Apply scope filters
        if ($filters['scope'] === 'department' && $filters['department_id']) {
            $query->where('department_id', $filters['department_id']);
        }
        
        if ($filters['scope'] === 'user' && $filters['user_id']) {
            $query->where('issued_to', $filters['user_id']);
            $inspectionQuery->where('user_id', $filters['user_id']);
        }
        
        if ($filters['scope'] === 'item' && $filters['ppe_item_id']) {
            $query->where('ppe_item_id', $filters['ppe_item_id']);
            $inspectionQuery->where('ppe_item_id', $filters['ppe_item_id']);
        }
        
        $totalIssuances = $query->count();
        $activeIssuances = $query->clone()->active()->count();
        $expiredIssuances = $query->clone()->expired()->count();
        $overdueInspections = PPEIssuance::forCompany($companyId)
            ->needsInspection()
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->count();
        $nonCompliant = $inspectionQuery->clone()->nonCompliant()->count();
        
        $complianceRate = $totalIssuances > 0 
            ? (($totalIssuances - $nonCompliant) / $totalIssuances) * 100 
            : 0;
        
        $usageRate = $totalIssuances > 0 
            ? ($activeIssuances / $totalIssuances) * 100 
            : 0;
        
        return [
            'total_issuances' => $totalIssuances,
            'active_issuances' => $activeIssuances,
            'expired_issuances' => $expiredIssuances,
            'overdue_inspections' => $overdueInspections,
            'non_compliant_count' => $nonCompliant,
            'compliance_rate' => round($complianceRate, 2),
            'usage_rate' => round($usageRate, 2),
            'metrics' => [
                'total_issuances' => $totalIssuances,
                'active_issuances' => $activeIssuances,
                'expired_issuances' => $expiredIssuances,
                'overdue_inspections' => $overdueInspections,
                'non_compliant_count' => $nonCompliant,
            ],
        ];
    }
}

