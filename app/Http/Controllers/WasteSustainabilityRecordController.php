<?php

namespace App\Http\Controllers;

use App\Models\WasteSustainabilityRecord;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WasteSustainabilityRecordController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = WasteSustainabilityRecord::forCompany($companyId)
            ->with(['recordedBy', 'department']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('record_type')) {
            $query->where('record_type', $request->record_type);
        }

        if ($request->filled('waste_category')) {
            $query->where('waste_category', $request->waste_category);
        }

        if ($request->filled('date_from')) {
            $query->where('record_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('record_date', '<=', $request->date_to);
        }

        $records = $query->latest('record_date')->paginate(15);
        $departments = Department::forCompany($companyId)->active()->get();
        
        return view('waste-sustainability.records.index', compact('records', 'departments'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('waste-sustainability.records.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'record_type' => 'required|in:recycling,waste_segregation,composting,reuse,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'record_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'waste_category' => 'nullable|string|max:255',
            'disposal_method' => 'nullable|string|max:255',
            'carbon_equivalent' => 'nullable|numeric|min:0',
            'energy_saved' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['recorded_by'] = Auth::id();
        WasteSustainabilityRecord::create($validated);

        return redirect()->route('waste-sustainability.records.index')
            ->with('success', 'Waste sustainability record created successfully.');
    }

    public function show(WasteSustainabilityRecord $record)
    {
        $record->load(['recordedBy', 'department']);
        return view('waste-sustainability.records.show', compact('record'));
    }

    public function edit(WasteSustainabilityRecord $record)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        return view('waste-sustainability.records.edit', compact('record', 'departments'));
    }

    public function update(Request $request, WasteSustainabilityRecord $record)
    {
        $validated = $request->validate([
            'record_type' => 'required|in:recycling,waste_segregation,composting,reuse,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'record_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'waste_category' => 'nullable|string|max:255',
            'disposal_method' => 'nullable|string|max:255',
            'carbon_equivalent' => 'nullable|numeric|min:0',
            'energy_saved' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $record->update($validated);

        return redirect()->route('waste-sustainability.records.show', $record)
            ->with('success', 'Waste sustainability record updated successfully.');
    }

    public function destroy(WasteSustainabilityRecord $record)
    {
        $record->delete();

        return redirect()->route('waste-sustainability.records.index')
            ->with('success', 'Waste sustainability record deleted successfully.');
    }
}
