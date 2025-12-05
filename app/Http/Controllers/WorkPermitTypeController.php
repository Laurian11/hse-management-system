<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkPermitType;

class WorkPermitTypeController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $types = WorkPermitType::forCompany($companyId)->latest()->get();
        
        return view('work-permits.types.index', compact('types'));
    }

    public function create()
    {
        return view('work-permits.types.create');
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:work_permit_types,code',
            'description' => 'nullable|string',
            'required_precautions' => 'nullable|array',
            'required_equipment' => 'nullable|array',
            'default_validity_hours' => 'required|integer|min:1|max:168',
            'max_validity_hours' => 'required|integer|min:1|max:168',
            'requires_risk_assessment' => 'boolean',
            'requires_jsa' => 'boolean',
            'requires_gas_test' => 'boolean',
            'requires_fire_watch' => 'boolean',
            'is_active' => 'boolean',
            'approval_levels' => 'required|integer|min:1|max:5',
        ]);
        
        $validated['company_id'] = $companyId;
        
        $type = WorkPermitType::create($validated);
        
        return redirect()->route('work-permits.types.index')
            ->with('success', 'Work permit type created successfully.');
    }

    public function show(WorkPermitType $workPermitType)
    {
        $workPermitType->load('workPermits');
        return view('work-permits.types.show', compact('workPermitType'));
    }

    public function edit(WorkPermitType $workPermitType)
    {
        return view('work-permits.types.edit', compact('workPermitType'));
    }

    public function update(Request $request, WorkPermitType $workPermitType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:work_permit_types,code,' . $workPermitType->id,
            'description' => 'nullable|string',
            'required_precautions' => 'nullable|array',
            'required_equipment' => 'nullable|array',
            'default_validity_hours' => 'required|integer|min:1|max:168',
            'max_validity_hours' => 'required|integer|min:1|max:168',
            'requires_risk_assessment' => 'boolean',
            'requires_jsa' => 'boolean',
            'requires_gas_test' => 'boolean',
            'requires_fire_watch' => 'boolean',
            'is_active' => 'boolean',
            'approval_levels' => 'required|integer|min:1|max:5',
        ]);
        
        $workPermitType->update($validated);
        
        return redirect()->route('work-permits.types.index')
            ->with('success', 'Work permit type updated successfully.');
    }

    public function destroy(WorkPermitType $workPermitType)
    {
        if ($workPermitType->workPermits()->count() > 0) {
            return redirect()->route('work-permits.types.index')
                ->with('error', 'Cannot delete permit type that has associated permits.');
        }
        
        $workPermitType->delete();
        
        return redirect()->route('work-permits.types.index')
            ->with('success', 'Work permit type deleted successfully.');
    }
}
