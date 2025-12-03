@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.companies.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Companies
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $company->name }}</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.companies.edit', $company) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.companies.statistics', $company) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-chart-bar mr-2"></i>Statistics
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Company Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Company Name</h3>
                        <p class="text-lg font-medium text-gray-900">{{ $company->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                        @if($company->is_active)
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </div>
                    @if($company->description)
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Description</h3>
                            <p class="text-gray-900">{{ $company->description }}</p>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Industry Type</h3>
                        <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $company->industry_type ?? 'N/A')) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Country</h3>
                        <p class="text-gray-900">{{ $company->country ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- License Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">License Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">License Type</h3>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                            {{ $company->license_type === 'enterprise' ? 'bg-purple-100 text-purple-800' : 
                               ($company->license_type === 'professional' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($company->license_type ?? 'N/A') }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">License Expiry</h3>
                        <p class="text-gray-900">
                            {{ $company->license_expiry ? $company->license_expiry->format('M d, Y') : 'N/A' }}
                            @if($company->license_expiry && $company->license_expiry->isPast())
                                <span class="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">Expired</span>
                            @elseif($company->license_expiry && $company->license_expiry->diffInDays(now()) <= 30)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full">Expiring Soon</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Max Users</h3>
                        <p class="text-gray-900">{{ number_format($company->max_users ?? 0) }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Max Departments</h3>
                        <p class="text-gray-900">{{ number_format($company->max_departments ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            @if($company->email || $company->phone || $company->address)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($company->email)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Email</h3>
                                <p class="text-gray-900">{{ $company->email }}</p>
                            </div>
                        @endif
                        @if($company->phone)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Phone</h3>
                                <p class="text-gray-900">{{ $company->phone }}</p>
                            </div>
                        @endif
                        @if($company->website)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Website</h3>
                                <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    {{ $company->website }}
                                </a>
                            </div>
                        @endif
                        @if($company->address)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Address</h3>
                                <p class="text-gray-900">{{ $company->address }}</p>
                                @if($company->city || $company->state || $company->postal_code)
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $company->city }}{{ $company->state ? ', ' . $company->state : '' }} {{ $company->postal_code ?? '' }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_users'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $statistics['active_users'] }} active</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Departments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_departments'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $statistics['active_departments'] }} active</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">License Usage</p>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $statistics['license_usage_percentage'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ number_format($statistics['license_usage_percentage'], 1) }}% used</p>
                        </div>
                    </div>
                    @if($statistics['days_until_expiry'] !== null)
                        <div>
                            <p class="text-sm text-gray-500">Days Until Expiry</p>
                            <p class="text-2xl font-bold {{ $statistics['days_until_expiry'] <= 30 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $statistics['days_until_expiry'] }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.companies.users', $company) }}" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-users mr-2"></i>View Users
                    </a>
                    <a href="{{ route('admin.companies.departments', $company) }}" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-sitemap mr-2"></i>View Departments
                    </a>
                    <a href="{{ route('admin.companies.statistics', $company) }}" class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>View Statistics
                    </a>
                    @if($company->is_active)
                        <form action="{{ route('admin.companies.deactivate', $company) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-ban mr-2"></i>Deactivate
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.companies.activate', $company) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>Activate
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

