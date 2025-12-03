<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = User::with(['role', 'company', 'department', 'directSupervisor'])
                     ->where('company_id', $companyId)
                     ->whereNull('deleted_at');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id_number', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->filled('job_title')) {
            $query->where('job_title', 'like', "%{$request->job_title}%");
        }

        $employees = $query->latest()->paginate(20);
        $roles = Role::active()->get();
        $departments = Department::where('company_id', $companyId)->active()->get();

        return view('admin.employees.index', compact('employees', 'roles', 'departments'));
    }

    public function create()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $roles = Role::active()->get();
        $departments = Department::where('company_id', $companyId)->active()->get();
        $supervisors = User::where('company_id', $companyId)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->get();

        return view('admin.employees.create', compact('roles', 'departments', 'supervisors', 'companyId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id_number' => 'nullable|string|max:50|unique:users,employee_id_number',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'emergency_contacts' => 'nullable|array',
            'date_of_hire' => 'nullable|date',
            'employment_type' => 'required|in:full_time,contractor,visitor',
            'job_title' => 'nullable|string|max:255',
            'direct_supervisor_id' => 'nullable|exists:users,id',
            'known_allergies' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['company_id'] = $companyId;
        $data['password'] = Hash::make($request->password);
        $data['password_changed_at'] = now();
        $data['is_active'] = true;

        $employee = User::create($data);

        ActivityLog::log('create', 'admin', 'Employee', $employee->id, "Created employee {$employee->name}");

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(User $employee)
    {
        // Ensure employee belongs to same company
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $employee->load(['role', 'company', 'department', 'directSupervisor', 'subordinates']);
        
        // Get employee statistics
        $stats = [
            'incidents_reported' => \App\Models\Incident::where('reported_by', $employee->id)->count(),
            'toolbox_talks_attended' => \App\Models\ToolboxTalkAttendance::where('user_id', $employee->id)->count(),
            'toolbox_talks_conducted' => \App\Models\ToolboxTalk::where('supervisor_id', $employee->id)->count(),
            'last_login' => $employee->last_login_at,
        ];

        $recentActivity = ActivityLog::where('user_id', $employee->id)
                                    ->latest()
                                    ->limit(20)
                                    ->get();

        return view('admin.employees.show', compact('employee', 'stats', 'recentActivity'));
    }

    public function edit(User $employee)
    {
        // Ensure employee belongs to same company
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();
        $companyId = $user->company_id;

        $roles = Role::active()->get();
        $departments = Department::where('company_id', $companyId)->active()->get();
        $supervisors = User::where('company_id', $companyId)
            ->where('is_active', true)
            ->where('id', '!=', $employee->id)
            ->whereNull('deleted_at')
            ->get();

        return view('admin.employees.edit', compact('employee', 'roles', 'departments', 'supervisors'));
    }

    public function update(Request $request, User $employee)
    {
        // Ensure employee belongs to same company
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($employee->id)],
            'role_id' => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id_number' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($employee->id)],
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'nationality' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'emergency_contacts' => 'nullable|array',
            'date_of_hire' => 'nullable|date',
            'employment_type' => 'required|in:full_time,contractor,visitor',
            'job_title' => 'nullable|string|max:255',
            'direct_supervisor_id' => 'nullable|exists:users,id',
            'known_allergies' => 'nullable|array',
            'hse_training_history' => 'nullable|array',
            'competency_certificates' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldData = $employee->toArray();
        $newData = $request->except(['password', 'password_confirmation']);

        $employee->update($newData);

        ActivityLog::log('update', 'admin', 'Employee', $employee->id, "Updated employee {$employee->name}");

        return redirect()->route('admin.employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        // Ensure employee belongs to same company
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        if ($employee->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $employeeName = $employee->name;
        $employee->delete();

        ActivityLog::log('delete', 'admin', 'Employee', $employee->id, "Deleted employee {$employeeName}");

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function activate(User $employee)
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $employee->update([
            'is_active' => true,
            'deactivated_at' => null,
            'deactivation_reason' => null,
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        ActivityLog::log('activate', 'admin', 'Employee', $employee->id, "Activated employee {$employee->name}");

        return redirect()->back()->with('success', 'Employee activated successfully.');
    }

    public function deactivate(Request $request, User $employee)
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        if ($employee->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $employee->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivation_reason' => $request->reason,
        ]);

        // End all active sessions
        UserSession::endAllSessionsForUser($employee->id);

        ActivityLog::log('deactivate', 'admin', 'Employee', $employee->id, "Deactivated employee {$employee->name}: {$request->reason}");

        return redirect()->back()->with('success', 'Employee deactivated successfully.');
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = User::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->with(['role', 'department']);

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

