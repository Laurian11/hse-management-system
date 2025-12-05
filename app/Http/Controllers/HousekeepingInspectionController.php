<?php

namespace App\Http\Controllers;

use App\Models\HousekeepingInspection;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HousekeepingInspectionController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = HousekeepingInspection::forCompany($companyId)
            ->with(['inspectedBy', 'department', 'followUpAssignee']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $inspections = $query->latest('inspection_date')->paginate(15);
        $departments = Department::forCompany($companyId)->active()->get();
        
        return view('housekeeping.inspections.index', compact('inspections', 'departments'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('housekeeping.inspections.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string|max:255',
            'inspected_by' => 'required|exists:users,id',
            'overall_rating' => 'required|in:excellent,good,fair,poor',
            'score' => 'nullable|numeric|min:0|max:100',
            'checklist_items' => 'nullable|array',
            'findings' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'status' => 'required|in:pending,completed,follow_up_required,overdue',
            'follow_up_date' => 'nullable|date',
            'follow_up_assigned_to' => 'nullable|exists:users,id',
            'corrective_actions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        HousekeepingInspection::create($validated);

        return redirect()->route('housekeeping.inspections.index')
            ->with('success', 'Housekeeping inspection created successfully.');
    }

    public function show(HousekeepingInspection $inspection)
    {
        $inspection->load(['inspectedBy', 'department', 'followUpAssignee']);
        return view('housekeeping.inspections.show', compact('inspection'));
    }

    public function edit(HousekeepingInspection $inspection)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('housekeeping.inspections.edit', compact('inspection', 'departments', 'users'));
    }

    public function update(Request $request, HousekeepingInspection $inspection)
    {
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string|max:255',
            'inspected_by' => 'required|exists:users,id',
            'overall_rating' => 'required|in:excellent,good,fair,poor',
            'score' => 'nullable|numeric|min:0|max:100',
            'checklist_items' => 'nullable|array',
            'findings' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'status' => 'required|in:pending,completed,follow_up_required,overdue',
            'follow_up_date' => 'nullable|date',
            'follow_up_assigned_to' => 'nullable|exists:users,id',
            'corrective_actions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $inspection->update($validated);

        return redirect()->route('housekeeping.inspections.show', $inspection)
            ->with('success', 'Housekeeping inspection updated successfully.');
    }

    public function destroy(HousekeepingInspection $inspection)
    {
        $inspection->delete();

        return redirect()->route('housekeeping.inspections.index')
            ->with('success', 'Housekeeping inspection deleted successfully.');
    }
}
