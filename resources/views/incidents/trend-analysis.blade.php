@extends('layouts.app')

@section('title', 'Incident Trend Analysis')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('incidents.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Incidents
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Incident Trend Analysis</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('incidents.dashboard') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chart-bar mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('incidents.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Report Incident
                    </a>
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
                        <p class="text-sm font-medium text-gray-600">Total Incidents</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_incidents'] }}</p>
                    </div>
                    <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Injury/Illness</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['injury_illness'] }}</p>
                    </div>
                    <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-injured text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Property Damage</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ $stats['property_damage'] }}</p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tools text-orange-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Near Miss</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['near_miss'] }}</p>
                    </div>
                    <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Trends Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Monthly Trends (Last 12 Months)</h2>
                <div style="height: 300px;">
                    <canvas id="monthlyTrendsChart"></canvas>
                </div>
            </div>

            <!-- Severity Distribution Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Severity Distribution</h2>
                <div style="height: 300px;">
                    <canvas id="severityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Event Type Distribution Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Type Distribution</h2>
                <div style="height: 300px;">
                    <canvas id="eventTypeChart"></canvas>
                </div>
            </div>

            <!-- Status Distribution Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Distribution</h2>
                <div style="height: 300px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Department Performance -->
        @if($departmentStats->count() > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Department Performance</h2>
                <div style="height: 350px;">
                    <canvas id="departmentChart"></canvas>
                </div>
            </div>
        @endif

        <!-- Top Root Causes -->
        @if($topRootCauses->count() > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Top Root Causes</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Root Cause</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occurrences</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $totalRootCauseCount = $topRootCauses->sum('count');
                            @endphp
                            @foreach($topRootCauses as $index => $rootCause)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                            {{ $index < 3 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($rootCause->root_cause, 100) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $rootCause->count }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-3">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($rootCause->count / $totalRootCauseCount) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600">{{ number_format(($rootCause->count / $totalRootCauseCount) * 100, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Key Metrics Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Open Incidents</h3>
                <p class="text-3xl font-bold text-red-600">{{ $stats['open'] }}</p>
                <p class="text-sm text-gray-500 mt-2">Requiring attention</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Under Investigation</h3>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['investigating'] }}</p>
                <p class="text-sm text-gray-500 mt-2">Active investigations</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500 mb-2">Closed Incidents</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['closed'] }}</p>
                <p class="text-sm text-gray-500 mt-2">Resolved incidents</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Monthly Trends Chart
const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
const monthlyTrendsData = @json($monthlyTrends);
new Chart(monthlyTrendsCtx, {
    type: 'line',
    data: {
        labels: monthlyTrendsData.map(d => d.month),
        datasets: [
            {
                label: 'Total',
                data: monthlyTrendsData.map(d => d.total),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Injury/Illness',
                data: monthlyTrendsData.map(d => d.injury_illness),
                borderColor: 'rgb(220, 38, 38)',
                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Property Damage',
                data: monthlyTrendsData.map(d => d.property_damage),
                borderColor: 'rgb(249, 115, 22)',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                tension: 0.4,
                fill: true
            },
            {
                label: 'Near Miss',
                data: monthlyTrendsData.map(d => d.near_miss),
                borderColor: 'rgb(234, 179, 8)',
                backgroundColor: 'rgba(234, 179, 8, 0.1)',
                tension: 0.4,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Severity Distribution Chart
const severityCtx = document.getElementById('severityChart').getContext('2d');
const severityData = @json($severityDistribution);
new Chart(severityCtx, {
    type: 'doughnut',
    data: {
        labels: ['Critical', 'High', 'Medium', 'Low'],
        datasets: [{
            data: [
                severityData.critical,
                severityData.high,
                severityData.medium,
                severityData.low
            ],
            backgroundColor: [
                'rgb(220, 38, 38)',
                'rgb(249, 115, 22)',
                'rgb(234, 179, 8)',
                'rgb(34, 197, 94)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Event Type Distribution Chart
const eventTypeCtx = document.getElementById('eventTypeChart').getContext('2d');
const eventTypeData = @json($eventTypeDistribution);
new Chart(eventTypeCtx, {
    type: 'bar',
    data: {
        labels: ['Injury/Illness', 'Property Damage', 'Near Miss', 'Environmental', 'Other'],
        datasets: [{
            label: 'Count',
            data: [
                eventTypeData.injury_illness,
                eventTypeData.property_damage,
                eventTypeData.near_miss,
                eventTypeData.environmental || 0,
                eventTypeData.other || 0
            ],
            backgroundColor: [
                'rgba(220, 38, 38, 0.8)',
                'rgba(249, 115, 22, 0.8)',
                'rgba(234, 179, 8, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(107, 114, 128, 0.8)'
            ],
            borderColor: [
                'rgb(220, 38, 38)',
                'rgb(249, 115, 22)',
                'rgb(234, 179, 8)',
                'rgb(34, 197, 94)',
                'rgb(107, 114, 128)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = {
    open: {{ $stats['open'] }},
    investigating: {{ $stats['investigating'] }},
    closed: {{ $stats['closed'] }}
};
new Chart(statusCtx, {
    type: 'pie',
    data: {
        labels: ['Open', 'Investigating', 'Closed'],
        datasets: [{
            data: [
                statusData.open,
                statusData.investigating,
                statusData.closed
            ],
            backgroundColor: [
                'rgb(220, 38, 38)',
                'rgb(234, 179, 8)',
                'rgb(34, 197, 94)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Department Performance Chart
@if($departmentStats->count() > 0)
const departmentCtx = document.getElementById('departmentChart').getContext('2d');
const departmentData = @json($departmentStats->values());
new Chart(departmentCtx, {
    type: 'bar',
    data: {
        labels: departmentData.map(d => d.name),
        datasets: [
            {
                label: 'Total',
                data: departmentData.map(d => d.total),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            },
            {
                label: 'Open',
                data: departmentData.map(d => d.open),
                backgroundColor: 'rgba(220, 38, 38, 0.8)',
                borderColor: 'rgb(220, 38, 38)',
                borderWidth: 1
            },
            {
                label: 'Closed',
                data: departmentData.map(d => d.closed),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
@endif
</script>
@endsection

