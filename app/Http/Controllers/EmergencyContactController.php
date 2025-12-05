<?php

namespace App\Http\Controllers;

use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyContactController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = EmergencyContact::forCompany($companyId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('phone_primary', 'like', "%{$search}%");
            });
        }

        if ($request->filled('contact_type')) {
            $query->where('contact_type', $request->contact_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $contacts = $query->latest()->paginate(15);
        return view('emergency.contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('emergency.contacts.create');
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'contact_type' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:255',
            'phone_secondary' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'availability' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:1|max:10',
        ]);

        $validated['company_id'] = $companyId;
        EmergencyContact::create($validated);

        return redirect()->route('emergency.contacts.index')
            ->with('success', 'Emergency contact created successfully.');
    }

    public function show(EmergencyContact $contact)
    {
        return view('emergency.contacts.show', compact('contact'));
    }

    public function edit(EmergencyContact $contact)
    {
        return view('emergency.contacts.edit', compact('contact'));
    }

    public function update(Request $request, EmergencyContact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'contact_type' => 'required|string|max:255',
            'phone_primary' => 'required|string|max:255',
            'phone_secondary' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'availability' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'priority' => 'nullable|integer|min:1|max:10',
        ]);

        $contact->update($validated);

        return redirect()->route('emergency.contacts.show', $contact)
            ->with('success', 'Emergency contact updated successfully.');
    }

    public function destroy(EmergencyContact $contact)
    {
        $contact->delete();
        return redirect()->route('emergency.contacts.index')
            ->with('success', 'Emergency contact deleted successfully.');
    }
}
