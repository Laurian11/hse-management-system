<?php

namespace App\Http\Controllers;

use App\Models\HealthCampaign;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthCampaignController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = HealthCampaign::forCompany($companyId)
            ->with(['department', 'coordinator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('campaign_title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('campaign_type')) {
            $query->where('campaign_type', $request->campaign_type);
        }

        $campaigns = $query->latest('start_date')->paginate(15);
        return view('health.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('health.campaigns.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'campaign_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'campaign_type' => 'required|in:wellness_program,health_screening,vaccination_drive,health_education,fitness_program,mental_health,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'target_audience' => 'nullable|array',
            'coordinator_id' => 'nullable|exists:users,id',
            'objectives' => 'nullable|string',
            'activities' => 'nullable|string',
            'target_participants' => 'nullable|integer|min:0',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;

        HealthCampaign::create($validated);

        return redirect()->route('health.campaigns.index')
            ->with('success', 'Health campaign created successfully.');
    }

    public function show(HealthCampaign $healthCampaign)
    {
        $healthCampaign->load(['department', 'coordinator']);
        return view('health.campaigns.show', compact('healthCampaign'));
    }

    public function edit(HealthCampaign $healthCampaign)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = User::forCompany($companyId)->active()->get();
        return view('health.campaigns.edit', compact('healthCampaign', 'departments', 'users'));
    }

    public function update(Request $request, HealthCampaign $healthCampaign)
    {
        $validated = $request->validate([
            'campaign_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'campaign_type' => 'required|in:wellness_program,health_screening,vaccination_drive,health_education,fitness_program,mental_health,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
            'target_audience' => 'nullable|array',
            'coordinator_id' => 'nullable|exists:users,id',
            'objectives' => 'nullable|string',
            'activities' => 'nullable|string',
            'target_participants' => 'nullable|integer|min:0',
            'actual_participants' => 'nullable|integer|min:0',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'outcomes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $healthCampaign->update($validated);

        return redirect()->route('health.campaigns.show', $healthCampaign)
            ->with('success', 'Health campaign updated successfully.');
    }

    public function destroy(HealthCampaign $healthCampaign)
    {
        $healthCampaign->delete();
        return redirect()->route('health.campaigns.index')
            ->with('success', 'Health campaign deleted successfully.');
    }
}
