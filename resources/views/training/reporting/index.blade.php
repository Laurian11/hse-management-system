@extends('layouts.app')

@section('title', 'Training Reporting & Analytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Training Reporting & Analytics</h1>
                    <p class="text-sm text-gray-500 mt-1">Comprehensive training analysis and insights</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('training.reporting.export', ['format' => 'excel', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('training.reporting.export', ['format' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Date Range Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Key Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Training Completion Rate</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($completionRate, 1) }}%</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Sessions</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_sessions']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['completed_sessions'] }} completed</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Certificates</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_certificates']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['active_certificates'] }} active</p>
                    </div>
                    <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-certificate text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Cost</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($costAnalysis->total_cost ?? 0, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Avg: ${{ number_format($costAnalysis->avg_cost ?? 0, 2) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Training by Department -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Training by Department</h2>
                @if($trainingByDepartment->count() > 0)
                    <div class="space-y-3">
                        @foreach($trainingByDepartment as $dept)
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $dept['department'] }}</span>
                                    <span class="text-sm text-gray-500">{{ $dept['count'] }} records ({{ $dept['unique_employees'] }} employees)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $maxCount = $trainingByDepartment->max('count');
                                        $percentage = $maxCount > 0 ? ($dept['count'] / $maxCount) * 100 : 0;
                                    @endphp
                                    <div class="bg-indigo-500 h-2 rounded-full progress-bar" data-width="{{ number_format($percentage, 2) }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No training records found</p>
                @endif
            </div>

            <!-- Competency Gaps -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Competency Gaps by Priority</h2>
                @if($competencyGaps->count() > 0)
                    <div class="space-y-3">
                        @php
                            $gapColors = [
                                'critical' => 'bg-red-500',
                                'high' => 'bg-orange-500',
                                'medium' => 'bg-yellow-500',
                                'low' => 'bg-green-500'
                            ];
                        @endphp
                        @foreach(['critical', 'high', 'medium', 'low'] as $priority)
                            @php
                                $count = $competencyGaps[$priority] ?? 0;
                                $total = $competencyGaps->sum();
                                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                $colorClass = $gapColors[$priority] ?? 'bg-gray-500';
                            @endphp
                            @if($count > 0)
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-gray-700 capitalize">{{ $priority }}</span>
                                        <span class="text-sm text-gray-500">{{ $count }} gaps</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="{{ $colorClass }} h-2 rounded-full progress-bar" data-width="{{ number_format($percentage, 2) }}"></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No competency gaps identified</p>
                @endif
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Monthly Training Trends</h2>
            @if($monthlyTrends->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Sessions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scheduled</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($monthlyTrends as $trend)
                                @php
                                    $completionRate = $trend['total_sessions'] > 0 
                                        ? ($trend['completed'] / $trend['total_sessions']) * 100 
                                        : 0;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $trend['month'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $trend['total_sessions'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-green-600 font-medium">{{ $trend['completed'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-yellow-600 font-medium">{{ $trend['scheduled'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                            {{ number_format($completionRate, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No training trends data available</p>
            @endif
        </div>

        <!-- Certificate Expiry Analysis -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Certificate Expiry Analysis</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Expiring in 30 Days</p>
                            <p class="text-2xl font-bold text-red-600 mt-2">{{ $certificateExpiry['expiring_30_days'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Expiring in 60 Days</p>
                            <p class="text-2xl font-bold text-yellow-600 mt-2">{{ $certificateExpiry['expiring_60_days'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                    </div>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Expired</p>
                            <p class="text-2xl font-bold text-gray-600 mt-2">{{ $certificateExpiry['expired'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-ban text-gray-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Training Types -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Top Training Types</h2>
            @if($topTrainingTypes->count() > 0)
                <div class="space-y-3">
                    @foreach($topTrainingTypes as $type => $count)
                        @php
                            $total = $topTrainingTypes->sum();
                            $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                <span class="text-sm text-gray-500">{{ $count }} sessions ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-500 h-2 rounded-full progress-bar" data-width="{{ number_format($percentage, 2) }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No training type data available</p>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.progress-bar').forEach(function(bar) {
            const width = bar.getAttribute('data-width');
            if (width) {
                bar.style.width = width + '%';
            }
        });
    });
</script>
@endsection
