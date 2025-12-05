<?php

namespace App\Http\Controllers;

use App\Models\EmergencyEquipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyEquipmentController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = EmergencyEquipment::forCompany($companyId)->with(['inspectedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('equipment_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('equipment_type')) {
            $query->where('equipment_type', $request->equipment_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $equipment = $query->latest()->paginate(15);
        return view('emergency.equipment.index', compact('equipment'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('emergency.equipment.create', compact('users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'equipment_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'last_inspection_date' => 'nullable|date',
            'next_inspection_date' => 'nullable|date',
            'inspection_frequency' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance,retired',
            'condition' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        EmergencyEquipment::create($validated);

        return redirect()->route('emergency.equipment.index')
            ->with('success', 'Emergency equipment created successfully.');
    }

    public function show(EmergencyEquipment $equipment)
    {
        $equipment->load(['inspectedBy']);
        return view('emergency.equipment.show', compact('equipment'));
    }

    public function edit(EmergencyEquipment $equipment)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('emergency.equipment.edit', compact('equipment', 'users'));
    }

    public function update(Request $request, EmergencyEquipment $equipment)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'equipment_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'last_inspection_date' => 'nullable|date',
            'next_inspection_date' => 'nullable|date',
            'inspection_frequency' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,maintenance,retired',
            'condition' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'inspected_by' => 'nullable|exists:users,id',
            'inspection_notes' => 'nullable|string',
        ]);

        $equipment->update($validated);

        return redirect()->route('emergency.equipment.show', $equipment)
            ->with('success', 'Emergency equipment updated successfully.');
    }

    public function destroy(EmergencyEquipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('emergency.equipment.index')
            ->with('success', 'Emergency equipment deleted successfully.');
    }
}
