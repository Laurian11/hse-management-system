<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\RootCauseAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RootCauseAnalysisController extends Controller
{
    /**
     * Show the form for creating a new RCA
     */
    public function create(Incident $incident)
    {
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        return view('incidents.rca.create', compact('incident'));
    }

    /**
     * Store a newly created RCA
     */
    public function store(Request $request, Incident $incident)
    {
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'analysis_type' => 'required|in:5_whys,fishbone,taproot,fault_tree,custom',
            'why_1' => 'nullable|string',
            'why_2' => 'nullable|string',
            'why_3' => 'nullable|string',
            'why_4' => 'nullable|string',
            'why_5' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'human_factors' => 'nullable|string',
            'organizational_factors' => 'nullable|string',
            'technical_factors' => 'nullable|string',
            'environmental_factors' => 'nullable|string',
            'procedural_factors' => 'nullable|string',
            'equipment_factors' => 'nullable|string',
            'direct_cause' => 'nullable|string',
            'contributing_causes' => 'nullable|string',
            'root_causes' => 'nullable|string',
            'systemic_failures' => 'nullable|string',
            'causal_factors' => 'nullable|array',
            'barriers_failed' => 'nullable|array',
            'prevention_possible' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
        ]);

        $validated['incident_id'] = $incident->id;
        $validated['company_id'] = Auth::user()->company_id;
        $validated['created_by'] = Auth::user()->id;
        $validated['investigation_id'] = $incident->investigation_id;
        $validated['status'] = 'draft';

        $rca = RootCauseAnalysis::create($validated);

        // Update incident
        $incident->update([
            'root_cause_analysis_id' => $rca->id,
        ]);

        return redirect()
            ->route('incidents.rca.show', [$incident, $rca])
            ->with('success', 'Root Cause Analysis created successfully!');
    }

    /**
     * Display the specified RCA
     */
    public function show(Incident $incident, RootCauseAnalysis $rootCauseAnalysis)
    {
        if ($rootCauseAnalysis->company_id !== Auth::user()->company_id || 
            $rootCauseAnalysis->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        $rootCauseAnalysis->load(['creator', 'reviewer', 'incident', 'capas']);

        return view('incidents.rca.show', compact('incident', 'rootCauseAnalysis'));
    }

    /**
     * Show the form for editing the RCA
     */
    public function edit(Incident $incident, RootCauseAnalysis $rootCauseAnalysis)
    {
        if ($rootCauseAnalysis->company_id !== Auth::user()->company_id || 
            $rootCauseAnalysis->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        return view('incidents.rca.edit', compact('incident', 'rootCauseAnalysis'));
    }

    /**
     * Update the RCA
     */
    public function update(Request $request, Incident $incident, RootCauseAnalysis $rootCauseAnalysis)
    {
        if ($rootCauseAnalysis->company_id !== Auth::user()->company_id || 
            $rootCauseAnalysis->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'why_1' => 'nullable|string',
            'why_2' => 'nullable|string',
            'why_3' => 'nullable|string',
            'why_4' => 'nullable|string',
            'why_5' => 'nullable|string',
            'root_cause' => 'nullable|string',
            'human_factors' => 'nullable|string',
            'organizational_factors' => 'nullable|string',
            'technical_factors' => 'nullable|string',
            'environmental_factors' => 'nullable|string',
            'procedural_factors' => 'nullable|string',
            'equipment_factors' => 'nullable|string',
            'direct_cause' => 'nullable|string',
            'contributing_causes' => 'nullable|string',
            'root_causes' => 'nullable|string',
            'systemic_failures' => 'nullable|string',
            'causal_factors' => 'nullable|array',
            'barriers_failed' => 'nullable|array',
            'prevention_possible' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
        ]);

        $rootCauseAnalysis->update($validated);

        return redirect()
            ->route('incidents.rca.show', [$incident, $rootCauseAnalysis])
            ->with('success', 'Root Cause Analysis updated successfully!');
    }

    /**
     * Complete the RCA
     */
    public function complete(Incident $incident, RootCauseAnalysis $rootCauseAnalysis)
    {
        if ($rootCauseAnalysis->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $rootCauseAnalysis->complete();

        return back()->with('success', 'Root Cause Analysis completed!');
    }

    /**
     * Review the RCA
     */
    public function review(Incident $incident, RootCauseAnalysis $rootCauseAnalysis)
    {
        if ($rootCauseAnalysis->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $rootCauseAnalysis->review(Auth::user());

        return back()->with('success', 'Root Cause Analysis reviewed!');
    }
}
