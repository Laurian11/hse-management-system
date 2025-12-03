<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Company;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['company', 'parentDepartment', 'headOfDepartment', 'hseOfficer'])
                           ->active();

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('parent_department_id')) {
            $query->where('parent_department_id', $request->parent_department_id);
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        $departments = $query->latest()->paginate(15);
        $companies = Company::active()->get();
        $locations = Department::getLocations();

        return view('admin.departments.index', compact('departments', 'companies', 'locations'));
    }

    public function create()
    {
        $companies = Company::active()->get();
        $departments = Department::active()->get();
        $users = User::where('is_active', true)->get();
        $riskFactors = Department::getRiskFactors();

        return view('admin.departments.create', compact('companies', 'departments', 'users', 'riskFactors'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'parent_department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:100|unique:departments,name,NULL,id,company_id,' . $request->company_id,
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'head_of_department_id' => 'nullable|exists:users,id',
            'hse_officer_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:255',
            'hse_objectives' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $department = Department::create($request->all());

        ActivityLog::log('create', 'admin', 'Department', $department->id, "Created department {$department->name}");

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['company', 'parentDepartment', 'childDepartments', 'headOfDepartment', 'hseOfficer', 'employees']);
        
        $performanceMetrics = $department->getPerformanceMetrics();

        return view('admin.departments.show', compact('department', 'performanceMetrics'));
    }

    public function edit(Department $department)
    {
        $department->load(['company', 'parentDepartment']);
        
        $companies = Company::active()->get();
        $departments = Department::where('id', '!=', $department->id)->active()->get();
        $users = User::where('is_active', true)->get();
        $riskFactors = Department::getRiskFactors();

        return view('admin.departments.edit', compact('department', 'companies', 'departments', 'users', 'riskFactors'));
    }

    public function update(Request $request, Department $department)
    {
        $validator = Validator::make($request->all(), [
            'parent_department_id' => 'nullable|exists:departments,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'head_of_department_id' => 'nullable|exists:users,id',
            'hse_officer_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string|max:100',
            'risk_profile' => 'nullable|array',
            'risk_profile.*' => 'integer|min:0|max:5',
            'hse_objectives' => 'nullable|array',
            'hse_objectives.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldData = $department->toArray();
        $newData = $request->all();

        $department->update($newData);

        ActivityLog::log('update', 'admin', 'Department', $department->id, "Updated department {$department->name}", $oldData, $newData);

        return redirect()->route('admin.departments.show', $department)
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if (!$department->canBeDeleted()) {
            return redirect()->back()->with('error', 'Department cannot be deleted. It has employees, child departments, or associated records.');
        }

        $departmentName = $department->name;
        $department->delete();

        ActivityLog::log('delete', 'admin', 'Department', $department->id, "Deleted department {$departmentName}");

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    public function activate(Department $department)
    {
        if ($department->is_active) {
            return redirect()->back()->with('info', 'Department is already active.');
        }

        $department->update(['is_active' => true]);

        ActivityLog::log('activate', 'admin', 'Department', $department->id, "Activated department {$department->name}");

        return redirect()->back()->with('success', 'Department activated successfully.');
    }

    public function deactivate(Department $department)
    {
        if (!$department->is_active) {
            return redirect()->back()->with('info', 'Department is already inactive.');
        }

        // Check if department has active employees
        if ($department->employees()->where('is_active', true)->count() > 0) {
            return redirect()->back()->with('error', 'Cannot deactivate department with active employees.');
        }

        $department->update(['is_active' => false]);

        ActivityLog::log('deactivate', 'admin', 'Department', $department->id, "Deactivated department {$department->name}");

        return redirect()->back()->with('success', 'Department deactivated successfully.');
    }

    public function hierarchy(Request $request)
    {
        $companyId = $request->company_id ?? auth()->user()->company_id;
        
        $departments = Department::where('company_id', $companyId)
                                ->with(['parentDepartment', 'childDepartments', 'headOfDepartment'])
                                ->active()
                                ->get()
                                ->sortBy('name');

        // Build hierarchical structure
        $hierarchy = $this->buildHierarchy($departments);

        return view('admin.departments.hierarchy', compact('hierarchy', 'departments'));
    }

    private function buildHierarchy($departments, $parentId = null)
    {
        $result = [];
        
        foreach ($departments as $department) {
            if ($department->parent_department_id == $parentId) {
                $department->children = $this->buildHierarchy($departments, $department->id);
                $result[] = $department;
            }
        }
        
        return $result;
    }

    public function performance(Department $department)
    {
        $department->load(['company', 'headOfDepartment']);
        
        $metrics = $department->getPerformanceMetrics();
        
        // Additional performance data
        $recentIncidents = $department->incidents()
                                     ->latest()
                                     ->limit(10)
                                     ->get();
        
        $upcomingTalks = $department->toolboxTalks()
                                   ->where('scheduled_date', '>=', now())
                                   ->where('status', 'scheduled')
                                   ->orderBy('scheduled_date')
                                   ->limit(5)
                                   ->get();

        return view('admin.departments.performance', compact('department', 'metrics', 'recentIncidents', 'upcomingTalks'));
    }
}
