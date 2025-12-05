<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GCALog;
use App\Models\WorkPermit;
use App\Models\User;

class GCALogController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = GCALog::forCompany($companyId)->with(['workPermit', 'createdBy', 'actionAssignedTo']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('compliance_status')) {
            $query->where('compliance_status', $request->compliance_status);
        }
        
        if ($request->filled('work_permit_id')) {
            $query->where('work_permit_id', $request->work_permit_id);
        }
        
        if ($request->filled('non_compliant')) {
            $query->nonCompliant();
        }
        
        if ($request->filled('pending_actions')) {
            $query->pendingActions();
        }
        
        $logs = $query->latest()->paginate(20);
        
        $workPermits = WorkPermit::forCompany($companyId)->where('status', 'active')->get();
        
        $stats = [
            'total' => GCALog::forCompany($companyId)->count(),
            'compliant' => GCALog::forCompany($companyId)->where('compliance_status', 'compliant')->count(),
            'non_compliant' => GCALog::forCompany($companyId)->nonCompliant()->count(),
            'pending_actions' => GCALog::forCompany($companyId)->pendingActions()->count(),
        ];
        
        return view('work-permits.gca-logs.index', compact('logs', 'workPermits', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $workPermits = WorkPermit::forCompany($companyId)->where('status', 'active')->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        $workPermitId = $request->work_permit_id;
        
        return view('work-permits.gca-logs.create', compact('workPermits', 'users', 'workPermitId'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'work_permit_id' => 'nullable|exists:work_permits,id',
            'gcla_type' => 'required|in:pre_work,during_work,post_work,continuous',
            'check_date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'checklist_items' => 'nullable|array',
            'findings' => 'nullable|array',
            'compliance_status' => 'required|in:compliant,non_compliant,partial',
            'corrective_actions' => 'nullable|string',
            'action_assigned_to' => 'nullable|exists:users,id',
            'action_due_date' => 'nullable|date|after:today',
        ]);
        
        $validated['company_id'] = $companyId;
        $validated['created_by'] = Auth::id();
        
        $log = GCALog::create($validated);
        
        return redirect()->route('gca-logs.show', $log)
            ->with('success', 'GCLA log created successfully.');
    }

    public function show(GCALog $gcaLog)
    {
        $gcaLog->load(['workPermit', 'createdBy', 'actionAssignedTo', 'verifiedBy']);
        return view('work-permits.gca-logs.show', compact('gcaLog'));
    }

    public function edit(GCALog $gcaLog)
    {
        $companyId = Auth::user()->company_id;
        $workPermits = WorkPermit::forCompany($companyId)->where('status', 'active')->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        
        return view('work-permits.gca-logs.edit', compact('gcaLog', 'workPermits', 'users'));
    }

    public function update(Request $request, GCALog $gcaLog)
    {
        $validated = $request->validate([
            'work_permit_id' => 'nullable|exists:work_permits,id',
            'gcla_type' => 'required|in:pre_work,during_work,post_work,continuous',
            'check_date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'checklist_items' => 'nullable|array',
            'findings' => 'nullable|array',
            'compliance_status' => 'required|in:compliant,non_compliant,partial',
            'corrective_actions' => 'nullable|string',
            'action_assigned_to' => 'nullable|exists:users,id',
            'action_due_date' => 'nullable|date|after:today',
        ]);
        
        $gcaLog->update($validated);
        
        return redirect()->route('gca-logs.show', $gcaLog)
            ->with('success', 'GCLA log updated successfully.');
    }

    public function destroy(GCALog $gcaLog)
    {
        $gcaLog->delete();
        
        return redirect()->route('gca-logs.index')
            ->with('success', 'GCLA log deleted successfully.');
    }

    public function completeAction(GCALog $gcaLog)
    {
        $gcaLog->update([
            'action_completed' => true,
            'action_completed_at' => now(),
        ]);
        
        return redirect()->route('gca-logs.show', $gcaLog)
            ->with('success', 'Corrective action marked as completed.');
    }

    public function verify(Request $request, GCALog $gcaLog)
    {
        $validated = $request->validate([
            'verification_notes' => 'required|string',
        ]);
        
        $gcaLog->update([
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_notes' => $validated['verification_notes'],
        ]);
        
        return redirect()->route('gca-logs.show', $gcaLog)
            ->with('success', 'GCLA log verified successfully.');
    }
}
