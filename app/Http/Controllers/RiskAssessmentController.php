<?php

namespace App\Http\Controllers;

use App\Models\RiskAssessment;
use App\Models\Hazard;
use App\Models\Department;
use App\Models\User;
use App\Models\Incident;
use App\Notifications\RiskAssessmentApprovalRequiredNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiskAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = RiskAssessment::forCompany($companyId)
            ->with(['hazard', 'creator', 'assignedTo', 'department']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('risk_level')) {
            $query->byRiskLevel($request->risk_level);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->filled('assessment_type')) {
            $query->where('assessment_type', $request->assessment_type);
        }
        
        if ($request->filled('overdue_reviews')) {
            $query->dueForReview();
        }
        
        $riskAssessments = $query->latest()->paginate(15);
        $departments = Department::where('company_id', $companyId)->active()->get();
        
        // Statistics
        $stats = [
            'total' => RiskAssessment::forCompany($companyId)->count(),
            'high_risk' => RiskAssessment::forCompany($companyId)->highRisk()->count(),
            'due_for_review' => RiskAssessment::forCompany($companyId)->dueForReview()->count(),
            'approved' => RiskAssessment::forCompany($companyId)->where('status', 'approved')->count(),
        ];
        
        return view('risk-assessment.risk-assessments.index', compact('riskAssessments', 'departments', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $hazards = Hazard::forCompany($companyId)->active()->get();
        $departments = Department::where('company_id', $companyId)->active()->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        
        // Pre-select hazard if provided
        $selectedHazard = null;
        if ($request->has('hazard_id')) {
            $selectedHazard = Hazard::forCompany($companyId)->findOrFail($request->hazard_id);
        }
        
        // Pre-select incident if provided (for closed-loop)
        $selectedIncident = null;
        if ($request->has('incident_id')) {
            $selectedIncident = Incident::where('company_id', $companyId)->findOrFail($request->incident_id);
        }
        
        return view('risk-assessment.risk-assessments.create', compact('hazards', 'departments', 'users', 'selectedHazard', 'selectedIncident'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'hazard_id' => 'nullable|exists:hazards,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assessment_type' => 'required|in:general,process,task,equipment,chemical,workplace,environmental,other',
            'severity' => 'required|in:negligible,minor,moderate,major,catastrophic',
            'likelihood' => 'required|in:rare,unlikely,possible,likely,almost_certain',
            'severity_score' => 'required|integer|min:1|max:5',
            'likelihood_score' => 'required|integer|min:1|max:5',
            'existing_controls' => 'nullable|string',
            'existing_controls_effectiveness' => 'nullable|in:none,poor,adequate,good,excellent',
            'residual_severity' => 'nullable|in:negligible,minor,moderate,major,catastrophic',
            'residual_likelihood' => 'nullable|in:rare,unlikely,possible,likely,almost_certain',
            'residual_severity_score' => 'nullable|integer|min:1|max:5',
            'residual_likelihood_score' => 'nullable|integer|min:1|max:5',
            'is_alarp' => 'nullable|boolean',
            'alarp_justification' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'assessment_date' => 'nullable|date',
            'review_frequency' => 'nullable|in:monthly,quarterly,semi_annually,annually,biannually,on_change,on_incident,custom',
            'related_incident_id' => 'nullable|exists:incidents,id',
            'status' => 'nullable|in:draft,under_review,approved,implementation,monitoring,closed,archived',
        ]);
        
        // Calculate risk scores
        $riskScore = $validated['severity_score'] * $validated['likelihood_score'];
        $riskLevel = RiskAssessment::calculateRiskLevel($riskScore);
        
        $validated['company_id'] = $companyId;
        $validated['created_by'] = Auth::id();
        $validated['risk_score'] = $riskScore;
        $validated['risk_level'] = $riskLevel;
        $validated['assessment_date'] = $validated['assessment_date'] ?? now();
        $validated['status'] = $validated['status'] ?? 'draft';
        
        // Calculate residual risk if provided
        if (isset($validated['residual_severity_score']) && isset($validated['residual_likelihood_score'])) {
            $residualRiskScore = $validated['residual_severity_score'] * $validated['residual_likelihood_score'];
            $validated['residual_risk_score'] = $residualRiskScore;
            $validated['residual_risk_level'] = RiskAssessment::calculateRiskLevel($residualRiskScore);
        }
        
        // Calculate next review date based on frequency
        if ($validated['review_frequency']) {
            $validated['next_review_date'] = $this->calculateNextReviewDate($validated['review_frequency'], $validated['assessment_date']);
        }
        
        $riskAssessment = RiskAssessment::create($validated);
        
        // Update hazard status if linked
        if ($riskAssessment->hazard_id) {
            $hazard = Hazard::find($riskAssessment->hazard_id);
            if ($hazard && $hazard->status === 'identified') {
                $hazard->update(['status' => 'assessed']);
            }
        }
        
        // Link to incident if provided (closed-loop)
        if ($riskAssessment->related_incident_id) {
            $incident = Incident::find($riskAssessment->related_incident_id);
            if ($incident && $incident->company_id === $companyId) {
                $incident->update([
                    'related_risk_assessment_id' => $riskAssessment->id,
                    'hazard_was_identified' => true,
                ]);
            }
        }
        
        // Send notification if status is 'under_review' and has assigned approver
        if ($riskAssessment->status === 'under_review') {
            if ($riskAssessment->assignedTo) {
                $riskAssessment->assignedTo->notify(new RiskAssessmentApprovalRequiredNotification($riskAssessment));
            } else {
                // Notify HSE managers if no specific approver assigned
                $hseManagers = User::where('company_id', $companyId)
                    ->whereHas('role', function($q) {
                        $q->whereIn('name', ['hse_manager', 'admin']);
                    })
                    ->get();
                
                foreach ($hseManagers as $manager) {
                    $manager->notify(new RiskAssessmentApprovalRequiredNotification($riskAssessment));
                }
            }
        }
        
        return redirect()
            ->route('risk-assessment.risk-assessments.show', $riskAssessment)
            ->with('success', 'Risk assessment created successfully!');
    }

    public function show(RiskAssessment $riskAssessment)
    {
        if ($riskAssessment->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $riskAssessment->load([
            'hazard',
            'creator',
            'assignedTo',
            'department',
            'relatedIncident',
            'relatedJSA',
            'controlMeasures',
            'reviews',
        ]);
        
        return view('risk-assessment.risk-assessments.show', compact('riskAssessment'));
    }

    public function edit(RiskAssessment $riskAssessment)
    {
        if ($riskAssessment->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $companyId = Auth::user()->company_id;
        $hazards = Hazard::forCompany($companyId)->active()->get();
        $departments = Department::where('company_id', $companyId)->active()->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        
        return view('risk-assessment.risk-assessments.edit', compact('riskAssessment', 'hazards', 'departments', 'users'));
    }

    public function update(Request $request, RiskAssessment $riskAssessment)
    {
        if ($riskAssessment->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $validated = $request->validate([
            'hazard_id' => 'nullable|exists:hazards,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assessment_type' => 'required|in:general,process,task,equipment,chemical,workplace,environmental,other',
            'severity' => 'required|in:negligible,minor,moderate,major,catastrophic',
            'likelihood' => 'required|in:rare,unlikely,possible,likely,almost_certain',
            'severity_score' => 'required|integer|min:1|max:5',
            'likelihood_score' => 'required|integer|min:1|max:5',
            'existing_controls' => 'nullable|string',
            'existing_controls_effectiveness' => 'nullable|in:none,poor,adequate,good,excellent',
            'residual_severity' => 'nullable|in:negligible,minor,moderate,major,catastrophic',
            'residual_likelihood' => 'nullable|in:rare,unlikely,possible,likely,almost_certain',
            'residual_severity_score' => 'nullable|integer|min:1|max:5',
            'residual_likelihood_score' => 'nullable|integer|min:1|max:5',
            'is_alarp' => 'nullable|boolean',
            'alarp_justification' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'assessment_date' => 'nullable|date',
            'review_frequency' => 'nullable|in:monthly,quarterly,semi_annually,annually,biannually,on_change,on_incident,custom',
            'status' => 'required|in:draft,under_review,approved,implementation,monitoring,closed,archived',
        ]);
        
        // Recalculate risk scores
        $riskScore = $validated['severity_score'] * $validated['likelihood_score'];
        $riskLevel = RiskAssessment::calculateRiskLevel($riskScore);
        
        $validated['risk_score'] = $riskScore;
        $validated['risk_level'] = $riskLevel;
        
        // Calculate residual risk if provided
        if (isset($validated['residual_severity_score']) && isset($validated['residual_likelihood_score'])) {
            $residualRiskScore = $validated['residual_severity_score'] * $validated['residual_likelihood_score'];
            $validated['residual_risk_score'] = $residualRiskScore;
            $validated['residual_risk_level'] = RiskAssessment::calculateRiskLevel($residualRiskScore);
        }
        
        // Update next review date if frequency changed
        if ($validated['review_frequency'] && $validated['review_frequency'] !== $riskAssessment->review_frequency) {
            $validated['next_review_date'] = $this->calculateNextReviewDate($validated['review_frequency'], $validated['assessment_date'] ?? $riskAssessment->assessment_date);
        }
        
        $riskAssessment->update($validated);
        
        return redirect()
            ->route('risk-assessment.risk-assessments.show', $riskAssessment)
            ->with('success', 'Risk assessment updated successfully!');
    }

    public function destroy(RiskAssessment $riskAssessment)
    {
        if ($riskAssessment->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        // Check if has controls or reviews
        if ($riskAssessment->controlMeasures()->count() > 0 || $riskAssessment->reviews()->count() > 0) {
            return back()->with('error', 'Cannot delete risk assessment that has associated control measures or reviews.');
        }
        
        $riskAssessment->delete();
        
        return redirect()
            ->route('risk-assessment.risk-assessments.index')
            ->with('success', 'Risk assessment deleted successfully!');
    }

    /**
     * Calculate next review date based on frequency
     */
    private function calculateNextReviewDate(string $frequency, $baseDate = null): Carbon
    {
        $base = $baseDate ? Carbon::parse($baseDate) : now();
        
        return match($frequency) {
            'monthly' => $base->addMonth(),
            'quarterly' => $base->addMonths(3),
            'semi_annually' => $base->addMonths(6),
            'annually' => $base->addYear(),
            'biannually' => $base->addYears(2),
            default => $base->addYear(),
        };
    }
}
