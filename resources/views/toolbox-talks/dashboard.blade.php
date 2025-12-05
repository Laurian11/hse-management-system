@extends('layouts.app')

@section('title', 'Toolbox Talks Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-hard-hat text-blue-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Toolbox Talks Dashboard</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('toolbox-talks.index') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-list mr-2"></i>All Talks
                    </a>
                    <a href="{{ route('toolbox-talks.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>New Talk
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-3 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-gray-600">Total Talks</p>
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ $stats['total_talks'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-2 md:p-3 rounded-full">
                        <i class="fas fa-comments text-blue-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="mt-2 md:mt-4">
                    <span class="text-xs md:text-sm text-green-600">
                        <i class="fas fa-arrow-up mr-1 hidden md:inline"></i>
                        {{ $stats['this_month'] }} this month
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-3 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-gray-600">Completed</p>
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ $stats['completed_this_month'] }}</p>
                    </div>
                    <div class="bg-green-100 p-2 md:p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="mt-2 md:mt-4">
                    <span class="text-xs md:text-sm text-gray-600">
                        {{ $stats['this_month'] > 0 ? round(($stats['completed_this_month'] / $stats['this_month']) * 100) : 0 }}% completion rate
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-3 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-gray-600">Avg Attendance</p>
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ round($stats['avg_attendance_rate']) }}%</p>
                    </div>
                    <div class="bg-purple-100 p-2 md:p-3 rounded-full">
                        <i class="fas fa-users text-purple-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="mt-2 md:mt-4">
                    <span class="text-xs md:text-sm {{ $stats['avg_attendance_rate'] >= 80 ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $stats['avg_attendance_rate'] >= 80 ? 'Good' : 'Needs Improvement' }}
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-3 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm text-gray-600">Avg Feedback</p>
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['avg_feedback_score'], 1) }}</p>
                    </div>
                    <div class="bg-yellow-100 p-2 md:p-3 rounded-full">
                        <i class="fas fa-star text-yellow-600 text-sm md:text-base"></i>
                    </div>
                </div>
                <div class="mt-2 md:mt-4">
                    <span class="text-xs md:text-sm {{ $stats['avg_feedback_score'] >= 4.0 ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $stats['avg_feedback_score'] >= 4.0 ? 'Excellent' : 'Good' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Trends Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trends (Last 6 Months)</h3>
                <div style="position: relative; height: 250px;">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>

            <!-- Status Distribution Pie Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Distribution</h3>
                <div style="position: relative; height: 250px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Weekly Attendance Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Weekly Attendance (Last 8 Weeks)</h3>
                <div style="position: relative; height: 250px;">
                    <canvas id="weeklyAttendanceChart"></canvas>
                </div>
            </div>

            <!-- Talk Type Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Talk Type Distribution</h3>
                <div style="position: relative; height: 250px;">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Department Performance Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Performance</h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>

            <!-- Top Topics Performance -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Topics</h3>
                @if(count($topTopics ?? []) > 0)
                    <div class="space-y-3">
                        @foreach($topTopics as $topic)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-medium text-gray-900">{{ $topic['name'] }}</div>
                                    <div class="text-sm text-gray-600">{{ $topic['count'] }} talks</div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="text-gray-600">Attendance:</span>
                                        <span class="font-medium">{{ number_format($topic['avg_attendance'], 1) }}%</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Feedback:</span>
                                        <span class="font-medium">{{ number_format($topic['avg_feedback'], 1) }}/5</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No topic data available</p>
                @endif
            </div>
        </div>

        <!-- Recent Talks and Upcoming -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Talks -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Talks</h3>
                    <a href="{{ route('toolbox-talks.index') }}" class="text-blue-600 hover:text-blue-700 text-sm">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentTalks as $talk)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $talk->title }}</p>
                                <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                    <span><i class="fas fa-calendar mr-1"></i>{{ $talk->scheduled_date->format('M d, Y') }}</span>
                                    <span><i class="fas fa-building mr-1"></i>{{ $talk->department?->name ?? 'All' }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                @switch($talk->status)
                                    @case('scheduled')
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Scheduled</span>
                                        @break
                                    @case('in_progress')
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                                        @break
                                    @case('completed')
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                        @break
                                    @default
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ $talk->status }}</span>
                                @endswitch
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent talks</p>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Talks -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Upcoming Talks</h3>
                    <a href="{{ route('toolbox-talks.index', ['status' => 'scheduled']) }}" class="text-blue-600 hover:text-blue-700 text-sm">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingTalks as $talk)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-medium text-gray-900 flex-1">{{ $talk->title }}</h4>
                                @if($talk->biometric_required)
                                    <i class="fas fa-fingerprint text-blue-600 ml-2" title="Biometric Required"></i>
                                @endif
                            </div>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2 text-gray-400 w-4"></i>
                                    {{ $talk->scheduled_date->format('M d, Y') }}
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-clock mr-2 text-gray-400 w-4"></i>
                                    {{ $talk->start_time ? $talk->start_time->format('g:i A') : 'Not set' }}
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt mr-2 text-gray-400 w-4"></i>
                                    {{ $talk->location }}
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t">
                                <a href="{{ route('toolbox-talks.show', $talk) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    View Details <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-500">No upcoming talks scheduled</p>
                            <a href="{{ route('toolbox-talks.create') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-700">
                                Schedule a talk <i class="fas fa-plus ml-1"></i>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyTrendsChart');
    if (monthlyCtx) {
        const monthlyData = @json($monthlyTrends ?? []);
        const monthlyLabels = monthlyData.map(item => item.month || '');
        const monthlyTotal = monthlyData.map(item => item.total || 0);
        const monthlyCompleted = monthlyData.map(item => item.completed || 0);
        const monthlyAttendance = monthlyData.map(item => parseFloat(item.avg_attendance) || 0);
        
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [
                    {
                        label: 'Total Talks',
                        data: monthlyTotal,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Completed',
                        data: monthlyCompleted,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Avg Attendance %',
                        data: monthlyAttendance,
                        borderColor: 'rgb(168, 85, 247)',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Talks'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Attendance %'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Status Distribution Pie Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Scheduled', 'In Progress', 'Completed'],
                datasets: [{
                    data: [
                        {{ $statusDistribution['scheduled'] ?? 0 }},
                        {{ $statusDistribution['in_progress'] ?? 0 }},
                        {{ $statusDistribution['completed'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(234, 179, 8)',
                        'rgb(34, 197, 94)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Weekly Attendance Chart
    const weeklyCtx = document.getElementById('weeklyAttendanceChart');
    if (weeklyCtx) {
        new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: @json(array_column($weeklyAttendance ?? [], 'week')),
                datasets: [
                    {
                        label: 'Total Attendances',
                        data: @json(array_column($weeklyAttendance ?? [], 'total')),
                        backgroundColor: 'rgba(59, 130, 246, 0.6)'
                    },
                    {
                        label: 'Present',
                        data: @json(array_column($weeklyAttendance ?? [], 'present')),
                        backgroundColor: 'rgba(34, 197, 94, 0.6)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Talk Type Distribution
    const typeCtx = document.getElementById('typeChart');
    if (typeCtx) {
        const typeData = @json($typeDistribution ?? []);
        new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(typeData),
                datasets: [{
                    data: Object.values(typeData),
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(234, 179, 8)',
                        'rgb(239, 68, 68)',
                        'rgb(168, 85, 247)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Department Performance Chart
    const deptCtx = document.getElementById('departmentChart');
    if (deptCtx) {
        const deptData = @json($departmentPerformance ?? []);
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptData.map(d => d.name),
                datasets: [
                    {
                        label: 'Avg Attendance %',
                        data: deptData.map(d => d.avg_attendance),
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Avg Feedback',
                        data: deptData.map(d => d.avg_feedback),
                        backgroundColor: 'rgba(234, 179, 8, 0.6)',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        position: 'left'
                    },
                    y1: {
                        beginAtZero: true,
                        max: 5,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
