@extends('layouts.app')

@section('title', 'Company Statistics: ' . $company->name)

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.companies.show', $company) }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Company
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Statistics: {{ $company->name }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['active_users'] ?? 0 }} active</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Departments</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_departments'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['active_departments'] ?? 0 }} active</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sitemap text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">License Usage</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($licenseInfo['usage_percentage'] ?? 0, 1) }}%</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $licenseInfo['users_used'] ?? 0 }}/{{ $licenseInfo['users_limit'] ?? 0 }} users</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-pie text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Days Until Expiry</p>
                    <p class="text-3xl font-bold {{ ($licenseInfo['days_until_expiry'] ?? 999) <= 30 ? 'text-red-600' : 'text-gray-900' }} mt-2">
                        {{ $licenseInfo['days_until_expiry'] ?? 'N/A' }}
                    </p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- HSE Metrics -->
    @if(isset($hseMetrics))
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">HSE Metrics</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Total Incidents</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $hseMetrics['total_incidents'] ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Toolbox Talks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $hseMetrics['total_toolbox_talks'] ?? 0 }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Safety Communications</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $hseMetrics['total_safety_communications'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Detailed Statistics -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Detailed Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">User Statistics</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Users:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_users'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Active Users:</span>
                        <span class="text-sm font-medium text-green-600">{{ $stats['active_users'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Inactive Users:</span>
                        <span class="text-sm font-medium text-red-600">{{ ($stats['total_users'] ?? 0) - ($stats['active_users'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Department Statistics</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Departments:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['total_departments'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Active Departments:</span>
                        <span class="text-sm font-medium text-green-600">{{ $stats['active_departments'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Inactive Departments:</span>
                        <span class="text-sm font-medium text-red-600">{{ ($stats['total_departments'] ?? 0) - ($stats['active_departments'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

