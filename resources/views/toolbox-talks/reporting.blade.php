@extends('layouts.app')

@section('title', 'Toolbox Talks Reporting')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Toolbox Talks Reporting</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('toolbox-talks.reports.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-chart-bar mr-2"></i>Comprehensive Reports
                    </a>
                    <a href="{{ route('toolbox-talks.export-reporting-excel') }}" class="px-4 py-2 text-green-700 bg-white border border-green-300 rounded-lg hover:bg-green-50">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('toolbox-talks.index') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Talks
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-600">Total Talks</div>
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_talks']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-600">Completion Rate</div>
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-green-600">{{ number_format($stats['completion_rate'], 1) }}%</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-600">Participation Rate</div>
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['participation_rate'], 1) }}%</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-600">Satisfaction Score</div>
                    <div class="bg-yellow-100 p-2 rounded-lg">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                </div>
                <div class="text-3xl font-bold text-purple-600">{{ number_format($stats['satisfaction_score'], 1) }}/5</div>
            </div>
        </div>

        <!-- Quick Links to Comprehensive Reports -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>Need Detailed Reports?
                    </h3>
                    <p class="text-sm text-gray-700 mb-4">
                        Access comprehensive reports with advanced filtering, period-based analysis, and Excel/PDF exports.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('toolbox-talks.reports.department-attendance') }}" class="px-4 py-2 bg-white text-blue-700 border border-blue-300 rounded-lg hover:bg-blue-50 text-sm">
                            <i class="fas fa-building mr-2"></i>Department Reports
                        </a>
                        <a href="{{ route('toolbox-talks.reports.employee-attendance') }}" class="px-4 py-2 bg-white text-green-700 border border-green-300 rounded-lg hover:bg-green-50 text-sm">
                            <i class="fas fa-users mr-2"></i>Employee Reports
                        </a>
                        <a href="{{ route('toolbox-talks.reports.period') }}" class="px-4 py-2 bg-white text-purple-700 border border-purple-300 rounded-lg hover:bg-purple-50 text-sm">
                            <i class="fas fa-calendar-alt mr-2"></i>Period Reports
                        </a>
                        <a href="{{ route('toolbox-talks.reports.companies') }}" class="px-4 py-2 bg-white text-orange-700 border border-orange-300 rounded-lg hover:bg-orange-50 text-sm">
                            <i class="fas fa-industry mr-2"></i>Companies Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Attendance Trends -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>Attendance Trends (Last 6 Months)
                    </h2>
                </div>
                <div class="space-y-3">
                    @forelse($attendanceTrends ?? [] as $trend)
                        <div class="flex items-center gap-3">
                            <div class="w-20 text-xs text-gray-600 font-medium">{{ $trend['month'] }}</div>
                            <div class="flex-1 bg-gray-200 rounded-full h-4">
                                <div class="bg-blue-600 h-4 rounded-full flex items-center justify-end pr-2" 
                                     style="width: {{ min(100, $trend['avg_attendance']) }}%">
                                    @if($trend['avg_attendance'] > 10)
                                        <span class="text-xs text-white font-medium">{{ number_format($trend['avg_attendance'], 1) }}%</span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-20 text-xs text-gray-600 text-right">{{ $trend['total_talks'] }} talks</div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-chart-line text-gray-400 text-4xl mb-2"></i>
                            <p>No attendance data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Topic Performance -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-book text-purple-600 mr-2"></i>Top Topics Performance
                    </h2>
                </div>
                <div class="space-y-3">
                    @forelse($topics ?? [] as $topic)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-medium text-gray-900">{{ $topic['name'] }}</div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $topic['count'] }} talks
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-sm mt-3">
                                <div class="flex items-center">
                                    <i class="fas fa-users text-blue-600 mr-2"></i>
                                    <span class="text-gray-600">Avg Attendance:</span>
                                    <span class="font-medium text-gray-900 ml-2">{{ number_format($topic['avg_attendance'], 1) }}%</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-600 mr-2"></i>
                                    <span class="text-gray-600">Avg Rating:</span>
                                    <span class="font-medium text-gray-900 ml-2">{{ number_format($topic['avg_rating'], 1) }}/5</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-book text-gray-400 text-4xl mb-2"></i>
                            <p>No topic data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Department Comparison -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-building text-green-600 mr-2"></i>Department Comparison
                    </h2>
                    <a href="{{ route('toolbox-talks.reports.department-attendance') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View Details <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($departmentPerformance ?? [] as $dept)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div>
                                <span class="text-sm font-medium text-gray-900">{{ $dept['name'] }}</span>
                                <div class="text-xs text-gray-600 mt-1">{{ $dept['total_talks'] }} talks</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-32 bg-gray-200 rounded-full h-3">
                                    <div class="bg-blue-600 h-3 rounded-full" style="width: {{ min(100, $dept['avg_attendance']) }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 w-16 text-right">{{ number_format($dept['avg_attendance'], 1) }}%</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-building text-gray-400 text-4xl mb-2"></i>
                            <p>No department data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-history text-orange-600 mr-2"></i>Recent Activity
                    </h2>
                </div>
                <div class="space-y-3">
                    @forelse($recentTalks ?? [] as $talk)
                        <div class="border-l-4 border-blue-500 pl-4 py-2 hover:bg-gray-50 rounded-r transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900 mb-1">{{ $talk->title }}</div>
                                    <div class="text-xs text-gray-600 mb-2">
                                        <i class="fas fa-clock mr-1"></i>{{ $talk->updated_at->diffForHumans() }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($talk->department)
                                            <span class="text-xs text-gray-600">
                                                <i class="fas fa-building mr-1"></i>{{ $talk->department->name }}
                                            </span>
                                        @endif
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            {{ $talk->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $talk->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $talk->status == 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $talk->status == 'overdue' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $talk->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $talk->status)) }}
                                        </span>
                                    </div>
                                </div>
                                <a href="{{ route('toolbox-talks.show', $talk) }}" class="text-blue-600 hover:text-blue-700 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-8">
                            <i class="fas fa-history text-gray-400 text-4xl mb-2"></i>
                            <p>No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
