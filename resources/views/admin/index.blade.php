@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Administration Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage employees, roles, departments, and system settings</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.employees.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>Add Employee
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Employees -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_employees']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['active_employees'] }} active</p>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-700 mb-1">Total Employees</h3>
                <div class="flex items-center mt-2">
                    <a href="{{ route('admin.employees.index') }}" class="text-xs text-blue-600 hover:text-blue-700">
                        Manage Employees <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Active Employees -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['active_employees']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['inactive_employees'] }} inactive</p>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-700 mb-1">Active Employees</h3>
                <div class="flex items-center mt-2">
                    <span class="text-xs text-gray-500">Currently working</span>
                </div>
            </div>

            <!-- Departments -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-purple-600 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_departments']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Departments</p>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-700 mb-1">Departments</h3>
                <div class="flex items-center mt-2">
                    <a href="{{ route('admin.departments.index') }}" class="text-xs text-blue-600 hover:text-blue-700">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Roles -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tag text-orange-600 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_roles']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Active roles</p>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-700 mb-1">Roles</h3>
                <div class="flex items-center mt-2">
                    <a href="{{ route('admin.roles.index') }}" class="text-xs text-blue-600 hover:text-blue-700">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <a href="{{ route('admin.employees.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all hover:border-blue-500 border-2 border-transparent">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Employees</h3>
                        <p class="text-sm text-gray-500">Manage employee records</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.roles.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all hover:border-purple-500 border-2 border-transparent">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-user-tag text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Roles</h3>
                        <p class="text-sm text-gray-500">Manage user roles</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.departments.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all hover:border-green-500 border-2 border-transparent">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-building text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Departments</h3>
                        <p class="text-sm text-gray-500">Manage departments</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.companies.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-all hover:border-orange-500 border-2 border-transparent">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-building text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Companies</h3>
                        <p class="text-sm text-gray-500">Manage companies</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Department Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employees by Department</h3>
                <div class="space-y-3">
                    @forelse($departmentStats as $dept)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $dept['name'] }}</span>
                                    <span class="text-sm text-gray-500">{{ $dept['employee_count'] }} employees</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $stats['total_employees'] > 0 ? ($dept['employee_count'] / $stats['total_employees'] * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No department data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Role Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Employees by Role</h3>
                <div class="space-y-3">
                    @forelse($roleStats as $role)
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-900">{{ $role['name'] }}</span>
                                    <span class="text-sm text-gray-500">{{ $role['employee_count'] }} employees</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $stats['total_employees'] > 0 ? ($role['employee_count'] / $stats['total_employees'] * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No role data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Employment Type Distribution -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Employment Type Distribution</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($employmentTypeStats['full_time']) }}</div>
                    <div class="text-sm text-gray-600">Full Time</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600 mb-2">{{ number_format($employmentTypeStats['contractor']) }}</div>
                    <div class="text-sm text-gray-600">Contractor</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600 mb-2">{{ number_format($employmentTypeStats['visitor']) }}</div>
                    <div class="text-sm text-gray-600">Visitor</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Recent Employees -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Employees -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Employees</h3>
                    <a href="{{ route('admin.employees.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentEmployees as $employee)
                        <a href="{{ route('admin.employees.show', $employee) }}" class="block border border-gray-200 rounded-lg p-4 hover:bg-gray-50 hover:border-blue-300 transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $employee->name }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $employee->email }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                <span><i class="fas fa-briefcase mr-1"></i>{{ $employee->job_title ?? 'N/A' }}</span>
                                <span><i class="fas fa-building mr-1"></i>{{ $employee->department->name ?? 'N/A' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-users text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">No employees found</p>
                            <a href="{{ route('admin.employees.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:text-blue-700">
                                Add employee <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                    <a href="{{ route('admin.activity-logs.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentActivity as $activity)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $activity->description }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $activity->user->name ?? 'System' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($activity->action) }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>{{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent activity</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

