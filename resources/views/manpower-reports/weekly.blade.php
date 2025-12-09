@extends('layouts.app')

@section('title', 'Weekly Manpower Report')

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
                    <h1 class="text-2xl font-bold text-gray-900">Weekly Manpower Report</h1>
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('manpower-reports.weekly') }}" method="GET" class="flex items-center space-x-2">
                        <input type="date" name="week_start" value="{{ $stats['week_start']->format('Y-m-d') }}" 
                               class="px-3 py-2 border border-gray-300 rounded-lg">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </form>
                    <a href="{{ route('manpower-reports.weekly', ['week_start' => $stats['week_start']->format('Y-m-d'), 'format' => 'excel']) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('manpower-reports.weekly', ['week_start' => $stats['week_start']->format('Y-m-d'), 'format' => 'pdf']) }}" 
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

        <!-- Week Period -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                Week: {{ $stats['week_start']->format('M j') }} - {{ $stats['week_end']->format('M j, Y') }}
            </h2>
        </div>

        <!-- Daily Breakdown -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Daily Breakdown</h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Present</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Late</th>
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
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-yellow-600">{{ $day['late'] }}</td>
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

