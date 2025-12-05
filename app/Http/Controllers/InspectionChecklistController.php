<?php

namespace App\Http\Controllers;

use App\Models\InspectionChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspectionChecklistController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = InspectionChecklist::forCompany($companyId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $checklists = $query->latest()->paginate(15);
        return view('inspections.checklists.index', compact('checklists'));
    }

    public function create()
    {
        return view('inspections.checklists.create');
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item' => 'required|string|max:255',
            'items.*.type' => 'required|in:yes_no,text,number,date,select',
            'items.*.options' => 'nullable|array|required_if:items.*.type,select',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $companyId;
        InspectionChecklist::create($validated);

        return redirect()->route('inspections.checklists.index')
            ->with('success', 'Inspection checklist created successfully.');
    }

    public function show(InspectionChecklist $checklist)
    {
        $checklist->load(['schedules', 'inspections']);
        return view('inspections.checklists.show', compact('checklist'));
    }

    public function edit(InspectionChecklist $checklist)
    {
        return view('inspections.checklists.edit', compact('checklist'));
    }

    public function update(Request $request, InspectionChecklist $checklist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item' => 'required|string|max:255',
            'items.*.type' => 'required|in:yes_no,text,number,date,select',
            'items.*.options' => 'nullable|array|required_if:items.*.type,select',
            'is_active' => 'boolean',
        ]);

        $checklist->update($validated);

        return redirect()->route('inspections.checklists.show', $checklist)
            ->with('success', 'Inspection checklist updated successfully.');
    }

    public function destroy(InspectionChecklist $checklist)
    {
        $checklist->delete();
        return redirect()->route('inspections.checklists.index')
            ->with('success', 'Inspection checklist deleted successfully.');
    }
}
