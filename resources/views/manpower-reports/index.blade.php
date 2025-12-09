@extends('layouts.app')

@section('title', 'Manpower Reports')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chart-bar text-blue-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Manpower Reports</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Daily Report Card -->
            <a href="{{ route('manpower-reports.daily') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-calendar-day text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Daily Report</h3>
                <p class="text-sm text-gray-600">View daily attendance statistics, device breakdown, and department analysis</p>
            </a>

            <!-- Weekly Report Card -->
            <a href="{{ route('manpower-reports.weekly') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-calendar-week text-green-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Weekly Report</h3>
                <p class="text-sm text-gray-600">Analyze attendance trends across the week with day-by-day breakdown</p>
            </a>

            <!-- Monthly Report Card -->
            <a href="{{ route('manpower-reports.monthly') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar-alt text-purple-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Monthly Report</h3>
                <p class="text-sm text-gray-600">Comprehensive monthly attendance analysis with weekly summaries</p>
            </a>

            <!-- Location Report Card -->
            <a href="{{ route('manpower-reports.location') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-3 rounded-full">
                        <i class="fas fa-map-marker-alt text-orange-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Location Report</h3>
                <p class="text-sm text-gray-600">Compare attendance performance across different locations and devices</p>
            </a>
        </div>
    </div>
</div>
@endsection

