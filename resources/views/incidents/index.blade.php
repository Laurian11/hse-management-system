@extends('layouts.app')

@section('title', 'Incidents')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">Incident Management</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('incidents.trend-analysis') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>Trend Analysis
                    </a>
                    <a href="{{ route('incidents.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Report Incident
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Incidents</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $incidents->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Open</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">
                            {{ $incidents->whereIn('status', ['reported', 'open'])->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Investigating</p>
                        <p class="text-2xl font-bold text-yellow-600 mt-1">
                            {{ $incidents->where('status', 'investigating')->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-search text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Closed</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">
                            {{ $incidents->whereIn('status', ['closed', 'resolved'])->count() }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('incidents.index') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request()->has('filter') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-list mr-2"></i>All
                </a>
                <a href="{{ route('incidents.index', ['filter' => 'open']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('filter') == 'open' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-clock mr-2"></i>Open
                </a>
                <a href="{{ route('incidents.index', ['filter' => 'investigating']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('filter') == 'investigating' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-search mr-2"></i>Investigating
                </a>
                <a href="{{ route('incidents.index', ['filter' => 'injury']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('filter') == 'injury' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-user-injured mr-2"></i>Injury/Illness
                </a>
                <a href="{{ route('incidents.index', ['filter' => 'property']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('filter') == 'property' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-tools mr-2"></i>Property Damage
                </a>
                <a href="{{ route('incidents.index', ['filter' => 'near_miss']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('filter') == 'near_miss' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Near Miss
                </a>
                <a href="{{ route('incidents.index', ['filter' => 'critical']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->get('filter') == 'critical' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-exclamation-circle mr-2"></i>Critical
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('incidents.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="reported" {{ request()->get('status') == 'reported' ? 'selected' : '' }}>Reported</option>
                        <option value="open" {{ request()->get('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="investigating" {{ request()->get('status') == 'investigating' ? 'selected' : '' }}>Investigating</option>
                        <option value="resolved" {{ request()->get('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request()->get('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Severity</label>
                    <select name="severity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Severity</option>
                        <option value="low" {{ request()->get('severity') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request()->get('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request()->get('severity') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ request()->get('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                    <select name="event_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="injury_illness" {{ request()->get('event_type') == 'injury_illness' ? 'selected' : '' }}>Injury/Illness</option>
                        <option value="property_damage" {{ request()->get('event_type') == 'property_damage' ? 'selected' : '' }}>Property Damage</option>
                        <option value="near_miss" {{ request()->get('event_type') == 'near_miss' ? 'selected' : '' }}>Near Miss</option>
                        <option value="environmental" {{ request()->get('event_type') == 'environmental' ? 'selected' : '' }}>Environmental</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request()->get('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Incidents List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Severity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($incidents as $incident)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $incident->reference_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $incident->title ?? $incident->incident_type }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ Str::limit($incident->description, 60) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($incident->event_type)
                                    @php
                                        $eventTypeIcons = [
                                            'injury_illness' => ['icon' => 'fa-user-injured', 'color' => 'red'],
                                            'property_damage' => ['icon' => 'fa-tools', 'color' => 'orange'],
                                            'near_miss' => ['icon' => 'fa-exclamation-triangle', 'color' => 'yellow'],
                                            'environmental' => ['icon' => 'fa-leaf', 'color' => 'green'],
                                        ];
                                        $eventType = $eventTypeIcons[$incident->event_type] ?? ['icon' => 'fa-circle', 'color' => 'gray'];
                                    @endphp
                                    <span class="px-2 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-{{ $eventType['color'] }}-100 text-{{ $eventType['color'] }}-800">
                                        <i class="fas {{ $eventType['icon'] }} mr-1"></i>
                                        {{ ucfirst(str_replace('_', ' ', $incident->event_type)) }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        N/A
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $incident->severity === 'critical' ? 'bg-red-100 text-red-800' : 
                                       ($incident->severity === 'high' ? 'bg-orange-100 text-orange-800' : 
                                       ($incident->severity === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                    <i class="fas fa-{{ $incident->severity === 'critical' ? 'exclamation-circle' : ($incident->severity === 'high' ? 'exclamation-triangle' : 'info-circle') }} mr-1"></i>
                                    {{ ucfirst($incident->severity) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $incident->status === 'open' || $incident->status === 'reported' ? 'bg-red-100 text-red-800' : 
                                       ($incident->status === 'investigating' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                    {{ ucfirst($incident->status) }}
                                </span>
                                @if($incident->investigation)
                                    <div class="mt-1">
                                        <span class="text-xs text-blue-600">
                                            <i class="fas fa-search mr-1"></i>Investigation
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $incident->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $incident->incident_date->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $incident->incident_date->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('incidents.show', $incident) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('incidents.edit', $incident) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-exclamation-triangle text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-500 text-lg font-medium">No incidents found</p>
                                    <p class="text-gray-400 text-sm mt-2">Get started by reporting a new incident</p>
                                    <a href="{{ route('incidents.create') }}" class="mt-4 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Report Incident
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($incidents->hasPages())
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    {{ $incidents->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
