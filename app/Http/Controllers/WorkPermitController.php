<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkPermit;
use App\Models\WorkPermitType;
use App\Models\WorkPermitApproval;
use App\Models\RiskAssessment;
use App\Models\JSA;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class WorkPermitController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = WorkPermit::forCompany($companyId)
            ->with(['workPermitType', 'requestedBy', 'department', 'approvedBy']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('work_title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('work_location', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('work_permit_type_id')) {
            $query->where('work_permit_type_id', $request->work_permit_type_id);
        }
        
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->filled('expired')) {
            $query->expired();
        }
        
        if ($request->filled('active')) {
            $query->active();
        }
        
        // Date range filters
        if ($request->filled('date_from')) {
            $query->where('work_start_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('work_start_date', '<=', $request->date_to);
        }
        
        // Sorting
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort column
        $allowedSortColumns = ['reference_number', 'work_title', 'work_start_date', 'status', 'work_location', 'created_at'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'created_at';
        }
        
        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $permits = $query->orderBy($sortColumn, $sortDirection)->paginate(20);
        
        // Append query parameters to pagination links
        $permits->appends($request->query());
        
        $permitTypes = WorkPermitType::forCompany($companyId)->active()->get();
        $departments = Department::where('company_id', $companyId)->active()->get();
        
        $stats = [
            'total' => WorkPermit::forCompany($companyId)->count(),
            'pending' => WorkPermit::forCompany($companyId)->pending()->count(),
            'active' => WorkPermit::forCompany($companyId)->active()->count(),
            'expired' => WorkPermit::forCompany($companyId)->expired()->count(),
        ];
        
        return view('work-permits.index', compact('permits', 'permitTypes', 'departments', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $permitTypes = WorkPermitType::forCompany($companyId)->active()->get();
        $departments = Department::where('company_id', $companyId)->active()->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        $riskAssessments = RiskAssessment::forCompany($companyId)->where('status', 'approved')->get();
        $jsas = JSA::forCompany($companyId)->where('status', 'approved')->get();
        
        // Copy from existing permit
        $copyFrom = null;
        if ($request->has('copy_from')) {
            $copyFrom = WorkPermit::where('company_id', $companyId)
                ->findOrFail($request->get('copy_from'));
        }
        
        return view('work-permits.create', compact('permitTypes', 'departments', 'users', 'riskAssessments', 'jsas', 'copyFrom'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'work_permit_type_id' => 'required|exists:work_permit_types,id',
            'work_title' => 'required|string|max:255',
            'work_description' => 'required|string',
            'work_location' => 'required|string|max:255',
            'work_start_date' => 'required|date',
            'work_end_date' => 'required|date|after:work_start_date',
            'validity_hours' => 'required|integer|min:1|max:168',
            'department_id' => 'nullable|exists:departments,id',
            'risk_assessment_id' => 'nullable|exists:risk_assessments,id',
            'jsa_id' => 'nullable|exists:jsas,id',
            'safety_precautions' => 'nullable|array',
            'required_equipment' => 'nullable|array',
            'gas_test_required' => 'boolean',
            'gas_test_results' => 'nullable|string',
            'gas_test_date' => 'nullable|date',
            'gas_tester_id' => 'nullable|exists:users,id',
            'fire_watch_required' => 'boolean',
            'fire_watch_person_id' => 'nullable|exists:users,id',
            'workers' => 'nullable|array',
            'supervisors' => 'nullable|array',
            'emergency_procedures' => 'nullable|string',
            'gcla_compliance_required' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        $validated['company_id'] = $companyId;
        $validated['requested_by'] = Auth::id();
        $validated['status'] = 'draft';
        
        // Calculate expiry date
        $validated['expiry_date'] = Carbon::parse($validated['work_start_date'])
            ->addHours($validated['validity_hours']);
        
        $permit = WorkPermit::create($validated);
        
        // Create approval records if permit type requires approvals
        $permitType = WorkPermitType::find($validated['work_permit_type_id']);
        if ($permitType && $permitType->approval_levels > 0) {
            for ($i = 1; $i <= $permitType->approval_levels; $i++) {
                WorkPermitApproval::create([
                    'work_permit_id' => $permit->id,
                    'company_id' => $companyId,
                    'approval_level' => $i,
                    'approver_id' => Auth::id(), // Default to requester, can be changed
                    'status' => 'pending',
                ]);
            }
        }
        
        return redirect()->route('work-permits.show', $permit)
            ->with('success', 'Work permit created successfully.');
    }

    public function show(WorkPermit $workPermit)
    {
        $workPermit->load([
            'workPermitType',
            'requestedBy',
            'department',
            'riskAssessment',
            'jsa',
            'gasTester',
            'fireWatchPerson',
            'approvedBy',
            'closedBy',
            'verifiedBy',
            'approvals.approver',
            'gcaLogs'
        ]);
        
        return view('work-permits.show', compact('workPermit'));
    }

    public function edit(WorkPermit $workPermit)
    {
        if (!in_array($workPermit->status, ['draft', 'rejected'])) {
            return redirect()->route('work-permits.show', $workPermit)
                ->with('error', 'Only draft or rejected permits can be edited.');
        }
        
        $companyId = Auth::user()->company_id;
        $permitTypes = WorkPermitType::forCompany($companyId)->active()->get();
        $departments = Department::where('company_id', $companyId)->active()->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        $riskAssessments = RiskAssessment::forCompany($companyId)->where('status', 'approved')->get();
        $jsas = JSA::forCompany($companyId)->where('status', 'approved')->get();
        
        return view('work-permits.edit', compact('workPermit', 'permitTypes', 'departments', 'users', 'riskAssessments', 'jsas'));
    }

    public function update(Request $request, WorkPermit $workPermit)
    {
        if (!in_array($workPermit->status, ['draft', 'rejected'])) {
            return redirect()->route('work-permits.show', $workPermit)
                ->with('error', 'Only draft or rejected permits can be edited.');
        }
        
        $validated = $request->validate([
            'work_permit_type_id' => 'required|exists:work_permit_types,id',
            'work_title' => 'required|string|max:255',
            'work_description' => 'required|string',
            'work_location' => 'required|string|max:255',
            'work_start_date' => 'required|date',
            'work_end_date' => 'required|date|after:work_start_date',
            'validity_hours' => 'required|integer|min:1|max:168',
            'department_id' => 'nullable|exists:departments,id',
            'risk_assessment_id' => 'nullable|exists:risk_assessments,id',
            'jsa_id' => 'nullable|exists:jsas,id',
            'safety_precautions' => 'nullable|array',
            'required_equipment' => 'nullable|array',
            'gas_test_required' => 'boolean',
            'gas_test_results' => 'nullable|string',
            'gas_test_date' => 'nullable|date',
            'gas_tester_id' => 'nullable|exists:users,id',
            'fire_watch_required' => 'boolean',
            'fire_watch_person_id' => 'nullable|exists:users,id',
            'workers' => 'nullable|array',
            'supervisors' => 'nullable|array',
            'emergency_procedures' => 'nullable|string',
            'gcla_compliance_required' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        // Calculate expiry date
        $validated['expiry_date'] = Carbon::parse($validated['work_start_date'])
            ->addHours($validated['validity_hours']);
        
        $workPermit->update($validated);
        
        return redirect()->route('work-permits.show', $workPermit)
            ->with('success', 'Work permit updated successfully.');
    }

    public function destroy(WorkPermit $workPermit)
    {
        if (!in_array($workPermit->status, ['draft', 'cancelled'])) {
            return redirect()->route('work-permits.index')
                ->with('error', 'Only draft or cancelled permits can be deleted.');
        }
        
        $workPermit->delete();
        
        return redirect()->route('work-permits.index')
            ->with('success', 'Work permit deleted successfully.');
    }

    public function submit(WorkPermit $workPermit)
    {
        if ($workPermit->status !== 'draft') {
            return redirect()->route('work-permits.show', $workPermit)
                ->with('error', 'Only draft permits can be submitted.');
        }
        
        $workPermit->update(['status' => 'submitted']);
        
        return redirect()->route('work-permits.show', $workPermit)
            ->with('success', 'Work permit submitted for approval.');
    }

    public function approve(Request $request, WorkPermit $workPermit)
    {
        if (!$workPermit->canBeApproved()) {
            return redirect()->route('work-permits.show', $workPermit)
                ->with('error', 'This permit cannot be approved at this time.');
        }
        
        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);
        
        $workPermit->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null,
        ]);
        
        return redirect()->route('work-permits.show', $workPermit)
            ->with('success', 'Work permit approved successfully.');
    }

    public function reject(Request $request, WorkPermit $workPermit)
    {
        if (!$workPermit->canBeApproved()) {
            return redirect()->route('work-permits.show', $workPermit)
                ->with('error', 'This permit cannot be rejected at this time.');
        }
        
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);
        
        $workPermit->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);
        
        return redirect()->route('work-permits.show', $workPermit)
            ->with('success', 'Work permit rejected.');
    }

    public function activate(WorkPermit $workPermit)
    {
        if ($workPermit->status !== 'approved') {
            return redirect()->route('work-permits.show', $workPermit)
                ->with('error', 'Only approved permits can be activated.');
        }
        
        $workPermit->update([
            'status' => 'active',
            'actual_start_date' => now(),
        ]);
        
        return redirect()->route('work-permits.show', $workPermit)
            ->with('success', 'Work permit activated.');
    }

    public function close(Request $request, WorkPermit $workPermit)
    {
        if (!$workPermit->canBeClosed()) {
            return redirect()->route('work-permits.show', $workPermit)
                ->with('error', 'This permit cannot be closed at this time.');
        }
        
        $validated = $request->validate([
            'closure_notes' => 'required|string',
        ]);
        
        $workPermit->update([
            'status' => 'closed',
            'closed_by' => Auth::id(),
            'closed_at' => now(),
            'actual_end_date' => now(),
            'closure_notes' => $validated['closure_notes'],
        ]);
        
        return redirect()->route('work-permits.show', $workPermit)
            ->with('success', 'Work permit closed successfully.');
    }

    public function verify(Request $request, WorkPermit $workPermit)
    {
        if ($workPermit->status !== 'closed') {
            return redirect()->route('work-permits.show', $workPermit)
                ->with('error', 'Only closed permits can be verified.');
        }
        
        $validated = $request->validate([
            'verification_notes' => 'required|string',
        ]);
        
        $workPermit->update([
            'verification_completed' => true,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'verification_notes' => $validated['verification_notes'],
        ]);
        
        return redirect()->route('work-permits.show', $workPermit)
            ->with('success', 'Work permit verified successfully.');
    }
    
    /**
     * Bulk delete work permits
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_permits,id'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        
        $deleted = WorkPermit::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->whereIn('status', ['draft', 'cancelled'])
            ->delete();
        
        return redirect()->route('work-permits.index')
            ->with('success', "Successfully deleted {$deleted} work permit(s).");
    }
    
    /**
     * Bulk update work permits status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_permits,id',
            'status' => 'required|in:draft,submitted,approved,active,expired,closed,rejected,cancelled'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        $status = $request->input('status');
        
        $updated = WorkPermit::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->update(['status' => $status]);
        
        return redirect()->route('work-permits.index')
            ->with('success', "Successfully updated {$updated} work permit(s) status to {$status}.");
    }
    
    /**
     * Export selected work permits
     */
    public function bulkExport(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:work_permits,id'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        
        $permits = WorkPermit::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->with(['workPermitType', 'requestedBy', 'department', 'approvedBy'])
            ->get();
        
        $filename = 'work_permits_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($permits) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Reference', 'Work Title', 'Type', 'Location', 'Start Date', 'End Date',
                'Status', 'Department', 'Requested By', 'Approved By', 'Created At'
            ]);
            
            foreach ($permits as $permit) {
                fputcsv($file, [
                    $permit->reference_number,
                    $permit->work_title,
                    $permit->workPermitType->name ?? 'N/A',
                    $permit->work_location,
                    $permit->work_start_date ? $permit->work_start_date->format('Y-m-d H:i') : 'N/A',
                    $permit->work_end_date ? $permit->work_end_date->format('Y-m-d H:i') : 'N/A',
                    ucfirst(str_replace('_', ' ', $permit->status)),
                    $permit->department->name ?? 'N/A',
                    $permit->requestedBy->name ?? 'N/A',
                    $permit->approvedBy->name ?? 'N/A',
                    $permit->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Copy work permit
     */
    public function copy(WorkPermit $workPermit)
    {
        if ($workPermit->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        return redirect()->route('work-permits.create', ['copy_from' => $workPermit->id]);
    }
}
