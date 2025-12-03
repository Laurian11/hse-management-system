<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::with(['permissions', 'users']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $roles = $query->latest()->paginate(15);
        $levels = Role::getLevels();

        return view('admin.roles.index', compact('roles', 'levels'));
    }

    public function create()
    {
        $permissions = Permission::active()->get()->groupBy('module');
        $levels = Role::getLevels();

        return view('admin.roles.create', compact('permissions', 'levels'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'level' => 'required|in:super_admin,admin,hse_officer,hod,employee',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'level' => $request->level,
            'default_permissions' => $request->permissions ?? [],
            'is_active' => true,
        ]);

        if ($request->permissions) {
            $role->permissions()->attach($request->permissions);
        }

        ActivityLog::log('create', 'admin', 'Role', $role->id, "Created role {$role->display_name}");

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        
        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()->with('error', 'System roles cannot be edited.');
        }

        $role->load(['permissions']);
        $permissions = Permission::active()->get()->groupBy('module');
        $levels = Role::getLevels();

        return view('admin.roles.edit', compact('role', 'permissions', 'levels'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()->with('error', 'System roles cannot be edited.');
        }

        $validator = Validator::make($request->all(), [
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldData = $role->toArray();
        $newData = $request->all();

        $role->update([
            'display_name' => $request->display_name,
            'description' => $request->description,
            'default_permissions' => $request->permissions ?? [],
        ]);

        if ($request->permissions) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->detach();
        }

        ActivityLog::log('update', 'admin', 'Role', $role->id, "Updated role {$role->display_name}", $oldData, $newData);

        return redirect()->route('admin.roles.show', $role)
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return redirect()->back()->with('error', 'System roles cannot be deleted.');
        }

        if (!$role->canBeDeleted()) {
            return redirect()->back()->with('error', 'Role cannot be deleted. It has assigned users or permissions.');
        }

        $roleName = $role->display_name;
        $role->delete();

        ActivityLog::log('delete', 'admin', 'Role', $role->id, "Deleted role {$roleName}");

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function activate(Role $role)
    {
        if ($role->is_active) {
            return redirect()->back()->with('info', 'Role is already active.');
        }

        $role->update(['is_active' => true]);

        ActivityLog::log('activate', 'admin', 'Role', $role->id, "Activated role {$role->display_name}");

        return redirect()->back()->with('success', 'Role activated successfully.');
    }

    public function deactivate(Role $role)
    {
        if (!$role->is_active) {
            return redirect()->back()->with('info', 'Role is already inactive.');
        }

        if ($role->is_system) {
            return redirect()->back()->with('error', 'System roles cannot be deactivated.');
        }

        $role->update(['is_active' => false]);

        ActivityLog::log('deactivate', 'admin', 'Role', $role->id, "Deactivated role {$role->display_name}");

        return redirect()->back()->with('success', 'Role deactivated successfully.');
    }

    public function duplicate(Role $role)
    {
        $newRole = $role->replicate();
        $newRole->name = $role->name . '_copy_' . time();
        $newRole->display_name = $role->display_name . ' (Copy)';
        $newRole->is_system = false;
        $newRole->save();

        // Copy permissions
        $permissions = $role->permissions->pluck('id')->toArray();
        $newRole->permissions()->attach($permissions);

        ActivityLog::log('duplicate', 'admin', 'Role', $newRole->id, "Duplicated role from {$role->display_name}");

        return redirect()->route('admin.roles.edit', $newRole)
            ->with('success', 'Role duplicated successfully.');
    }
}
