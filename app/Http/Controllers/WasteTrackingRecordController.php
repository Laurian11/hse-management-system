<?php

namespace App\Http\Controllers;

use App\Models\WasteTrackingRecord;
use App\Models\WasteManagementRecord;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WasteTrackingRecordController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = WasteTrackingRecord::forCompany($companyId)
            ->with(['wasteManagementRecord', 'contractor', 'trackedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('waste_type', 'like', "%{$search}%")
                  ->orWhere('manifest_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->latest('tracking_date')->paginate(15);
        return view('environmental.waste-tracking.index', compact('records'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $wasteRecords = WasteManagementRecord::forCompany($companyId)->latest()->get();
        $suppliers = Supplier::forCompany($companyId)->active()->get();
        return view('environmental.waste-tracking.create', compact('wasteRecords', 'suppliers'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'waste_management_record_id' => 'nullable|exists:waste_management_records,id',
            'waste_type' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'volume' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'tracking_date' => 'required|date',
            'source_location' => 'nullable|string|max:255',
            'destination_location' => 'nullable|string|max:255',
            'contractor_id' => 'nullable|exists:suppliers,id',
            'transport_method' => 'nullable|string|max:255',
            'vehicle_registration' => 'nullable|string|max:255',
            'manifest_number' => 'nullable|string|max:255',
            'status' => 'required|in:in_transit,delivered,disposed,recycled,returned',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['tracked_by'] = Auth::id();

        WasteTrackingRecord::create($validated);

        return redirect()->route('environmental.waste-tracking.index')
            ->with('success', 'Waste tracking record created successfully.');
    }

    public function show(WasteTrackingRecord $wasteTrackingRecord)
    {
        $wasteTrackingRecord->load(['wasteManagementRecord', 'contractor', 'trackedBy']);
        return view('environmental.waste-tracking.show', compact('wasteTrackingRecord'));
    }

    public function edit(WasteTrackingRecord $wasteTrackingRecord)
    {
        $companyId = Auth::user()->company_id;
        $wasteRecords = WasteManagementRecord::forCompany($companyId)->latest()->get();
        $suppliers = Supplier::forCompany($companyId)->active()->get();
        return view('environmental.waste-tracking.edit', compact('wasteTrackingRecord', 'wasteRecords', 'suppliers'));
    }

    public function update(Request $request, WasteTrackingRecord $wasteTrackingRecord)
    {
        $validated = $request->validate([
            'waste_management_record_id' => 'nullable|exists:waste_management_records,id',
            'waste_type' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'volume' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'tracking_date' => 'required|date',
            'source_location' => 'nullable|string|max:255',
            'destination_location' => 'nullable|string|max:255',
            'contractor_id' => 'nullable|exists:suppliers,id',
            'transport_method' => 'nullable|string|max:255',
            'vehicle_registration' => 'nullable|string|max:255',
            'manifest_number' => 'nullable|string|max:255',
            'status' => 'required|in:in_transit,delivered,disposed,recycled,returned',
            'notes' => 'nullable|string',
        ]);

        $wasteTrackingRecord->update($validated);

        return redirect()->route('environmental.waste-tracking.show', $wasteTrackingRecord)
            ->with('success', 'Waste tracking record updated successfully.');
    }

    public function destroy(WasteTrackingRecord $wasteTrackingRecord)
    {
        $wasteTrackingRecord->delete();
        return redirect()->route('environmental.waste-tracking.index')
            ->with('success', 'Waste tracking record deleted successfully.');
    }
}
