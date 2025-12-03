@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Welcome Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-primary-black mb-2">HSE Management Dashboard</h1>
        <p class="text-lg text-medium-gray">Welcome to your comprehensive safety management overview</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-primary-black">{{ number_format($stats['total_incidents']) }}</span>
            </div>
            <h3 class="text-sm font-medium text-primary-black mb-1">Total Incidents</h3>
            <p class="text-xs text-medium-gray">{{ $stats['open_incidents'] }} open</p>
        </div>

        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-comments text-blue-600 text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-primary-black">{{ number_format($stats['total_toolbox_talks']) }}</span>
            </div>
            <h3 class="text-sm font-medium text-primary-black mb-1">Toolbox Talks</h3>
            <p class="text-xs text-medium-gray">{{ $stats['completed_talks'] }} completed</p>
        </div>

        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-primary-black">{{ number_format($stats['total_attendances']) }}</span>
            </div>
            <h3 class="text-sm font-medium text-primary-black mb-1">Total Attendances</h3>
            <p class="text-xs text-medium-gray">{{ $stats['total_feedback'] }} feedback received</p>
        </div>

        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bullhorn text-purple-600 text-xl"></i>
                </div>
                <span class="text-3xl font-bold text-primary-black">{{ number_format($stats['total_communications']) }}</span>
            </div>
            <h3 class="text-sm font-medium text-primary-black mb-1">Safety Communications</h3>
            <p class="text-xs text-medium-gray">{{ $stats['active_users'] }} active users</p>
        </div>
    </div>

    <!-- Charts Row 1: Incident Trends & Severity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Incident Trends -->
        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-primary-black mb-4">Monthly Incident Trends</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="incidentTrendsChart"></canvas>
            </div>
        </div>

        <!-- Incident Severity Distribution -->
        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-primary-black mb-4">Incident Severity Distribution</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="severityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Incident Status & Toolbox Talk Trends -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Incident Status Distribution -->
        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-primary-black mb-4">Incident Status Distribution</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="incidentStatusChart"></canvas>
            </div>
        </div>

        <!-- Toolbox Talk Trends -->
        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-primary-black mb-4">Toolbox Talk Trends</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="talkTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 3: Weekly Activity & Talk Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Weekly Activity -->
        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-primary-black mb-4">Weekly Activity (Last 8 Weeks)</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="weeklyActivityChart"></canvas>
            </div>
        </div>

        <!-- Toolbox Talk Status -->
        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-primary-black mb-4">Toolbox Talk Status</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="talkStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Department Performance -->
    @if(count($departmentStats ?? []) > 0)
    <div class="bg-white border border-border-gray p-6 rounded-lg mb-8">
        <h3 class="text-lg font-semibold text-primary-black mb-4">Department Performance</h3>
        <div style="position: relative; height: 350px;">
            <canvas id="departmentChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Incidents -->
        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-primary-black">Recent Incidents</h3>
                <a href="{{ route('incidents.index') }}" class="text-blue-600 hover:text-blue-700 text-sm">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentIncidents ?? [] as $incident)
                    <div class="border border-border-gray rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-primary-black">{{ $incident->title ?? $incident->incident_type }}</h4>
                                <p class="text-xs text-medium-gray mt-1">{{ $incident->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded
                                {{ $incident->severity == 'critical' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $incident->severity == 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $incident->severity == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $incident->severity == 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ ucfirst($incident->severity) }}
                            </span>
                        </div>
                        <div class="text-sm text-medium-gray">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $incident->incident_date->format('M d, Y') }}</span>
                            <span class="ml-3"><i class="fas fa-building mr-1"></i>{{ $incident->department->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-medium-gray text-center py-4">No recent incidents</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Toolbox Talks -->
        <div class="bg-white border border-border-gray p-6 rounded-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-primary-black">Recent Toolbox Talks</h3>
                <a href="{{ route('toolbox-talks.index') }}" class="text-blue-600 hover:text-blue-700 text-sm">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentTalks ?? [] as $talk)
                    <div class="border border-border-gray rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-primary-black">{{ $talk->title }}</h4>
                                <p class="text-xs text-medium-gray mt-1">{{ $talk->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded
                                {{ $talk->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $talk->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $talk->status == 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $talk->status)) }}
                            </span>
                        </div>
                        <div class="text-sm text-medium-gray">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $talk->scheduled_date->format('M d, Y') }}</span>
                            <span class="ml-3"><i class="fas fa-building mr-1"></i>{{ $talk->department->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-medium-gray text-center py-4">No recent talks</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Incident Trends Chart
    const incidentTrendsCtx = document.getElementById('incidentTrendsChart');
    if (incidentTrendsCtx) {
        const incidentData = @json($incidentTrends ?? []);
        const incidentLabels = incidentData.map(item => item.month || '');
        const incidentTotal = incidentData.map(item => item.total || 0);
        const incidentOpen = incidentData.map(item => item.open || 0);
        const incidentClosed = incidentData.map(item => item.closed || 0);
        
        new Chart(incidentTrendsCtx, {
            type: 'line',
            data: {
                labels: incidentLabels,
                datasets: [
                    {
                        label: 'Total Incidents',
                        data: incidentTotal,
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Open',
                        data: incidentOpen,
                        borderColor: 'rgb(234, 179, 8)',
                        backgroundColor: 'rgba(234, 179, 8, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Closed',
                        data: incidentClosed,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Incident Severity Distribution
    const severityCtx = document.getElementById('severityChart');
    if (severityCtx) {
        const severityData = @json($severityDistribution ?? []);
        new Chart(severityCtx, {
            type: 'doughnut',
            data: {
                labels: ['Critical', 'High', 'Medium', 'Low'],
                datasets: [{
                    data: [
                        severityData.critical || 0,
                        severityData.high || 0,
                        severityData.medium || 0,
                        severityData.low || 0
                    ],
                    backgroundColor: [
                        'rgb(239, 68, 68)',
                        'rgb(249, 115, 22)',
                        'rgb(234, 179, 8)',
                        'rgb(34, 197, 94)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Incident Status Distribution
    const incidentStatusCtx = document.getElementById('incidentStatusChart');
    if (incidentStatusCtx) {
        const statusData = @json($incidentStatusDistribution ?? []);
        new Chart(incidentStatusCtx, {
            type: 'pie',
            data: {
                labels: ['Reported', 'Open', 'Investigating', 'Resolved', 'Closed'],
                datasets: [{
                    data: [
                        statusData.reported || 0,
                        statusData.open || 0,
                        statusData.investigating || 0,
                        statusData.resolved || 0,
                        statusData.closed || 0
                    ],
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(234, 179, 8)',
                        'rgb(168, 85, 247)',
                        'rgb(34, 197, 94)',
                        'rgb(107, 114, 128)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Toolbox Talk Trends
    const talkTrendsCtx = document.getElementById('talkTrendsChart');
    if (talkTrendsCtx) {
        const talkData = @json($talkTrends ?? []);
        const talkLabels = talkData.map(item => item.month || '');
        const talkTotal = talkData.map(item => item.total || 0);
        const talkCompleted = talkData.map(item => item.completed || 0);
        const talkAttendance = talkData.map(item => parseFloat(item.avg_attendance) || 0);
        
        new Chart(talkTrendsCtx, {
            type: 'line',
            data: {
                labels: talkLabels,
                datasets: [
                    {
                        label: 'Total Talks',
                        data: talkTotal,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Completed',
                        data: talkCompleted,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Avg Attendance %',
                        data: talkAttendance,
                        borderColor: 'rgb(168, 85, 247)',
                        backgroundColor: 'rgba(168, 85, 247, 0.1)',
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, position: 'left' },
                    y1: {
                        beginAtZero: true,
                        max: 100,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { callback: function(value) { return value + '%'; } }
                    }
                }
            }
        });
    }

    // Weekly Activity Chart
    const weeklyCtx = document.getElementById('weeklyActivityChart');
    if (weeklyCtx) {
        const weeklyData = @json($weeklyActivity ?? []);
        const weeklyLabels = weeklyData.map(item => item.week || '');
        const weeklyIncidents = weeklyData.map(item => item.incidents || 0);
        const weeklyTalks = weeklyData.map(item => item.talks || 0);
        
        new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: weeklyLabels,
                datasets: [
                    {
                        label: 'Incidents',
                        data: weeklyIncidents,
                        backgroundColor: 'rgba(239, 68, 68, 0.6)'
                    },
                    {
                        label: 'Toolbox Talks',
                        data: weeklyTalks,
                        backgroundColor: 'rgba(59, 130, 246, 0.6)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Toolbox Talk Status
    const talkStatusCtx = document.getElementById('talkStatusChart');
    if (talkStatusCtx) {
        const talkStatusData = @json($talkStatusDistribution ?? []);
        new Chart(talkStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Scheduled', 'In Progress', 'Completed'],
                datasets: [{
                    data: [
                        talkStatusData.scheduled || 0,
                        talkStatusData.in_progress || 0,
                        talkStatusData.completed || 0
                    ],
                    backgroundColor: [
                        'rgb(234, 179, 8)',
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Department Performance Chart
    const deptCtx = document.getElementById('departmentChart');
    if (deptCtx) {
        const deptData = @json($departmentStats ?? []);
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptData.map(d => d.name),
                datasets: [
                    {
                        label: 'Incidents',
                        data: deptData.map(d => d.incidents),
                        backgroundColor: 'rgba(239, 68, 68, 0.6)',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Open Incidents',
                        data: deptData.map(d => d.open_incidents),
                        backgroundColor: 'rgba(249, 115, 22, 0.6)',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Toolbox Talks',
                        data: deptData.map(d => d.talks),
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        yAxisID: 'y1'
                    },
                    {
                        label: 'Avg Attendance %',
                        data: deptData.map(d => d.avg_attendance),
                        backgroundColor: 'rgba(34, 197, 94, 0.6)',
                        yAxisID: 'y2'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, position: 'left' },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
                    y2: { beginAtZero: true, max: 100, position: 'right', grid: { drawOnChartArea: false }, ticks: { callback: function(value) { return value + '%'; } } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
