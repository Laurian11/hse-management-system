@extends('layouts.app')

@section('title', 'Company Statistics: ' . $company->name)

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.companies.show', $company) }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Company
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Statistics: {{ $company->name }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @php
        $stats = $company->getDetailedStatistics();
    @endphp

    <!-- License Information -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">License Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500">License Type</p>
                <p class="text-xl font-semibold text-gray-900">{{ $stats['license']['type'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="text-xl font-semibold {{ $stats['license']['is_expired'] ? 'text-red-600' : 'text-green-600' }}">
                    {{ $stats['license']['status'] }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Expiry Date</p>
                <p class="text-xl font-semibold text-gray-900">{{ $stats['license']['expiry_date'] }}</p>
                @if($stats['license']['days_until_expiry'] < 30)
                <p class="text-sm text-red-600 mt-1">{{ abs($stats['license']['days_until_expiry']) }} days {{ $stats['license']['is_expired'] ? 'expired' : 'remaining' }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">User Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-500">Total Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['users']['total'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Active Users</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['users']['active'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Usage</p>
                <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($stats['users']['usage_percentage'], 100) }}%"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ number_format($stats['users']['usage_percentage'], 1) }}%</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['users']['remaining'] }} remaining of {{ $stats['users']['limit'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Inactive Users</p>
                <p class="text-2xl font-bold text-gray-600">{{ $stats['users']['inactive'] }}</p>
            </div>
        </div>
    </div>

    <!-- Department Statistics -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Department Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-500">Total Departments</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['departments']['total'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Active Departments</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['departments']['active'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Usage</p>
                <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min($stats['departments']['usage_percentage'], 100) }}%"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ number_format($stats['departments']['usage_percentage'], 1) }}%</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">{{ $stats['departments']['remaining'] }} remaining of {{ $stats['departments']['limit'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Inactive Departments</p>
                <p class="text-2xl font-bold text-gray-600">{{ $stats['departments']['inactive'] }}</p>
            </div>
        </div>
    </div>

    <!-- HSE Metrics -->
    @php
        $hseMetrics = $company->getHSEMetrics();
    @endphp
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">HSE Metrics</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-500">HSE Policies</p>
                <p class="text-2xl font-bold text-gray-900">{{ $hseMetrics['policies_count'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Safety Standards</p>
                <p class="text-2xl font-bold text-gray-900">{{ $hseMetrics['standards_count'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Certifications</p>
                <p class="text-2xl font-bold text-gray-900">{{ $hseMetrics['certifications_count'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Compliance Score</p>
                <div class="flex items-center space-x-2">
                    <p class="text-2xl font-bold {{ $hseMetrics['compliance_score'] >= 80 ? 'text-green-600' : ($hseMetrics['compliance_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $hseMetrics['compliance_score'] }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500">Industry Type</p>
                <p class="text-lg font-medium text-gray-900">{{ $hseMetrics['industry_type'] }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Employee Count</p>
                <p class="text-lg font-medium text-gray-900">{{ $hseMetrics['employee_count'] ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
