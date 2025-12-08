<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\CAPA;
use App\Notifications\CAPAAssignedNotification;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CAPAController extends Controller
{
    use UsesCompanyGroup;

    /**
     * Show the form for creating a new CAPA
     */
    public function create(Incident $incident)
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($incident->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }

        $users = \App\Models\User::whereIn('company_id', $companyGroupIds)->get();
        $departments = \App\Models\Department::whereIn('company_id', $companyGroupIds)->get();
        $rootCauses = $incident->rootCauseAnalysis ? [$incident->rootCauseAnalysis] : [];

        return view('incidents.capas.create', compact('incident', 'users', 'departments', 'rootCauses'));
    }

    /**
     * Store a newly created CAPA
     */
    public function store(Request $request, Incident $incident)
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($incident->company_id, $companyGroupIds)) {
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
        $capa->load('assignedTo');

        // Send notification to assigned user
        if ($capa->assignedTo) {
            $capa->assignedTo->notify(new CAPAAssignedNotification($capa));
        }

        // Also notify supervisor if assigned user has one
        if ($capa->assignedTo && $capa->assignedTo->directSupervisor) {
            $capa->assignedTo->directSupervisor->notify(new CAPAAssignedNotification($capa));
        }

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'CAPA created successfully!');
    }

    /**
     * Display the specified CAPA
     */
    public function show(Incident $incident, CAPA $capa)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($capa->company_id, $companyGroupIds) || 
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
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($capa->company_id, $companyGroupIds) || 
            $capa->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        $companyGroupIds = $this->getCompanyGroupIds();
        $users = \App\Models\User::whereIn('company_id', $companyGroupIds)->get();
        $departments = \App\Models\Department::whereIn('company_id', $companyGroupIds)->get();

        return view('incidents.capas.edit', compact('incident', 'capa', 'users', 'departments'));
    }

    /**
     * Update the CAPA
     */
    public function update(Request $request, Incident $incident, CAPA $capa)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($capa->company_id, $companyGroupIds) || 
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
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($capa->company_id, $companyGroupIds)) {
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
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($capa->company_id, $companyGroupIds)) {
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
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($capa->company_id, $companyGroupIds)) {
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
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($capa->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'closure_notes' => 'nullable|string',
        ]);

        $capa->close(Auth::user(), $validated['closure_notes'] ?? null);

        return back()->with('success', 'CAPA closed!');
    }
}
