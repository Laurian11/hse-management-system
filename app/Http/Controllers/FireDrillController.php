<?php

namespace App\Http\Controllers;

use App\Models\FireDrill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FireDrillController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = FireDrill::forCompany($companyId)->with(['conductedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('drill_type')) {
            $query->where('drill_type', $request->drill_type);
        }

        if ($request->filled('drill_date_from')) {
            $query->whereDate('drill_date', '>=', $request->drill_date_from);
        }

        if ($request->filled('drill_date_to')) {
            $query->whereDate('drill_date', '<=', $request->drill_date_to);
        }

        $fireDrills = $query->latest('drill_date')->paginate(15);
        return view('emergency.fire-drills.index', compact('fireDrills'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('emergency.fire-drills.create', compact('users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'drill_date' => 'required|date',
            'drill_time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'drill_type' => 'required|in:announced,unannounced,partial,full',
            'objectives' => 'nullable|string',
            'scenario' => 'nullable|string',
            'expected_participants' => 'nullable|integer|min:0',
            'participants' => 'nullable|array',
            'evacuation_time' => 'nullable|date_format:H:i:s',
            'overall_result' => 'nullable|in:excellent,good,satisfactory,needs_improvement,poor',
            'observations' => 'nullable|string',
            'strengths' => 'nullable|array',
            'weaknesses' => 'nullable|array',
            'recommendations' => 'nullable|array',
            'observers' => 'nullable|array',
            'requires_follow_up' => 'boolean',
            'follow_up_actions' => 'nullable|string',
            'follow_up_due_date' => 'nullable|date|required_if:requires_follow_up,1',
        ]);

        $validated['company_id'] = $companyId;
        $validated['conducted_by'] = Auth::id();
        $validated['total_participants'] = count($validated['participants'] ?? []);

        FireDrill::create($validated);

        return redirect()->route('emergency.fire-drills.index')
            ->with('success', 'Fire drill record created successfully.');
    }

    public function show(FireDrill $fireDrill)
    {
        $fireDrill->load(['conductedBy']);
        return view('emergency.fire-drills.show', compact('fireDrill'));
    }

    public function edit(FireDrill $fireDrill)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('emergency.fire-drills.edit', compact('fireDrill', 'users'));
    }

    public function update(Request $request, FireDrill $fireDrill)
    {
        $validated = $request->validate([
            'drill_date' => 'required|date',
            'drill_time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'drill_type' => 'required|in:announced,unannounced,partial,full',
            'objectives' => 'nullable|string',
            'scenario' => 'nullable|string',
            'expected_participants' => 'nullable|integer|min:0',
            'participants' => 'nullable|array',
            'evacuation_time' => 'nullable|date_format:H:i:s',
            'overall_result' => 'nullable|in:excellent,good,satisfactory,needs_improvement,poor',
            'observations' => 'nullable|string',
            'strengths' => 'nullable|array',
            'weaknesses' => 'nullable|array',
            'recommendations' => 'nullable|array',
            'observers' => 'nullable|array',
            'requires_follow_up' => 'boolean',
            'follow_up_actions' => 'nullable|string',
            'follow_up_due_date' => 'nullable|date|required_if:requires_follow_up,1',
            'follow_up_completed' => 'boolean',
        ]);

        $validated['total_participants'] = count($validated['participants'] ?? []);

        $fireDrill->update($validated);

        return redirect()->route('emergency.fire-drills.show', $fireDrill)
            ->with('success', 'Fire drill record updated successfully.');
    }

    public function destroy(FireDrill $fireDrill)
    {
        $fireDrill->delete();
        return redirect()->route('emergency.fire-drills.index')
            ->with('success', 'Fire drill record deleted successfully.');
    }
}
