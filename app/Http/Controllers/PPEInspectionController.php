<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\PPEInspection;
use App\Models\PPEIssuance;
use App\Models\PPEItem;
use App\Models\User;

class PPEInspectionController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = PPEInspection::forCompany($companyId)
            ->with(['ppeItem', 'user', 'inspectedBy', 'ppeIssuance']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('ppeItem', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('inspection_type')) {
            $query->where('inspection_type', $request->inspection_type);
        }
        
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        
        if ($request->filled('is_compliant')) {
            $query->where('is_compliant', $request->is_compliant);
        }
        
        if ($request->filled('ppe_item_id')) {
            $query->where('ppe_item_id', $request->ppe_item_id);
        }
        
        $inspections = $query->latest()->paginate(20);
        
        $items = PPEItem::forCompany($companyId)->active()->get();
        
        $stats = [
            'total' => PPEInspection::forCompany($companyId)->count(),
            'compliant' => PPEInspection::forCompany($companyId)->compliant()->count(),
            'non_compliant' => PPEInspection::forCompany($companyId)->nonCompliant()->count(),
            'pending' => PPEInspection::forCompany($companyId)->where('status', 'pending')->count(),
        ];
        
        return view('ppe.inspections.index', compact('inspections', 'items', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $issuances = PPEIssuance::forCompany($companyId)
            ->active()
            ->with(['ppeItem', 'issuedTo'])
            ->get();
        
        $selectedIssuance = null;
        if ($request->has('ppe_issuance_id')) {
            $selectedIssuance = PPEIssuance::forCompany($companyId)->findOrFail($request->ppe_issuance_id);
        }
        
        return view('ppe.inspections.create', compact('issuances', 'selectedIssuance'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'ppe_issuance_id' => 'required|exists:ppe_issuances,id',
            'inspection_date' => 'required|date',
            'inspection_type' => 'required|in:scheduled,pre_use,post_use,damage_report,random',
            'condition' => 'required|in:excellent,good,fair,poor,unsafe,damaged',
            'inspection_checklist' => 'nullable|array',
            'findings' => 'nullable|string',
            'defects' => 'nullable|string',
            'defect_photos' => 'nullable|array',
            'defect_photos.*' => 'image|mimes:jpeg,jpg,png,gif|max:5120',
            'action_taken' => 'required|in:approved,repair,replace,dispose,quarantine',
            'action_notes' => 'nullable|string',
            'next_inspection_date' => 'nullable|date|after:inspection_date',
            'is_compliant' => 'boolean',
            'non_compliance_reason' => 'nullable|string',
            'compliance_issues' => 'nullable|array',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('defect_photos')) {
            foreach ($request->file('defect_photos') as $photo) {
                $path = $photo->store('ppe-inspections', 'public');
                $photoPaths[] = $path;
            }
        }
        
        if (!empty($photoPaths)) {
            $validated['defect_photos'] = $photoPaths;
        }
        
        $issuance = PPEIssuance::forCompany($companyId)->findOrFail($validated['ppe_issuance_id']);
        
        $validated['company_id'] = $companyId;
        $validated['ppe_item_id'] = $issuance->ppe_item_id;
        $validated['user_id'] = $issuance->issued_to;
        $validated['inspected_by'] = Auth::id();
        
        $inspection = PPEInspection::create($validated);
        
        return redirect()->route('ppe.inspections.show', $inspection)
            ->with('success', 'PPE inspection created successfully.');
    }

    public function show(PPEInspection $inspection)
    {
        $inspection->load(['ppeItem', 'ppeIssuance.issuedTo', 'user', 'inspectedBy']);
        
        return view('ppe.inspections.show', compact('inspection'));
    }
}

