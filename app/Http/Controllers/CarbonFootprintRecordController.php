<?php

namespace App\Http\Controllers;

use App\Models\CarbonFootprintRecord;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarbonFootprintRecordController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = CarbonFootprintRecord::forCompany($companyId)
            ->with(['recordedBy', 'department']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('source_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->filled('date_from')) {
            $query->where('record_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('record_date', '<=', $request->date_to);
        }

        $records = $query->latest('record_date')->paginate(15);
        $departments = Department::forCompany($companyId)->active()->get();
        
        return view('waste-sustainability.carbon-footprint.index', compact('records', 'departments'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('waste-sustainability.carbon-footprint.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'record_date' => 'required|date',
            'source_type' => 'required|in:electricity,fuel,water,transportation,waste,other',
            'source_name' => 'required|string|max:255',
            'consumption' => 'required|numeric|min:0',
            'consumption_unit' => 'required|string|max:50',
            'emission_factor' => 'nullable|numeric|min:0',
            'carbon_equivalent' => 'nullable|numeric|min:0',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['recorded_by'] = Auth::id();
        
        // Auto-calculate carbon equivalent if not provided
        if (!$validated['carbon_equivalent'] && $validated['consumption'] && $validated['emission_factor']) {
            $validated['carbon_equivalent'] = $validated['consumption'] * $validated['emission_factor'];
        }
        
        CarbonFootprintRecord::create($validated);

        return redirect()->route('waste-sustainability.carbon-footprint.index')
            ->with('success', 'Carbon footprint record created successfully.');
    }

    public function show(CarbonFootprintRecord $carbonFootprint)
    {
        $carbonFootprint->load(['recordedBy', 'department']);
        return view('waste-sustainability.carbon-footprint.show', compact('carbonFootprint'));
    }

    public function edit(CarbonFootprintRecord $carbonFootprint)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        return view('waste-sustainability.carbon-footprint.edit', compact('carbonFootprint', 'departments'));
    }

    public function update(Request $request, CarbonFootprintRecord $carbonFootprint)
    {
        $validated = $request->validate([
            'record_date' => 'required|date',
            'source_type' => 'required|in:electricity,fuel,water,transportation,waste,other',
            'source_name' => 'required|string|max:255',
            'consumption' => 'required|numeric|min:0',
            'consumption_unit' => 'required|string|max:50',
            'emission_factor' => 'nullable|numeric|min:0',
            'carbon_equivalent' => 'nullable|numeric|min:0',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Auto-calculate carbon equivalent if not provided
        if (!$validated['carbon_equivalent'] && $validated['consumption'] && $validated['emission_factor']) {
            $validated['carbon_equivalent'] = $validated['consumption'] * $validated['emission_factor'];
        }

        $carbonFootprint->update($validated);

        return redirect()->route('waste-sustainability.carbon-footprint.show', $carbonFootprint)
            ->with('success', 'Carbon footprint record updated successfully.');
    }

    public function destroy(CarbonFootprintRecord $carbonFootprint)
    {
        $carbonFootprint->delete();

        return redirect()->route('waste-sustainability.carbon-footprint.index')
            ->with('success', 'Carbon footprint record deleted successfully.');
    }
}
