@extends('layouts.app')

@section('title', 'Training Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Training & Competency Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Overview of training activities and statistics</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('training.training-needs.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>New Training Need
                    </a>
                    <a href="{{ route('training.training-sessions.calendar') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calendar mr-2"></i>Calendar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Training Needs -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-indigo-600 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_training_needs']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['validated_tnas'] }} validated</p>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-700 mb-1">Training Needs</h3>
                <a href="{{ route('training.training-needs.index') }}" class="text-xs text-blue-600 hover:text-blue-700">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Training Plans -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_plans']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['approved_plans'] }} approved</p>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-700 mb-1">Training Plans</h3>
                <a href="{{ route('training.training-plans.index') }}" class="text-xs text-blue-600 hover:text-blue-700">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Training Sessions -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-blue-600 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_sessions']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['upcoming_sessions'] }} upcoming</p>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-700 mb-1">Training Sessions</h3>
                <a href="{{ route('training.training-sessions.index') }}" class="text-xs text-blue-600 hover:text-blue-700">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Certificates -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-certificate text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_certificates']) }}</p>
                        <p class="text-xs text-red-500 mt-1">{{ $stats['expiring_soon'] }} expiring soon</p>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-700 mb-1">Certificates</h3>
                <a href="#" class="text-xs text-blue-600 hover:text-blue-700">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Recent Training Needs -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Training Needs</h2>
                    <a href="{{ route('training.training-needs.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View All
                    </a>
                </div>
                @if($recentTNAs->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentTNAs as $tna)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <a href="{{ route('training.training-needs.show', $tna) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                            {{ $tna->training_title }}
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit($tna->training_description, 80) }}</p>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-tag mr-1"></i>{{ ucfirst($tna->priority) }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-user mr-1"></i>{{ $tna->creator->name ?? 'N/A' }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>{{ $tna->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $tna->status === 'validated' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($tna->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No training needs found</p>
                @endif
            </div>

            <!-- Upcoming Sessions -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Upcoming Sessions</h2>
                    <a href="{{ route('training.training-sessions.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                        View All
                    </a>
                </div>
                @if($upcomingSessions->count() > 0)
                    <div class="space-y-3">
                        @foreach($upcomingSessions as $session)
                            <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                <a href="{{ route('training.training-sessions.show', $session) }}" class="font-medium text-gray-900 hover:text-blue-600 text-sm">
                                    {{ $session->title }}
                                </a>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>{{ $session->scheduled_start->format('M j, Y') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>{{ $session->scheduled_start->format('g:i A') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8 text-sm">No upcoming sessions</p>
                @endif
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Training by Priority -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Training Needs by Priority</h2>
                <div class="space-y-3">
                    @php
                        $priorityColors = [
                            'critical' => 'bg-red-500',
                            'high' => 'bg-orange-500',
                            'medium' => 'bg-yellow-500',
                            'low' => 'bg-green-500'
                        ];
                    @endphp
                    @foreach(['critical', 'high', 'medium', 'low'] as $priority)
                        @php
                            $count = $trainingByPriority[$priority] ?? 0;
                            $total = $trainingByPriority->sum();
                            $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                            $colorClass = $priorityColors[$priority] ?? 'bg-gray-500';
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ $priority }}</span>
                                <span class="text-sm text-gray-500">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $colorClass }} h-2 rounded-full progress-bar" data-width="{{ number_format($percentage, 2) }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Training by Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Training Needs by Status</h2>
                <div class="space-y-3">
                    @php
                        $statusColors = [
                            'identified' => 'bg-gray-500',
                            'validated' => 'bg-blue-500',
                            'planned' => 'bg-yellow-500',
                            'in_progress' => 'bg-indigo-500',
                            'completed' => 'bg-green-500'
                        ];
                    @endphp
                    @foreach(['identified', 'validated', 'planned', 'in_progress', 'completed'] as $status)
                        @php
                            $count = $trainingByStatus[$status] ?? 0;
                            $total = $trainingByStatus->sum();
                            $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                            $colorClass = $statusColors[$status] ?? 'bg-gray-500';
                        @endphp
                        @if($count > 0)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                    <span class="text-sm text-gray-500">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="{{ $colorClass }} h-2 rounded-full progress-bar" data-width="{{ $percentage }}"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Expiring Certificates -->
        @if($expiringCertificates->count() > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Certificates Expiring Soon (Next 60 Days)</h2>
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                        {{ $expiringCertificates->count() }} expiring
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Certificate</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Remaining</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($expiringCertificates as $certificate)
                                @php
                                    $daysRemaining = now()->diffInDays($certificate->expiry_date, false);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $certificate->user->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $certificate->certificate_title ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $certificate->expiry_date->format('M j, Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $daysRemaining < 30 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $daysRemaining }} days
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set progress bar widths
        document.querySelectorAll('.progress-bar').forEach(function(bar) {
            const width = bar.getAttribute('data-width');
            if (width) {
                bar.style.width = width + '%';
            }
        });
    });
</script>
@endsection
