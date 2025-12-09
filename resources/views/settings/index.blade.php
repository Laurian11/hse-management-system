@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-cog text-gray-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Settings Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Notifications & Alerts -->
            <a href="{{ route('notifications.rules.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-bell text-orange-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Notifications & Alerts</h3>
                <p class="text-sm text-gray-600 mb-4">Configure notification rules and escalation matrices</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Rules: {{ $stats['notification_rules'] }}</span>
                    <span class="text-gray-500">Matrices: {{ $stats['escalation_matrices'] }}</span>
                </div>
            </a>

            <!-- Biometric Attendance -->
            <a href="{{ route('biometric-devices.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <i class="fas fa-fingerprint text-indigo-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Biometric Attendance</h3>
                <p class="text-sm text-gray-600 mb-4">Manage devices, track attendance, and generate reports</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Devices: {{ $stats['active_devices'] }}/{{ $stats['biometric_devices'] }}</span>
                    <span class="text-gray-500">Today: {{ $stats['total_attendance_today'] }}</span>
                </div>
            </a>

            <!-- Administration -->
            @if(auth()->user()->role && (auth()->user()->role->name === 'super_admin' || auth()->user()->role->name === 'admin'))
            <a href="{{ route('admin.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gray-100 p-3 rounded-full">
                        <i class="fas fa-cog text-gray-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Administration</h3>
                <p class="text-sm text-gray-600 mb-4">Manage users, companies, departments, and system settings</p>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Users: {{ $stats['total_users'] }}</span>
                    <span class="text-gray-500">Employees: {{ $stats['total_employees'] }}</span>
                </div>
            </a>
            @else
            <div class="bg-white rounded-lg shadow p-6 opacity-50">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-gray-100 p-3 rounded-full">
                        <i class="fas fa-lock text-gray-400 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-400 mb-2">Administration</h3>
                <p class="text-sm text-gray-400 mb-4">Admin access required</p>
            </div>
            @endif
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Notifications -->
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Notifications</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('notifications.rules.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-cog mr-1"></i>Notification Rules
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('notifications.escalation-matrices.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-up mr-1"></i>Escalation Matrices
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Biometric Attendance -->
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Biometric Attendance</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('biometric-devices.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-server mr-1"></i>Devices
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('daily-attendance.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-clock mr-1"></i>Daily Attendance
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('daily-attendance.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-chart-pie mr-1"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('manpower-reports.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-chart-bar mr-1"></i>Reports
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Administration -->
                @if(auth()->user()->role && (auth()->user()->role->name === 'super_admin' || auth()->user()->role->name === 'admin'))
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Administration</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.employees.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-user-tie mr-1"></i>Employees
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-users mr-1"></i>Users
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.companies.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-building mr-1"></i>Companies
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.departments.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-sitemap mr-1"></i>Departments
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.roles.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-user-shield mr-1"></i>Roles & Permissions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.activity-logs.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-history mr-1"></i>Activity Logs
                            </a>
                        </li>
                    </ul>
                </div>
                @endif

                <!-- System Info -->
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-2">System Information</h3>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li>Companies: {{ $stats['total_companies'] }}</li>
                        <li>Departments: {{ $stats['total_departments'] }}</li>
                        <li>Total Users: {{ $stats['total_users'] }}</li>
                        <li>Total Employees: {{ $stats['total_employees'] }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

