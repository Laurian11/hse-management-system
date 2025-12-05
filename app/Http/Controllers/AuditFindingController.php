<?php

namespace App\Http\Controllers;

use App\Models\AuditFinding;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditFindingController extends Controller
{
    public function index(Request $request, Audit $audit = null)
    {
        $companyId = Auth::user()->company_id;
        
        if ($audit) {
            // Nested route: findings for a specific audit
            $query = $audit->findings()->with(['audit', 'correctiveActionAssignedTo', 'verifiedBy']);
        } else {
            // Standalone route: all findings
            $query = AuditFinding::forCompany($companyId)->with(['audit', 'correctiveActionAssignedTo', 'verifiedBy']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $findings = $query->latest()->paginate(15);
        
        if ($audit) {
            return view('inspections.audits.findings.index', compact('audit', 'findings'));
        } else {
            return view('inspections.audit-findings.index', compact('findings'));
        }
    }

    public function create(Request $request, Audit $audit = null)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        $audits = Audit::forCompany($companyId)->latest()->get();
        $selectedAuditId = $request->get('audit_id', $audit?->id);
        
        if ($audit) {
            return view('inspections.audits.findings.create', compact('audit', 'users'));
        } else {
            return view('inspections.audit-findings.create', compact('audits', 'users', 'selectedAuditId'));
        }
    }

    public function store(Request $request, Audit $audit = null)
    {
        $validated = $request->validate([
            'audit_id' => 'required|exists:audits,id',
            'finding_type' => 'required|in:non_conformance,observation,opportunity_for_improvement,strength',
            'severity' => 'required|in:critical,major,minor,observation',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'clause_reference' => 'nullable|string|max:255',
            'evidence' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'corrective_action_required' => 'boolean',
            'corrective_action_plan' => 'nullable|string|required_if:corrective_action_required,1',
            'corrective_action_due_date' => 'nullable|date|required_if:corrective_action_required,1',
            'corrective_action_assigned_to' => 'nullable|exists:users,id|required_if:corrective_action_required,1',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $audit = $audit ?? Audit::findOrFail($validated['audit_id']);
        $validated['audit_id'] = $audit->id;
        $validated['company_id'] = Auth::user()->company_id;
        $validated['reference_number'] = AuditFinding::generateReferenceNumber($audit);
        
        $finding = AuditFinding::create($validated);

        // Update audit findings summary
        $this->updateAuditFindingsSummary($audit);

        if ($request->has('from_audit')) {
            return redirect()->route('inspections.audits.findings.index', $audit)
                ->with('success', 'Audit finding created successfully.');
        } else {
            return redirect()->route('inspections.audit-findings.index')
                ->with('success', 'Audit finding created successfully.');
        }
    }

    public function show(AuditFinding $finding, Audit $audit = null)
    {
        $finding->load(['audit', 'correctiveActionAssignedTo', 'verifiedBy']);
        $audit = $audit ?? $finding->audit;
        
        if ($audit && request()->routeIs('inspections.audits.findings.show')) {
            return view('inspections.audits.findings.show', compact('audit', 'finding'));
        } else {
            return view('inspections.audit-findings.show', compact('finding'));
        }
    }

    public function edit(AuditFinding $finding, Audit $audit = null)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        $audit = $audit ?? $finding->audit;
        
        if ($audit && request()->routeIs('inspections.audits.findings.edit')) {
            return view('inspections.audits.findings.edit', compact('audit', 'finding', 'users'));
        } else {
            return view('inspections.audit-findings.edit', compact('finding', 'users'));
        }
    }

    public function update(Request $request, AuditFinding $finding, Audit $audit = null)
    {
        $validated = $request->validate([
            'finding_type' => 'required|in:non_conformance,observation,opportunity_for_improvement',
            'severity' => 'required|in:critical,major,minor,observation',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'clause_reference' => 'nullable|string|max:255',
            'evidence' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'corrective_action_required' => 'boolean',
            'corrective_action_plan' => 'nullable|string|required_if:corrective_action_required,1',
            'corrective_action_due_date' => 'nullable|date|required_if:corrective_action_required,1',
            'corrective_action_assigned_to' => 'nullable|exists:users,id|required_if:corrective_action_required,1',
            'corrective_action_completed' => 'boolean',
            'corrective_action_completed_date' => 'nullable|date|required_if:corrective_action_completed,1',
            'verified_by' => 'nullable|exists:users,id',
            'verified_at' => 'nullable|date',
            'verification_notes' => 'nullable|string',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $finding->update($validated);

        // Update audit findings summary
        $audit = $audit ?? $finding->audit;
        $this->updateAuditFindingsSummary($audit);

        if ($audit && request()->routeIs('inspections.audits.findings.update')) {
            return redirect()->route('inspections.audits.findings.show', [$audit, $finding])
                ->with('success', 'Audit finding updated successfully.');
        } else {
            return redirect()->route('inspections.audit-findings.show', $finding)
                ->with('success', 'Audit finding updated successfully.');
        }
    }

    public function destroy(AuditFinding $finding, Audit $audit = null)
    {
        $audit = $audit ?? $finding->audit;
        $finding->delete();

        // Update audit findings summary
        $this->updateAuditFindingsSummary($audit);

        if ($audit && request()->routeIs('inspections.audits.findings.destroy')) {
            return redirect()->route('inspections.audits.findings.index', $audit)
                ->with('success', 'Audit finding deleted successfully.');
        } else {
            return redirect()->route('inspections.audit-findings.index')
                ->with('success', 'Audit finding deleted successfully.');
        }
    }

    private function updateAuditFindingsSummary(Audit $audit)
    {
        $findings = $audit->findings;
        $audit->update([
            'total_findings' => $findings->count(),
            'critical_findings' => $findings->where('severity', 'critical')->count(),
            'major_findings' => $findings->where('severity', 'major')->count(),
            'minor_findings' => $findings->where('severity', 'minor')->count(),
            'observations' => $findings->where('severity', 'observation')->count(),
        ]);
    }
}
