<?php

namespace App\Http\Controllers;

use App\Models\EquipmentCertification;
use App\Models\Department;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EquipmentCertificationController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = EquipmentCertification::forCompany($companyId)
            ->with(['certifiedBy', 'department']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('equipment_name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('certificate_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certifications = $query->latest('certification_date')->paginate(15);
        return view('procurement.equipment-certifications.index', compact('certifications'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $suppliers = Supplier::forCompany($companyId)->active()->get();
        return view('procurement.equipment-certifications.create', compact('departments', 'suppliers'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'equipment_type' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'certification_type' => 'nullable|string|max:255',
            'certificate_number' => 'nullable|string|max:255',
            'certification_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:certification_date',
            'next_due_date' => 'nullable|date',
            'certified_by' => 'nullable|exists:suppliers,id',
            'certifier_name' => 'nullable|string|max:255',
            'certification_details' => 'nullable|string',
            'status' => 'required|in:valid,expired,pending,rejected',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;

        EquipmentCertification::create($validated);

        return redirect()->route('procurement.equipment-certifications.index')
            ->with('success', 'Equipment certification created successfully.');
    }

    public function show(EquipmentCertification $equipmentCertification)
    {
        $equipmentCertification->load(['certifiedBy', 'department']);
        return view('procurement.equipment-certifications.show', compact('equipmentCertification'));
    }

    public function edit(EquipmentCertification $equipmentCertification)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $suppliers = Supplier::forCompany($companyId)->active()->get();
        return view('procurement.equipment-certifications.edit', compact('equipmentCertification', 'departments', 'suppliers'));
    }

    public function update(Request $request, EquipmentCertification $equipmentCertification)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'equipment_type' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'certification_type' => 'nullable|string|max:255',
            'certificate_number' => 'nullable|string|max:255',
            'certification_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:certification_date',
            'next_due_date' => 'nullable|date',
            'certified_by' => 'nullable|exists:suppliers,id',
            'certifier_name' => 'nullable|string|max:255',
            'certification_details' => 'nullable|string',
            'status' => 'required|in:valid,expired,pending,rejected',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $equipmentCertification->update($validated);

        return redirect()->route('procurement.equipment-certifications.show', $equipmentCertification)
            ->with('success', 'Equipment certification updated successfully.');
    }

    public function destroy(EquipmentCertification $equipmentCertification)
    {
        $equipmentCertification->delete();
        return redirect()->route('procurement.equipment-certifications.index')
            ->with('success', 'Equipment certification deleted successfully.');
    }
}
