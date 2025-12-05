<?php

namespace App\Http\Controllers;

use App\Models\FirstAidLogbookEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FirstAidLogbookEntryController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = FirstAidLogbookEntry::forCompany($companyId)
            ->with(['injuredPerson', 'firstAider']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('injured_person_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $entries = $query->latest('incident_date')->paginate(15);
        return view('health.first-aid.index', compact('entries'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('health.first-aid.create', compact('users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'incident_date' => 'required|date',
            'incident_time' => 'nullable',
            'injured_person_id' => 'nullable|exists:users,id',
            'injured_person_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'nature_of_injury' => 'nullable|string',
            'first_aid_provided' => 'nullable|string',
            'severity' => 'required|in:minor,moderate,serious',
            'referred_to_medical' => 'boolean',
            'medical_facility' => 'nullable|string|max:255',
            'first_aider_id' => 'nullable|exists:users,id',
            'treatment_details' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;

        FirstAidLogbookEntry::create($validated);

        return redirect()->route('health.first-aid.index')
            ->with('success', 'First aid logbook entry created successfully.');
    }

    public function show(FirstAidLogbookEntry $firstAidLogbookEntry)
    {
        $firstAidLogbookEntry->load(['injuredPerson', 'firstAider']);
        return view('health.first-aid.show', compact('firstAidLogbookEntry'));
    }

    public function edit(FirstAidLogbookEntry $firstAidLogbookEntry)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('health.first-aid.edit', compact('firstAidLogbookEntry', 'users'));
    }

    public function update(Request $request, FirstAidLogbookEntry $firstAidLogbookEntry)
    {
        $validated = $request->validate([
            'incident_date' => 'required|date',
            'incident_time' => 'nullable',
            'injured_person_id' => 'nullable|exists:users,id',
            'injured_person_name' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'nature_of_injury' => 'nullable|string',
            'first_aid_provided' => 'nullable|string',
            'severity' => 'required|in:minor,moderate,serious',
            'referred_to_medical' => 'boolean',
            'medical_facility' => 'nullable|string|max:255',
            'first_aider_id' => 'nullable|exists:users,id',
            'treatment_details' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $firstAidLogbookEntry->update($validated);

        return redirect()->route('health.first-aid.show', $firstAidLogbookEntry)
            ->with('success', 'First aid logbook entry updated successfully.');
    }

    public function destroy(FirstAidLogbookEntry $firstAidLogbookEntry)
    {
        $firstAidLogbookEntry->delete();
        return redirect()->route('health.first-aid.index')
            ->with('success', 'First aid logbook entry deleted successfully.');
    }
}
