@extends('layouts.app')

@section('title', 'Activity Log Dashboard')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-bold text-gray-900">Activity Log Dashboard</h1>
            <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-list mr-2"></i>View All Logs
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Total Logs</p>
                    <p class="text-xl md:text-3xl font-bold text-gray-900 mt-1 md:mt-2">{{ number_format($stats['total_logs']) }}</p>
                </div>
                <div class="w-10 h-10 md:w-14 md:h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list text-blue-600 text-lg md:text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Critical Events</p>
                    <p class="text-xl md:text-3xl font-bold text-red-600 mt-1 md:mt-2">{{ number_format($stats['critical_events']) }}</p>
                </div>
                <div class="w-10 h-10 md:w-14 md:h-14 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg md:text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Login Attempts Today</p>
                    <p class="text-xl md:text-3xl font-bold text-green-600 mt-1 md:mt-2">{{ number_format($stats['login_attempts_today']) }}</p>
                </div>
                <div class="w-10 h-10 md:w-14 md:h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sign-in-alt text-green-600 text-lg md:text-2xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Failed Logins Today</p>
                    <p class="text-xl md:text-3xl font-bold text-yellow-600 mt-1 md:mt-2">{{ number_format($stats['failed_logins_today']) }}</p>
                </div>
                <div class="w-10 h-10 md:w-14 md:h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-yellow-600 text-lg md:text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
            <div class="space-y-3">
                @forelse($recentActivity as $activity)
                    <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full 
                                {{ $activity->action === 'create' ? 'bg-green-100' : 
                                   ($activity->action === 'update' ? 'bg-blue-100' : 
                                   ($activity->action === 'delete' ? 'bg-red-100' : 'bg-gray-100')) }} 
                                flex items-center justify-center">
                                <i class="fas fa-{{ $activity->action === 'create' ? 'plus' : ($activity->action === 'update' ? 'edit' : ($activity->action === 'delete' ? 'trash' : 'info')) }} 
                                    {{ $activity->action === 'create' ? 'text-green-600' : 
                                       ($activity->action === 'update' ? 'text-blue-600' : 
                                       ($activity->action === 'delete' ? 'text-red-600' : 'text-gray-600')) }} 
                                    text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ Str::limit($activity->description, 60) }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $activity->user->name ?? 'System' }} • {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No recent activity</p>
                @endforelse
            </div>
        </div>

        <!-- Critical Events -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Critical Events</h2>
            <div class="space-y-3">
                @forelse($criticalEvents as $event)
                    <div class="flex items-start space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-red-900">{{ Str::limit($event->description, 60) }}</p>
                            <p class="text-xs text-red-700 mt-1">
                                {{ $event->user->name ?? 'System' }} • {{ $event->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <a href="{{ route('admin.activity-logs.show', $event) }}" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-external-link-alt text-xs"></i>
                        </a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No critical events</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Module Activity & Top Users -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Module Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Module Activity (Last 7 Days)</h2>
            <div class="space-y-3">
                @forelse($moduleActivity as $module)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-folder text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $module->module)) }}</p>
                                <p class="text-xs text-gray-500">{{ $module->count }} activities</p>
                            </div>
                        </div>
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($module->count / $moduleActivity->sum('count')) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No module activity</p>
                @endforelse
            </div>
        </div>

        <!-- Top Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Most Active Users (Last 7 Days)</h2>
            <div class="space-y-3">
                @forelse($topUsers as $user)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->user->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $user->count }} activities</p>
                            </div>
                        </div>
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($user->count / $topUsers->max('count')) * 100 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No user activity</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

