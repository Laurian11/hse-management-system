<?php

namespace App\Http\Controllers;

use App\Models\ControlMeasure;
use App\Models\RiskAssessment;
use App\Models\Hazard;
use App\Models\JSA;
use App\Models\Incident;
use App\Models\CAPA;
use App\Models\User;
use App\Notifications\ControlMeasureVerificationRequiredNotification;
use App\Traits\ChecksPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControlMeasureController extends Controller
{
    use ChecksPermissions;
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = ControlMeasure::forCompany($companyId)
            ->with(['riskAssessment', 'hazard', 'jsa', 'assignedTo', 'responsibleParty']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('control_type')) {
            $query->byControlType($request->control_type);
        }
        
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('overdue')) {
            $query->overdue();
        }
        
        if ($request->filled('risk_assessment_id')) {
            $query->where('risk_assessment_id', $request->risk_assessment_id);
        }
        
        $controlMeasures = $query->latest()->paginate(15);
        
        // Statistics
        $stats = [
            'total' => ControlMeasure::forCompany($companyId)->count(),
            'planned' => ControlMeasure::forCompany($companyId)->byStatus('planned')->count(),
            'implemented' => ControlMeasure::forCompany($companyId)->byStatus('implemented')->count(),
            'verified' => ControlMeasure::forCompany($companyId)->byStatus('verified')->count(),
            'overdue' => ControlMeasure::forCompany($companyId)->overdue()->count(),
        ];
        
        return view('risk-assessment.control-measures.index', compact('controlMeasures', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        
        // Pre-select based on context
        $riskAssessment = null;
        $hazard = null;
        $jsa = null;
        $incident = null;
        $capa = null;
        
        if ($request->has('risk_assessment_id')) {
            $riskAssessment = RiskAssessment::forCompany($companyId)->findOrFail($request->risk_assessment_id);
        }
        
        if ($request->has('hazard_id')) {
            $hazard = Hazard::forCompany($companyId)->findOrFail($request->hazard_id);
        }
        
        if ($request->has('jsa_id')) {
            $jsa = JSA::forCompany($companyId)->findOrFail($request->jsa_id);
        }
        
        if ($request->has('incident_id')) {
            $incident = Incident::where('company_id', $companyId)->findOrFail($request->incident_id);
        }
        
        if ($request->has('capa_id')) {
            $capa = CAPA::where('company_id', $companyId)->findOrFail($request->capa_id);
        }
        
        return view('risk-assessment.control-measures.create', compact(
            'users', 'riskAssessment', 'hazard', 'jsa', 'incident', 'capa'
        ));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'risk_assessment_id' => 'nullable|exists:risk_assessments,id',
            'hazard_id' => 'nullable|exists:hazards,id',
            'jsa_id' => 'nullable|exists:jsas,id',
            'incident_id' => 'nullable|exists:incidents,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'control_type' => 'required|in:elimination,substitution,engineering,administrative,ppe,combination',
            'effectiveness_level' => 'nullable|in:low,medium,high,very_high',
            'status' => 'nullable|in:planned,in_progress,implemented,verified,ineffective,closed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'responsible_party' => 'nullable|exists:users,id',
            'target_completion_date' => 'nullable|date|after:today',
            'estimated_cost' => 'nullable|numeric|min:0',
            'resources_required' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
            'related_capa_id' => 'nullable|exists:capas,id',
        ]);
        
        // Ensure at least one parent relationship
        if (!$validated['risk_assessment_id'] && !$validated['hazard_id'] && !$validated['jsa_id'] && !$validated['incident_id']) {
            return back()->withErrors(['error' => 'Control measure must be linked to a risk assessment, hazard, JSA, or incident.'])->withInput();
        }
        
        $validated['company_id'] = $companyId;
        $validated['status'] = $validated['status'] ?? 'planned';
        
        $controlMeasure = ControlMeasure::create($validated);
        $controlMeasure->load('assignedTo', 'responsibleParty');
        
        // Send notification to assigned user or responsible party
        $notifyUser = $controlMeasure->assignedTo ?? $controlMeasure->responsibleParty;
        
        if ($notifyUser) {
            $notifyUser->notify(new ControlMeasureVerificationRequiredNotification($controlMeasure));
        }
        
        return redirect()
            ->route('risk-assessment.control-measures.show', $controlMeasure)
            ->with('success', 'Control measure created successfully!');
    }

    public function show(ControlMeasure $controlMeasure)
    {
        $this->authorizeCompanyResource($controlMeasure->company_id);
        
        $controlMeasure->load([
            'riskAssessment',
            'hazard',
            'jsa',
            'incident',
            'assignedTo',
            'responsibleParty',
            'verifiedBy',
            'relatedCAPA',
            'relatedTrainingNeed',
            'relatedTrainingPlan',
        ]);
        
        return view('risk-assessment.control-measures.show', compact('controlMeasure'));
    }

    public function edit(ControlMeasure $controlMeasure)
    {
        $this->authorizeCompanyResource($controlMeasure->company_id);
        
        $user = Auth::user();
        $companyId = $user->company_id;
        
        // For super admin, get all users
        if (!$companyId) {
            $users = User::where('is_active', true)->get();
        } else {
            $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        }
        
        return view('risk-assessment.control-measures.edit', compact('controlMeasure', 'users'));
    }

    public function update(Request $request, ControlMeasure $controlMeasure)
    {
        $this->authorizeCompanyResource($controlMeasure->company_id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'control_type' => 'required|in:elimination,substitution,engineering,administrative,ppe,combination',
            'effectiveness_level' => 'nullable|in:low,medium,high,very_high',
            'status' => 'required|in:planned,in_progress,implemented,verified,ineffective,closed,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'responsible_party' => 'nullable|exists:users,id',
            'target_completion_date' => 'nullable|date',
            'actual_completion_date' => 'nullable|date',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'resources_required' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,critical',
        ]);
        
        // Update status-specific fields
        if ($validated['status'] === 'implemented' && !$controlMeasure->actual_completion_date) {
            $validated['actual_completion_date'] = now();
        }
        
        if ($validated['status'] === 'verified' && !$controlMeasure->verification_date) {
            $validated['verification_date'] = now();
            $validated['verified_by'] = Auth::id();
        }
        
        $controlMeasure->update($validated);
        
        return redirect()
            ->route('risk-assessment.control-measures.show', $controlMeasure)
            ->with('success', 'Control measure updated successfully!');
    }

    public function destroy(ControlMeasure $controlMeasure)
    {
        $this->authorizeCompanyResource($controlMeasure->company_id);
        
        if (in_array($controlMeasure->status, ['implemented', 'verified'])) {
            return back()->with('error', 'Cannot delete implemented or verified control measures.');
        }
        
        $controlMeasure->delete();
        
        return redirect()
            ->route('risk-assessment.control-measures.index')
            ->with('success', 'Control measure deleted successfully!');
    }

    public function verify(Request $request, ControlMeasure $controlMeasure)
    {
        $this->authorizeCompanyResource($controlMeasure->company_id);
        
        $validated = $request->validate([
            'verification_method' => 'required|string',
            'verification_results' => 'required|string',
            'is_effective' => 'required|boolean',
            'effectiveness_notes' => 'nullable|string',
        ]);
        
        $controlMeasure->update([
            'status' => 'verified',
            'verification_date' => now(),
            'verified_by' => Auth::id(),
            ...$validated,
        ]);
        
        return back()->with('success', 'Control measure verified successfully!');
    }
}
