<?php

namespace App\Http\Controllers;

use App\Models\RiskReview;
use App\Models\RiskAssessment;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RiskReviewController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = RiskReview::forCompany($companyId)
            ->with(['riskAssessment', 'reviewedBy', 'assignedTo', 'triggeringIncident']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('riskAssessment', function ($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('review_type')) {
            $query->byReviewType($request->review_type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('overdue')) {
            $query->overdue();
        }
        
        if ($request->filled('risk_assessment_id')) {
            $query->where('risk_assessment_id', $request->risk_assessment_id);
        }
        
        $reviews = $query->latest()->paginate(15);
        
        // Statistics
        $stats = [
            'total' => RiskReview::forCompany($companyId)->count(),
            'scheduled' => RiskReview::forCompany($companyId)->scheduled()->count(),
            'triggered' => RiskReview::forCompany($companyId)->triggered()->count(),
            'overdue' => RiskReview::forCompany($companyId)->overdue()->count(),
            'completed' => RiskReview::forCompany($companyId)->where('status', 'completed')->count(),
        ];
        
        return view('risk-assessment.risk-reviews.index', compact('reviews', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $riskAssessments = RiskAssessment::forCompany($companyId)->active()->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        $incidents = null;
        
        // Pre-select risk assessment if provided
        $selectedRiskAssessment = null;
        if ($request->has('risk_assessment_id')) {
            $selectedRiskAssessment = RiskAssessment::forCompany($companyId)->findOrFail($request->risk_assessment_id);
        }
        
        // Pre-select incident if provided (for triggered reviews)
        if ($request->has('incident_id')) {
            $incident = Incident::where('company_id', $companyId)->findOrFail($request->incident_id);
            $incidents = collect([$incident]);
        }
        
        return view('risk-assessment.risk-reviews.create', compact('riskAssessments', 'users', 'selectedRiskAssessment', 'incidents'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'risk_assessment_id' => 'required|exists:risk_assessments,id',
            'review_type' => 'required|in:scheduled,triggered_by_incident,triggered_by_change,triggered_by_audit,triggered_by_regulation,triggered_by_control_failure,manual,other',
            'trigger_description' => 'nullable|string',
            'triggering_incident_id' => 'nullable|exists:incidents,id',
            'scheduled_date' => 'nullable|date',
            'due_date' => 'required|date|after_or_equal:today',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'nullable|in:scheduled,in_progress,completed,overdue,cancelled',
        ]);
        
        // Verify risk assessment belongs to company
        $riskAssessment = RiskAssessment::forCompany($companyId)->findOrFail($validated['risk_assessment_id']);
        
        $validated['company_id'] = $companyId;
        $validated['status'] = $validated['status'] ?? 'scheduled';
        
        // If triggered by incident, link it
        if ($validated['review_type'] === 'triggered_by_incident' && $validated['triggering_incident_id']) {
            $incident = Incident::where('company_id', $companyId)->findOrFail($validated['triggering_incident_id']);
            // Update incident to link to risk assessment
            $incident->update([
                'related_risk_assessment_id' => $riskAssessment->id,
            ]);
        }
        
        $review = RiskReview::create($validated);
        
        return redirect()
            ->route('risk-assessment.risk-reviews.show', $review)
            ->with('success', 'Risk review created successfully!');
    }

    public function show(RiskReview $riskReview)
    {
        if ($riskReview->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $riskReview->load([
            'riskAssessment',
            'reviewedBy',
            'assignedTo',
            'triggeringIncident',
            'approvedBy',
        ]);
        
        return view('risk-assessment.risk-reviews.show', compact('riskReview'));
    }

    public function edit(RiskReview $riskReview)
    {
        if ($riskReview->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        if ($riskReview->status === 'completed') {
            return back()->with('error', 'Cannot edit completed reviews.');
        }
        
        $companyId = Auth::user()->company_id;
        $riskAssessments = RiskAssessment::forCompany($companyId)->active()->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        
        return view('risk-assessment.risk-reviews.edit', compact('riskReview', 'riskAssessments', 'users'));
    }

    public function update(Request $request, RiskReview $riskReview)
    {
        if ($riskReview->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        if ($riskReview->status === 'completed') {
            return back()->with('error', 'Cannot edit completed reviews.');
        }
        
        $validated = $request->validate([
            'due_date' => 'required|date',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:scheduled,in_progress,completed,overdue,cancelled',
        ]);
        
        $riskReview->update($validated);
        
        return redirect()
            ->route('risk-assessment.risk-reviews.show', $riskReview)
            ->with('success', 'Risk review updated successfully!');
    }

    public function destroy(RiskReview $riskReview)
    {
        if ($riskReview->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        if ($riskReview->status === 'completed') {
            return back()->with('error', 'Cannot delete completed reviews.');
        }
        
        $riskReview->delete();
        
        return redirect()
            ->route('risk-assessment.risk-reviews.index')
            ->with('success', 'Risk review deleted successfully!');
    }

    public function complete(Request $request, RiskReview $riskReview)
    {
        if ($riskReview->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $validated = $request->validate([
            'review_findings' => 'required|string',
            'changes_identified' => 'nullable|string',
            'control_effectiveness' => 'nullable|string',
            'updated_severity' => 'nullable|in:negligible,minor,moderate,major,catastrophic',
            'updated_likelihood' => 'nullable|in:rare,unlikely,possible,likely,almost_certain',
            'updated_severity_score' => 'nullable|integer|min:1|max:5',
            'updated_likelihood_score' => 'nullable|integer|min:1|max:5',
            'risk_change' => 'nullable|in:increased,decreased,unchanged,eliminated',
            'risk_change_reason' => 'nullable|string',
            'next_review_frequency' => 'nullable|in:monthly,quarterly,semi_annually,annually,biannually,on_change,on_incident,custom',
            'requires_new_controls' => 'nullable|boolean',
            'requires_control_modification' => 'nullable|boolean',
            'recommended_actions' => 'nullable|string',
        ]);
        
        // Calculate updated risk score if provided
        if (isset($validated['updated_severity_score']) && isset($validated['updated_likelihood_score'])) {
            $validated['updated_risk_score'] = $validated['updated_severity_score'] * $validated['updated_likelihood_score'];
            $validated['updated_risk_level'] = RiskAssessment::calculateRiskLevel($validated['updated_risk_score']);
        }
        
        // Calculate next review date
        if ($validated['next_review_frequency']) {
            $validated['next_review_date'] = $this->calculateNextReviewDate($validated['next_review_frequency']);
        }
        
        $validated['review_date'] = now();
        $validated['reviewed_by'] = Auth::id();
        $validated['status'] = 'completed';
        
        $riskReview->update($validated);
        
        // Update the risk assessment with new scores if provided
        if ($riskReview->updated_risk_score) {
            $riskAssessment = $riskReview->riskAssessment;
            $riskAssessment->update([
                'severity' => $riskReview->updated_severity,
                'likelihood' => $riskReview->updated_likelihood,
                'severity_score' => $riskReview->updated_severity_score,
                'likelihood_score' => $riskReview->updated_likelihood_score,
                'risk_score' => $riskReview->updated_risk_score,
                'risk_level' => $riskReview->updated_risk_level,
                'next_review_date' => $riskReview->next_review_date,
                'review_frequency' => $riskReview->next_review_frequency,
            ]);
        }
        
        return redirect()
            ->route('risk-assessment.risk-reviews.show', $riskReview)
            ->with('success', 'Risk review completed successfully!');
    }

    private function calculateNextReviewDate(string $frequency): Carbon
    {
        return match($frequency) {
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'semi_annually' => now()->addMonths(6),
            'annually' => now()->addYear(),
            'biannually' => now()->addYears(2),
            default => now()->addYear(),
        };
    }
}
