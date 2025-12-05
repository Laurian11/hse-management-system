<?php

namespace App\Http\Controllers;

use App\Models\EmergencyResponseTeam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyResponseTeamController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = EmergencyResponseTeam::forCompany($companyId)->with(['teamLeader', 'deputyLeader']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('team_type')) {
            $query->where('team_type', $request->team_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $teams = $query->latest()->paginate(15);
        return view('emergency.response-teams.index', compact('teams'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('emergency.response-teams.create', compact('users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'team_type' => 'required|in:fire_warden,first_aid,evacuation,search_rescue,hazmat,security,medical,general',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'team_members' => 'nullable|array',
            'team_leader_id' => 'nullable|exists:users,id',
            'deputy_leader_id' => 'nullable|exists:users,id',
            'last_training_date' => 'nullable|date',
            'next_training_date' => 'nullable|date',
            'training_requirements' => 'nullable|string',
            'certifications' => 'nullable|array',
            'is_24_7' => 'boolean',
            'availability_schedule' => 'nullable|array',
            'contact_information' => 'nullable|array',
            'assigned_equipment' => 'nullable|array',
            'equipment_requirements' => 'nullable|string',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        EmergencyResponseTeam::create($validated);

        return redirect()->route('emergency.response-teams.index')
            ->with('success', 'Emergency response team created successfully.');
    }

    public function show(EmergencyResponseTeam $team)
    {
        $team->load(['teamLeader', 'deputyLeader']);
        return view('emergency.response-teams.show', compact('team'));
    }

    public function edit(EmergencyResponseTeam $team)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('emergency.response-teams.edit', compact('team', 'users'));
    }

    public function update(Request $request, EmergencyResponseTeam $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'team_type' => 'required|in:fire_warden,first_aid,evacuation,search_rescue,hazmat,security,medical,general',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'team_members' => 'nullable|array',
            'team_leader_id' => 'nullable|exists:users,id',
            'deputy_leader_id' => 'nullable|exists:users,id',
            'last_training_date' => 'nullable|date',
            'next_training_date' => 'nullable|date',
            'training_requirements' => 'nullable|string',
            'certifications' => 'nullable|array',
            'is_24_7' => 'boolean',
            'availability_schedule' => 'nullable|array',
            'contact_information' => 'nullable|array',
            'assigned_equipment' => 'nullable|array',
            'equipment_requirements' => 'nullable|string',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $team->update($validated);

        return redirect()->route('emergency.response-teams.show', $team)
            ->with('success', 'Emergency response team updated successfully.');
    }

    public function destroy(EmergencyResponseTeam $team)
    {
        $team->delete();
        return redirect()->route('emergency.response-teams.index')
            ->with('success', 'Emergency response team deleted successfully.');
    }
}
