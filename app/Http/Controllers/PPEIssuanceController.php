<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PPEIssuance;
use App\Models\PPEItem;
use App\Models\User;
use App\Models\Department;

class PPEIssuanceController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = PPEIssuance::forCompany($companyId)
            ->with(['ppeItem', 'issuedTo', 'issuedBy', 'department']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('ppeItem', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('issuedTo', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        if ($request->filled('user_id')) {
            $query->where('issued_to', $request->user_id);
        }
        
        if ($request->filled('ppe_item_id')) {
            $query->where('ppe_item_id', $request->ppe_item_id);
        }
        
        if ($request->filled('expired')) {
            $query->expired();
        }
        
        if ($request->filled('expiring_soon')) {
            $query->expiringSoon();
        }
        
        if ($request->filled('needs_inspection')) {
            $query->needsInspection();
        }
        
        $issuances = $query->latest()->paginate(20);
        
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        $items = PPEItem::forCompany($companyId)->active()->get();
        
        $stats = [
            'total' => PPEIssuance::forCompany($companyId)->count(),
            'active' => PPEIssuance::forCompany($companyId)->active()->count(),
            'expired' => PPEIssuance::forCompany($companyId)->expired()->count(),
            'expiring_soon' => PPEIssuance::forCompany($companyId)->expiringSoon()->count(),
            'needs_inspection' => PPEIssuance::forCompany($companyId)->needsInspection()->count(),
        ];
        
        return view('ppe.issuances.index', compact('issuances', 'users', 'items', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $items = PPEItem::forCompany($companyId)->active()->get();
        $users = User::where('company_id', $companyId)->where('is_active', true)->get();
        $departments = Department::where('company_id', $companyId)->active()->get();
        
        $selectedItem = null;
        if ($request->has('ppe_item_id')) {
            $selectedItem = PPEItem::forCompany($companyId)->findOrFail($request->ppe_item_id);
        }
        
        return view('ppe.issuances.create', compact('items', 'users', 'departments', 'selectedItem'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        // Check if this is a bulk issue request
        if ($request->has('users') && is_array($request->users) && count($request->users) > 0) {
            return $this->bulkIssue($request);
        }
        
        $validated = $request->validate([
            'ppe_item_id' => 'required|exists:ppe_items,id',
            'issued_to' => 'required_without:users|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'transaction_type' => 'required|in:issuance,return,replacement,exchange',
            'quantity' => 'required|integer|min:1',
            'issue_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after:issue_date',
            'initial_condition' => 'required|in:new,good,fair,poor',
            'condition_notes' => 'nullable|string',
            'reason' => 'nullable|string',
            'serial_numbers' => 'nullable|array',
            'batch_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Check availability
        $item = PPEItem::forCompany($companyId)->findOrFail($validated['ppe_item_id']);
        if ($validated['transaction_type'] === 'issuance' && $item->available_quantity < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Insufficient stock available. Available: ' . $item->available_quantity]);
        }
        
        $validated['company_id'] = $companyId;
        $validated['issued_by'] = Auth::id();
        
        $issuance = PPEIssuance::create($validated);
        
        return redirect()->route('ppe.issuances.show', $issuance)
            ->with('success', 'PPE issuance created successfully.');
    }

    public function show(PPEIssuance $issuance)
    {
        $issuance->load(['ppeItem.supplier', 'issuedTo', 'issuedBy', 'department', 'inspections.inspectedBy']);
        
        return view('ppe.issuances.show', compact('issuance'));
    }

    public function returnItem(Request $request, PPEIssuance $issuance)
    {
        $validated = $request->validate([
            'actual_return_date' => 'required|date',
            'return_condition' => 'required|in:good,fair,poor,damaged,lost',
            'return_notes' => 'nullable|string',
        ]);
        
        $issuance->update([
            'status' => 'returned',
            'actual_return_date' => $validated['actual_return_date'],
            'return_condition' => $validated['return_condition'],
            'return_notes' => $validated['return_notes'],
        ]);
        
        // Update item quantities
        if ($issuance->transaction_type === 'issuance') {
            $item = $issuance->ppeItem;
            $item->issued_quantity -= $issuance->quantity;
            if ($validated['return_condition'] !== 'damaged' && $validated['return_condition'] !== 'lost') {
                $item->available_quantity += $issuance->quantity;
            }
            $item->save();
        }
        
        return redirect()->route('ppe.issuances.show', $issuance)
            ->with('success', 'PPE returned successfully.');
    }

    public function bulkIssue(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'ppe_item_id' => 'required|exists:ppe_items,id',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
            'quantity' => 'required|integer|min:1',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'replacement_due_date' => 'nullable|date|after:issue_date',
            'department_id' => 'nullable|exists:departments,id',
            'initial_condition' => 'nullable|in:new,good,fair',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $item = PPEItem::forCompany($companyId)->findOrFail($validated['ppe_item_id']);
        $totalQuantity = count($validated['users']) * $validated['quantity'];
        
        if ($item->available_quantity < $totalQuantity) {
            return back()->withErrors(['quantity' => "Insufficient stock. Available: {$item->available_quantity}, Required: {$totalQuantity}"]);
        }

        $issuances = [];
        foreach ($validated['users'] as $userId) {
            $user = User::find($userId);
            $issuance = PPEIssuance::create([
                'company_id' => $companyId,
                'ppe_item_id' => $validated['ppe_item_id'],
                'issued_to' => $userId,
                'issued_by' => Auth::id(),
                'department_id' => $validated['department_id'] ?? $user->department_id,
                'transaction_type' => 'issuance',
                'quantity' => $validated['quantity'],
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'] ?? null,
                'replacement_due_date' => $validated['replacement_due_date'] ?? null,
                'initial_condition' => $validated['initial_condition'] ?? 'new',
                'status' => 'active',
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);
            $issuances[] = $issuance;
        }

        return redirect()->route('ppe.issuances.index')
            ->with('success', count($issuances) . ' PPE items issued successfully to ' . count($validated['users']) . ' user(s).');
    }
}

