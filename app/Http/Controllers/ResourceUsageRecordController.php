<?php

namespace App\Http\Controllers;

use App\Models\ResourceUsageRecord;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceUsageRecordController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = ResourceUsageRecord::forCompany($companyId)
            ->with(['department', 'recordedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('meter_number', 'like', "%{$search}%")
                  ->orWhere('meter_location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->resource_type);
        }

        $records = $query->latest('reading_date')->paginate(15);
        return view('environmental.resource-usage.index', compact('records'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        return view('environmental.resource-usage.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'resource_type' => 'required|in:water,fuel,electricity,gas,steam,other',
            'reading_date' => 'required|date',
            'meter_location' => 'nullable|string|max:255',
            'meter_number' => 'nullable|string|max:255',
            'previous_reading' => 'nullable|numeric|min:0',
            'current_reading' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['recorded_by'] = Auth::id();
        
        // Calculate consumption
        if ($validated['previous_reading'] && $validated['current_reading']) {
            $validated['consumption'] = $validated['current_reading'] - $validated['previous_reading'];
        }

        ResourceUsageRecord::create($validated);

        return redirect()->route('environmental.resource-usage.index')
            ->with('success', 'Resource usage record created successfully.');
    }

    public function show(ResourceUsageRecord $resourceUsageRecord)
    {
        $resourceUsageRecord->load(['department', 'recordedBy']);
        return view('environmental.resource-usage.show', compact('resourceUsageRecord'));
    }

    public function edit(ResourceUsageRecord $resourceUsageRecord)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        return view('environmental.resource-usage.edit', compact('resourceUsageRecord', 'departments'));
    }

    public function update(Request $request, ResourceUsageRecord $resourceUsageRecord)
    {
        $validated = $request->validate([
            'resource_type' => 'required|in:water,fuel,electricity,gas,steam,other',
            'reading_date' => 'required|date',
            'meter_location' => 'nullable|string|max:255',
            'meter_number' => 'nullable|string|max:255',
            'previous_reading' => 'nullable|numeric|min:0',
            'current_reading' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string',
        ]);

        // Recalculate consumption
        if ($validated['previous_reading'] && $validated['current_reading']) {
            $validated['consumption'] = $validated['current_reading'] - $validated['previous_reading'];
        }

        $resourceUsageRecord->update($validated);

        return redirect()->route('environmental.resource-usage.show', $resourceUsageRecord)
            ->with('success', 'Resource usage record updated successfully.');
    }

    public function destroy(ResourceUsageRecord $resourceUsageRecord)
    {
        $resourceUsageRecord->delete();
        return redirect()->route('environmental.resource-usage.index')
            ->with('success', 'Resource usage record deleted successfully.');
    }
}
