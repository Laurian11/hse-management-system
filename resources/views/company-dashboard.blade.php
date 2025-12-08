<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->name }} - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'primary-black': '#000000',
                        'dark-gray': '#1a1a1a',
                        'medium-gray': '#666666',
                        'light-gray': '#999999',
                        'background-gray': '#f5f5f5',
                        'border-gray': '#e0e0e0',
                        'hover-gray': '#f0f0f0',
                        'accent-gray': '#333333',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-inter bg-white min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-border-gray">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button onclick="window.location.href='{{ route('landing') }}'" class="text-dark-gray hover:text-primary-black flex items-center space-x-2">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Home</span>
                    </button>
                    <div class="h-8 w-px bg-border-gray"></div>
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-primary-black rounded flex items-center justify-center">
                            <i class="fas fa-building text-white text-lg"></i>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h1 class="text-xl font-medium text-primary-black">{{ $company->name }}</h1>
                                @if(isset($dashboardData['is_parent_company']) && $dashboardData['is_parent_company'])
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full" title="Parent Company - Data includes all sister companies">
                                        <i class="fas fa-crown"></i> Parent
                                    </span>
                                @elseif(isset($dashboardData['is_sister_company']) && $dashboardData['is_sister_company'])
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full" title="Sister Company">
                                        <i class="fas fa-link"></i> Sister
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-medium-gray">
                                Company Dashboard
                                @if(isset($dashboardData['is_parent_company']) && $dashboardData['is_parent_company'] && isset($dashboardData['company_group_count']) && $dashboardData['company_group_count'] > 1)
                                    <span class="text-xs">(Aggregated data from {{ $dashboardData['company_group_count'] }} companies)</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-sm text-medium-gray">
                    Last updated: {{ now()->format('M j, Y g:i A') }}
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
        <!-- Key Metrics -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white border border-border-gray p-6 hover:border-dark-gray transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-check-circle text-dark-gray text-xl"></i>
                    </div>
                    <span class="text-sm text-medium-gray">Days Safe</span>
                </div>
                <div class="text-3xl font-medium text-primary-black">{{ number_format($dashboardData['days_without_incident']) }}</div>
                <p class="text-sm text-medium-gray mt-2">No incidents recorded</p>
            </div>

            <div class="bg-white border border-border-gray p-6 hover:border-dark-gray transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-dark-gray text-xl"></i>
                    </div>
                    <span class="text-sm text-medium-gray">Inspections</span>
                </div>
                <div class="text-3xl font-medium text-primary-black">{{ number_format($dashboardData['total_inspections']) }}</div>
                <p class="text-sm text-medium-gray mt-2">Risk Assessments & JSAs</p>
            </div>

            <div class="bg-white border border-border-gray p-6 hover:border-dark-gray transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-star text-dark-gray text-xl"></i>
                    </div>
                    <span class="text-sm text-medium-gray">Safety Score</span>
                </div>
                <div class="text-3xl font-medium text-primary-black">{{ $dashboardData['safety_score'] }}%</div>
                <p class="text-sm text-medium-gray mt-2">
                    @if($dashboardData['safety_score'] >= 90)
                        Excellent
                    @elseif($dashboardData['safety_score'] >= 75)
                        Good
                    @elseif($dashboardData['safety_score'] >= 60)
                        Fair
                    @else
                        Needs Improvement
                    @endif
                </p>
            </div>

            <div class="bg-white border border-border-gray p-6 hover:border-dark-gray transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-book text-dark-gray text-xl"></i>
                    </div>
                    <span class="text-sm text-medium-gray">Training</span>
                </div>
                <div class="text-3xl font-medium text-primary-black">{{ number_format($dashboardData['pending_trainings']) }}</div>
                <p class="text-sm text-medium-gray mt-2">Pending sessions</p>
            </div>
        </section>

        <!-- Additional Statistics Grid -->
        <section class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white border border-border-gray p-4 text-center">
                <div class="text-2xl font-medium text-primary-black">{{ number_format($dashboardData['total_incidents']) }}</div>
                <p class="text-xs text-medium-gray mt-1">Total Incidents</p>
                @if($dashboardData['open_incidents'] > 0)
                    <p class="text-xs text-red-600 mt-1">{{ $dashboardData['open_incidents'] }} open</p>
                @endif
            </div>
            <div class="bg-white border border-border-gray p-4 text-center">
                <div class="text-2xl font-medium text-primary-black">{{ number_format($dashboardData['total_toolbox_talks']) }}</div>
                <p class="text-xs text-medium-gray mt-1">Toolbox Talks</p>
                <p class="text-xs text-medium-gray mt-1">{{ number_format($dashboardData['completed_toolbox_talks']) }} completed</p>
            </div>
            <div class="bg-white border border-border-gray p-4 text-center">
                <div class="text-2xl font-medium text-primary-black">{{ number_format($dashboardData['total_hazards']) }}</div>
                <p class="text-xs text-medium-gray mt-1">Hazards</p>
            </div>
            <div class="bg-white border border-border-gray p-4 text-center">
                <div class="text-2xl font-medium text-primary-black">{{ number_format($dashboardData['total_capas']) }}</div>
                <p class="text-xs text-medium-gray mt-1">CAPAs</p>
                @if($dashboardData['open_capas'] > 0)
                    <p class="text-xs text-amber-600 mt-1">{{ $dashboardData['open_capas'] }} open</p>
                @endif
            </div>
            <div class="bg-white border border-border-gray p-4 text-center">
                <div class="text-2xl font-medium text-primary-black">{{ number_format($dashboardData['total_ppe_items']) }}</div>
                <p class="text-xs text-medium-gray mt-1">PPE Items</p>
                @if($dashboardData['low_stock_ppe'] > 0)
                    <p class="text-xs text-red-600 mt-1">{{ $dashboardData['low_stock_ppe'] }} low stock</p>
                @endif
            </div>
            <div class="bg-white border border-border-gray p-4 text-center">
                <div class="text-2xl font-medium text-primary-black">{{ number_format($dashboardData['total_training_sessions']) }}</div>
                <p class="text-xs text-medium-gray mt-1">Training Sessions</p>
                <p class="text-xs text-medium-gray mt-1">{{ number_format($dashboardData['completed_training_sessions']) }} completed</p>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Incident Trends Chart -->
            <div class="lg:col-span-2 bg-white border border-border-gray p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-primary-black">Monthly Incident Trends</h3>
                    <div class="flex items-center space-x-2 text-sm text-medium-gray">
                        <span>Last 6 months</span>
                    </div>
                </div>
                <div class="relative" style="height: 300px;">
                    <canvas id="incidentChart"></canvas>
                </div>
            </div>

            <!-- Announcements -->
            <div class="bg-white border border-border-gray p-6">
                <h3 class="text-lg font-medium text-primary-black mb-4">Safety Announcements</h3>
                <div class="space-y-3">
                    @foreach ($dashboardData['announcements'] as $announcement)
                        <div class="flex items-start space-x-3 p-3 bg-hover-gray border border-border-gray hover:border-dark-gray transition-colors">
                            <i class="fas fa-info-circle text-dark-gray mt-0.5 flex-shrink-0"></i>
                            <p class="text-sm text-medium-gray">{{ $announcement }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Severity Distribution -->
            <div class="bg-white border border-border-gray p-6">
                <h3 class="text-lg font-medium text-primary-black mb-4">Incident Severity Distribution</h3>
                <div class="relative" style="height: 250px;">
                    <canvas id="severityChart"></canvas>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="bg-white border border-border-gray p-6">
                <h3 class="text-lg font-medium text-primary-black mb-4">Incident Status Distribution</h3>
                <div class="relative" style="height: 250px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Toolbox Talks Trend -->
        <div class="bg-white border border-border-gray p-6 mb-8">
            <h3 class="text-lg font-medium text-primary-black mb-4">Toolbox Talk Trends</h3>
            <div class="relative" style="height: 250px;">
                <canvas id="toolboxChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Recent Incidents -->
            <div class="bg-white border border-border-gray p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-primary-black">Recent Incidents</h3>
                    <span class="text-xs text-medium-gray">{{ $dashboardData['recent_incidents']->count() }} items</span>
                </div>
                <div class="space-y-3">
                    @forelse($dashboardData['recent_incidents'] as $incident)
                        <div class="p-3 bg-hover-gray border border-border-gray hover:border-dark-gray transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-primary-black">{{ \Illuminate\Support\Str::limit($incident->title ?? $incident->event_type, 40) }}</p>
                                    <p class="text-xs text-medium-gray mt-1">
                                        {{ $incident->incident_date ? \Carbon\Carbon::parse($incident->incident_date)->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($incident->severity == 'critical') bg-red-100 text-red-800
                                    @elseif($incident->severity == 'high') bg-orange-100 text-orange-800
                                    @elseif($incident->severity == 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($incident->severity) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-medium-gray text-center py-4">No recent incidents</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Toolbox Talks -->
            <div class="bg-white border border-border-gray p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-primary-black">Recent Toolbox Talks</h3>
                    <span class="text-xs text-medium-gray">{{ $dashboardData['recent_toolbox_talks']->count() }} items</span>
                </div>
                <div class="space-y-3">
                    @forelse($dashboardData['recent_toolbox_talks'] as $talk)
                        <div class="p-3 bg-hover-gray border border-border-gray hover:border-dark-gray transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-primary-black">{{ \Illuminate\Support\Str::limit($talk->topic->title ?? 'Toolbox Talk', 40) }}</p>
                                    <p class="text-xs text-medium-gray mt-1">
                                        {{ $talk->scheduled_date ? \Carbon\Carbon::parse($talk->scheduled_date)->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($talk->status == 'completed') bg-green-100 text-green-800
                                    @elseif($talk->status == 'in_progress') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $talk->status)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-medium-gray text-center py-4">No recent toolbox talks</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Risk Assessments -->
            <div class="bg-white border border-border-gray p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-primary-black">Recent Risk Assessments</h3>
                    <span class="text-xs text-medium-gray">{{ $dashboardData['recent_risk_assessments']->count() }} items</span>
                </div>
                <div class="space-y-3">
                    @forelse($dashboardData['recent_risk_assessments'] as $assessment)
                        <div class="p-3 bg-hover-gray border border-border-gray hover:border-dark-gray transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-primary-black">{{ \Illuminate\Support\Str::limit($assessment->title, 40) }}</p>
                                    <p class="text-xs text-medium-gray mt-1">
                                        {{ $assessment->assessment_date ? \Carbon\Carbon::parse($assessment->assessment_date)->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($assessment->risk_level == 'extreme' || $assessment->risk_level == 'critical') bg-red-100 text-red-800
                                    @elseif($assessment->risk_level == 'high') bg-orange-100 text-orange-800
                                    @elseif($assessment->risk_level == 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ ucfirst($assessment->risk_level) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-medium-gray text-center py-4">No recent risk assessments</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <section class="mt-8 bg-white border border-border-gray p-6">
            <h3 class="text-lg font-medium text-primary-black mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button class="flex items-center space-x-3 p-4 border border-border-gray hover:border-dark-gray transition-colors">
                    <div class="w-10 h-10 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-plus text-dark-gray"></i>
                    </div>
                    <div class="text-left">
                        <p class="font-medium text-primary-black">Report Incident</p>
                        <p class="text-sm text-medium-gray">Submit new incident report</p>
                    </div>
                </button>

                <button class="flex items-center space-x-3 p-4 border border-border-gray hover:border-dark-gray transition-colors">
                    <div class="w-10 h-10 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-calendar-check text-dark-gray"></i>
                    </div>
                    <div class="text-left">
                        <p class="font-medium text-primary-black">Schedule Inspection</p>
                        <p class="text-sm text-medium-gray">Book safety inspection</p>
                    </div>
                </button>

                <button class="flex items-center space-x-3 p-4 border border-border-gray hover:border-dark-gray transition-colors">
                    <div class="w-10 h-10 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-dark-gray"></i>
                    </div>
                    <div class="text-left">
                        <p class="font-medium text-primary-black">Training Materials</p>
                        <p class="text-sm text-medium-gray">Access safety resources</p>
                    </div>
                </button>
            </div>
        </section>
    </main>

    <script>
        // Chart configuration
        document.addEventListener('DOMContentLoaded', function() {
            // Incident Trends Chart
            const incidentCtx = document.getElementById('incidentChart');
            if (incidentCtx) {
                new Chart(incidentCtx, {
                    type: 'line',
                    data: {
                        labels: @json($dashboardData['month_labels']),
                        datasets: [{
                            label: 'Incidents',
                            data: @json($dashboardData['monthly_incidents']),
                            borderColor: '#000000',
                            backgroundColor: 'rgba(0, 0, 0, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#000000',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#000000',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: '#666666',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Incidents: ' + context.parsed.y;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#666666',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e0e0e0',
                                    borderDash: [5, 5]
                                },
                                ticks: {
                                    color: '#666666',
                                    font: {
                                        size: 12
                                    },
                                    stepSize: 1,
                                    precision: 0
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }

            // Severity Distribution Chart
            const severityCtx = document.getElementById('severityChart');
            if (severityCtx) {
                const severityData = @json($dashboardData['severity_distribution']);
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
                                '#dc2626',
                                '#ea580c',
                                '#eab308',
                                '#22c55e'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    color: '#666666',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#000000',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: '#666666',
                                borderWidth: 1,
                                padding: 12
                            }
                        }
                    }
                });
            }

            // Status Distribution Chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                const statusData = @json($dashboardData['incident_status_distribution']);
                new Chart(statusCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Reported', 'Open', 'Investigating', 'Resolved', 'Closed'],
                        datasets: [{
                            label: 'Incidents',
                            data: [
                                statusData.reported || 0,
                                statusData.open || 0,
                                statusData.investigating || 0,
                                statusData.resolved || 0,
                                statusData.closed || 0
                            ],
                            backgroundColor: '#000000',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#000000',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: '#666666',
                                borderWidth: 1,
                                padding: 12
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#666666',
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e0e0e0',
                                    borderDash: [5, 5]
                                },
                                ticks: {
                                    color: '#666666',
                                    font: {
                                        size: 12
                                    },
                                    stepSize: 1,
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // Toolbox Talk Trends Chart
            const toolboxCtx = document.getElementById('toolboxChart');
            if (toolboxCtx) {
                const toolboxData = @json($dashboardData['toolbox_talk_trends']);
                new Chart(toolboxCtx, {
                    type: 'bar',
                    data: {
                        labels: toolboxData.map(item => item.month),
                        datasets: [
                            {
                                label: 'Total',
                                data: toolboxData.map(item => item.total),
                                backgroundColor: '#000000',
                                borderRadius: 4
                            },
                            {
                                label: 'Completed',
                                data: toolboxData.map(item => item.completed),
                                backgroundColor: '#22c55e',
                                borderRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    color: '#666666',
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#000000',
                                titleColor: '#ffffff',
                                bodyColor: '#ffffff',
                                borderColor: '#666666',
                                borderWidth: 1,
                                padding: 12
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#666666',
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#e0e0e0',
                                    borderDash: [5, 5]
                                },
                                ticks: {
                                    color: '#666666',
                                    font: {
                                        size: 12
                                    },
                                    stepSize: 1,
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
