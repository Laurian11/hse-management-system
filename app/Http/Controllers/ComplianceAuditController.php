<?php

namespace App\Http\Controllers;

use App\Models\ComplianceAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplianceAuditController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = ComplianceAudit::forCompany($companyId)
            ->with(['auditor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('audit_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('audit_type')) {
            $query->where('audit_type', $request->audit_type);
        }

        if ($request->filled('audit_status')) {
            $query->where('audit_status', $request->audit_status);
        }

        if ($request->filled('standard')) {
            $query->where('standard', $request->standard);
        }

        $audits = $query->latest('audit_date')->paginate(15);
        return view('compliance.audits.index', compact('audits'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('compliance.audits.create', compact('users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'audit_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audit_type' => 'required|in:internal,external,iso_14001,iso_45001,iso_9001,regulatory,other',
            'standard' => 'nullable|string|max:255',
            'audit_date' => 'required|date',
            'audit_end_date' => 'nullable|date|after_or_equal:audit_date',
            'auditor_id' => 'nullable|exists:users,id',
            'external_auditor_name' => 'nullable|string|max:255',
            'auditor_organization' => 'nullable|string|max:255',
            'audit_scope' => 'nullable|array',
            'audit_status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'total_findings' => 'nullable|integer|min:0',
            'major_non_conformances' => 'nullable|integer|min:0',
            'minor_non_conformances' => 'nullable|integer|min:0',
            'observations' => 'nullable|integer|min:0',
            'positive_findings' => 'nullable|string',
            'overall_result' => 'nullable|in:passed,failed,conditional,not_applicable',
            'summary' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'corrective_action_due_date' => 'nullable|date',
            'follow_up_audit_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('compliance-audits', 'public');
            $validated['audit_report_path'] = $path;
        }

        ComplianceAudit::create($validated);

        return redirect()->route('compliance.audits.index')
            ->with('success', 'Compliance audit created successfully.');
    }

    public function show(ComplianceAudit $audit)
    {
        $audit->load(['auditor']);
        return view('compliance.audits.show', compact('audit'));
    }

    public function edit(ComplianceAudit $audit)
    {
        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('compliance.audits.edit', compact('audit', 'users'));
    }

    public function update(Request $request, ComplianceAudit $audit)
    {
        $validated = $request->validate([
            'audit_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'audit_type' => 'required|in:internal,external,iso_14001,iso_45001,iso_9001,regulatory,other',
            'standard' => 'nullable|string|max:255',
            'audit_date' => 'required|date',
            'audit_end_date' => 'nullable|date|after_or_equal:audit_date',
            'auditor_id' => 'nullable|exists:users,id',
            'external_auditor_name' => 'nullable|string|max:255',
            'auditor_organization' => 'nullable|string|max:255',
            'audit_scope' => 'nullable|array',
            'audit_status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'total_findings' => 'nullable|integer|min:0',
            'major_non_conformances' => 'nullable|integer|min:0',
            'minor_non_conformances' => 'nullable|integer|min:0',
            'observations' => 'nullable|integer|min:0',
            'positive_findings' => 'nullable|string',
            'overall_result' => 'nullable|in:passed,failed,conditional,not_applicable',
            'summary' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'corrective_action_due_date' => 'nullable|date',
            'follow_up_audit_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($audit->audit_report_path && Storage::disk('public')->exists($audit->audit_report_path)) {
                Storage::disk('public')->delete($audit->audit_report_path);
            }

            $file = $request->file('file');
            $path = $file->store('compliance-audits', 'public');
            $validated['audit_report_path'] = $path;
        }

        $audit->update($validated);

        return redirect()->route('compliance.audits.show', $audit)
            ->with('success', 'Compliance audit updated successfully.');
    }

    public function destroy(ComplianceAudit $audit)
    {
        // Delete file if exists
        if ($audit->audit_report_path && Storage::disk('public')->exists($audit->audit_report_path)) {
            Storage::disk('public')->delete($audit->audit_report_path);
        }

        $audit->delete();

        return redirect()->route('compliance.audits.index')
            ->with('success', 'Compliance audit deleted successfully.');
    }
}
