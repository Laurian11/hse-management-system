@extends('layouts.app')

@section('title', 'Incident Reports')

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
                    <i class="fas fa-chart-bar text-red-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Incident Reports</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Report Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Department Report -->
            <a href="{{ route('incidents.reports.department') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-red-100 p-3 rounded-lg">
                        <i class="fas fa-building text-red-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Department Reports</h3>
                <p class="text-sm text-gray-600">View incident statistics by department with day, week, month, and annual reports.</p>
                <div class="mt-4 flex items-center text-red-600 text-sm">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </a>

            <!-- Employee Report -->
            <a href="{{ route('incidents.reports.employee') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow block">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-users text-orange-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Employee Reports</h3>
                <p class="text-sm text-gray-600">Track individual employee incident reporting and assignment statistics.</p>
                <div class="mt-4 flex items-center text-orange-600 text-sm">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </a>

            <!-- Period Report -->
            <a href="{{ route('incidents.reports.period') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow block">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-alt text-yellow-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Period Report</h3>
                <p class="text-sm text-gray-600">Generate day, week, month, and annual reports with comprehensive statistics.</p>
                <div class="mt-4 flex items-center text-yellow-600 text-sm">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </a>

            <!-- Companies Report -->
            <a href="{{ route('incidents.reports.companies') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow block">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-industry text-purple-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Companies Report</h3>
                <p class="text-sm text-gray-600">Compare incident performance across parent and sister companies.</p>
                <div class="mt-4 flex items-center text-purple-600 text-sm">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </a>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-check-circle text-green-600 mt-1"></i>
                    <div>
                        <h3 class="font-medium text-gray-900">Multiple Time Periods</h3>
                        <p class="text-sm text-gray-600">Generate reports for day, week, month, or annual periods</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <i class="fas fa-file-excel text-green-600 mt-1"></i>
                    <div>
                        <h3 class="font-medium text-gray-900">Excel Export</h3>
                        <p class="text-sm text-gray-600">Export all reports to Excel format for further analysis</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <i class="fas fa-file-pdf text-red-600 mt-1"></i>
                    <div>
                        <h3 class="font-medium text-gray-900">PDF Export</h3>
                        <p class="text-sm text-gray-600">Generate professional PDF reports for sharing and archiving</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <i class="fas fa-filter text-blue-600 mt-1"></i>
                    <div>
                        <h3 class="font-medium text-gray-900">Advanced Filtering</h3>
                        <p class="text-sm text-gray-600">Filter by date range, department, employee, severity, and more</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

