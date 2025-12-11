<?php

namespace App\Http\Controllers;

use App\Models\RiskAssessment;
use App\Models\Hazard;
use App\Models\Department;
use App\Models\User;
use App\Models\Incident;
use App\Notifications\RiskAssessmentApprovalRequiredNotification;
use App\Notifications\RiskAssessmentStatusChangedNotification;
use App\Notifications\RiskAssessmentReviewDueNotification;
use App\Traits\UsesCompanyGroup;
use App\Traits\ChecksPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RiskAssessmentController extends Controller
{
    use UsesCompanyGroup, ChecksPermissions;

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $query = RiskAssessment::whereIn('company_id', $companyGroupIds)
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
        
        // Date range filters
        if ($request->filled('date_from')) {
            $query->where('assessment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('assessment_date', '<=', $request->date_to);
        }
        
        // Sorting
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort column
        $allowedSortColumns = ['reference_number', 'title', 'risk_level', 'risk_score', 'status', 'assessment_date', 'next_review_date', 'created_at'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'created_at';
        }
        
        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $riskAssessments = $query->orderBy($sortColumn, $sortDirection)->paginate(15);
        
        // Append query parameters to pagination links
        $riskAssessments->appends($request->query());
        
        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        
        // Statistics
        $stats = [
            'total' => RiskAssessment::whereIn('company_id', $companyGroupIds)->count(),
            'high_risk' => RiskAssessment::whereIn('company_id', $companyGroupIds)->highRisk()->count(),
            'due_for_review' => RiskAssessment::whereIn('company_id', $companyGroupIds)->dueForReview()->count(),
            'approved' => RiskAssessment::whereIn('company_id', $companyGroupIds)->where('status', 'approved')->count(),
        ];
        
        return view('risk-assessment.risk-assessments.index', compact('riskAssessments', 'departments', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        $hazards = Hazard::whereIn('company_id', $companyGroupIds)->active()->get();
        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        $users = User::whereIn('company_id', $companyGroupIds)->where('is_active', true)->get();
        
        // Pre-select hazard if provided
        $selectedHazard = null;
        if ($request->has('hazard_id')) {
            $selectedHazard = Hazard::whereIn('company_id', $companyGroupIds)->findOrFail($request->hazard_id);
        }
        
        // Pre-select incident if provided (for closed-loop)
        $selectedIncident = null;
        if ($request->has('incident_id')) {
            $selectedIncident = Incident::whereIn('company_id', $companyGroupIds)->findOrFail($request->incident_id);
        }
        
        // Copy from existing assessment
        $copyFrom = null;
        if ($request->has('copy_from')) {
            $copyFrom = RiskAssessment::whereIn('company_id', $companyGroupIds)
                ->findOrFail($request->get('copy_from'));
        }
        
        return view('risk-assessment.risk-assessments.create', compact('hazards', 'departments', 'users', 'selectedHazard', 'selectedIncident', 'copyFrom'));
    }

    public function store(Request $request)
    {
        $this->authorize('risk_assessments.create');
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
        $this->authorizeCompanyResource($riskAssessment->company_id);
        
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
        $this->authorize('risk_assessments.edit');
        $this->authorizeCompanyResource($riskAssessment->company_id);
        
        $companyGroupIds = $this->getCompanyGroupIds();
        $hazards = Hazard::whereIn('company_id', $companyGroupIds)->active()->get();
        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        $users = User::whereIn('company_id', $companyGroupIds)->where('is_active', true)->get();
        
        return view('risk-assessment.risk-assessments.edit', compact('riskAssessment', 'hazards', 'departments', 'users'));
    }

    public function update(Request $request, RiskAssessment $riskAssessment)
    {
        $this->authorize('risk_assessments.edit');
        $this->authorizeCompanyResource($riskAssessment->company_id);
        
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
        
        // Track status change for notifications
        $oldStatus = $riskAssessment->status;
        $statusChanged = isset($validated['status']) && $validated['status'] !== $oldStatus;
        
        $riskAssessment->update($validated);
        
        // Send notification if status changed
        if ($statusChanged) {
            $this->notifyStatusChange($riskAssessment, $oldStatus, $validated['status']);
        }
        
        return redirect()
            ->route('risk-assessment.risk-assessments.show', $riskAssessment)
            ->with('success', 'Risk assessment updated successfully!');
    }

    public function destroy(RiskAssessment $riskAssessment)
    {
        $this->authorize('risk_assessments.delete');
        $this->authorizeCompanyResource($riskAssessment->company_id);
        
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
     * Bulk delete risk assessments
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:risk_assessments,id'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        
        $deleted = RiskAssessment::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->delete();
        
        return redirect()->route('risk-assessment.risk-assessments.index')
            ->with('success', "Successfully deleted {$deleted} risk assessment(s).");
    }
    
    /**
     * Bulk update risk assessments status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:risk_assessments,id',
            'status' => 'required|in:draft,under_review,approved,implementation,monitoring,closed,archived'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        $status = $request->input('status');
        
        $updated = RiskAssessment::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->update(['status' => $status]);
        
        return redirect()->route('risk-assessment.risk-assessments.index')
            ->with('success', "Successfully updated {$updated} risk assessment(s) status to {$status}.");
    }
    
    /**
     * Export selected risk assessments
     */
    public function bulkExport(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:risk_assessments,id'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        
        $assessments = RiskAssessment::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->with(['hazard', 'creator', 'assignedTo', 'department'])
            ->get();
        
        $filename = 'risk_assessments_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($assessments) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Reference', 'Title', 'Risk Level', 'Risk Score', 'Status', 
                'Assessment Type', 'Department', 'Assigned To', 'Assessment Date', 
                'Next Review Date', 'Created By', 'Created At'
            ]);
            
            foreach ($assessments as $assessment) {
                fputcsv($file, [
                    $assessment->reference_number,
                    $assessment->title,
                    strtoupper($assessment->risk_level),
                    $assessment->risk_score,
                    ucfirst($assessment->status),
                    ucfirst(str_replace('_', ' ', $assessment->assessment_type)),
                    $assessment->department->name ?? 'N/A',
                    $assessment->assignedTo->name ?? 'N/A',
                    $assessment->assessment_date ? $assessment->assessment_date->format('Y-m-d') : 'N/A',
                    $assessment->next_review_date ? $assessment->next_review_date->format('Y-m-d') : 'N/A',
                    $assessment->creator->name ?? 'N/A',
                    $assessment->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export all risk assessments (filtered by current filters)
     */
    public function exportAll(Request $request)
    {
        $this->authorize('risk_assessments.export');
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        // Build query same as index method
        $query = RiskAssessment::whereIn('company_id', $companyGroupIds)
            ->with(['hazard', 'creator', 'assignedTo', 'department']);
        
        // Apply same filters as index
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
        
        if ($request->filled('date_from')) {
            $query->where('assessment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('assessment_date', '<=', $request->date_to);
        }
        
        $assessments = $query->orderBy('created_at', 'desc')->get();
        
        $format = $request->get('format', 'excel');
        
        if ($format === 'pdf') {
            return $this->exportToPDF($assessments, $request);
        }
        
        return $this->exportToExcel($assessments);
    }
    
    /**
     * Export risk assessments to Excel (CSV)
     */
    private function exportToExcel($assessments)
    {
        $filename = 'risk_assessments_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($assessments) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Reference Number',
                'Title',
                'Assessment Type',
                'Risk Level',
                'Risk Score',
                'Severity',
                'Likelihood',
                'Status',
                'Department',
                'Created By',
                'Assigned To',
                'Assessment Date',
                'Next Review Date',
                'Hazard',
                'Description'
            ]);
            
            // Data
            foreach ($assessments as $assessment) {
                fputcsv($file, [
                    $assessment->reference_number,
                    $assessment->title,
                    ucfirst($assessment->assessment_type ?? 'N/A'),
                    ucfirst($assessment->risk_level ?? 'N/A'),
                    $assessment->risk_score ?? 'N/A',
                    ucfirst($assessment->severity ?? 'N/A'),
                    ucfirst($assessment->likelihood ?? 'N/A'),
                    ucfirst($assessment->status ?? 'N/A'),
                    $assessment->department?->name ?? 'N/A',
                    $assessment->creator?->name ?? 'N/A',
                    $assessment->assignedTo?->name ?? 'N/A',
                    $assessment->assessment_date ? $assessment->assessment_date->format('Y-m-d') : 'N/A',
                    $assessment->next_review_date ? $assessment->next_review_date->format('Y-m-d') : 'N/A',
                    $assessment->hazard?->title ?? 'N/A',
                    $assessment->description ?? 'N/A',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export risk assessments to PDF
     */
    private function exportToPDF($assessments, $request)
    {
        $pdf = Pdf::loadView('risk-assessment.risk-assessments.exports.pdf', [
            'assessments' => $assessments,
            'filters' => $request->all(),
        ]);
        
        return $pdf->download('risk_assessments_export_' . date('Y-m-d_His') . '.pdf');
    }
    
    /**
     * Export single risk assessment to PDF
     */
    public function exportPDF(RiskAssessment $riskAssessment)
    {
        $this->authorize('risk_assessments.print');
        $this->authorizeCompanyResource($riskAssessment->company_id);
        
        $riskAssessment->load(['hazard', 'creator', 'assignedTo', 'department', 'company', 'controlMeasures', 'reviews', 'relatedIncident', 'relatedJSA']);
        
        $pdf = Pdf::loadView('risk-assessment.risk-assessments.exports.single-pdf', [
            'assessment' => $riskAssessment,
        ]);
        
        return $pdf->download("risk-assessment-{$riskAssessment->reference_number}.pdf");
    }
    
    /**
     * Copy risk assessment
     */
    public function copy(RiskAssessment $riskAssessment)
    {
        $this->authorizeCompanyResource($riskAssessment->company_id);
        
        return redirect()->route('risk-assessment.risk-assessments.create', ['copy_from' => $riskAssessment->id]);
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
    
    /**
     * Notify relevant users about status change
     */
    private function notifyStatusChange(RiskAssessment $riskAssessment, string $oldStatus, string $newStatus)
    {
        $user = Auth::user();
        $notifyUsers = collect();
        
        // Notify creator
        if ($riskAssessment->creator) {
            $notifyUsers->push($riskAssessment->creator);
        }
        
        // Notify assigned user
        if ($riskAssessment->assignedTo) {
            $notifyUsers->push($riskAssessment->assignedTo);
        }
        
        // Notify HSE managers
        $hseManagers = User::where('company_id', $riskAssessment->company_id)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['hse_manager', 'hse_officer', 'admin', 'super_admin']);
            })
            ->get();
        
        $notifyUsers = $notifyUsers->merge($hseManagers)->unique('id');
        
        // Don't notify the user who made the change
        $notifyUsers = $notifyUsers->reject(function($notifyUser) use ($user) {
            return $notifyUser->id === $user->id;
        });
        
        foreach ($notifyUsers as $notifyUser) {
            $notifyUser->notify(new RiskAssessmentStatusChangedNotification(
                $riskAssessment,
                $oldStatus,
                $newStatus,
                $user->name
            ));
        }
    }
}
