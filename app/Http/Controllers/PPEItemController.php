<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PPEItem;
use App\Models\PPESupplier;
use App\Models\Department;
use App\Models\ActivityLog;

class PPEItemController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = PPEItem::forCompany($companyId)->with('supplier');
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('low_stock')) {
            $query->lowStock();
        }
        
        // Sorting
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortColumns = ['name', 'category', 'available_quantity', 'supplier_id', 'status', 'created_at'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'created_at';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $items = $query->orderBy($sortColumn, $sortDirection)->paginate(20);
        $items->appends($request->query());
        
        $categories = PPEItem::forCompany($companyId)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();
        
        $stats = [
            'total' => PPEItem::forCompany($companyId)->count(),
            'active' => PPEItem::forCompany($companyId)->active()->count(),
            'low_stock' => PPEItem::forCompany($companyId)->lowStock()->count(),
            'needs_reorder' => PPEItem::forCompany($companyId)->needsReorder()->count(),
        ];
        
        return view('ppe.items.index', compact('items', 'categories', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $suppliers = PPESupplier::forCompany($companyId)->active()->get();
        
        $copyFrom = null;
        if ($request->has('copy_from')) {
            $copyFrom = PPEItem::where('company_id', $companyId)
                ->findOrFail($request->get('copy_from'));
        }
        
        return view('ppe.items.create', compact('suppliers', 'copyFrom'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'total_quantity' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'specifications' => 'nullable|array',
            'standards_compliance' => 'nullable|array',
            'unit_of_measure' => 'nullable|string|max:50',
            'has_expiry' => 'boolean',
            'expiry_days' => 'nullable|integer|min:1',
            'replacement_alert_days' => 'nullable|integer|min:1',
            'requires_inspection' => 'boolean',
            'inspection_frequency_days' => 'nullable|integer|min:1',
            'supplier_id' => 'nullable|exists:ppe_suppliers,id',
            'unit_cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'storage_location' => 'nullable|string|max:255',
            'warehouse' => 'nullable|string|max:255',
            'storage_conditions' => 'nullable|string',
            'status' => 'required|in:active,inactive,discontinued',
            'notes' => 'nullable|string',
        ]);
        
        $validated['company_id'] = $companyId;
        $validated['available_quantity'] = $validated['total_quantity'];
        
        $item = PPEItem::create($validated);
        
        return redirect()->route('ppe.items.show', $item)
            ->with('success', 'PPE item created successfully.');
    }

    public function show(PPEItem $item)
    {
        $item->load(['supplier', 'issuances.issuedTo', 'issuances.department', 'inspections.inspectedBy']);
        
        return view('ppe.items.show', compact('item'));
    }

    public function edit(PPEItem $item)
    {
        $companyId = Auth::user()->company_id;
        $suppliers = PPESupplier::forCompany($companyId)->active()->get();
        
        return view('ppe.items.edit', compact('item', 'suppliers'));
    }

    public function update(Request $request, PPEItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'manufacturer' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'minimum_stock_level' => 'required|integer|min:0',
            'reorder_quantity' => 'nullable|integer|min:0',
            'specifications' => 'nullable|array',
            'standards_compliance' => 'nullable|array',
            'unit_of_measure' => 'nullable|string|max:50',
            'has_expiry' => 'boolean',
            'expiry_days' => 'nullable|integer|min:1',
            'replacement_alert_days' => 'nullable|integer|min:1',
            'requires_inspection' => 'boolean',
            'inspection_frequency_days' => 'nullable|integer|min:1',
            'supplier_id' => 'nullable|exists:ppe_suppliers,id',
            'unit_cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'storage_location' => 'nullable|string|max:255',
            'warehouse' => 'nullable|string|max:255',
            'storage_conditions' => 'nullable|string',
            'status' => 'required|in:active,inactive,discontinued',
            'notes' => 'nullable|string',
        ]);
        
        $item->update($validated);
        
        return redirect()->route('ppe.items.show', $item)
            ->with('success', 'PPE item updated successfully.');
    }

    public function destroy(PPEItem $item)
    {
        $item->delete();
        
        return redirect()->route('ppe.items.index')
            ->with('success', 'PPE item deleted successfully.');
    }

    public function adjustStock(Request $request, PPEItem $item)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ]);

        $quantity = $validated['quantity'];
        
        switch ($validated['adjustment_type']) {
            case 'add':
                $item->total_quantity += $quantity;
                $item->available_quantity += $quantity;
                break;
            case 'remove':
                if ($item->available_quantity < $quantity) {
                    return back()->withErrors(['quantity' => 'Insufficient available quantity']);
                }
                $item->total_quantity -= $quantity;
                $item->available_quantity -= $quantity;
                break;
            case 'set':
                $item->total_quantity = $quantity;
                $item->available_quantity = $quantity;
                break;
        }
        
        $item->save();
        
        // Log activity
        ActivityLog::log('update', 'ppe', 'PPEItem', $item->id, 
            "Stock adjusted: {$validated['adjustment_type']} {$quantity} - " . ($validated['reason'] ?? 'No reason provided'));
        
        return redirect()->route('ppe.items.show', $item)
            ->with('success', 'Stock adjusted successfully.');
    }

    public function export(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = PPEItem::forCompany($companyId)->with('supplier');
        
        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $items = $query->get();
        
        $filename = 'ppe_items_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Reference Number',
                'Name',
                'Category',
                'Type',
                'Total Quantity',
                'Available',
                'Issued',
                'Minimum Stock',
                'Supplier',
                'Status'
            ]);
            
            // Data
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->reference_number,
                    $item->name,
                    $item->category,
                    $item->type ?? 'N/A',
                    $item->total_quantity,
                    $item->available_quantity,
                    $item->issued_quantity,
                    $item->minimum_stock_level,
                    $item->supplier->name ?? 'N/A',
                    ucfirst($item->status),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Bulk delete PPE items
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ppe_items,id'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        
        $deleted = PPEItem::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->delete();
        
        return redirect()->route('ppe.items.index')
            ->with('success', "Successfully deleted {$deleted} item(s).");
    }
    
    /**
     * Bulk update PPE items status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ppe_items,id',
            'status' => 'required|in:active,inactive,discontinued'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        $status = $request->input('status');
        
        $updated = PPEItem::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->update(['status' => $status]);
        
        return redirect()->route('ppe.items.index')
            ->with('success', "Successfully updated {$updated} item(s) status to {$status}.");
    }
    
    /**
     * Bulk export selected PPE items
     */
    public function bulkExport(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ppe_items,id'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        
        $items = PPEItem::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->with('supplier')
            ->get();
        
        $filename = 'ppe_items_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Reference Number',
                'Name',
                'Category',
                'Type',
                'Total Quantity',
                'Available',
                'Issued',
                'Minimum Stock',
                'Supplier',
                'Status'
            ]);
            
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->reference_number,
                    $item->name,
                    $item->category,
                    $item->type ?? 'N/A',
                    $item->total_quantity,
                    $item->available_quantity,
                    $item->issued_quantity,
                    $item->minimum_stock_level,
                    $item->supplier->name ?? 'N/A',
                    ucfirst($item->status),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}

