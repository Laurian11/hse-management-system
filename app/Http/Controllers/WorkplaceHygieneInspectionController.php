<?php

namespace App\Http\Controllers;

use App\Models\WorkplaceHygieneInspection;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkplaceHygieneInspectionController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = WorkplaceHygieneInspection::forCompany($companyId)
            ->with(['department', 'inspectedBy', 'correctiveActionAssignedTo', 'verifiedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('area_inspected', 'like', "%{$search}%");
            });
        }

        if ($request->filled('overall_status')) {
            $query->where('overall_status', $request->overall_status);
        }

        $inspections = $query->latest('inspection_date')->paginate(15);
        return view('health.hygiene.index', compact('inspections'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('health.hygiene.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'area_inspected' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'inspection_items' => 'nullable|array',
            'findings' => 'nullable|array',
            'non_compliance_details' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
            'corrective_action_due_date' => 'nullable|date',
            'corrective_action_assigned_to' => 'nullable|exists:users,id',
            'overall_status' => 'required|in:satisfactory,needs_improvement,unsatisfactory',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['inspected_by'] = Auth::id();

        WorkplaceHygieneInspection::create($validated);

        return redirect()->route('health.hygiene.index')
            ->with('success', 'Workplace hygiene inspection created successfully.');
    }

    public function show(WorkplaceHygieneInspection $workplaceHygieneInspection)
    {
        $workplaceHygieneInspection->load(['department', 'inspectedBy', 'correctiveActionAssignedTo', 'verifiedBy']);
        return view('health.hygiene.show', compact('workplaceHygieneInspection'));
    }

    public function edit(WorkplaceHygieneInspection $workplaceHygieneInspection)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('health.hygiene.edit', compact('workplaceHygieneInspection', 'departments', 'users'));
    }

    public function update(Request $request, WorkplaceHygieneInspection $workplaceHygieneInspection)
    {
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'area_inspected' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'inspection_items' => 'nullable|array',
            'findings' => 'nullable|array',
            'non_compliance_details' => 'nullable|string',
            'corrective_actions' => 'nullable|string',
            'corrective_action_due_date' => 'nullable|date',
            'corrective_action_assigned_to' => 'nullable|exists:users,id',
            'corrective_action_completed' => 'boolean',
            'corrective_action_completed_date' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
            'verification_date' => 'nullable|date',
            'overall_status' => 'required|in:satisfactory,needs_improvement,unsatisfactory',
            'notes' => 'nullable|string',
        ]);

        $workplaceHygieneInspection->update($validated);

        return redirect()->route('health.hygiene.show', $workplaceHygieneInspection)
            ->with('success', 'Workplace hygiene inspection updated successfully.');
    }

    public function destroy(WorkplaceHygieneInspection $workplaceHygieneInspection)
    {
        $workplaceHygieneInspection->delete();
        return redirect()->route('health.hygiene.index')
            ->with('success', 'Workplace hygiene inspection deleted successfully.');
    }
}
