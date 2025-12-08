@extends('layouts.app')

@section('title', 'Companies Incident Report')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('incidents.reports.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Reports
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Companies Incident Report</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('incidents.reports.companies', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                       class="px-4 py-2 text-green-700 bg-white border border-green-300 rounded-lg hover:bg-green-50">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('incidents.reports.companies', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                       class="px-4 py-2 text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('incidents.reports.companies') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                    <select name="period" id="period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="day" {{ request('period', 'month') == 'day' ? 'selected' : '' }}>Day</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Week</option>
                        <option value="month" {{ request('period', 'month') == 'month' ? 'selected' : '' }}>Month</option>
                        <option value="annual" {{ request('period') == 'annual' ? 'selected' : '' }}>Annual</option>
                    </select>
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" id="date" value="{{ request('date', now()->format('Y-m-d')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('incidents.reports.companies') }}" class="w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 text-center">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Report Period Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">Report Period</h3>
                    <p class="text-sm text-blue-700">
                        {{ ucfirst($period) }} Report: {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Company Statistics -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Company Incident Statistics</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Incidents</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Open</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Closed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Critical</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Injury/Illness</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($companyStats as $comp)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $comp['name'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $comp['total_incidents'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $comp['open'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $comp['closed'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $comp['critical'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                        {{ $comp['injury'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No incidents found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

