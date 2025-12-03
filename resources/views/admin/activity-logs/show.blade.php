@extends('layouts.app')

@section('title', 'Activity Log Details')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.activity-logs.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Logs
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Activity Log Details</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <!-- Header -->
        <div class="border-b border-gray-200 pb-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $activityLog->description }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $activityLog->created_at->format('F j, Y g:i A') }}</p>
                </div>
                @if($activityLog->is_critical)
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Critical Event
                    </span>
                @endif
            </div>
        </div>

        <!-- Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Action</h3>
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                    {{ $activityLog->action === 'create' ? 'bg-green-100 text-green-800' : 
                       ($activityLog->action === 'update' ? 'bg-blue-100 text-blue-800' : 
                       ($activityLog->action === 'delete' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                    {{ ucfirst($activityLog->action) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Module</h3>
                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                    {{ ucfirst(str_replace('_', ' ', $activityLog->module)) }}
                </span>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">User</h3>
                @if($activityLog->user)
                    <div class="flex items-center space-x-2">
                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                            <i class="fas fa-user text-gray-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $activityLog->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $activityLog->user->email }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-900">System</p>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Company</h3>
                <p class="text-sm text-gray-900">{{ $activityLog->company->name ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">IP Address</h3>
                <p class="text-sm text-gray-900">{{ $activityLog->ip_address ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">User Agent</h3>
                <p class="text-sm text-gray-900">{{ Str::limit($activityLog->user_agent ?? 'N/A', 50) }}</p>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
            <p class="text-gray-900">{{ $activityLog->description }}</p>
        </div>

        <!-- Changes (if available) -->
        @if($activityLog->old_values || $activityLog->new_values)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($activityLog->old_values)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Old Values</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif
                @if($activityLog->new_values)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">New Values</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

