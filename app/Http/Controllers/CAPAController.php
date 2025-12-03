<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\CAPA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CAPAController extends Controller
{
    /**
     * Show the form for creating a new CAPA
     */
    public function create(Incident $incident)
    {
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::where('company_id', $companyId)->get();
        $departments = \App\Models\Department::where('company_id', $companyId)->get();
        $rootCauses = $incident->rootCauseAnalysis ? [$incident->rootCauseAnalysis] : [];

        return view('incidents.capas.create', compact('incident', 'users', 'departments', 'rootCauses'));
    }

    /**
     * Store a newly created CAPA
     */
    public function store(Request $request, Incident $incident)
    {
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'action_type' => 'required|in:corrective,preventive,both',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'root_cause_addressed' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|in:low,medium,high,critical',
            'required_resources' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'budget_approved' => 'nullable|string',
            'due_date' => 'required|date|after:today',
            'implementation_plan' => 'nullable|string',
        ]);

        $validated['incident_id'] = $incident->id;
        $validated['company_id'] = Auth::user()->company_id;
        $validated['assigned_by'] = Auth::user()->id;
        $validated['root_cause_analysis_id'] = $incident->root_cause_analysis_id;
        $validated['status'] = 'pending';

        $capa = CAPA::create($validated);

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'CAPA created successfully!');
    }

    /**
     * Display the specified CAPA
     */
    public function show(Incident $incident, CAPA $capa)
    {
        if ($capa->company_id !== Auth::user()->company_id || 
            $capa->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        $capa->load(['assignedTo', 'assignedBy', 'department', 'verifiedBy', 'closedBy', 'incident']);

        return view('incidents.capas.show', compact('incident', 'capa'));
    }

    /**
     * Show the form for editing the CAPA
     */
    public function edit(Incident $incident, CAPA $capa)
    {
        if ($capa->company_id !== Auth::user()->company_id || 
            $capa->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::where('company_id', $companyId)->get();
        $departments = \App\Models\Department::where('company_id', $companyId)->get();

        return view('incidents.capas.edit', compact('incident', 'capa', 'users', 'departments'));
    }

    /**
     * Update the CAPA
     */
    public function update(Request $request, Incident $incident, CAPA $capa)
    {
        if ($capa->company_id !== Auth::user()->company_id || 
            $capa->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'due_date' => 'required|date',
            'implementation_plan' => 'nullable|string',
            'progress_notes' => 'nullable|string',
            'challenges_encountered' => 'nullable|string',
        ]);

        $capa->update($validated);

        return redirect()
            ->route('incidents.capas.show', [$incident, $capa])
            ->with('success', 'CAPA updated successfully!');
    }

    /**
     * Start the CAPA
     */
    public function start(Incident $incident, CAPA $capa)
    {
        if ($capa->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $capa->start();

        return back()->with('success', 'CAPA started!');
    }

    /**
     * Complete the CAPA
     */
    public function complete(Incident $incident, CAPA $capa)
    {
        if ($capa->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $capa->complete();

        return back()->with('success', 'CAPA completed and ready for review!');
    }

    /**
     * Verify the CAPA
     */
    public function verify(Request $request, Incident $incident, CAPA $capa)
    {
        if ($capa->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'verification_notes' => 'nullable|string',
            'effectiveness' => 'required|in:effective,partially_effective,ineffective,not_yet_measured',
            'effectiveness_evidence' => 'nullable|string',
        ]);

        $capa->verify(Auth::user(), $validated['verification_notes'], $validated['effectiveness']);
        $capa->update(['effectiveness_evidence' => $validated['effectiveness_evidence'] ?? null]);

        return back()->with('success', 'CAPA verified!');
    }

    /**
     * Close the CAPA
     */
    public function close(Request $request, Incident $incident, CAPA $capa)
    {
        if ($capa->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'closure_notes' => 'nullable|string',
        ]);

        $capa->close(Auth::user(), $validated['closure_notes'] ?? null);

        return back()->with('success', 'CAPA closed!');
    }
}
