<?php

namespace App\Http\Controllers;

use App\Models\WasteManagementRecord;
use App\Models\Department;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WasteManagementRecordController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = WasteManagementRecord::forCompany($companyId)
            ->with(['department', 'recordedBy', 'disposalContractor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('waste_type', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('waste_type')) {
            $query->where('waste_type', $request->waste_type);
        }

        if ($request->filled('segregation_status')) {
            $query->where('segregation_status', $request->segregation_status);
        }

        $records = $query->latest()->paginate(15);
        return view('environmental.waste-management.index', compact('records'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $suppliers = Supplier::forCompany($companyId)->active()->get();
        return view('environmental.waste-management.create', compact('departments', 'suppliers'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'waste_type' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'segregation_status' => 'required|in:properly_segregated,improperly_segregated,mixed',
            'storage_location' => 'nullable|string|max:255',
            'storage_method' => 'nullable|in:container,tank,drum,bag,other',
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'collection_date' => 'nullable|date',
            'disposal_date' => 'nullable|date',
            'disposal_method' => 'nullable|in:landfill,incineration,recycling,treatment,reuse,other',
            'disposal_contractor_id' => 'nullable|exists:suppliers,id',
            'disposal_certificate_number' => 'nullable|string|max:255',
            'disposal_certificate_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['recorded_by'] = Auth::id();

        WasteManagementRecord::create($validated);

        return redirect()->route('environmental.waste-management.index')
            ->with('success', 'Waste management record created successfully.');
    }

    public function show(WasteManagementRecord $wasteManagementRecord)
    {
        $wasteManagementRecord->load(['department', 'recordedBy', 'disposalContractor']);
        return view('environmental.waste-management.show', compact('wasteManagementRecord'));
    }

    public function edit(WasteManagementRecord $wasteManagementRecord)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $suppliers = Supplier::forCompany($companyId)->active()->get();
        return view('environmental.waste-management.edit', compact('wasteManagementRecord', 'departments', 'suppliers'));
    }

    public function update(Request $request, WasteManagementRecord $wasteManagementRecord)
    {
        $validated = $request->validate([
            'waste_type' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'segregation_status' => 'required|in:properly_segregated,improperly_segregated,mixed',
            'storage_location' => 'nullable|string|max:255',
            'storage_method' => 'nullable|in:container,tank,drum,bag,other',
            'quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'collection_date' => 'nullable|date',
            'disposal_date' => 'nullable|date',
            'disposal_method' => 'nullable|in:landfill,incineration,recycling,treatment,reuse,other',
            'disposal_contractor_id' => 'nullable|exists:suppliers,id',
            'disposal_certificate_number' => 'nullable|string|max:255',
            'disposal_certificate_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string',
        ]);

        $wasteManagementRecord->update($validated);

        return redirect()->route('environmental.waste-management.show', $wasteManagementRecord)
            ->with('success', 'Waste management record updated successfully.');
    }

    public function destroy(WasteManagementRecord $wasteManagementRecord)
    {
        $wasteManagementRecord->delete();
        return redirect()->route('environmental.waste-management.index')
            ->with('success', 'Waste management record deleted successfully.');
    }
}
