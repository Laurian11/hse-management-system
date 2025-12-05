<?php

namespace App\Http\Controllers;

use App\Models\NonConformanceReport;
use App\Models\Inspection;
use App\Models\Department;
use App\Models\User;
use App\Models\CAPA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NonConformanceReportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = NonConformanceReport::forCompany($companyId)->with(['identifiedBy', 'department', 'inspection']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $ncrs = $query->latest('identified_date')->paginate(15);
        return view('inspections.ncrs.index', compact('ncrs'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $inspections = Inspection::forCompany($companyId)->latest()->get();
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        $selectedInspectionId = $request->get('inspection_id');
        return view('inspections.ncrs.create', compact('inspections', 'departments', 'users', 'selectedInspectionId'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'inspection_id' => 'nullable|exists:inspections,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:critical,major,minor,observation',
            'identified_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string|max:255',
            'root_cause' => 'nullable|string',
            'immediate_action' => 'nullable|string',
            'corrective_action_plan' => 'nullable|string',
            'corrective_action_due_date' => 'nullable|date',
            'corrective_action_assigned_to' => 'nullable|exists:users,id',
            'verification_required' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;
        $validated['identified_by'] = Auth::id();
        $validated['status'] = 'open';

        NonConformanceReport::create($validated);

        return redirect()->route('inspections.ncrs.index')
            ->with('success', 'Non-Conformance Report created successfully.');
    }

    public function show(NonConformanceReport $ncr)
    {
        $ncr->load(['identifiedBy', 'department', 'inspection', 'correctiveAction', 'correctiveActionAssignedTo', 'verifiedBy', 'closedBy']);
        return view('inspections.ncrs.show', compact('ncr'));
    }

    public function edit(NonConformanceReport $ncr)
    {
        $companyId = Auth::user()->company_id;
        $inspections = Inspection::forCompany($companyId)->latest()->get();
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('inspections.ncrs.edit', compact('ncr', 'inspections', 'departments', 'users'));
    }

    public function update(Request $request, NonConformanceReport $ncr)
    {
        $validated = $request->validate([
            'inspection_id' => 'nullable|exists:inspections,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:critical,major,minor,observation',
            'status' => 'required|in:open,investigating,corrective_action,closed,cancelled',
            'identified_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string|max:255',
            'root_cause' => 'nullable|string',
            'immediate_action' => 'nullable|string',
            'corrective_action_plan' => 'nullable|string',
            'corrective_action_due_date' => 'nullable|date',
            'corrective_action_assigned_to' => 'nullable|exists:users,id',
            'verification_required' => 'boolean',
        ]);

        $ncr->update($validated);

        return redirect()->route('inspections.ncrs.show', $ncr)
            ->with('success', 'Non-Conformance Report updated successfully.');
    }

    public function destroy(NonConformanceReport $ncr)
    {
        $ncr->delete();
        return redirect()->route('inspections.ncrs.index')
            ->with('success', 'Non-Conformance Report deleted successfully.');
    }
}
