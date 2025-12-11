<?php

namespace App\Http\Controllers;

use App\Models\JSA;
use App\Models\Department;
use App\Models\User;
use App\Models\RiskAssessment;
use App\Traits\UsesCompanyGroup;
use App\Traits\ChecksPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JSAController extends Controller
{
    use UsesCompanyGroup, ChecksPermissions;

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $query = JSA::whereIn('company_id', $companyGroupIds)
            ->with(['creator', 'supervisor', 'department']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('job_description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('overall_risk_level')) {
            $query->byRiskLevel($request->overall_risk_level);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        $jsas = $query->latest()->paginate(15);
        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        
        // Statistics
        $stats = [
            'total' => JSA::whereIn('company_id', $companyGroupIds)->count(),
            'approved' => JSA::whereIn('company_id', $companyGroupIds)->approved()->count(),
            'high_risk' => JSA::whereIn('company_id', $companyGroupIds)->byRiskLevel('high')->count(),
            'critical_risk' => JSA::whereIn('company_id', $companyGroupIds)->byRiskLevel('critical')->count(),
        ];
        
        return view('risk-assessment.jsas.index', compact('jsas', 'departments', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        $supervisors = User::whereIn('company_id', $companyGroupIds)->where('is_active', true)->get();
        $users = User::whereIn('company_id', $companyGroupIds)->where('is_active', true)->get();
        $riskAssessments = RiskAssessment::whereIn('company_id', $companyGroupIds)->active()->get();
        
        // Pre-select risk assessment if provided
        $selectedRiskAssessment = null;
        if ($request->has('risk_assessment_id')) {
            $selectedRiskAssessment = RiskAssessment::whereIn('company_id', $companyGroupIds)->findOrFail($request->risk_assessment_id);
        }
        
        return view('risk-assessment.jsas.create', compact('departments', 'supervisors', 'users', 'riskAssessments', 'selectedRiskAssessment'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'work_area' => 'nullable|string|max:255',
            'job_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'job_steps' => 'required|array|min:1',
            'job_steps.*.step_number' => 'required|integer',
            'job_steps.*.description' => 'required|string',
            'job_steps.*.hazards' => 'nullable|array',
            'job_steps.*.controls' => 'nullable|array',
            'team_members' => 'nullable|array',
            'required_qualifications' => 'nullable|string',
            'required_training' => 'nullable|string',
            'equipment_required' => 'nullable|array',
            'materials_required' => 'nullable|array',
            'ppe_required' => 'nullable|array',
            'weather_conditions' => 'nullable|string',
            'site_conditions' => 'nullable|string',
            'special_considerations' => 'nullable|string',
            'emergency_contacts' => 'nullable|string',
            'emergency_procedures' => 'nullable|string',
            'first_aid_location' => 'nullable|string',
            'evacuation_route' => 'nullable|string',
            'overall_risk_level' => 'required|in:low,medium,high,critical',
            'risk_summary' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'related_risk_assessment_id' => 'nullable|exists:risk_assessments,id',
            'status' => 'nullable|in:draft,pending_approval,approved,in_progress,completed,cancelled',
        ]);
        
        $validated['company_id'] = $companyId;
        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'draft';
        
        $jsa = JSA::create($validated);
        
        // Link to risk assessment if provided
        if ($jsa->related_risk_assessment_id) {
            $riskAssessment = RiskAssessment::find($jsa->related_risk_assessment_id);
            if ($riskAssessment && $riskAssessment->company_id === $companyId) {
                $riskAssessment->update(['related_jsa_id' => $jsa->id]);
            }
        }
        
        return redirect()
            ->route('risk-assessment.jsas.show', $jsa)
            ->with('success', 'JSA created successfully!');
    }

    public function show(JSA $jsa)
    {
        $this->authorizeCompanyResource($jsa->company_id);
        
        $jsa->load([
            'creator',
            'supervisor',
            'department',
            'approvedBy',
            'relatedRiskAssessment',
            'controlMeasures',
        ]);
        
        return view('risk-assessment.jsas.show', compact('jsa'));
    }

    public function edit(JSA $jsa)
    {
        $this->authorizeCompanyResource($jsa->company_id);
        
        if ($jsa->status === 'completed') {
            return back()->with('error', 'Cannot edit completed JSAs.');
        }
        
        $companyGroupIds = $this->getCompanyGroupIds();
        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        $supervisors = User::whereIn('company_id', $companyGroupIds)->where('is_active', true)->get();
        $users = User::whereIn('company_id', $companyGroupIds)->where('is_active', true)->get();
        $riskAssessments = RiskAssessment::whereIn('company_id', $companyGroupIds)->active()->get();
        
        return view('risk-assessment.jsas.edit', compact('jsa', 'departments', 'supervisors', 'users', 'riskAssessments'));
    }

    public function update(Request $request, JSA $jsa)
    {
        $this->authorizeCompanyResource($jsa->company_id);
        
        if ($jsa->status === 'completed') {
            return back()->with('error', 'Cannot edit completed JSAs.');
        }
        
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'work_area' => 'nullable|string|max:255',
            'job_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'job_steps' => 'required|array|min:1',
            'team_members' => 'nullable|array',
            'overall_risk_level' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:draft,pending_approval,approved,in_progress,completed,cancelled',
        ]);
        
        $jsa->update($validated);
        
        return redirect()
            ->route('risk-assessment.jsas.show', $jsa)
            ->with('success', 'JSA updated successfully!');
    }

    public function destroy(JSA $jsa)
    {
        $this->authorizeCompanyResource($jsa->company_id);
        
        if ($jsa->status === 'completed') {
            return back()->with('error', 'Cannot delete completed JSAs.');
        }
        
        $jsa->delete();
        
        return redirect()
            ->route('risk-assessment.jsas.index')
            ->with('success', 'JSA deleted successfully!');
    }

    public function approve(Request $request, JSA $jsa)
    {
        $this->authorizeCompanyResource($jsa->company_id);
        
        $jsa->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        return back()->with('success', 'JSA approved successfully!');
    }
}
