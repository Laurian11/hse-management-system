<?php

namespace App\Http\Controllers;

use App\Models\SpillIncident;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpillIncidentController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = SpillIncident::forCompany($companyId)
            ->with(['reportedBy', 'investigatedBy', 'department']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('spill_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $incidents = $query->latest('incident_date')->paginate(15);
        return view('environmental.spills.index', compact('incidents'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        return view('environmental.spills.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'incident_date' => 'required|date',
            'incident_time' => 'nullable',
            'location' => 'required|string|max:255',
            'spill_type' => 'required|in:chemical,oil,fuel,hazardous_waste,water,other',
            'substance_description' => 'nullable|string',
            'estimated_volume' => 'nullable|numeric|min:0',
            'volume_unit' => 'nullable|string|max:50',
            'severity' => 'required|in:minor,moderate,major,catastrophic',
            'cause' => 'nullable|string',
            'immediate_response' => 'nullable|string',
            'containment_measures' => 'nullable|string',
            'cleanup_procedures' => 'nullable|string',
            'environmental_impact' => 'boolean',
            'environmental_impact_description' => 'nullable|string',
            'regulatory_notification_required' => 'boolean',
            'regulatory_notification_details' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'preventive_measures' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['reported_by'] = Auth::id();
        $validated['status'] = 'reported';

        SpillIncident::create($validated);

        return redirect()->route('environmental.spills.index')
            ->with('success', 'Spill incident reported successfully.');
    }

    public function show(SpillIncident $spillIncident)
    {
        $spillIncident->load(['reportedBy', 'investigatedBy', 'department']);
        return view('environmental.spills.show', compact('spillIncident'));
    }

    public function edit(SpillIncident $spillIncident)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('environmental.spills.edit', compact('spillIncident', 'departments', 'users'));
    }

    public function update(Request $request, SpillIncident $spillIncident)
    {
        $validated = $request->validate([
            'incident_date' => 'required|date',
            'incident_time' => 'nullable',
            'location' => 'required|string|max:255',
            'spill_type' => 'required|in:chemical,oil,fuel,hazardous_waste,water,other',
            'substance_description' => 'nullable|string',
            'estimated_volume' => 'nullable|numeric|min:0',
            'volume_unit' => 'nullable|string|max:50',
            'severity' => 'required|in:minor,moderate,major,catastrophic',
            'cause' => 'nullable|string',
            'immediate_response' => 'nullable|string',
            'containment_measures' => 'nullable|string',
            'cleanup_procedures' => 'nullable|string',
            'status' => 'required|in:reported,contained,cleaned_up,investigating,closed',
            'environmental_impact' => 'boolean',
            'environmental_impact_description' => 'nullable|string',
            'regulatory_notification_required' => 'boolean',
            'regulatory_notification_details' => 'nullable|string',
            'investigated_by' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'preventive_measures' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $spillIncident->update($validated);

        return redirect()->route('environmental.spills.show', $spillIncident)
            ->with('success', 'Spill incident updated successfully.');
    }

    public function destroy(SpillIncident $spillIncident)
    {
        $spillIncident->delete();
        return redirect()->route('environmental.spills.index')
            ->with('success', 'Spill incident deleted successfully.');
    }
}
