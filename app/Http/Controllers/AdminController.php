<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\Department;
use App\Models\Incident;
use App\Models\ToolboxTalk;
use App\Models\ActivityLog;

class AdminController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $companyId = $user->company_id;

        // Overall Statistics
        $stats = [
            'total_employees' => User::where('company_id', $companyId)->whereNull('deleted_at')->count(),
            'active_employees' => User::where('company_id', $companyId)->where('is_active', true)->whereNull('deleted_at')->count(),
            'inactive_employees' => User::where('company_id', $companyId)->where('is_active', false)->whereNull('deleted_at')->count(),
            'total_roles' => Role::where('is_active', true)->count(),
            'total_departments' => Department::where('company_id', $companyId)->where('is_active', true)->count(),
            'total_companies' => Company::where('is_active', true)->count(),
            'recent_incidents' => Incident::where('company_id', $companyId)->latest()->limit(5)->count(),
            'recent_toolbox_talks' => ToolboxTalk::where('company_id', $companyId)->latest()->limit(5)->count(),
        ];

        // Employee Statistics by Department
        $departmentStats = Department::where('company_id', $companyId)
            ->where('is_active', true)
            ->withCount(['users' => function($query) {
                $query->where('is_active', true)->whereNull('deleted_at');
            }])
            ->get()
            ->map(function($dept) {
                return [
                    'name' => $dept->name,
                    'employee_count' => $dept->users_count,
                ];
            })
            ->sortByDesc('employee_count')
            ->take(10);

        // Employee Statistics by Role
        $roleStats = Role::where('is_active', true)
            ->withCount(['users' => function($query) use ($companyId) {
                $query->where('company_id', $companyId)->where('is_active', true)->whereNull('deleted_at');
            }])
            ->get()
            ->map(function($role) {
                return [
                    'name' => $role->display_name ?? $role->name,
                    'employee_count' => $role->users_count,
                ];
            })
            ->sortByDesc('employee_count')
            ->take(10);

        // Employment Type Distribution
        $employmentTypeStats = [
            'full_time' => User::where('company_id', $companyId)->where('employment_type', 'full_time')->whereNull('deleted_at')->count(),
            'contractor' => User::where('company_id', $companyId)->where('employment_type', 'contractor')->whereNull('deleted_at')->count(),
            'visitor' => User::where('company_id', $companyId)->where('employment_type', 'visitor')->whereNull('deleted_at')->count(),
        ];

        // Recent Activity
        $recentActivity = ActivityLog::where('module', 'admin')
            ->orWhere('module', 'users')
            ->latest()
            ->limit(10)
            ->with(['user', 'company'])
            ->get();

        // Recent Employees
        $recentEmployees = User::where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->with(['role', 'department', 'company'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.index', compact(
            'stats',
            'departmentStats',
            'roleStats',
            'employmentTypeStats',
            'recentActivity',
            'recentEmployees'
        ));
    }
}

