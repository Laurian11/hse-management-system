<?php

namespace App\Http\Controllers;

use App\Models\FiveSAudit;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FiveSAuditController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = FiveSAudit::forCompany($companyId)
            ->with(['auditedBy', 'department', 'teamLeader']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('overall_rating')) {
            $query->where('overall_rating', $request->overall_rating);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $audits = $query->latest('audit_date')->paginate(15);
        $departments = Department::forCompany($companyId)->active()->get();
        
        return view('housekeeping.5s-audits.index', compact('audits', 'departments'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('housekeeping.5s-audits.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'audit_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'area' => 'required|string|max:255',
            'audited_by' => 'required|exists:users,id',
            'team_leader_id' => 'nullable|exists:users,id',
            'sort_score' => 'nullable|integer|min:0|max:100',
            'set_score' => 'nullable|integer|min:0|max:100',
            'shine_score' => 'nullable|integer|min:0|max:100',
            'standardize_score' => 'nullable|integer|min:0|max:100',
            'sustain_score' => 'nullable|integer|min:0|max:100',
            'overall_rating' => 'required|in:excellent,good,fair,needs_improvement',
            'sort_findings' => 'nullable|string',
            'set_findings' => 'nullable|string',
            'shine_findings' => 'nullable|string',
            'standardize_findings' => 'nullable|string',
            'sustain_findings' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'improvement_actions' => 'nullable|string',
            'next_audit_date' => 'nullable|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        FiveSAudit::create($validated);

        return redirect()->route('housekeeping.5s-audits.index')
            ->with('success', '5S audit created successfully.');
    }

    public function show(FiveSAudit $audit)
    {
        $audit->load(['auditedBy', 'department', 'teamLeader']);
        return view('housekeeping.5s-audits.show', compact('audit'));
    }

    public function edit(FiveSAudit $audit)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('housekeeping.5s-audits.edit', compact('audit', 'departments', 'users'));
    }

    public function update(Request $request, FiveSAudit $audit)
    {
        $validated = $request->validate([
            'audit_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'area' => 'required|string|max:255',
            'audited_by' => 'required|exists:users,id',
            'team_leader_id' => 'nullable|exists:users,id',
            'sort_score' => 'nullable|integer|min:0|max:100',
            'set_score' => 'nullable|integer|min:0|max:100',
            'shine_score' => 'nullable|integer|min:0|max:100',
            'standardize_score' => 'nullable|integer|min:0|max:100',
            'sustain_score' => 'nullable|integer|min:0|max:100',
            'overall_rating' => 'required|in:excellent,good,fair,needs_improvement',
            'sort_findings' => 'nullable|string',
            'set_findings' => 'nullable|string',
            'shine_findings' => 'nullable|string',
            'standardize_findings' => 'nullable|string',
            'sustain_findings' => 'nullable|string',
            'strengths' => 'nullable|string',
            'weaknesses' => 'nullable|string',
            'improvement_actions' => 'nullable|string',
            'next_audit_date' => 'nullable|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $audit->update($validated);

        return redirect()->route('housekeeping.5s-audits.show', $audit)
            ->with('success', '5S audit updated successfully.');
    }

    public function destroy(FiveSAudit $audit)
    {
        $audit->delete();

        return redirect()->route('housekeeping.5s-audits.index')
            ->with('success', '5S audit deleted successfully.');
    }
}
