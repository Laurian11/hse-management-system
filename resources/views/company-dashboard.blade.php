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
                            <h1 class="text-xl font-medium text-primary-black">{{ $company->name }}</h1>
                            <p class="text-sm text-medium-gray">Company Dashboard</p>
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
            <div class="bg-white border border-border-gray p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-check-circle text-dark-gray text-xl"></i>
                    </div>
                    <span class="text-sm text-medium-gray">Days Safe</span>
                </div>
                <div class="text-3xl font-medium text-primary-black">{{ $dashboardData['days_without_incident'] }}</div>
                <p class="text-sm text-medium-gray mt-2">No incidents recorded</p>
            </div>

            <div class="bg-white border border-border-gray p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-clipboard-check text-dark-gray text-xl"></i>
                    </div>
                    <span class="text-sm text-medium-gray">Inspections</span>
                </div>
                <div class="text-3xl font-medium text-primary-black">{{ $dashboardData['total_inspections'] }}</div>
                <p class="text-sm text-medium-gray mt-2">Total completed</p>
            </div>

            <div class="bg-white border border-border-gray p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-star text-dark-gray text-xl"></i>
                    </div>
                    <span class="text-sm text-medium-gray">Safety Score</span>
                </div>
                <div class="text-3xl font-medium text-primary-black">{{ $dashboardData['safety_score'] }}%</div>
                <p class="text-sm text-medium-gray mt-2">Excellent performance</p>
            </div>

            <div class="bg-white border border-border-gray p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-background-gray border border-border-gray rounded flex items-center justify-center">
                        <i class="fas fa-book text-dark-gray text-xl"></i>
                    </div>
                    <span class="text-sm text-medium-gray">Training</span>
                </div>
                <div class="text-3xl font-medium text-primary-black">{{ $dashboardData['pending_trainings'] }}</div>
                <p class="text-sm text-medium-gray mt-2">Pending sessions</p>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Incident Trends Chart -->
            <div class="lg:col-span-2 bg-white border border-border-gray p-6">
                <h3 class="text-lg font-medium text-primary-black mb-4">Monthly Incident Trends</h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="incidentChart"></canvas>
                </div>
            </div>

            <!-- Announcements -->
            <div class="bg-white border border-border-gray p-6">
                <h3 class="text-lg font-medium text-primary-black mb-4">Safety Announcements</h3>
                <div class="space-y-3">
                    @foreach ($dashboardData['announcements'] as $announcement)
                        <div class="flex items-start space-x-3 p-3 bg-hover-gray border border-border-gray">
                            <i class="fas fa-info-circle text-dark-gray mt-0.5 flex-shrink-0"></i>
                            <p class="text-sm text-medium-gray">{{ $announcement }}</p>
                        </div>
                    @endforeach
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
            const ctx = document.getElementById('incidentChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['January', 'February', 'March', 'April', 'May', 'June'],
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
        });
    </script>
</body>
</html>
