<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentInvestigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentInvestigationController extends Controller
{
    /**
     * Show the form for creating a new investigation
     */
    public function create(Incident $incident)
    {
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::where('company_id', $companyId)->get();

        return view('incidents.investigations.create', compact('incident', 'users'));
    }

    /**
     * Store a newly created investigation
     */
    public function store(Request $request, Incident $incident)
    {
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'investigator_id' => 'required|exists:users,id',
            'due_date' => 'required|date|after:today',
            'what_happened' => 'nullable|string',
            'when_occurred' => 'nullable|string',
            'where_occurred' => 'nullable|string',
            'who_involved' => 'nullable|string',
            'how_occurred' => 'nullable|string',
            'investigation_team' => 'nullable|array',
        ]);

        $validated['incident_id'] = $incident->id;
        $validated['company_id'] = Auth::user()->company_id;
        $validated['assigned_by'] = Auth::user()->id;
        $validated['status'] = 'pending';

        $investigation = IncidentInvestigation::create($validated);

        // Update incident status
        $incident->update([
            'status' => 'investigating',
            'investigation_id' => $investigation->id,
        ]);

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'Investigation created successfully!');
    }

    /**
     * Display the specified investigation
     */
    public function show(Incident $incident, IncidentInvestigation $investigation)
    {
        if ($investigation->company_id !== Auth::user()->company_id || 
            $investigation->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        $investigation->load(['investigator', 'assignedBy', 'incident']);

        return view('incidents.investigations.show', compact('incident', 'investigation'));
    }

    /**
     * Show the form for editing the investigation
     */
    public function edit(Incident $incident, IncidentInvestigation $investigation)
    {
        if ($investigation->company_id !== Auth::user()->company_id || 
            $investigation->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::where('company_id', $companyId)->get();

        return view('incidents.investigations.edit', compact('incident', 'investigation', 'users'));
    }

    /**
     * Update the investigation
     */
    public function update(Request $request, Incident $incident, IncidentInvestigation $investigation)
    {
        if ($investigation->company_id !== Auth::user()->company_id || 
            $investigation->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'what_happened' => 'nullable|string',
            'when_occurred' => 'nullable|string',
            'where_occurred' => 'nullable|string',
            'who_involved' => 'nullable|string',
            'how_occurred' => 'nullable|string',
            'immediate_causes' => 'nullable|string',
            'contributing_factors' => 'nullable|string',
            'witnesses' => 'nullable|array',
            'witness_statements' => 'nullable|array',
            'environmental_conditions' => 'nullable|string',
            'equipment_conditions' => 'nullable|string',
            'procedures_followed' => 'nullable|string',
            'training_received' => 'nullable|string',
            'investigation_team' => 'nullable|array',
            'key_findings' => 'nullable|string',
            'evidence_collected' => 'nullable|string',
            'interviews_conducted' => 'nullable|string',
            'investigator_notes' => 'nullable|string',
            'recommendations' => 'nullable|string',
        ]);

        $investigation->update($validated);

        return redirect()
            ->route('incidents.investigations.show', [$incident, $investigation])
            ->with('success', 'Investigation updated successfully!');
    }

    /**
     * Start the investigation
     */
    public function start(Incident $incident, IncidentInvestigation $investigation)
    {
        if ($investigation->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $investigation->start();

        return back()->with('success', 'Investigation started!');
    }

    /**
     * Complete the investigation
     */
    public function complete(Incident $incident, IncidentInvestigation $investigation)
    {
        if ($investigation->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $investigation->complete();

        return back()->with('success', 'Investigation completed!');
    }
}
