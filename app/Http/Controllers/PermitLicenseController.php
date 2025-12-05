<?php

namespace App\Http\Controllers;

use App\Models\PermitLicense;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PermitLicenseController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = PermitLicense::forCompany($companyId)
            ->with(['responsiblePerson', 'department']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('permit_license_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $permits = $query->latest()->paginate(15);
        return view('compliance.permits-licenses.index', compact('permits'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('compliance.permits-licenses.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'permit_license_number' => 'required|string|max:255|unique:permits_licenses,permit_license_number',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:environmental_permit,operating_license,fire_safety_certificate,building_permit,health_permit,other',
            'category' => 'nullable|string|max:255',
            'issuing_authority' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'renewal_due_date' => 'nullable|date',
            'status' => 'required|in:active,expired,pending_renewal,revoked',
            'responsible_person_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'conditions' => 'nullable|string',
            'renewal_requirements' => 'nullable|string',
            'last_renewal_date' => 'nullable|date',
            'renewal_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('permits-licenses', 'public');
            $validated['attachment_path'] = $path;
        }

        PermitLicense::create($validated);

        return redirect()->route('compliance.permits-licenses.index')
            ->with('success', 'Permit/License created successfully.');
    }

    public function show(PermitLicense $permitsLicense)
    {
        $permitsLicense->load(['responsiblePerson', 'department']);
        return view('compliance.permits-licenses.show', compact('permitsLicense'));
    }

    public function edit(PermitLicense $permitsLicense)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('compliance.permits-licenses.edit', compact('permitsLicense', 'departments', 'users'));
    }

    public function update(Request $request, PermitLicense $permitsLicense)
    {
        $validated = $request->validate([
            'permit_license_number' => 'required|string|max:255|unique:permits_licenses,permit_license_number,' . $permitsLicense->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:environmental_permit,operating_license,fire_safety_certificate,building_permit,health_permit,other',
            'category' => 'nullable|string|max:255',
            'issuing_authority' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'renewal_due_date' => 'nullable|date',
            'status' => 'required|in:active,expired,pending_renewal,revoked',
            'responsible_person_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'conditions' => 'nullable|string',
            'renewal_requirements' => 'nullable|string',
            'last_renewal_date' => 'nullable|date',
            'renewal_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($permitsLicense->attachment_path && Storage::disk('public')->exists($permitsLicense->attachment_path)) {
                Storage::disk('public')->delete($permitsLicense->attachment_path);
            }

            $file = $request->file('file');
            $path = $file->store('permits-licenses', 'public');
            $validated['attachment_path'] = $path;
        }

        $permitsLicense->update($validated);

        return redirect()->route('compliance.permits-licenses.show', $permitsLicense)
            ->with('success', 'Permit/License updated successfully.');
    }

    public function destroy(PermitLicense $permitsLicense)
    {
        // Delete file if exists
        if ($permitsLicense->attachment_path && Storage::disk('public')->exists($permitsLicense->attachment_path)) {
            Storage::disk('public')->delete($permitsLicense->attachment_path);
        }

        $permitsLicense->delete();

        return redirect()->route('compliance.permits-licenses.index')
            ->with('success', 'Permit/License deleted successfully.');
    }
}
