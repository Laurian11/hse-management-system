@extends('layouts.app')

@section('title', 'Toolbox Talks Reports')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chart-bar text-blue-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Toolbox Talks Reports</h1>
                </div>
                <a href="{{ route('toolbox-talks.index') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Talks
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Report Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Department Attendance Report -->
            <a href="{{ route('toolbox-talks.reports.department-attendance') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-building text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Department Attendance</h3>
                <p class="text-sm text-gray-600">View attendance statistics by department with day, week, month, and annual reports.</p>
                <div class="mt-4 flex items-center text-blue-600 text-sm">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </a>

            <!-- Employee Attendance Report -->
            <a href="{{ route('toolbox-talks.reports.employee-attendance') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-users text-green-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Employee Attendance</h3>
                <p class="text-sm text-gray-600">Track individual employee attendance across all toolbox talks with detailed statistics.</p>
                <div class="mt-4 flex items-center text-green-600 text-sm">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </a>

            <!-- Period Report -->
            <a href="{{ route('toolbox-talks.reports.period') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-calendar-alt text-purple-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Period Report</h3>
                <p class="text-sm text-gray-600">Generate day, week, month, and annual reports with comprehensive statistics and breakdowns.</p>
                <div class="mt-4 flex items-center text-purple-600 text-sm">
                    <span>View Report</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </div>
            </a>

            <!-- Companies Report -->
            <a href="{{ route('toolbox-talks.reports.companies') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-industry text-orange-600 text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Companies Report</h3>
                <p class="text-sm text-gray-600">Compare performance across parent and sister companies with aggregated statistics.</p>
                <div class="mt-4 flex items-center text-orange-600 text-sm">
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
                        <p class="text-sm text-gray-600">Filter by date range, department, employee, and more</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

