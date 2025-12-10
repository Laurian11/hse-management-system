<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\InspectionChecklist;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = Inspection::forCompany($companyId)->with(['inspectedBy', 'department', 'checklist', 'schedule']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('overall_status')) {
            $query->where('overall_status', $request->overall_status);
        }

        if ($request->filled('inspection_date_from')) {
            $query->whereDate('inspection_date', '>=', $request->inspection_date_from);
        }

        if ($request->filled('inspection_date_to')) {
            $query->whereDate('inspection_date', '<=', $request->inspection_date_to);
        }

        $inspections = $query->latest('inspection_date')->paginate(15);
        return view('inspections.index', compact('inspections'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $schedules = InspectionSchedule::forCompany($companyId)->active()->get();
        $checklists = InspectionChecklist::forCompany($companyId)->active()->get();
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        
        $selectedScheduleId = $request->get('schedule_id');
        $selectedChecklistId = $request->get('checklist_id');
        $prefilledLocation = $request->get('location');
        
        // Auto-load schedule data if provided
        $selectedSchedule = null;
        if ($selectedScheduleId) {
            $selectedSchedule = InspectionSchedule::forCompany($companyId)->with(['checklist', 'department'])->find($selectedScheduleId);
            if ($selectedSchedule && !$selectedChecklistId && $selectedSchedule->checklist) {
                $selectedChecklistId = $selectedSchedule->checklist->id;
            }
        }
        
        // Auto-load checklist data if provided
        $selectedChecklist = null;
        if ($selectedChecklistId) {
            $selectedChecklist = InspectionChecklist::forCompany($companyId)->find($selectedChecklistId);
        }
        
        return view('inspections.create', compact(
            'schedules', 
            'checklists', 
            'departments', 
            'users', 
            'selectedScheduleId',
            'selectedChecklistId',
            'selectedSchedule',
            'selectedChecklist',
            'prefilledLocation'
        ));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'inspection_schedule_id' => 'nullable|exists:inspection_schedules,id',
            'inspection_checklist_id' => 'nullable|exists:inspection_checklists,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'inspection_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string|max:255',
            'checklist_responses' => 'required|array',
            'overall_status' => 'required|in:compliant,non_compliant,partial,pending',
            'findings' => 'nullable|array',
            'observations' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'requires_follow_up' => 'boolean',
            'follow_up_date' => 'nullable|date|required_if:requires_follow_up,1',
            'follow_up_assigned_to' => 'nullable|exists:users,id|required_if:requires_follow_up,1',
        ]);

        // Calculate compliance metrics
        $responses = $validated['checklist_responses'];
        $validated['total_items'] = count($responses);
        $validated['compliant_items'] = count(array_filter($responses, fn($r) => ($r['status'] ?? '') === 'compliant'));
        $validated['non_compliant_items'] = count(array_filter($responses, fn($r) => ($r['status'] ?? '') === 'non_compliant'));
        $validated['na_items'] = count(array_filter($responses, fn($r) => ($r['status'] ?? '') === 'na'));

        $validated['company_id'] = $companyId;
        $validated['inspected_by'] = Auth::id();

        Inspection::create($validated);

        return redirect()->route('inspections.index')
            ->with('success', 'Inspection created successfully.');
    }

    public function show(Inspection $inspection)
    {
        $inspection->load(['inspectedBy', 'department', 'checklist', 'schedule', 'followUpAssignedTo', 'nonConformanceReports']);
        return view('inspections.show', compact('inspection'));
    }

    public function edit(Inspection $inspection)
    {
        $companyId = Auth::user()->company_id;
        $schedules = InspectionSchedule::forCompany($companyId)->active()->get();
        $checklists = InspectionChecklist::forCompany($companyId)->active()->get();
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('inspections.edit', compact('inspection', 'schedules', 'checklists', 'departments', 'users'));
    }

    public function update(Request $request, Inspection $inspection)
    {
        $validated = $request->validate([
            'inspection_schedule_id' => 'nullable|exists:inspection_schedules,id',
            'inspection_checklist_id' => 'nullable|exists:inspection_checklists,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'inspection_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string|max:255',
            'checklist_responses' => 'required|array',
            'overall_status' => 'required|in:compliant,non_compliant,partial,pending',
            'findings' => 'nullable|array',
            'observations' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'requires_follow_up' => 'boolean',
            'follow_up_date' => 'nullable|date|required_if:requires_follow_up,1',
            'follow_up_assigned_to' => 'nullable|exists:users,id|required_if:requires_follow_up,1',
        ]);

        // Recalculate compliance metrics
        $responses = $validated['checklist_responses'];
        $validated['total_items'] = count($responses);
        $validated['compliant_items'] = count(array_filter($responses, fn($r) => ($r['status'] ?? '') === 'compliant'));
        $validated['non_compliant_items'] = count(array_filter($responses, fn($r) => ($r['status'] ?? '') === 'non_compliant'));
        $validated['na_items'] = count(array_filter($responses, fn($r) => ($r['status'] ?? '') === 'na'));

        $inspection->update($validated);

        return redirect()->route('inspections.show', $inspection)
            ->with('success', 'Inspection updated successfully.');
    }

    public function destroy(Inspection $inspection)
    {
        $inspection->delete();
        return redirect()->route('inspections.index')
            ->with('success', 'Inspection deleted successfully.');
    }
}
