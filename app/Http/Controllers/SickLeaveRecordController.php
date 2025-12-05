<?php

namespace App\Http\Controllers;

use App\Models\SickLeaveRecord;
use App\Models\User;
use App\Models\Incident;
use App\Models\FirstAidLogbookEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SickLeaveRecordController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = SickLeaveRecord::forCompany($companyId)
            ->with(['user', 'relatedIncident', 'relatedFirstAid', 'recordedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->filled('work_related')) {
            $query->where('work_related', $request->work_related == '1');
        }

        $records = $query->latest('leave_start_date')->paginate(15);
        return view('health.sick-leave.index', compact('records'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        $incidents = Incident::forCompany($companyId)->latest()->limit(50)->get();
        $firstAidEntries = FirstAidLogbookEntry::forCompany($companyId)->latest()->limit(50)->get();
        return view('health.sick-leave.create', compact('users', 'incidents', 'firstAidEntries'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_start_date' => 'required|date',
            'leave_end_date' => 'nullable|date|after_or_equal:leave_start_date',
            'leave_type' => 'required|in:sick_leave,medical_leave,injury_leave,work_related_injury',
            'reason' => 'nullable|string',
            'medical_certificate_provided' => 'nullable|string',
            'work_related' => 'boolean',
            'related_incident_id' => 'nullable|exists:incidents,id',
            'related_first_aid_id' => 'nullable|exists:first_aid_logbook_entries,id',
            'treatment_received' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'return_to_work_date' => 'nullable|date',
            'return_to_work_status' => 'nullable|in:full_duty,light_duty,restricted,pending_clearance',
            'medical_clearance_notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['recorded_by'] = Auth::id();
        
        // Calculate days absent
        if ($validated['leave_start_date'] && $validated['leave_end_date']) {
            $start = \Carbon\Carbon::parse($validated['leave_start_date']);
            $end = \Carbon\Carbon::parse($validated['leave_end_date']);
            $validated['days_absent'] = $start->diffInDays($end) + 1;
        }

        SickLeaveRecord::create($validated);

        return redirect()->route('health.sick-leave.index')
            ->with('success', 'Sick leave record created successfully.');
    }

    public function show(SickLeaveRecord $sickLeaveRecord)
    {
        $sickLeaveRecord->load(['user', 'relatedIncident', 'relatedFirstAid', 'recordedBy']);
        return view('health.sick-leave.show', compact('sickLeaveRecord'));
    }

    public function edit(SickLeaveRecord $sickLeaveRecord)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        $incidents = Incident::forCompany($companyId)->latest()->limit(50)->get();
        $firstAidEntries = FirstAidLogbookEntry::forCompany($companyId)->latest()->limit(50)->get();
        return view('health.sick-leave.edit', compact('sickLeaveRecord', 'users', 'incidents', 'firstAidEntries'));
    }

    public function update(Request $request, SickLeaveRecord $sickLeaveRecord)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_start_date' => 'required|date',
            'leave_end_date' => 'nullable|date|after_or_equal:leave_start_date',
            'leave_type' => 'required|in:sick_leave,medical_leave,injury_leave,work_related_injury',
            'reason' => 'nullable|string',
            'medical_certificate_provided' => 'nullable|string',
            'work_related' => 'boolean',
            'related_incident_id' => 'nullable|exists:incidents,id',
            'related_first_aid_id' => 'nullable|exists:first_aid_logbook_entries,id',
            'treatment_received' => 'nullable|string',
            'follow_up_required' => 'nullable|string',
            'return_to_work_date' => 'nullable|date',
            'return_to_work_status' => 'nullable|in:full_duty,light_duty,restricted,pending_clearance',
            'medical_clearance_notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Recalculate days absent
        if ($validated['leave_start_date'] && $validated['leave_end_date']) {
            $start = \Carbon\Carbon::parse($validated['leave_start_date']);
            $end = \Carbon\Carbon::parse($validated['leave_end_date']);
            $validated['days_absent'] = $start->diffInDays($end) + 1;
        }

        $sickLeaveRecord->update($validated);

        return redirect()->route('health.sick-leave.show', $sickLeaveRecord)
            ->with('success', 'Sick leave record updated successfully.');
    }

    public function destroy(SickLeaveRecord $sickLeaveRecord)
    {
        $sickLeaveRecord->delete();
        return redirect()->route('health.sick-leave.index')
            ->with('success', 'Sick leave record deleted successfully.');
    }
}
