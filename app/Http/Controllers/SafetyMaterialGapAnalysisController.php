<?php

namespace App\Http\Controllers;

use App\Models\SafetyMaterialGapAnalysis;
use App\Models\ProcurementRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SafetyMaterialGapAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = SafetyMaterialGapAnalysis::forCompany($companyId)
            ->with(['analyzedBy', 'department', 'relatedProcurementRequest']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('required_material', 'like', "%{$search}%")
                  ->orWhere('material_category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $analyses = $query->latest('analysis_date')->paginate(15);
        return view('procurement.gap-analysis.index', compact('analyses'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        return view('procurement.gap-analysis.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'analysis_date' => 'required|date',
            'material_category' => 'nullable|string|max:255',
            'required_material' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required_quantity' => 'nullable|integer|min:0',
            'available_quantity' => 'nullable|integer|min:0',
            'priority' => 'required|in:low,medium,high,critical',
            'impact' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['analyzed_by'] = Auth::id();
        $validated['status'] = 'identified';
        
        // Calculate gap quantity
        $required = $validated['required_quantity'] ?? 0;
        $available = $validated['available_quantity'] ?? 0;
        $validated['gap_quantity'] = max(0, $required - $available);

        SafetyMaterialGapAnalysis::create($validated);

        return redirect()->route('procurement.gap-analysis.index')
            ->with('success', 'Safety material gap analysis created successfully.');
    }

    public function show(SafetyMaterialGapAnalysis $safetyMaterialGapAnalysis)
    {
        $safetyMaterialGapAnalysis->load(['analyzedBy', 'department', 'relatedProcurementRequest']);
        return view('procurement.gap-analysis.show', compact('safetyMaterialGapAnalysis'));
    }

    public function edit(SafetyMaterialGapAnalysis $safetyMaterialGapAnalysis)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $procurementRequests = ProcurementRequest::forCompany($companyId)->where('status', '!=', 'cancelled')->latest()->get();
        return view('procurement.gap-analysis.edit', compact('safetyMaterialGapAnalysis', 'departments', 'procurementRequests'));
    }

    public function update(Request $request, SafetyMaterialGapAnalysis $safetyMaterialGapAnalysis)
    {
        $validated = $request->validate([
            'analysis_date' => 'required|date',
            'material_category' => 'nullable|string|max:255',
            'required_material' => 'required|string|max:255',
            'description' => 'nullable|string',
            'required_quantity' => 'nullable|integer|min:0',
            'available_quantity' => 'nullable|integer|min:0',
            'priority' => 'required|in:low,medium,high,critical',
            'impact' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'procurement_requested' => 'boolean',
            'related_procurement_request_id' => 'nullable|exists:procurement_requests,id',
            'status' => 'required|in:identified,procurement_requested,procured,resolved,closed',
            'notes' => 'nullable|string',
        ]);

        // Recalculate gap quantity
        $required = $validated['required_quantity'] ?? 0;
        $available = $validated['available_quantity'] ?? 0;
        $validated['gap_quantity'] = max(0, $required - $available);

        $safetyMaterialGapAnalysis->update($validated);

        return redirect()->route('procurement.gap-analysis.show', $safetyMaterialGapAnalysis)
            ->with('success', 'Safety material gap analysis updated successfully.');
    }

    public function destroy(SafetyMaterialGapAnalysis $safetyMaterialGapAnalysis)
    {
        $safetyMaterialGapAnalysis->delete();
        return redirect()->route('procurement.gap-analysis.index')
            ->with('success', 'Safety material gap analysis deleted successfully.');
    }
}
