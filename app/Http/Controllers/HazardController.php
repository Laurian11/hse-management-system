<?php

namespace App\Http\Controllers;

use App\Models\Hazard;
use App\Models\Incident;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HazardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = Hazard::forCompany($companyId)
            ->with(['creator', 'department', 'relatedIncident']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('hazard_category')) {
            $query->byCategory($request->hazard_category);
        }
        
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->filled('identification_method')) {
            $query->where('identification_method', $request->identification_method);
        }
        
        $hazards = $query->latest()->paginate(15);
        $departments = Department::where('company_id', $companyId)->active()->get();
        
        // Statistics
        $stats = [
            'total' => Hazard::forCompany($companyId)->count(),
            'identified' => Hazard::forCompany($companyId)->identified()->count(),
            'assessed' => Hazard::forCompany($companyId)->assessed()->count(),
            'controlled' => Hazard::forCompany($companyId)->controlled()->count(),
        ];
        
        return view('risk-assessment.hazards.index', compact('hazards', 'departments', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::where('company_id', $companyId)->active()->get();
        $incidents = null;
        
        // If creating from incident, pre-populate
        if ($request->has('incident_id')) {
            $incident = Incident::where('company_id', $companyId)
                ->findOrFail($request->incident_id);
            $incidents = collect([$incident]);
        }
        
        return view('risk-assessment.hazards.create', compact('departments', 'incidents'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'hazard_category' => 'required|in:physical,chemical,biological,ergonomic,psychosocial,mechanical,electrical,fire,environmental,other',
            'location' => 'nullable|string|max:255',
            'process_or_activity' => 'nullable|string|max:255',
            'asset_or_equipment' => 'nullable|string|max:255',
            'hazard_source' => 'nullable|in:routine_activity,non_routine_activity,maintenance,change_introduction,emergency_situation,contractor_work,other',
            'identification_method' => 'nullable|in:hazid_checklist,what_if_analysis,hazop,job_observation,incident_analysis,audit_finding,employee_report,other',
            'at_risk_personnel' => 'nullable|array',
            'exposure_description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'related_incident_id' => 'nullable|exists:incidents,id',
            'status' => 'nullable|in:identified,assessed,controlled,closed,archived',
        ]);
        
        $validated['company_id'] = $companyId;
        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'identified';
        
        $hazard = Hazard::create($validated);
        
        // If created from incident, update incident
        if ($hazard->related_incident_id) {
            $incident = Incident::find($hazard->related_incident_id);
            if ($incident && $incident->company_id === $companyId) {
                $incident->update([
                    'related_hazard_id' => $hazard->id,
                    'hazard_was_identified' => true,
                ]);
            }
        }
        
        return redirect()
            ->route('risk-assessment.hazards.show', $hazard)
            ->with('success', 'Hazard identified successfully!');
    }

    public function show(Hazard $hazard)
    {
        if ($hazard->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $hazard->load([
            'creator',
            'department',
            'relatedIncident',
            'riskAssessments',
            'controlMeasures',
        ]);
        
        return view('risk-assessment.hazards.show', compact('hazard'));
    }

    public function edit(Hazard $hazard)
    {
        if ($hazard->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $companyId = Auth::user()->company_id;
        $departments = Department::where('company_id', $companyId)->active()->get();
        
        return view('risk-assessment.hazards.edit', compact('hazard', 'departments'));
    }

    public function update(Request $request, Hazard $hazard)
    {
        if ($hazard->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'hazard_category' => 'required|in:physical,chemical,biological,ergonomic,psychosocial,mechanical,electrical,fire,environmental,other',
            'location' => 'nullable|string|max:255',
            'process_or_activity' => 'nullable|string|max:255',
            'asset_or_equipment' => 'nullable|string|max:255',
            'hazard_source' => 'nullable|in:routine_activity,non_routine_activity,maintenance,change_introduction,emergency_situation,contractor_work,other',
            'identification_method' => 'nullable|in:hazid_checklist,what_if_analysis,hazop,job_observation,incident_analysis,audit_finding,employee_report,other',
            'at_risk_personnel' => 'nullable|array',
            'exposure_description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:identified,assessed,controlled,closed,archived',
        ]);
        
        $hazard->update($validated);
        
        return redirect()
            ->route('risk-assessment.hazards.show', $hazard)
            ->with('success', 'Hazard updated successfully!');
    }

    public function destroy(Hazard $hazard)
    {
        if ($hazard->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        // Check if hazard has risk assessments or controls
        if ($hazard->riskAssessments()->count() > 0 || $hazard->controlMeasures()->count() > 0) {
            return back()->with('error', 'Cannot delete hazard that has associated risk assessments or control measures.');
        }
        
        $hazard->delete();
        
        return redirect()
            ->route('risk-assessment.hazards.index')
            ->with('success', 'Hazard deleted successfully!');
    }
}
