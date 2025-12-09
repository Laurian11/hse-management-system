<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\Department;
use App\Models\Permission;
use App\Models\ActivityLog;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'company', 'department', 'directSupervisor'])
                     ->whereNull('deleted_at');

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
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

        $users = $query->latest()->paginate(15);
        $roles = Role::active()->get();
        $companies = Company::active()->get();
        $departments = Department::active()->get();

        return view('admin.users.index', compact('users', 'roles', 'companies', 'departments'));
    }

    public function create()
    {
        $roles = Role::active()->get();
        $companies = Company::active()->get();
        $departments = Department::active()->get();
        $users = User::where('is_active', true)->get();

        return view('admin.users.create', compact('roles', 'companies', 'departments', 'users'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'company_id' => 'required|exists:companies,id',
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
        $data['password'] = Hash::make($request->password);
        $data['password_changed_at'] = now();
        $data['is_active'] = true;

        $user = User::create($data);

        ActivityLog::log('create', 'admin', 'User', $user->id, "Created user {$user->name}", null, $data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['role', 'company', 'department', 'directSupervisor']);
        
        $recentActivity = ActivityLog::byUser($user->id)
                                    ->latest()
                                    ->limit(20)
                                    ->get();

        return view('admin.users.show', compact('user', 'recentActivity'));
    }

    public function edit(User $user)
    {
        $roles = Role::active()->get();
        $companies = Company::active()->get();
        $departments = Department::where('company_id', $user->company_id)->active()->get();
        $users = User::where('is_active', true)->where('id', '!=', $user->id)->get();

        return view('admin.users.edit', compact('user', 'roles', 'companies', 'departments', 'users'));
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id_number' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
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

        $oldData = $user->toArray();
        $newData = $request->except(['password', 'password_confirmation']);

        $user->update($newData);

        ActivityLog::log('update', 'admin', 'User', $user->id, "Updated user {$user->name}", $oldData, $newData);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log('delete', 'admin', 'User', $user->id, "Deleted user {$userName}");

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function activate(User $user)
    {
        $user->update([
            'is_active' => true,
            'deactivated_at' => null,
            'deactivation_reason' => null,
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);

        ActivityLog::log('activate', 'admin', 'User', $user->id, "Activated user {$user->name}");

        return redirect()->back()->with('success', 'User activated successfully.');
    }

    public function deactivate(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $user->update([
            'is_active' => false,
            'deactivated_at' => now(),
            'deactivation_reason' => $request->reason,
        ]);

        // End all active sessions
        UserSession::endAllSessionsForUser($user->id);

        ActivityLog::log('deactivate', 'admin', 'User', $user->id, "Deactivated user {$user->name}: {$request->reason}");

        return redirect()->back()->with('success', 'User deactivated successfully.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
            'must_change_password' => true,
        ]);

        // End all active sessions to force re-login
        UserSession::endAllSessionsForUser($user->id);

        ActivityLog::log('password_reset', 'admin', 'User', $user->id, "Reset password for {$user->name}");

        return redirect()->back()->with('success', 'Password reset successfully. User must change password on next login.');
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            if ($extension === 'csv') {
                $data = array_map('str_getcsv', file($file->getRealPath()));
                $header = array_shift($data);
            } else {
                // For Excel files, would need maatwebsite/excel package
                return redirect()->back()->with('error', 'Excel import requires maatwebsite/excel package. Please use CSV format.');
            }

            $imported = 0;
            $errors = [];

            foreach ($data as $row) {
                if (count($row) < 2) continue; // Skip invalid rows
                
                try {
                    $userData = array_combine($header, $row);
                    
                    // Validate required fields
                    if (empty($userData['name']) || empty($userData['email'])) {
                        $errors[] = "Row missing required fields: " . implode(', ', $row);
                        continue;
                    }

                    // Check if user exists
                    if (User::where('email', $userData['email'])->exists()) {
                        $errors[] = "User with email {$userData['email']} already exists";
                        continue;
                    }

                    // Create user
                    User::create([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => Hash::make($userData['password'] ?? 'password123'),
                        'company_id' => $userData['company_id'] ?? auth()->user()->company_id,
                        'role_id' => $userData['role_id'] ?? null,
                        'department_id' => $userData['department_id'] ?? null,
                        'is_active' => true,
                        'password_changed_at' => now(),
                        'must_change_password' => true,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error importing row: " . $e->getMessage();
                }
            }

            ActivityLog::log('bulk_import', 'admin', 'User', null, "Bulk imported {$imported} users");

            $message = "Successfully imported {$imported} users.";
            if (count($errors) > 0) {
                $message .= " " . count($errors) . " errors occurred.";
            }

            return redirect()->back()->with(count($errors) > 0 ? 'warning' : 'success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = User::with(['role', 'company', 'department']);

        // Apply same filters as index
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $users = $query->get();

        ActivityLog::log('export', 'admin', 'User', null, 'Exported users list');

        // Generate CSV
        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Name', 'Email', 'Role', 'Company', 'Department', 'Phone', 'Status', 'Created At']);
            
            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->role->display_name ?? $user->role->name ?? 'N/A',
                    $user->company->name ?? 'N/A',
                    $user->department->name ?? 'N/A',
                    $user->phone ?? 'N/A',
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show user permissions management page
     */
    public function permissions(User $user)
    {
        $user->load(['role', 'company', 'department']);
        $permissions = Permission::active()->get()->groupBy('module');
        $userPermissions = $user->permissions ?? [];
        
        // Get all modules
        $modules = Permission::getModules();
        
        return view('admin.users.permissions', compact('user', 'permissions', 'userPermissions', 'modules'));
    }

    /**
     * Update user permissions
     */
    public function updatePermissions(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldPermissions = $user->permissions ?? [];
        $newPermissions = $request->permissions ?? [];

        // Validate that all permission names exist
        $validPermissions = Permission::whereIn('name', $newPermissions)
            ->pluck('name')
            ->toArray();
        
        $invalidPermissions = array_diff($newPermissions, $validPermissions);
        if (count($invalidPermissions) > 0) {
            return redirect()->back()
                ->withErrors(['permissions' => 'Invalid permissions: ' . implode(', ', $invalidPermissions)])
                ->withInput();
        }

        $user->syncPermissions($validPermissions);

        ActivityLog::log('permission_change', 'admin', 'User', $user->id, 
            "Updated permissions for {$user->name}", 
            ['permissions' => $oldPermissions], 
            ['permissions' => $validPermissions]);

        return redirect()->route('admin.users.permissions', $user)
            ->with('success', 'User permissions updated successfully.');
    }
}
