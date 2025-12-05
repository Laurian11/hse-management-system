@extends('layouts.app')

@section('title', 'Risk Assessment Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Risk Assessment & Hazard Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Proactive Safety Management Dashboard</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('risk-assessment.hazards.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Identify Hazard
                    </a>
                    <a href="{{ route('risk-assessment.risk-assessments.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-clipboard-list mr-2"></i>New Assessment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
            <!-- Hazards -->
            <div class="bg-white rounded-lg shadow p-3 md:p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_hazards']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $stats['identified_hazards'] }} identified</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Total Hazards</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('risk-assessment.hazards.index') }}" class="text-xs text-blue-600 hover:text-blue-700">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Risk Assessments -->
            <div class="bg-white rounded-lg shadow p-3 md:p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-orange-600 text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_risk_assessments']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $stats['high_risk_assessments'] }} high risk</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Risk Assessments</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('risk-assessment.risk-assessments.index') }}" class="text-xs text-blue-600 hover:text-blue-700">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Control Measures -->
            <div class="bg-white rounded-lg shadow p-3 md:p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-green-600 text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_control_measures']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $stats['verified_controls'] }} verified</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Control Measures</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('risk-assessment.control-measures.index') }}" class="text-xs text-blue-600 hover:text-blue-700">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Overdue Reviews -->
            <div class="bg-white rounded-lg shadow p-3 md:p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sync-alt text-yellow-600 text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['overdue_reviews']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $stats['due_for_review'] }} due</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Overdue Reviews</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('risk-assessment.risk-reviews.index') }}?overdue=1" class="text-xs text-blue-600 hover:text-blue-700">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Risk Level Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Risk Level Distribution</h2>
                <div style="height: 300px;">
                    <canvas id="riskLevelChart"></canvas>
                </div>
            </div>

            <!-- Hazard Category Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Hazard Category Distribution</h2>
                <div style="height: 300px;">
                    <canvas id="hazardCategoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Control Measures Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Control Type Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Control Type Distribution</h2>
                <div style="height: 300px;">
                    <canvas id="controlTypeChart"></canvas>
                </div>
            </div>

            <!-- Control Status Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Control Status Distribution</h2>
                <div style="height: 300px;">
                    <canvas id="controlStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Monthly Risk Assessments Trend</h2>
            <div style="height: 300px;">
                <canvas id="monthlyTrendsChart"></canvas>
            </div>
        </div>

        <!-- Top High Risks and Overdue Reviews -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Top 5 High-Risk Assessments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Top High-Risk Assessments</h2>
                    <a href="{{ route('risk-assessment.risk-assessments.index') }}?risk_level=high" class="text-sm text-blue-600 hover:text-blue-700">
                        View All
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($topHighRisks as $risk)
                        <div class="border-l-4 border-red-500 bg-red-50 p-4 rounded">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $risk->title }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $risk->reference_number }}</p>
                                    @if($risk->department)
                                        <p class="text-xs text-gray-500 mt-1">{{ $risk->department->name }}</p>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $risk->getRiskLevelColor() }}">
                                        {{ strtoupper($risk->risk_level) }} ({{ $risk->risk_score }})
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('risk-assessment.risk-assessments.show', $risk) }}" class="text-xs text-blue-600 hover:text-blue-700 mt-2 inline-block">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No high-risk assessments found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Overdue Reviews -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Overdue Reviews</h2>
                    <a href="{{ route('risk-assessment.risk-reviews.index') }}?overdue=1" class="text-sm text-blue-600 hover:text-blue-700">
                        View All
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($overdueReviews as $review)
                        <div class="border-l-4 border-yellow-500 bg-yellow-50 p-4 rounded">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $review->riskAssessment->title ?? 'N/A' }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $review->reference_number }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Due: {{ $review->due_date->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        {{ strtoupper($review->review_type) }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('risk-assessment.risk-reviews.show', $review) }}" class="text-xs text-blue-600 hover:text-blue-700 mt-2 inline-block">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No overdue reviews.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Hazards -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Hazards</h2>
                    <a href="{{ route('risk-assessment.hazards.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View All
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentHazards as $hazard)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $hazard->title }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $hazard->reference_number }} • {{ $hazard->created_at->diffForHumans() }}</p>
                            </div>
                            <a href="{{ route('risk-assessment.hazards.show', $hazard) }}" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No recent hazards.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Risk Assessments -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Risk Assessments</h2>
                    <a href="{{ route('risk-assessment.risk-assessments.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View All
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentRiskAssessments as $assessment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $assessment->title }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $assessment->reference_number }} • {{ $assessment->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $assessment->getRiskLevelColor() }}">
                                    {{ strtoupper($assessment->risk_level) }}
                                </span>
                                <a href="{{ route('risk-assessment.risk-assessments.show', $assessment) }}" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No recent risk assessments.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Risk Level Distribution Chart
    const riskLevelCtx = document.getElementById('riskLevelChart').getContext('2d');
    new Chart(riskLevelCtx, {
        type: 'doughnut',
        data: {
            labels: ['Low', 'Medium', 'High', 'Critical', 'Extreme'],
            datasets: [{
                data: [
                    {{ $riskLevelDistribution['low'] ?? 0 }},
                    {{ $riskLevelDistribution['medium'] ?? 0 }},
                    {{ $riskLevelDistribution['high'] ?? 0 }},
                    {{ $riskLevelDistribution['critical'] ?? 0 }},
                    {{ $riskLevelDistribution['extreme'] ?? 0 }}
                ],
                backgroundColor: ['#10b981', '#f59e0b', '#f97316', '#ef4444', '#991b1b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Hazard Category Chart
    const hazardCategoryCtx = document.getElementById('hazardCategoryChart').getContext('2d');
    new Chart(hazardCategoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($hazardCategoryDistribution->toArray())) !!},
            datasets: [{
                label: 'Hazards',
                data: {!! json_encode(array_values($hazardCategoryDistribution->toArray())) !!},
                backgroundColor: '#f97316'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Control Type Chart
    const controlTypeCtx = document.getElementById('controlTypeChart').getContext('2d');
    new Chart(controlTypeCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($controlTypeDistribution->toArray())) !!},
            datasets: [{
                data: {!! json_encode(array_values($controlTypeDistribution->toArray())) !!},
                backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#6366f1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Control Status Chart
    const controlStatusCtx = document.getElementById('controlStatusChart').getContext('2d');
    new Chart(controlStatusCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($controlStatusDistribution->toArray())) !!},
            datasets: [{
                label: 'Controls',
                data: {!! json_encode(array_values($controlStatusDistribution->toArray())) !!},
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Monthly Trends Chart
    const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(monthlyTrendsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyTrends, 'month')) !!},
            datasets: [{
                label: 'Risk Assessments',
                data: {!! json_encode(array_column($monthlyTrends, 'total')) !!},
                borderColor: '#f97316',
                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
@endsection

