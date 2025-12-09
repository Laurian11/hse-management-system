@extends('layouts.app')

@section('title', 'Monthly Manpower Report')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('manpower-reports.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Reports
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Monthly Manpower Report</h1>
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('manpower-reports.monthly') }}" method="GET" class="flex items-center space-x-2">
                        <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" 
                               class="px-3 py-2 border border-gray-300 rounded-lg">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </form>
                    <a href="{{ route('manpower-reports.monthly', ['month' => request('month', now()->format('Y-m')), 'format' => 'excel']) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('manpower-reports.monthly', ['month' => request('month', now()->format('Y-m')), 'format' => 'pdf']) }}" 
                       class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600">Total Employees</div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_employees'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600">Average Present</div>
                <div class="text-2xl font-bold text-green-600">{{ $stats['avg_present'] }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600">Average Attendance Rate</div>
                <div class="text-2xl font-bold text-blue-600">{{ $stats['avg_attendance_rate'] }}%</div>
            </div>
        </div>

        <!-- Month Period -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900">{{ $stats['month'] }}</h2>
        </div>

        <!-- Weekly Summary -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Weekly Summary</h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Week</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Present</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendance Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($weeklySummary as $week)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $week['week'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $week['start'] }} - {{ $week['end'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600">{{ $week['avg_present'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-blue-600">{{ $week['attendance_rate'] }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Daily Breakdown (Scrollable) -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Daily Breakdown</h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Present</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Absent</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendance Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($dailyBreakdown as $day)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $day['date'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $day['day'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-green-600 font-medium">{{ $day['present'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-red-600">{{ $day['absent'] }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-blue-600 font-medium">{{ $day['attendance_rate'] }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

