<?php

namespace App\Http\Controllers;

use App\Models\EmissionMonitoringRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmissionMonitoringRecordController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = EmissionMonitoringRecord::forCompany($companyId)
            ->with(['monitoredBy', 'verifiedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('parameter', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('compliance_status')) {
            $query->where('compliance_status', $request->compliance_status);
        }

        if ($request->filled('monitoring_type')) {
            $query->where('monitoring_type', $request->monitoring_type);
        }

        $records = $query->latest('monitoring_date')->paginate(15);
        return view('environmental.emissions.index', compact('records'));
    }

    public function create()
    {
        return view('environmental.emissions.create');
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'monitoring_type' => 'required|in:air_emission,water_effluent,noise,vibration,odor,other',
            'source' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'monitoring_date' => 'required|date',
            'monitoring_time' => 'nullable',
            'parameter' => 'nullable|string|max:255',
            'measured_value' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
            'permissible_limit' => 'nullable|numeric',
            'compliance_status' => 'required|in:compliant,non_compliant,marginal',
            'weather_conditions' => 'nullable|string',
            'operating_conditions' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['monitored_by'] = Auth::id();

        EmissionMonitoringRecord::create($validated);

        return redirect()->route('environmental.emissions.index')
            ->with('success', 'Emission monitoring record created successfully.');
    }

    public function show(EmissionMonitoringRecord $emissionMonitoringRecord)
    {
        $emissionMonitoringRecord->load(['monitoredBy', 'verifiedBy']);
        return view('environmental.emissions.show', compact('emissionMonitoringRecord'));
    }

    public function edit(EmissionMonitoringRecord $emissionMonitoringRecord)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('environmental.emissions.edit', compact('emissionMonitoringRecord', 'users'));
    }

    public function update(Request $request, EmissionMonitoringRecord $emissionMonitoringRecord)
    {
        $validated = $request->validate([
            'monitoring_type' => 'required|in:air_emission,water_effluent,noise,vibration,odor,other',
            'source' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'monitoring_date' => 'required|date',
            'monitoring_time' => 'nullable',
            'parameter' => 'nullable|string|max:255',
            'measured_value' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
            'permissible_limit' => 'nullable|numeric',
            'compliance_status' => 'required|in:compliant,non_compliant,marginal',
            'weather_conditions' => 'nullable|string',
            'operating_conditions' => 'nullable|string',
            'verified_by' => 'nullable|exists:users,id',
            'verification_date' => 'nullable|date',
            'corrective_action' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $emissionMonitoringRecord->update($validated);

        return redirect()->route('environmental.emissions.show', $emissionMonitoringRecord)
            ->with('success', 'Emission monitoring record updated successfully.');
    }

    public function destroy(EmissionMonitoringRecord $emissionMonitoringRecord)
    {
        $emissionMonitoringRecord->delete();
        return redirect()->route('environmental.emissions.index')
            ->with('success', 'Emission monitoring record deleted successfully.');
    }
}
