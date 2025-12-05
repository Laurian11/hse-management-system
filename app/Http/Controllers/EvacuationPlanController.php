<?php

namespace App\Http\Controllers;

use App\Models\EvacuationPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvacuationPlanController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = EvacuationPlan::forCompany($companyId)->with(['reviewedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('plan_type')) {
            $query->where('plan_type', $request->plan_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $plans = $query->latest()->paginate(15);
        return view('emergency.evacuation-plans.index', compact('plans'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('emergency.evacuation-plans.create', compact('users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'plan_type' => 'required|in:building,floor,area,site,general',
            'assembly_points' => 'nullable|array',
            'evacuation_routes' => 'nullable|array',
            'emergency_exits' => 'nullable|array',
            'hazard_zones' => 'nullable|array',
            'evacuation_procedures' => 'nullable|string',
            'accountability_procedures' => 'nullable|string',
            'special_needs_procedures' => 'nullable|string',
            'roles_and_responsibilities' => 'nullable|array',
            'required_equipment' => 'nullable|array',
            'communication_methods' => 'nullable|array',
            'next_review_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;
        EvacuationPlan::create($validated);

        return redirect()->route('emergency.evacuation-plans.index')
            ->with('success', 'Evacuation plan created successfully.');
    }

    public function show(EvacuationPlan $plan)
    {
        $plan->load(['reviewedBy']);
        return view('emergency.evacuation-plans.show', compact('plan'));
    }

    public function edit(EvacuationPlan $plan)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('emergency.evacuation-plans.edit', compact('plan', 'users'));
    }

    public function update(Request $request, EvacuationPlan $plan)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'plan_type' => 'required|in:building,floor,area,site,general',
            'assembly_points' => 'nullable|array',
            'evacuation_routes' => 'nullable|array',
            'emergency_exits' => 'nullable|array',
            'hazard_zones' => 'nullable|array',
            'evacuation_procedures' => 'nullable|string',
            'accountability_procedures' => 'nullable|string',
            'special_needs_procedures' => 'nullable|string',
            'roles_and_responsibilities' => 'nullable|array',
            'required_equipment' => 'nullable|array',
            'communication_methods' => 'nullable|array',
            'last_review_date' => 'nullable|date',
            'next_review_date' => 'nullable|date',
            'reviewed_by' => 'nullable|exists:users,id',
            'review_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $plan->update($validated);

        return redirect()->route('emergency.evacuation-plans.show', $plan)
            ->with('success', 'Evacuation plan updated successfully.');
    }

    public function destroy(EvacuationPlan $plan)
    {
        $plan->delete();
        return redirect()->route('emergency.evacuation-plans.index')
            ->with('success', 'Evacuation plan deleted successfully.');
    }
}
