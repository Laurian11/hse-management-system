<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Department;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with(['users', 'departments']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($request->filled('license_type')) {
            $query->where('license_type', $request->license_type);
        }

        if ($request->filled('industry_type')) {
            $query->where('industry_type', $request->industry_type);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $companies = $query->latest()->paginate(15);
        $licenseTypes = Company::getLicenseTypes();
        $industryTypes = Company::getIndustryTypes();

        return view('admin.companies.index', compact('companies', 'licenseTypes', 'industryTypes'));
    }

    public function create()
    {
        $licenseTypes = Company::getLicenseTypes();
        $industryTypes = Company::getIndustryTypes();
        $countries = Company::getCountries();
        $features = Company::getAvailableFeatures();

        return view('admin.companies.create', compact('licenseTypes', 'industryTypes', 'countries', 'features'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'license_type' => 'required|in:basic,professional,enterprise',
            'license_expiry' => 'required|date|after:today',
            'max_users' => 'required|integer|min:1|max:10000',
            'max_departments' => 'required|integer|min:1|max:1000',
            'features' => 'nullable|array',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'industry_type' => 'required|in:construction,manufacturing,oil_gas,mining,transportation,healthcare,retail,hospitality,technology,education,government,agriculture,energy,utilities,telecommunications,finance,insurance,real_estate,other',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['is_active'] = true;

        $company = Company::create($data);

        ActivityLog::log('create', 'admin', 'Company', $company->id, "Created company {$company->name}");

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $company->load(['users', 'departments', 'users.role']);
        
        $statistics = [
            'total_users' => $company->users()->count(),
            'active_users' => $company->users()->where('is_active', true)->count(),
            'total_departments' => $company->departments()->count(),
            'active_departments' => $company->departments()->where('is_active', true)->count(),
            'license_usage_percentage' => $company->getLicenseUsagePercentage(),
            'days_until_expiry' => $company->getDaysUntilLicenseExpiry(),
        ];

        return view('admin.companies.show', compact('company', 'statistics'));
    }

    public function edit(Company $company)
    {
        $company->load(['users', 'departments']);
        
        $licenseTypes = Company::getLicenseTypes();
        $industryTypes = Company::getIndustryTypes();
        $countries = Company::getCountries();
        $features = Company::getAvailableFeatures();

        return view('admin.companies.edit', compact('company', 'licenseTypes', 'industryTypes', 'countries', 'features'));
    }

    public function update(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'license_type' => 'required|in:basic,professional,enterprise',
            'license_expiry' => 'required|date',
            'max_users' => 'required|integer|min:1|max:10000',
            'max_departments' => 'required|integer|min:1|max:1000',
            'features' => 'nullable|array',
            'timezone' => 'required|string|max:50',
            'currency' => 'required|string|size:3',
            'language' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'industry_type' => 'required|string|max:100',
            'hse_policies' => 'nullable|array',
            'safety_standards' => 'nullable|array',
            'compliance_certifications' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldData = $company->toArray();
        $newData = $request->all();

        $company->update($newData);

        ActivityLog::log('update', 'admin', 'Company', $company->id, "Updated company {$company->name}", $oldData, $newData);

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        if ($company->users()->count() > 0) {
            return redirect()->back()->with('error', 'Company cannot be deleted. It has assigned users.');
        }

        if ($company->departments()->count() > 0) {
            return redirect()->back()->with('error', 'Company cannot be deleted. It has departments.');
        }

        $companyName = $company->name;
        $company->delete();

        ActivityLog::log('delete', 'admin', 'Company', $company->id, "Deleted company {$companyName}");

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    public function activate(Company $company)
    {
        if ($company->is_active) {
            return redirect()->back()->with('info', 'Company is already active.');
        }

        $company->update([
            'is_active' => true,
            'deactivated_at' => null,
            'deactivation_reason' => null,
        ]);

        ActivityLog::log('activate', 'admin', 'Company', $company->id, "Activated company {$company->name}");

        return redirect()->back()->with('success', 'Company activated successfully.');
    }

    public function deactivate(Request $request, Company $company)
    {
        if (!$company->is_active) {
            return redirect()->back()->with('info', 'Company is already inactive.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $company->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivation_reason' => $request->reason,
        ]);

        // Deactivate all users
        $company->users()->update(['is_active' => false]);

        ActivityLog::log('deactivate', 'admin', 'Company', $company->id, "Deactivated company {$company->name}: {$request->reason}");

        return redirect()->back()->with('success', 'Company deactivated successfully.');
    }

    public function upgrade(Request $request, Company $company)
    {
        $request->validate([
            'license_type' => 'required|in:basic,professional,enterprise',
            'license_expiry' => 'required|date|after:today',
            'max_users' => 'required|integer|min:1|max:10000',
            'max_departments' => 'required|integer|min:1|max:1000',
            'features' => 'nullable|array',
        ]);

        $oldData = $company->toArray();
        $newData = $request->only(['license_type', 'license_expiry', 'max_users', 'max_departments', 'features']);

        $company->update($newData);

        ActivityLog::log('upgrade', 'admin', 'Company', $company->id, "Upgraded company {$company->name}", $oldData, $newData);

        return redirect()->back()->with('success', 'Company license upgraded successfully.');
    }

    public function users(Company $company)
    {
        $users = $company->users()
                        ->with(['role', 'department'])
                        ->latest()
                        ->paginate(15);

        return view('admin.companies.users', compact('company', 'users'));
    }

    public function departments(Company $company)
    {
        $departments = $company->departments()
                             ->with(['parentDepartment', 'headOfDepartment'])
                             ->latest()
                             ->paginate(15);

        return view('admin.companies.departments', compact('company', 'departments'));
    }

    public function statistics(Company $company)
    {
        $company->load(['users', 'departments']);
        
        $stats = $company->getDetailedStatistics();
        $licenseInfo = $company->getLicenseInformation();
        $hseMetrics = $company->getHSEMetrics();

        return view('admin.companies.statistics', compact('company', 'stats', 'licenseInfo', 'hseMetrics'));
    }
}
