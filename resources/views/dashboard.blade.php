@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    .stat-card {
        @apply bg-white border border-gray-300 hover:border-[#0066CC] transition-all p-3 md:p-6;
    }
    .stat-icon {
        @apply w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center;
    }
    .stat-icon i {
        @apply text-lg md:text-2xl;
    }
    .stat-number {
        @apply text-xl md:text-3xl font-bold text-gray-900;
    }
    .stat-label {
        @apply text-xs md:text-sm font-medium text-gray-700 mb-1;
    }
    .stat-meta {
        @apply text-xs text-gray-500 mt-0.5 md:mt-1;
    }
    .stat-link {
        @apply text-xs text-[#0066CC] hover:underline;
    }
    .stat-link i {
        @apply hidden md:inline;
    }
</style>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">HSE Management Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Welcome back, {{ Auth::user()->name }} • {{ now()->format('l, F j, Y') }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('incidents.create') }}" class="px-4 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3] transition-colors">
                        <i class="fas fa-plus mr-2"></i>Report Incident
                    </a>
                    <a href="{{ route('toolbox-talks.create') }}" class="px-4 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3] transition-colors">
                        <i class="fas fa-comments mr-2"></i>Schedule Talk
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Key Metrics Row 1 -->
        <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6">
            <!-- Total Incidents -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_incidents']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $stats['open_incidents'] }} open</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Total Incidents</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('incidents.index') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Toolbox Talks -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-comments text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_toolbox_talks']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $stats['completed_talks'] }} completed</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Toolbox Talks</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('toolbox-talks.index') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Total Attendances -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-users text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_attendances']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $stats['total_feedback'] }} feedback</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Total Attendances</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('toolbox-talks.attendance') }}" class="text-xs text-blue-600 hover:text-blue-700">
                        View Details <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Safety Communications -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-bullhorn text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_communications']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $stats['active_users'] }} active users</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Safety Communications</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('safety-communications.index') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Key Metrics Row 2: Risk Assessment, Training, PPE -->
        <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-6">
            <!-- Risk Assessments -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_risk_assessments']) }}</p>
                        <p class="text-xs text-[#CC0000] mt-0.5 md:mt-1">{{ $stats['high_risk_assessments'] }} high risk</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Risk Assessments</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('risk-assessment.risk-assessments.index') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- JSAs -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-file-alt text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_jsas']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 md:mt-1">{{ $stats['approved_jsas'] }} approved</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Job Safety Analyses</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('risk-assessment.jsas.index') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Training Sessions -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_training_sessions']) }}</p>
                        <p class="text-xs text-[#0066CC] mt-0.5 md:mt-1">{{ $stats['upcoming_sessions'] }} upcoming</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Training Sessions</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('training.training-sessions.index') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- PPE Items -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-hard-hat text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_ppe_items']) }}</p>
                        <p class="text-xs text-[#CC0000] mt-0.5 md:mt-1">{{ $stats['low_stock_ppe'] }} low stock</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">PPE Items</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('ppe.items.index') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Key Metrics Row 3: Alerts & Warnings -->
        <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
            <!-- Open CAPAs -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all {{ $stats['overdue_capas'] > 0 ? 'border-l-4 border-l-[#CC0000]' : '' }}">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-tasks text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['open_capas']) }}</p>
                        <p class="text-xs {{ $stats['overdue_capas'] > 0 ? 'text-[#CC0000]' : 'text-gray-500' }} mt-0.5 md:mt-1">{{ $stats['overdue_capas'] }} overdue</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Open CAPAs</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('incidents.index') }}?tab=capas" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Training Needs -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-book text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_training_needs']) }}</p>
                        <p class="text-xs text-[#FF9900] mt-0.5 md:mt-1">{{ $stats['pending_training_needs'] }} pending</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Training Needs</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('training.training-needs.index') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Expiring Certificates -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all {{ $stats['expiring_certificates'] > 0 ? 'border-l-4 border-l-[#FF9900]' : '' }}">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-certificate text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['total_certificates']) }}</p>
                        <p class="text-xs {{ $stats['expiring_certificates'] > 0 ? 'text-[#FF9900]' : 'text-gray-500' }} mt-0.5 md:mt-1">{{ $stats['expiring_certificates'] }} expiring</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Certificates</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('training.dashboard') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>

            <!-- Expiring PPE -->
            <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all {{ $stats['expiring_ppe'] > 0 ? 'border-l-4 border-l-[#FF9900]' : '' }}">
                <div class="flex items-center justify-between mb-2 md:mb-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-black text-lg md:text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xl md:text-3xl font-bold text-gray-900">{{ number_format($stats['active_ppe_issuances']) }}</p>
                        <p class="text-xs {{ $stats['expiring_ppe'] > 0 ? 'text-[#FF9900]' : 'text-gray-500' }} mt-0.5 md:mt-1">{{ $stats['expiring_ppe'] }} expiring</p>
                    </div>
                </div>
                <h3 class="text-xs md:text-sm font-medium text-gray-700 mb-1">Active PPE Issuances</h3>
                <div class="flex items-center mt-1 md:mt-2">
                    <a href="{{ route('ppe.issuances.index') }}" class="text-xs text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1 hidden md:inline"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Safety Score Card -->
            <div class="bg-white border border-gray-300 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Safety Score</p>
                        <p class="text-4xl font-bold text-black">{{ $stats['safety_score'] }}%</p>
                    </div>
                    <div class="w-16 h-16 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-shield-alt text-3xl text-black"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full bg-[#F5F5F5] border border-gray-300 h-2">
                        <div class="bg-[#0066CC] h-2" style="width: {{ $stats['safety_score'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Days Without Incident -->
            <div class="bg-white border border-gray-300 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Days Without Incident</p>
                        <p class="text-4xl font-bold text-black">{{ number_format($stats['days_without_incident']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-3xl text-black"></i>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                        @php
                            $lastIncident = \App\Models\Incident::where('company_id', Auth::user()->company_id)
                                ->orderBy('incident_date', 'desc')
                                ->first();
                        @endphp
                    @if($lastIncident)
                        Last incident: {{ $lastIncident->incident_date->format('M d, Y') }}
                    @else
                        No incidents recorded
                    @endif
                </p>
            </div>

            <!-- Compliance Rate -->
            <div class="bg-white border border-gray-300 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Talk Completion Rate</p>
                        <p class="text-4xl font-bold text-black">
                            {{ $stats['total_toolbox_talks'] > 0 ? number_format(($stats['completed_talks'] / $stats['total_toolbox_talks']) * 100, 1) : 0 }}%
                        </p>
                    </div>
                    <div class="w-16 h-16 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                        <i class="fas fa-chart-line text-3xl text-black"></i>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    {{ $stats['completed_talks'] }} of {{ $stats['total_toolbox_talks'] }} talks completed
                </p>
            </div>
        </div>

        <!-- Charts Row 1: Incident Trends & Severity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Incident Trends -->
            <div class="bg-white border border-gray-300 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Monthly Incident Trends</h3>
                    <a href="{{ route('incidents.trend-analysis') }}" class="text-sm text-[#0066CC] hover:underline">
                        View Details <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div style="height: 300px;">
                    <canvas id="incidentTrendsChart"></canvas>
                </div>
            </div>

            <!-- Incident Severity Distribution -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Incident Severity Distribution</h3>
                <div style="height: 300px;">
                    <canvas id="severityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2: Incident Status & Toolbox Talk Trends -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Incident Status Distribution -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Incident Status Distribution</h3>
                <div style="height: 300px;">
                    <canvas id="incidentStatusChart"></canvas>
                </div>
            </div>

            <!-- Toolbox Talk Trends -->
            <div class="bg-white border border-gray-300 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Toolbox Talk Trends</h3>
                    <a href="{{ route('toolbox-talks.dashboard') }}" class="text-sm text-[#0066CC] hover:underline">
                        View Details <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div style="height: 300px;">
                    <canvas id="talkTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 3: Weekly Activity & Talk Status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Weekly Activity -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Weekly Activity (Last 8 Weeks)</h3>
                <div style="height: 300px;">
                    <canvas id="weeklyActivityChart"></canvas>
                </div>
            </div>

            <!-- Toolbox Talk Status -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Toolbox Talk Status</h3>
                <div style="height: 300px;">
                    <canvas id="talkStatusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Department Performance -->
        @if(count($departmentStats ?? []) > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Department Performance</h3>
            <div style="height: 350px;">
                <canvas id="departmentChart"></canvas>
            </div>
        </div>
        @endif

        <!-- Recent Activity Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Incidents -->
            <div class="bg-white border border-gray-300 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Incidents</h3>
                    <a href="{{ route('incidents.index') }}" class="text-sm text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentIncidents ?? [] as $incident)
                        <a href="{{ route('incidents.show', $incident) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-black">{{ $incident->title ?? $incident->incident_type }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $incident->reference_number }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $incident->severity == 'critical' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                    {{ $incident->severity == 'high' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                    {{ $incident->severity == 'medium' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                    {{ $incident->severity == 'low' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}">
                                    {{ ucfirst($incident->severity) }}
                                </span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $incident->incident_date->format('M d, Y') }}</span>
                                <span><i class="fas fa-building mr-1"></i>{{ $incident->department->name ?? 'N/A' }}</span>
                                <span class="px-2 py-0.5 text-xs border border-gray-300
                                    {{ $incident->status == 'open' || $incident->status == 'reported' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                    {{ $incident->status == 'investigating' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                    {{ in_array($incident->status, ['closed', 'resolved']) ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}">
                                    {{ ucfirst($incident->status) }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">No recent incidents</p>
                            <a href="{{ route('incidents.create') }}" class="mt-3 inline-block text-sm text-[#0066CC] hover:underline">
                                Report an incident <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Toolbox Talks -->
            <div class="bg-white border border-gray-300 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Toolbox Talks</h3>
                    <a href="{{ route('toolbox-talks.index') }}" class="text-sm text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentTalks ?? [] as $talk)
                        <a href="{{ route('toolbox-talks.show', $talk) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-black">{{ $talk->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $talk->reference_number ?? 'N/A' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $talk->status == 'completed' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}
                                    {{ $talk->status == 'in_progress' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                    {{ $talk->status == 'scheduled' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $talk->status)) }}
                                </span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $talk->scheduled_date->format('M d, Y') }}</span>
                                <span><i class="fas fa-building mr-1"></i>{{ $talk->department->name ?? 'N/A' }}</span>
                                @if($talk->attendance_rate)
                                    <span><i class="fas fa-users mr-1"></i>{{ number_format($talk->attendance_rate, 1) }}% attendance</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-comments text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">No recent toolbox talks</p>
                            <a href="{{ route('toolbox-talks.create') }}" class="mt-3 inline-block text-sm text-[#0066CC] hover:underline">
                                Schedule a talk <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Additional Recent Activity: Risk Assessments & CAPAs -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Risk Assessments -->
            <div class="bg-white border border-gray-300 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Risk Assessments</h3>
                    <a href="{{ route('risk-assessment.risk-assessments.index') }}" class="text-sm text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentRiskAssessments ?? [] as $assessment)
                        <a href="{{ route('risk-assessment.risk-assessments.show', $assessment) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-black">{{ $assessment->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $assessment->reference_number }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ in_array($assessment->risk_level, ['extreme', 'critical']) ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                    {{ $assessment->risk_level == 'high' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                    {{ $assessment->risk_level == 'medium' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                    {{ $assessment->risk_level == 'low' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}">
                                    {{ ucfirst($assessment->risk_level) }}
                                </span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                <span><i class="fas fa-building mr-1"></i>{{ $assessment->department->name ?? 'N/A' }}</span>
                                <span><i class="fas fa-user mr-1"></i>{{ $assessment->creator->name ?? 'N/A' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-list text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">No recent risk assessments</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent CAPAs -->
            <div class="bg-white border border-gray-300 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Recent CAPAs</h3>
                    <a href="{{ route('incidents.index') }}" class="text-sm text-[#0066CC] hover:underline">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentCAPAs ?? [] as $capa)
                        @if($capa->incident)
                        <a href="{{ route('incidents.capas.show', [$capa->incident, $capa]) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-black">{{ $capa->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $capa->reference_number }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $capa->priority == 'critical' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                    {{ $capa->priority == 'high' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                    {{ $capa->priority == 'medium' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                    {{ $capa->priority == 'low' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}">
                                    {{ ucfirst($capa->priority) }}
                                </span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 space-x-4">
                                <span><i class="fas fa-user mr-1"></i>{{ $capa->assignedTo->name ?? 'Unassigned' }}</span>
                                @if($capa->due_date)
                                    <span class="{{ $capa->due_date < now() && !in_array($capa->status, ['closed', 'completed']) ? 'text-[#CC0000]' : '' }}">
                                        <i class="fas fa-calendar mr-1"></i>Due: {{ $capa->due_date->format('M d, Y') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                        @endif
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-tasks text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">No recent CAPAs</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('incidents.create') }}" class="flex flex-col items-center justify-center p-4 border border-gray-300 hover:border-[#0066CC] hover:bg-[#F5F5F5] transition-all group">
                    <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center mb-2 group-hover:bg-[#E0E0E0]">
                        <i class="fas fa-exclamation-triangle text-black text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black group-hover:text-[#0066CC]">Report Incident</span>
                </a>
                <a href="{{ route('toolbox-talks.create') }}" class="flex flex-col items-center justify-center p-4 border border-gray-300 hover:border-[#0066CC] hover:bg-[#F5F5F5] transition-all group">
                    <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center mb-2 group-hover:bg-[#E0E0E0]">
                        <i class="fas fa-comments text-black text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black group-hover:text-[#0066CC]">Schedule Talk</span>
                </a>
                <a href="{{ route('incidents.index') }}" class="flex flex-col items-center justify-center p-4 border border-gray-300 hover:border-[#0066CC] hover:bg-[#F5F5F5] transition-all group">
                    <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center mb-2 group-hover:bg-[#E0E0E0]">
                        <i class="fas fa-list text-black text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black group-hover:text-[#0066CC]">View Incidents</span>
                </a>
                <a href="{{ route('toolbox-talks.index') }}" class="flex flex-col items-center justify-center p-4 border border-gray-300 hover:border-[#0066CC] hover:bg-[#F5F5F5] transition-all group">
                    <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center mb-2 group-hover:bg-[#E0E0E0]">
                        <i class="fas fa-clipboard-list text-black text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black group-hover:text-[#0066CC]">View Talks</span>
                </a>
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
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
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
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                label += context.parsed + ' (' + percentage + '%)';
                                return label;
                            }
                        }
                    }
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
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                label += context.parsed + ' (' + percentage + '%)';
                                return label;
                            }
                        }
                    }
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
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
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
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgb(239, 68, 68)',
                        borderWidth: 1
                    },
                    {
                        label: 'Toolbox Talks',
                        data: weeklyTalks,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                label += context.parsed + ' (' + percentage + '%)';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Department Performance Chart
    const deptCtx = document.getElementById('departmentChart');
    if (deptCtx) {
        const deptData = @json($departmentStats ?? []);
        if (deptData.length > 0) {
            new Chart(deptCtx, {
                type: 'bar',
                data: {
                    labels: deptData.map(d => d.name),
                    datasets: [
                        {
                            label: 'Total Incidents',
                            data: deptData.map(d => d.incidents),
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Open Incidents',
                            data: deptData.map(d => d.open_incidents),
                            backgroundColor: 'rgba(249, 115, 22, 0.8)',
                            borderColor: 'rgb(249, 115, 22)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Toolbox Talks',
                            data: deptData.map(d => d.talks),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Avg Attendance %',
                            data: deptData.map(d => Math.round(d.avg_attendance || 0)),
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1,
                            yAxisID: 'y2'
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true, position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, position: 'left' },
                        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } },
                        y2: { 
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
    }

    // Risk Level Distribution Chart
    const riskLevelCtx = document.getElementById('riskLevelChart');
    if (riskLevelCtx) {
        const riskData = @json($riskLevelDistribution ?? []);
        new Chart(riskLevelCtx, {
            type: 'doughnut',
            data: {
                labels: ['Extreme', 'Critical', 'High', 'Medium', 'Low'],
                datasets: [{
                    data: [
                        riskData.extreme || 0,
                        riskData.critical || 0,
                        riskData.high || 0,
                        riskData.medium || 0,
                        riskData.low || 0
                    ],
                    backgroundColor: [
                        'rgb(127, 29, 29)',
                        'rgb(239, 68, 68)',
                        'rgb(249, 115, 22)',
                        'rgb(234, 179, 8)',
                        'rgb(34, 197, 94)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                label += context.parsed + ' (' + percentage + '%)';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Training Trends Chart
    const trainingTrendsCtx = document.getElementById('trainingTrendsChart');
    if (trainingTrendsCtx) {
        const trainingData = @json($trainingTrends ?? []);
        const trainingLabels = trainingData.map(item => item.month || '');
        const trainingTotal = trainingData.map(item => item.total || 0);
        const trainingCompleted = trainingData.map(item => item.completed || 0);
        
        new Chart(trainingTrendsCtx, {
            type: 'line',
            data: {
                labels: trainingLabels,
                datasets: [
                    {
                        label: 'Total Sessions',
                        data: trainingTotal,
                        borderColor: 'rgb(20, 184, 166)',
                        backgroundColor: 'rgba(20, 184, 166, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Completed',
                        data: trainingCompleted,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // PPE Category Distribution Chart
    const ppeCategoryCtx = document.getElementById('ppeCategoryChart');
    if (ppeCategoryCtx) {
        @php
            $ppeData = $ppeCategoryDistribution ?? [];
        @endphp
        const ppeData = @json($ppeData);
        const ppeLabels = Object.keys(ppeData);
        const ppeValues = Object.values(ppeData);
        
        if (ppeLabels.length > 0) {
            new Chart(ppeCategoryCtx, {
                type: 'doughnut',
                data: {
                    labels: ppeLabels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
                    datasets: [{
                        data: ppeValues,
                        backgroundColor: [
                            'rgb(234, 179, 8)',
                            'rgb(20, 184, 166)',
                            'rgb(59, 130, 246)',
                            'rgb(168, 85, 247)',
                            'rgb(239, 68, 68)',
                            'rgb(34, 197, 94)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    label += context.parsed + ' (' + percentage + '%)';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
@endsection
