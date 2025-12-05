<?php

namespace App\Http\Controllers;

use App\Models\HealthSurveillanceRecord;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthSurveillanceRecordController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = HealthSurveillanceRecord::forCompany($companyId)
            ->with(['user', 'conductedBy', 'medicalProvider']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('examination_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('surveillance_type')) {
            $query->where('surveillance_type', $request->surveillance_type);
        }

        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        $records = $query->latest('examination_date')->paginate(15);
        return view('health.surveillance.index', compact('records'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        $suppliers = Supplier::forCompany($companyId)->where('supplier_type', 'medical')->active()->get();
        return view('health.surveillance.create', compact('users', 'suppliers'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'surveillance_type' => 'required|in:medical_examination,health_test,vaccination,audiometry,lung_function,vision_test,blood_test,other',
            'examination_name' => 'nullable|string|max:255',
            'examination_date' => 'required|date',
            'next_due_date' => 'nullable|date',
            'medical_provider_id' => 'nullable|exists:suppliers,id',
            'provider_name' => 'nullable|string|max:255',
            'findings' => 'nullable|string',
            'result' => 'required|in:fit,unfit,fit_with_restrictions,requires_follow_up,pending',
            'restrictions' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['conducted_by'] = Auth::id();

        HealthSurveillanceRecord::create($validated);

        return redirect()->route('health.surveillance.index')
            ->with('success', 'Health surveillance record created successfully.');
    }

    public function show(HealthSurveillanceRecord $healthSurveillanceRecord)
    {
        $healthSurveillanceRecord->load(['user', 'conductedBy', 'medicalProvider']);
        return view('health.surveillance.show', compact('healthSurveillanceRecord'));
    }

    public function edit(HealthSurveillanceRecord $healthSurveillanceRecord)
    {
        $companyId = Auth::user()->company_id;
        $users = User::forCompany($companyId)->active()->get();
        $suppliers = Supplier::forCompany($companyId)->where('supplier_type', 'medical')->active()->get();
        return view('health.surveillance.edit', compact('healthSurveillanceRecord', 'users', 'suppliers'));
    }

    public function update(Request $request, HealthSurveillanceRecord $healthSurveillanceRecord)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'surveillance_type' => 'required|in:medical_examination,health_test,vaccination,audiometry,lung_function,vision_test,blood_test,other',
            'examination_name' => 'nullable|string|max:255',
            'examination_date' => 'required|date',
            'next_due_date' => 'nullable|date',
            'medical_provider_id' => 'nullable|exists:suppliers,id',
            'provider_name' => 'nullable|string|max:255',
            'findings' => 'nullable|string',
            'result' => 'required|in:fit,unfit,fit_with_restrictions,requires_follow_up,pending',
            'restrictions' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $healthSurveillanceRecord->update($validated);

        return redirect()->route('health.surveillance.show', $healthSurveillanceRecord)
            ->with('success', 'Health surveillance record updated successfully.');
    }

    public function destroy(HealthSurveillanceRecord $healthSurveillanceRecord)
    {
        $healthSurveillanceRecord->delete();
        return redirect()->route('health.surveillance.index')
            ->with('success', 'Health surveillance record deleted successfully.');
    }
}
