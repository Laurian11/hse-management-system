<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PPESupplier;

class PPESupplierController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = PPESupplier::forCompany($companyId);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('is_preferred')) {
            $query->where('is_preferred', $request->is_preferred);
        }
        
        $suppliers = $query->withCount('ppeItems')->latest()->paginate(20);
        
        $stats = [
            'total' => PPESupplier::forCompany($companyId)->count(),
            'active' => PPESupplier::forCompany($companyId)->active()->count(),
            'preferred' => PPESupplier::forCompany($companyId)->preferred()->count(),
        ];
        
        return view('ppe.suppliers.index', compact('suppliers', 'stats'));
    }

    public function create()
    {
        return view('ppe.suppliers.create');
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blacklisted',
            'certifications' => 'nullable|array',
            'rating' => 'nullable|array',
            'is_preferred' => 'boolean',
        ]);
        
        $validated['company_id'] = $companyId;
        
        $supplier = PPESupplier::create($validated);
        
        return redirect()->route('ppe.suppliers.show', $supplier)
            ->with('success', 'PPE supplier created successfully.');
    }

    public function show(PPESupplier $supplier)
    {
        $supplier->load(['ppeItems']);
        
        return view('ppe.suppliers.show', compact('supplier'));
    }

    public function edit(PPESupplier $supplier)
    {
        return view('ppe.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, PPESupplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:active,inactive,blacklisted',
            'certifications' => 'nullable|array',
            'rating' => 'nullable|array',
            'is_preferred' => 'boolean',
        ]);
        
        $supplier->update($validated);
        
        return redirect()->route('ppe.suppliers.show', $supplier)
            ->with('success', 'PPE supplier updated successfully.');
    }

    public function destroy(PPESupplier $supplier)
    {
        $supplier->delete();
        
        return redirect()->route('ppe.suppliers.index')
            ->with('success', 'PPE supplier deleted successfully.');
    }
}

