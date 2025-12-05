<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = Supplier::forCompany($companyId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('supplier_type')) {
            $query->where('supplier_type', $request->supplier_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $suppliers = $query->latest()->paginate(15);
        return view('procurement.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('procurement.suppliers.create');
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'supplier_type' => 'required|in:equipment,services,materials,waste_disposal,medical,other',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'certifications' => 'nullable|array',
            'status' => 'required|in:active,inactive,suspended,blacklisted',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;

        Supplier::create($validated);

        return redirect()->route('procurement.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('procurementRequests');
        return view('procurement.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('procurement.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'supplier_type' => 'required|in:equipment,services,materials,waste_disposal,medical,other',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'certifications' => 'nullable|array',
            'status' => 'required|in:active,inactive,suspended,blacklisted',
            'notes' => 'nullable|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('procurement.suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('procurement.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
