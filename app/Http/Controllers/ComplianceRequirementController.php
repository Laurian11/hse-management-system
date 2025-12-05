<?php

namespace App\Http\Controllers;

use App\Models\ComplianceRequirement;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplianceRequirementController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = ComplianceRequirement::forCompany($companyId)
            ->with(['responsiblePerson', 'department']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('requirement_title', 'like', "%{$search}%")
                  ->orWhere('regulation_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('regulatory_body')) {
            $query->where('regulatory_body', $request->regulatory_body);
        }

        if ($request->filled('compliance_status')) {
            $query->where('compliance_status', $request->compliance_status);
        }

        $requirements = $query->latest()->paginate(15);
        return view('compliance.requirements.index', compact('requirements'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('compliance.requirements.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'requirement_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regulatory_body' => 'required|string|max:255',
            'regulation_code' => 'nullable|string|max:255',
            'requirement_type' => 'required|in:regulatory,internal_policy,standard,certification,other',
            'category' => 'nullable|string|max:255',
            'effective_date' => 'nullable|date',
            'compliance_due_date' => 'nullable|date',
            'compliance_status' => 'required|in:compliant,non_compliant,partially_compliant,not_applicable,under_review',
            'responsible_person_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'compliance_evidence' => 'nullable|string',
            'non_compliance_issues' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
            'last_review_date' => 'nullable|date',
            'next_review_date' => 'nullable|date',
            'review_frequency_months' => 'nullable|integer|min:1|max:120',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        ComplianceRequirement::create($validated);

        return redirect()->route('compliance.requirements.index')
            ->with('success', 'Compliance requirement created successfully.');
    }

    public function show(ComplianceRequirement $requirement)
    {
        $requirement->load(['responsiblePerson', 'department']);
        return view('compliance.requirements.show', compact('requirement'));
    }

    public function edit(ComplianceRequirement $requirement)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('compliance.requirements.edit', compact('requirement', 'departments', 'users'));
    }

    public function update(Request $request, ComplianceRequirement $requirement)
    {
        $validated = $request->validate([
            'requirement_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'regulatory_body' => 'required|string|max:255',
            'regulation_code' => 'nullable|string|max:255',
            'requirement_type' => 'required|in:regulatory,internal_policy,standard,certification,other',
            'category' => 'nullable|string|max:255',
            'effective_date' => 'nullable|date',
            'compliance_due_date' => 'nullable|date',
            'compliance_status' => 'required|in:compliant,non_compliant,partially_compliant,not_applicable,under_review',
            'responsible_person_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'compliance_evidence' => 'nullable|string',
            'non_compliance_issues' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
            'last_review_date' => 'nullable|date',
            'next_review_date' => 'nullable|date',
            'review_frequency_months' => 'nullable|integer|min:1|max:120',
            'notes' => 'nullable|string',
        ]);

        $requirement->update($validated);

        return redirect()->route('compliance.requirements.show', $requirement)
            ->with('success', 'Compliance requirement updated successfully.');
    }

    public function destroy(ComplianceRequirement $requirement)
    {
        $requirement->delete();

        return redirect()->route('compliance.requirements.index')
            ->with('success', 'Compliance requirement deleted successfully.');
    }
}
