<?php

namespace App\Http\Controllers;

use App\Models\ProcurementRequest;
use App\Models\Department;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProcurementRequestNotification;

class ProcurementRequestController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = ProcurementRequest::forCompany($companyId)
            ->with(['requestedBy', 'department', 'supplier', 'reviewedBy', 'approvedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $requests = $query->latest()->paginate(15);
        return view('procurement.requests.index', compact('requests'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        
        // Get suggested suppliers based on category (if provided in request)
        $suggestedSuppliers = collect();
        if (request()->has('item_category') && request('item_category')) {
            $suggestedSuppliers = Supplier::forCompany($companyId)
                ->active()
                ->where(function($q) {
                    $q->where('supplier_type', request('item_category'))
                      ->orWhere('supplier_type', 'other');
                })
                ->orderBy('name')
                ->get();
        }
        
        return view('procurement.requests.create', compact('departments', 'suggestedSuppliers'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'item_category' => 'required|in:safety_equipment,ppe,tools,materials,services,other',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'estimated_cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'justification' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'required_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['requested_by'] = Auth::id();
        $validated['status'] = 'draft';

        $procurementRequest = ProcurementRequest::create($validated);

        // Send notification to procurement emails if configured
        // Only send on creation if status is not 'draft' or if explicitly enabled
        if (config('procurement.auto_send_notifications')) {
            if (config('procurement.notify_on.created') && $validated['status'] !== 'draft') {
                $this->sendProcurementNotification($procurementRequest, 'created');
            }
        }

        return redirect()->route('procurement.requests.index')
            ->with('success', 'Procurement request created successfully.');
    }

    public function show(ProcurementRequest $procurementRequest)
    {
        $procurementRequest->load(['requestedBy', 'department', 'supplier', 'reviewedBy', 'approvedBy']);
        return view('procurement.requests.show', compact('procurementRequest'));
    }

    public function edit(ProcurementRequest $procurementRequest)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $allSuppliers = Supplier::forCompany($companyId)->active()->get();
        
        // Get suggested suppliers based on item category
        $suggestedSuppliers = Supplier::forCompany($companyId)
            ->active()
            ->where(function($q) use ($procurementRequest) {
                $q->where('supplier_type', $procurementRequest->item_category)
                  ->orWhere('supplier_type', 'other');
            })
            ->orderBy('name')
            ->get();
        
        $users = User::forCompany($companyId)->active()->get();
        return view('procurement.requests.edit', compact('procurementRequest', 'departments', 'allSuppliers', 'suggestedSuppliers', 'users'));
    }

    public function update(Request $request, ProcurementRequest $procurementRequest)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'item_category' => 'required|in:safety_equipment,ppe,tools,materials,services,other',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'estimated_cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'justification' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'required_date' => 'nullable|date',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,purchased,received,cancelled',
            'reviewed_by' => 'nullable|exists:users,id',
            'review_date' => 'nullable|date',
            'review_notes' => 'nullable|string',
            'approved_by' => 'nullable|exists:users,id',
            'approval_date' => 'nullable|date',
            'approval_notes' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_cost' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'received_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $procurementRequest->status;
        $procurementRequest->update($validated);

        // Send notification if status changed to 'submitted' or if update notifications are enabled
        if (config('procurement.auto_send_notifications')) {
            if ($oldStatus !== 'submitted' && $validated['status'] === 'submitted' && config('procurement.notify_on.submitted')) {
                $this->sendProcurementNotification($procurementRequest, 'submitted');
            } elseif (config('procurement.notify_on.updated') && $oldStatus !== $validated['status']) {
                $this->sendProcurementNotification($procurementRequest, 'updated');
            }
        }

        return redirect()->route('procurement.requests.show', $procurementRequest)
            ->with('success', 'Procurement request updated successfully.');
    }

    public function destroy(ProcurementRequest $procurementRequest)
    {
        $procurementRequest->delete();
        return redirect()->route('procurement.requests.index')
            ->with('success', 'Procurement request deleted successfully.');
    }

    /**
     * Send procurement request notification to configured email addresses
     */
    private function sendProcurementNotification(ProcurementRequest $procurementRequest, string $action = 'created'): void
    {
        $emails = config('procurement.notification_emails');
        
        if (empty($emails)) {
            return;
        }

        // Parse emails (support comma-separated or array)
        $emailList = is_array($emails) ? $emails : array_map('trim', explode(',', $emails));
        $emailList = array_filter($emailList, fn($email) => !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL));

        if (empty($emailList)) {
            return;
        }

        // Send notification to each email address
        foreach ($emailList as $email) {
            Notification::route('mail', $email)
                ->notify(new ProcurementRequestNotification($procurementRequest, $action));
        }
    }
}
