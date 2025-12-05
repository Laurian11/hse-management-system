<?php

namespace App\Http\Controllers;

use App\Models\InspectionSchedule;
use App\Models\InspectionChecklist;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspectionScheduleController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = InspectionSchedule::forCompany($companyId)->with(['assignedTo', 'department', 'checklist']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        $schedules = $query->latest()->paginate(15);
        return view('inspections.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $checklists = InspectionChecklist::forCompany($companyId)->active()->get();
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('inspections.schedules.create', compact('checklists', 'departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,annually,custom',
            'custom_days' => 'nullable|integer|min:1|required_if:frequency,custom',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'next_inspection_date' => 'required|date|after_or_equal:start_date',
            'assigned_to' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'inspection_checklist_id' => 'nullable|exists:inspection_checklists,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        InspectionSchedule::create($validated);

        return redirect()->route('inspections.schedules.index')
            ->with('success', 'Inspection schedule created successfully.');
    }

    public function show(InspectionSchedule $schedule)
    {
        $schedule->load(['assignedTo', 'department', 'checklist', 'inspections']);
        return view('inspections.schedules.show', compact('schedule'));
    }

    public function edit(InspectionSchedule $schedule)
    {
        $companyId = Auth::user()->company_id;
        $checklists = InspectionChecklist::forCompany($companyId)->active()->get();
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('inspections.schedules.edit', compact('schedule', 'checklists', 'departments', 'users'));
    }

    public function update(Request $request, InspectionSchedule $schedule)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,annually,custom',
            'custom_days' => 'nullable|integer|min:1|required_if:frequency,custom',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'next_inspection_date' => 'required|date|after_or_equal:start_date',
            'assigned_to' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'inspection_checklist_id' => 'nullable|exists:inspection_checklists,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $schedule->update($validated);

        return redirect()->route('inspections.schedules.show', $schedule)
            ->with('success', 'Inspection schedule updated successfully.');
    }

    public function destroy(InspectionSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('inspections.schedules.index')
            ->with('success', 'Inspection schedule deleted successfully.');
    }
}
