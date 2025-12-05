<?php

namespace App\Http\Controllers;

use App\Models\ISO14001ComplianceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ISO14001ComplianceRecordController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = ISO14001ComplianceRecord::forCompany($companyId)
            ->with(['assessedBy', 'correctiveActionAssignedTo', 'verifiedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('clause_reference', 'like', "%{$search}%")
                  ->orWhere('requirement', 'like', "%{$search}%");
            });
        }

        if ($request->filled('compliance_status')) {
            $query->where('compliance_status', $request->compliance_status);
        }

        $records = $query->latest('assessment_date')->paginate(15);
        return view('environmental.iso14001.index', compact('records'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('environmental.iso14001.create', compact('users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'clause_reference' => 'nullable|string|max:255',
            'requirement' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'compliance_status' => 'required|in:compliant,non_compliant,partially_compliant,not_applicable',
            'evidence' => 'nullable|string',
            'assessment_date' => 'required|date',
            'findings' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'corrective_action_due_date' => 'nullable|date',
            'corrective_action_assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['assessed_by'] = Auth::id();

        ISO14001ComplianceRecord::create($validated);

        return redirect()->route('environmental.iso14001.index')
            ->with('success', 'ISO 14001 compliance record created successfully.');
    }

    public function show(ISO14001ComplianceRecord $iSO14001ComplianceRecord)
    {
        $iSO14001ComplianceRecord->load(['assessedBy', 'correctiveActionAssignedTo', 'verifiedBy']);
        return view('environmental.iso14001.show', compact('iSO14001ComplianceRecord'));
    }

    public function edit(ISO14001ComplianceRecord $iSO14001ComplianceRecord)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        return view('environmental.iso14001.edit', compact('iSO14001ComplianceRecord', 'users'));
    }

    public function update(Request $request, ISO14001ComplianceRecord $iSO14001ComplianceRecord)
    {
        $validated = $request->validate([
            'clause_reference' => 'nullable|string|max:255',
            'requirement' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'compliance_status' => 'required|in:compliant,non_compliant,partially_compliant,not_applicable',
            'evidence' => 'nullable|string',
            'assessment_date' => 'required|date',
            'findings' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'corrective_action_due_date' => 'nullable|date',
            'corrective_action_assigned_to' => 'nullable|exists:users,id',
            'corrective_action_completed' => 'boolean',
            'corrective_action_completed_date' => 'nullable|date',
            'verified_by' => 'nullable|exists:users,id',
            'verification_date' => 'nullable|date',
            'verification_notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $iSO14001ComplianceRecord->update($validated);

        return redirect()->route('environmental.iso14001.show', $iSO14001ComplianceRecord)
            ->with('success', 'ISO 14001 compliance record updated successfully.');
    }

    public function destroy(ISO14001ComplianceRecord $iSO14001ComplianceRecord)
    {
        $iSO14001ComplianceRecord->delete();
        return redirect()->route('environmental.iso14001.index')
            ->with('success', 'ISO 14001 compliance record deleted successfully.');
    }
}
