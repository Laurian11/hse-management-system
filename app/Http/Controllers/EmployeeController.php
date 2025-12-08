<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Company;
use App\Models\Department;
use App\Models\ActivityLog;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Search employees for autocomplete
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([]);
        }

        $companyId = $user->company_id;
        $query = $request->get('q', '');

        if (empty($query)) {
            return response()->json([]);
        }

        $employeesQuery = Employee::where('is_active', true)
            ->whereNull('deleted_at');
        
        if ($companyId) {
            $employeesQuery->where('company_id', $companyId);
        }
        
        $employees = $employeesQuery
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('employee_id_number', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'email' => $employee->email,
                    'employee_id_number' => $employee->employee_id_number,
                    'display' => $employee->display_name,
                ];
            });

        return response()->json($employees);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Super admin can see all employees, regular users see only their company's employees
        $companyId = $user->company_id;
        
        if (!$companyId && $user->role && $user->role->name !== 'super_admin') {
            abort(403, 'User does not belong to any company');
        }

        $baseQuery = $companyId 
            ? Employee::where('company_id', $companyId)->whereNull('deleted_at')
            : Employee::whereNull('deleted_at'); // Super admin sees all

        // Statistics
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->active()->count(),
            'inactive' => (clone $baseQuery)->inactive()->count(),
            'with_user_access' => (clone $baseQuery)->withUserAccess()->count(),
            'without_user_access' => (clone $baseQuery)->withoutUserAccess()->count(),
            'full_time' => (clone $baseQuery)->byEmploymentType('full_time')->count(),
            'contractors' => (clone $baseQuery)->byEmploymentType('contractor')->count(),
            'visitors' => (clone $baseQuery)->byEmploymentType('visitor')->count(),
        ];

        $query = Employee::with(['user.role', 'company', 'department', 'directSupervisor'])
                         ->whereNull('deleted_at');
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id_number', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('is_active')) {
            if ($request->boolean('is_active')) {
                $query->active();
            } else {
                $query->inactive();
            }
        }

        if ($request->filled('employment_type')) {
            $query->byEmploymentType($request->employment_type);
        }

        if ($request->filled('employment_status')) {
            $query->byEmploymentStatus($request->employment_status);
        }

        if ($request->filled('has_user_access')) {
            if ($request->boolean('has_user_access')) {
                $query->withUserAccess();
            } else {
                $query->withoutUserAccess();
            }
        }

        if ($request->filled('job_title')) {
            $query->where('job_title', 'like', "%{$request->job_title}%");
        }

        $employees = $query->latest('date_of_hire')->paginate(20);
        $roles = Role::active()->get();
        $departments = $companyId 
            ? Department::where('company_id', $companyId)->active()->get()
            : Department::active()->get(); // Super admin sees all departments

        return view('admin.employees.index', compact('employees', 'roles', 'departments', 'stats'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $companyId = $user->company_id;
        
        if (!$companyId && $user->role && $user->role->name !== 'super_admin') {
            abort(403, 'User does not belong to any company');
        }

        $departments = $companyId 
            ? Department::where('company_id', $companyId)->active()->get()
            : Department::active()->get();
            
        $supervisorsQuery = Employee::where('is_active', true)
            ->whereNull('deleted_at');
        
        if ($companyId) {
            $supervisorsQuery->where('company_id', $companyId);
        }
        
        $supervisors = $supervisorsQuery->get();

        return view('admin.employees.create', compact('departments', 'supervisors', 'companyId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $companyId = $user->company_id;
        
        if (!$companyId && $user->role && $user->role->name !== 'super_admin') {
            abort(403, 'User does not belong to any company');
        }
        
        // If super admin, require company_id in request
        if (!$companyId) {
            $request->validate([
                'company_id' => 'required|exists:companies,id',
            ]);
            $companyId = $request->company_id;
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:employees,email',
            'employee_id_number' => 'required|string|max:50|unique:employees,employee_id_number',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'emergency_contacts' => 'nullable|array',
            'date_of_hire' => 'nullable|date',
            'employment_type' => 'required|in:full_time,part_time,contractor,visitor,intern',
            'employment_status' => 'nullable|in:active,on_leave,suspended,terminated',
            'job_title' => 'nullable|string|max:255',
            'direct_supervisor_id' => 'nullable|exists:employees,id',
            'known_allergies' => 'nullable|array',
            'create_user_account' => 'nullable|boolean',
            'role_id' => 'required_if:create_user_account,1|exists:roles,id',
            'password' => 'required_if:create_user_account,1|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except(['create_user_account', 'role_id', 'password', 'password_confirmation']);
        $data['company_id'] = $companyId;
        $data['employment_status'] = $data['employment_status'] ?? 'active';
        $data['is_active'] = true;

        $employee = Employee::create($data);

        // Create user account if requested
        if ($request->boolean('create_user_account')) {
            $userData = [
                'name' => $employee->full_name,
                'email' => $employee->email ?? $employee->employee_id_number . '@' . strtolower(str_replace(' ', '', $user->company->name)) . '.co.tz',
                'password' => Hash::make($request->password),
                'company_id' => $companyId,
                'role_id' => $request->role_id,
                'department_id' => $employee->department_id,
                'employee_id_number' => $employee->employee_id_number,
                'phone' => $employee->phone,
                'is_active' => true,
                'email_verified_at' => now(),
            ];

            $userAccount = User::create($userData);
            $employee->update(['user_id' => $userAccount->id]);
        }

        ActivityLog::log('create', 'admin', 'Employee', $employee->id, "Created employee {$employee->full_name}");

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Super admin can view any employee, others can only view their company's employees
        $companyId = $user->company_id;
        if ($companyId && $employee->company_id !== $companyId) {
            abort(403, 'Unauthorized');
        }

        $employee->load(['user.role', 'company', 'department', 'directSupervisor', 'subordinates']);
        
        // Get employee statistics
        $stats = [
            'incidents_reported' => $employee->user ? \App\Models\Incident::where('reported_by', $employee->user->id)->count() : 0,
            'toolbox_talks_attended' => $employee->user ? \App\Models\ToolboxTalkAttendance::where('user_id', $employee->user->id)->count() : 0,
            'toolbox_talks_conducted' => $employee->user ? \App\Models\ToolboxTalk::where('supervisor_id', $employee->user->id)->count() : 0,
            'last_login' => $employee->user ? $employee->user->last_login_at : null,
        ];

        $recentActivity = $employee->user ? ActivityLog::where('user_id', $employee->user->id)
                                    ->latest()
                                    ->limit(20)
                                    ->get() : collect();

        return view('admin.employees.show', compact('employee', 'stats', 'recentActivity'));
    }

    public function edit(Employee $employee)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Super admin can edit any employee, others can only edit their company's employees
        $companyId = $user->company_id;
        if ($companyId && $employee->company_id !== $companyId) {
            abort(403, 'Unauthorized');
        }

        $roles = Role::active()->get();
        $departments = Department::where('company_id', $companyId)->active()->get();
        $supervisors = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('id', '!=', $employee->id)
            ->whereNull('deleted_at')
            ->get();

        return view('admin.employees.edit', compact('employee', 'roles', 'departments', 'supervisors'));
    }

    public function createUser(Employee $employee)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Super admin can create user for any employee, others can only for their company's employees
        $companyId = $user->company_id;
        if ($companyId && $employee->company_id !== $companyId) {
            abort(403, 'Unauthorized');
        }

        if ($employee->hasUserAccess()) {
            return redirect()->route('admin.employees.show', $employee)
                ->with('error', 'Employee already has a user account.');
        }

        $roles = Role::active()->get();
        return view('admin.employees.create-user', compact('employee', 'roles'));
    }

    public function storeUser(Request $request, Employee $employee)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Super admin can create user for any employee, others can only for their company's employees
        $companyId = $user->company_id;
        if ($companyId && $employee->company_id !== $companyId) {
            abort(403, 'Unauthorized');
        }

        if ($employee->hasUserAccess()) {
            return redirect()->route('admin.employees.show', $employee)
                ->with('error', 'Employee already has a user account.');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userData = [
            'name' => $employee->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $employee->company_id,
            'role_id' => $request->role_id,
            'department_id' => $employee->department_id,
            'employee_id_number' => $employee->employee_id_number,
            'phone' => $employee->phone,
            'is_active' => true,
            'email_verified_at' => now(),
        ];

        $userAccount = User::create($userData);
        $employee->update(['user_id' => $userAccount->id]);

        ActivityLog::log('create', 'admin', 'User', $userAccount->id, "Created user account for employee {$employee->full_name}");

        return redirect()->route('admin.employees.show', $employee)
            ->with('success', 'User account created successfully.');
    }

    public function update(Request $request, Employee $employee)
    {
        // Ensure employee belongs to same company
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('employees')->ignore($employee->id)],
            'employee_id_number' => ['required', 'string', 'max:50', Rule::unique('employees')->ignore($employee->id)],
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'emergency_contacts' => 'nullable|array',
            'date_of_hire' => 'nullable|date',
            'employment_type' => 'required|in:full_time,part_time,contractor,visitor,intern',
            'employment_status' => 'nullable|in:active,on_leave,suspended,terminated',
            'job_title' => 'nullable|string|max:255',
            'direct_supervisor_id' => 'nullable|exists:employees,id',
            'known_allergies' => 'nullable|array',
            'hse_training_history' => 'nullable|array',
            'competency_certificates' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $newData = $request->except(['password', 'password_confirmation', 'create_user_account', 'role_id']);

        $employee->update($newData);

        // Update user account if exists
        if ($employee->user) {
            $userData = [
                'name' => $employee->full_name,
                'email' => $employee->email ?? $employee->user->email,
                'department_id' => $employee->department_id,
                'employee_id_number' => $employee->employee_id_number,
                'phone' => $employee->phone,
            ];
            $employee->user->update($userData);
        }

        ActivityLog::log('update', 'admin', 'Employee', $employee->id, "Updated employee {$employee->full_name}");

        return redirect()->route('admin.employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // Ensure employee belongs to same company
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        if ($employee->user && $employee->user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own employee record.');
        }

        $employeeName = $employee->full_name;
        $employee->delete();

        ActivityLog::log('delete', 'admin', 'Employee', $employee->id, "Deleted employee {$employeeName}");

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function activate(Employee $employee)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Super admin can activate any employee, others can only activate their company's employees
        $companyId = $user->company_id;
        if ($companyId && $employee->company_id !== $companyId) {
            abort(403, 'Unauthorized');
        }

        $employee->update([
            'is_active' => true,
            'employment_status' => 'active',
            'deactivated_at' => null,
            'deactivation_reason' => null,
        ]);

        // Also activate user account if exists
        if ($employee->user) {
            $employee->user->update([
                'is_active' => true,
                'deactivated_at' => null,
                'deactivation_reason' => null,
                'failed_login_attempts' => 0,
                'locked_until' => null,
            ]);
        }

        ActivityLog::log('activate', 'admin', 'Employee', $employee->id, "Activated employee {$employee->full_name}");

        return redirect()->back()->with('success', 'Employee activated successfully.');
    }

    public function deactivate(Request $request, Employee $employee)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Super admin can deactivate any employee, others can only deactivate their company's employees
        $companyId = $user->company_id;
        if ($companyId && $employee->company_id !== $companyId) {
            abort(403, 'Unauthorized');
        }

        if ($employee->user && $employee->user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own employee record.');
        }

        $request->validate([
            'deactivation_reason' => 'nullable|string|max:255',
            'employment_status' => 'nullable|in:on_leave,suspended,terminated',
        ]);

        $employee->update([
            'is_active' => false,
            'employment_status' => $request->employment_status ?? 'suspended',
            'deactivated_at' => now(),
            'deactivation_reason' => $request->deactivation_reason,
        ]);

        // Also deactivate user account if exists
        if ($employee->user) {
            $employee->user->update([
                'is_active' => false,
                'deactivated_at' => now(),
                'deactivation_reason' => $request->deactivation_reason,
            ]);
            
            // End all active sessions
            UserSession::endAllSessionsForUser($employee->user->id);
        }

        ActivityLog::log('deactivate', 'admin', 'Employee', $employee->id, "Deactivated employee {$employee->full_name}: {$request->deactivation_reason}");

        return redirect()->back()->with('success', 'Employee deactivated successfully.');
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $companyId = $user->company_id;

        $query = Employee::whereNull('deleted_at')
            ->with(['user.role', 'department']);
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        // Apply same filters as index
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        $employees = $query->get();

        ActivityLog::log('export', 'admin', 'Employee', null, 'Exported employees list');

        // Generate CSV
        $filename = 'employees_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Employee ID', 'Name', 'Email', 'Job Title', 'Department', 'Role', 'Employment Type', 'Phone', 'Status', 'Date of Hire', 'Created At']);
            
            // Data
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->employee_id_number ?? 'N/A',
                    $employee->name,
                    $employee->email,
                    $employee->job_title ?? 'N/A',
                    $employee->department->name ?? 'N/A',
                    $employee->role->display_name ?? $employee->role->name ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $employee->employment_type ?? 'N/A')),
                    $employee->phone ?? 'N/A',
                    $employee->is_active ? 'Active' : 'Inactive',
                    $employee->date_of_hire ? $employee->date_of_hire->format('Y-m-d') : 'N/A',
                    $employee->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

