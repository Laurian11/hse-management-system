@extends('layouts.app')

@section('title', 'Department Performance: ' . $department->name)

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.departments.show', $department) }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Department
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Performance: {{ $department->name }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['total_employees'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Incidents</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $metrics['total_incidents'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Toolbox Talks</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $metrics['total_toolbox_talks'] ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Safety Score</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $metrics['safety_score'] ?? 'N/A' }}</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Incidents -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Incidents</h2>
            @if($recentIncidents->count() > 0)
                <div class="space-y-3">
                    @foreach($recentIncidents as $incident)
                        <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $incident->title ?? $incident->incident_type }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $incident->severity }} • {{ $incident->created_at->format('M d, Y') }}
                                </p>
                            </div>
                            <a href="{{ route('incidents.show', $incident) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">No recent incidents</p>
            @endif
        </div>

        <!-- Upcoming Toolbox Talks -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Toolbox Talks</h2>
            @if($upcomingTalks->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingTalks as $talk)
                        <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-comments text-green-600 text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $talk->topic->title ?? 'No Topic' }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $talk->scheduled_date->format('M d, Y H:i') }}
                                </p>
                            </div>
                            <a href="{{ route('toolbox-talks.show', $talk) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500">No upcoming toolbox talks</p>
            @endif
        </div>
    </div>
</div>
@endsection

