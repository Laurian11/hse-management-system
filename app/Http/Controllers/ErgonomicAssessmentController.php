<?php

namespace App\Http\Controllers;

use App\Models\ErgonomicAssessment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErgonomicAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = ErgonomicAssessment::forCompany($companyId)
            ->with(['assessedEmployee', 'assessedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('workstation_location', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        $assessments = $query->latest('assessment_date')->paginate(15);
        return view('health.ergonomic.index', compact('assessments'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('health.ergonomic.create', compact('users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'workstation_location' => 'nullable|string|max:255',
            'assessed_employee_id' => 'nullable|exists:users,id',
            'job_title' => 'nullable|string|max:255',
            'task_description' => 'nullable|string',
            'assessment_date' => 'required|date',
            'risk_factors' => 'nullable|array',
            'risk_level' => 'required|in:low,medium,high,very_high',
            'findings' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'control_measures' => 'nullable|string',
            'review_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,reviewed',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['assessed_by'] = Auth::id();

        ErgonomicAssessment::create($validated);

        return redirect()->route('health.ergonomic.index')
            ->with('success', 'Ergonomic assessment created successfully.');
    }

    public function show(ErgonomicAssessment $ergonomicAssessment)
    {
        $ergonomicAssessment->load(['assessedEmployee', 'assessedBy']);
        return view('health.ergonomic.show', compact('ergonomicAssessment'));
    }

    public function edit(ErgonomicAssessment $ergonomicAssessment)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('health.ergonomic.edit', compact('ergonomicAssessment', 'users'));
    }

    public function update(Request $request, ErgonomicAssessment $ergonomicAssessment)
    {
        $validated = $request->validate([
            'workstation_location' => 'nullable|string|max:255',
            'assessed_employee_id' => 'nullable|exists:users,id',
            'job_title' => 'nullable|string|max:255',
            'task_description' => 'nullable|string',
            'assessment_date' => 'required|date',
            'risk_factors' => 'nullable|array',
            'risk_level' => 'required|in:low,medium,high,very_high',
            'findings' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'control_measures' => 'nullable|string',
            'review_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed,reviewed',
            'notes' => 'nullable|string',
        ]);

        $ergonomicAssessment->update($validated);

        return redirect()->route('health.ergonomic.show', $ergonomicAssessment)
            ->with('success', 'Ergonomic assessment updated successfully.');
    }

    public function destroy(ErgonomicAssessment $ergonomicAssessment)
    {
        $ergonomicAssessment->delete();
        return redirect()->route('health.ergonomic.index')
            ->with('success', 'Ergonomic assessment deleted successfully.');
    }
}
