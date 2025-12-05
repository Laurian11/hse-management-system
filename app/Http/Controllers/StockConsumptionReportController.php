<?php

namespace App\Http\Controllers;

use App\Models\StockConsumptionReport;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockConsumptionReportController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = StockConsumptionReport::forCompany($companyId)
            ->with(['department', 'preparedBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('item_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('item_category')) {
            $query->where('item_category', $request->item_category);
        }

        if ($request->filled('report_period_start')) {
            $query->where('report_period_start', '>=', $request->report_period_start);
        }

        if ($request->filled('report_period_end')) {
            $query->where('report_period_end', '<=', $request->report_period_end);
        }

        $reports = $query->latest('report_period_start')->paginate(15);
        return view('procurement.stock-reports.index', compact('reports'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        return view('procurement.stock-reports.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_category' => 'nullable|string|max:255',
            'item_code' => 'nullable|string|max:255',
            'opening_stock' => 'nullable|numeric|min:0',
            'received_quantity' => 'nullable|numeric|min:0',
            'consumed_quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after_or_equal:report_period_start',
            'department_id' => 'nullable|exists:departments,id',
            'consumption_details' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['prepared_by'] = Auth::id();
        
        // Calculate closing stock
        $opening = $validated['opening_stock'] ?? 0;
        $received = $validated['received_quantity'] ?? 0;
        $consumed = $validated['consumed_quantity'] ?? 0;
        $validated['closing_stock'] = $opening + $received - $consumed;

        StockConsumptionReport::create($validated);

        return redirect()->route('procurement.stock-reports.index')
            ->with('success', 'Stock consumption report created successfully.');
    }

    public function show(StockConsumptionReport $stockConsumptionReport)
    {
        $stockConsumptionReport->load(['department', 'preparedBy']);
        return view('procurement.stock-reports.show', compact('stockConsumptionReport'));
    }

    public function edit(StockConsumptionReport $stockConsumptionReport)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        return view('procurement.stock-reports.edit', compact('stockConsumptionReport', 'departments'));
    }

    public function update(Request $request, StockConsumptionReport $stockConsumptionReport)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'item_category' => 'nullable|string|max:255',
            'item_code' => 'nullable|string|max:255',
            'opening_stock' => 'nullable|numeric|min:0',
            'received_quantity' => 'nullable|numeric|min:0',
            'consumed_quantity' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'report_period_start' => 'required|date',
            'report_period_end' => 'required|date|after_or_equal:report_period_start',
            'department_id' => 'nullable|exists:departments,id',
            'consumption_details' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Recalculate closing stock
        $opening = $validated['opening_stock'] ?? 0;
        $received = $validated['received_quantity'] ?? 0;
        $consumed = $validated['consumed_quantity'] ?? 0;
        $validated['closing_stock'] = $opening + $received - $consumed;

        $stockConsumptionReport->update($validated);

        return redirect()->route('procurement.stock-reports.show', $stockConsumptionReport)
            ->with('success', 'Stock consumption report updated successfully.');
    }

    public function destroy(StockConsumptionReport $stockConsumptionReport)
    {
        $stockConsumptionReport->delete();
        return redirect()->route('procurement.stock-reports.index')
            ->with('success', 'Stock consumption report deleted successfully.');
    }
}
