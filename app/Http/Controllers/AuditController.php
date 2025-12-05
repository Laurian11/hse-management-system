<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = Audit::forCompany($companyId)->with(['leadAuditor', 'department']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('audit_type')) {
            $query->where('audit_type', $request->audit_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $audits = $query->latest('planned_start_date')->paginate(15);
        return view('inspections.audits.index', compact('audits'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('inspections.audits.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'audit_type' => 'required|in:internal,external,certification,regulatory,supplier',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => 'required|in:full,partial,focused',
            'scope_description' => 'nullable|string',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date|after:planned_start_date',
            'lead_auditor_id' => 'required|exists:users,id',
            'audit_team' => 'nullable|array',
            'department_id' => 'nullable|exists:departments,id',
            'applicable_standards' => 'nullable|array',
            'audit_criteria' => 'nullable|array',
        ]);

        $validated['company_id'] = $companyId;
        $validated['status'] = 'planned';

        Audit::create($validated);

        return redirect()->route('inspections.audits.index')
            ->with('success', 'Audit created successfully.');
    }

    public function show(Audit $audit)
    {
        $audit->load(['leadAuditor', 'department', 'findings.correctiveActionAssignedTo', 'findings.verifiedBy']);
        return view('inspections.audits.show', compact('audit'));
    }

    public function edit(Audit $audit)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('inspections.audits.edit', compact('audit', 'departments', 'users'));
    }

    public function update(Request $request, Audit $audit)
    {
        $validated = $request->validate([
            'audit_type' => 'required|in:internal,external,certification,regulatory,supplier',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => 'required|in:full,partial,focused',
            'scope_description' => 'nullable|string',
            'planned_start_date' => 'required|date',
            'planned_end_date' => 'required|date|after:planned_start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date',
            'lead_auditor_id' => 'required|exists:users,id',
            'audit_team' => 'nullable|array',
            'department_id' => 'nullable|exists:departments,id',
            'applicable_standards' => 'nullable|array',
            'audit_criteria' => 'nullable|array',
            'status' => 'required|in:planned,in_progress,completed,cancelled,postponed',
            'result' => 'nullable|in:compliant,non_compliant,partial,pending',
            'executive_summary' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'follow_up_required' => 'boolean',
            'follow_up_date' => 'nullable|date|required_if:follow_up_required,1',
        ]);

        $audit->update($validated);

        return redirect()->route('inspections.audits.show', $audit)
            ->with('success', 'Audit updated successfully.');
    }

    public function destroy(Audit $audit)
    {
        $audit->delete();
        return redirect()->route('inspections.audits.index')
            ->with('success', 'Audit deleted successfully.');
    }
}
