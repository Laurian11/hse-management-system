@extends('layouts.app')

@section('title', 'Incidents')

@php
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'fa-home'],
    ['label' => 'Incidents', 'url' => route('incidents.index'), 'active' => true]
];
@endphp

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
                    <a href="{{ route('incidents.export-all', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                       class="px-4 py-2 text-green-700 bg-white border border-green-300 rounded-lg hover:bg-green-50">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('incidents.export-all', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                       class="px-4 py-2 text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                    <a href="{{ route('incidents.reports.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-chart-bar mr-2"></i>Reports
                    </a>
                    <a href="{{ route('incidents.trend-analysis') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-chart-line mr-2"></i>Trend Analysis
                    </a>
                    <a href="{{ route('incidents.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
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
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Filters</h3>
                <div class="flex items-center space-x-2">
                    <!-- Saved Searches Dropdown -->
                    <div class="relative" id="savedSearchesContainer">
                        <button onclick="toggleSavedSearches()" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                            <i class="fas fa-bookmark mr-1"></i>Saved Searches
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div id="savedSearchesDropdown" class="hidden absolute right-0 mt-1 w-64 bg-white border border-gray-300 rounded shadow-lg z-50 max-h-64 overflow-y-auto">
                            <div class="p-2">
                                <div id="savedSearchesList" class="space-y-1">
                                    <!-- Saved searches will be loaded here -->
                                </div>
                                <button onclick="saveCurrentSearch()" class="w-full mt-2 px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Save Current Search
                                </button>
                            </div>
                        </div>
                    </div>
                    <button onclick="clearFilters()" class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-1"></i>Clear All
                    </button>
                </div>
            </div>
            <form method="GET" action="{{ route('incidents.index') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-6 gap-4">
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request()->get('date_to') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Actions Bar (hidden by default) -->
        <div id="bulkActionsBar" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span id="selectedCount" class="text-sm font-medium text-blue-900">0 items selected</span>
                    <div class="flex items-center space-x-2">
                        <button onclick="bulkExport()" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-1"></i>Export Selected
                        </button>
                        <button onclick="bulkDelete()" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-1"></i>Delete Selected
                        </button>
                        <button onclick="bulkStatusUpdate()" class="px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition-colors">
                            <i class="fas fa-edit mr-1"></i>Update Status
                        </button>
                    </div>
                </div>
                <button onclick="clearSelection()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="fas fa-times mr-1"></i>Clear Selection
                </button>
            </div>
        </div>

        <!-- Incidents List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('reference_number')">
                                Reference <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('title')">
                                Title <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('event_type')">
                                Event Type <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('severity')">
                                Severity <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('status')">
                                Status <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('department_id')">
                                Department <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('incident_date')">
                                Date <i class="fas fa-sort ml-1 text-gray-400"></i>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($incidents as $incident)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_items[]" value="{{ $incident->id }}" class="item-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" onchange="updateBulkActions()">
                            </td>
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
                            <td colspan="9" class="px-6 py-12 text-center">
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

@push('scripts')
<script>
    // Bulk Operations Functions
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        updateBulkActions();
    }
    
    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        const count = checkboxes.length;
        const bulkBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');
        
        if (count > 0) {
            bulkBar.classList.remove('hidden');
            selectedCount.textContent = count + ' item' + (count > 1 ? 's' : '') + ' selected';
        } else {
            bulkBar.classList.add('hidden');
        }
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.item-checkbox');
        const selectAll = document.getElementById('selectAll');
        selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
    }
    
    function clearSelection() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        updateBulkActions();
    }
    
    function bulkExport() {
        const selected = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select at least one item to export.');
            return;
        }
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("incidents.export") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
    
    function bulkDelete() {
        const selected = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select at least one item to delete.');
            return;
        }
        
        if (!confirm(`Are you sure you want to delete ${selected.length} item(s)? This action cannot be undone.`)) {
            return;
        }
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("incidents.bulk-delete") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
    
    function bulkStatusUpdate() {
        const selected = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) {
            alert('Please select at least one item to update.');
            return;
        }
        
        const newStatus = prompt('Enter new status (reported, open, investigating, resolved, closed):');
        if (!newStatus) return;
        
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("incidents.bulk-update") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = newStatus;
        form.appendChild(statusInput);
        
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // Saved Searches Functions
    const STORAGE_KEY = 'incidents_saved_searches';
    
    function getSavedSearches() {
        const saved = localStorage.getItem(STORAGE_KEY);
        return saved ? JSON.parse(saved) : [];
    }
    
    function saveSavedSearches(searches) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(searches));
    }
    
    function loadSavedSearches() {
        const searches = getSavedSearches();
        const container = document.getElementById('savedSearchesList');
        
        if (searches.length === 0) {
            container.innerHTML = '<p class="text-sm text-gray-500 p-2">No saved searches</p>';
            return;
        }
        
        container.innerHTML = searches.map((search, index) => `
            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                <div class="flex-1 cursor-pointer" onclick="loadSavedSearch(${index})">
                    <div class="text-sm font-medium text-gray-900">${search.name}</div>
                    <div class="text-xs text-gray-500">${Object.keys(search.params).length} filter(s)</div>
                </div>
                <button onclick="deleteSavedSearch(${index})" class="ml-2 text-red-600 hover:text-red-800" title="Delete">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            </div>
        `).join('');
    }
    
    function toggleSavedSearches() {
        const dropdown = document.getElementById('savedSearchesDropdown');
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            loadSavedSearches();
        }
    }
    
    function saveCurrentSearch() {
        const name = prompt('Enter a name for this search:');
        if (!name) return;
        
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = {};
        
        for (const [key, value] of formData.entries()) {
            if (value) {
                params[key] = value;
            }
        }
        
        // Also get URL params
        const urlParams = new URLSearchParams(window.location.search);
        for (const [key, value] of urlParams.entries()) {
            if (value && !params[key]) {
                params[key] = value;
            }
        }
        
        if (Object.keys(params).length === 0) {
            alert('No filters to save. Please apply some filters first.');
            return;
        }
        
        const searches = getSavedSearches();
        searches.push({
            name: name,
            params: params,
            createdAt: new Date().toISOString()
        });
        
        saveSavedSearches(searches);
        loadSavedSearches();
        alert('Search saved successfully!');
    }
    
    function loadSavedSearch(index) {
        const searches = getSavedSearches();
        const search = searches[index];
        
        if (!search) return;
        
        // Build URL with saved params
        const url = new URL(window.location.href);
        url.search = '';
        
        Object.keys(search.params).forEach(key => {
            url.searchParams.set(key, search.params[key]);
        });
        
        window.location.href = url.toString();
    }
    
    function deleteSavedSearch(index) {
        if (!confirm('Are you sure you want to delete this saved search?')) {
            return;
        }
        
        const searches = getSavedSearches();
        searches.splice(index, 1);
        saveSavedSearches(searches);
        loadSavedSearches();
    }
    
    function clearFilters() {
        window.location.href = '{{ route("incidents.index") }}';
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('savedSearchesContainer');
        if (container && !container.contains(event.target)) {
            document.getElementById('savedSearchesDropdown').classList.add('hidden');
        }
    });
    
    // Table Sorting
    let currentSort = {
        column: '{{ request()->get("sort", "created_at") }}',
        direction: '{{ request()->get("direction", "desc") }}'
    };
    
    function sortTable(column) {
        // Toggle direction if same column
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }
        
        // Update URL and reload
        const url = new URL(window.location.href);
        url.searchParams.set('sort', currentSort.column);
        url.searchParams.set('direction', currentSort.direction);
        window.location.href = url.toString();
    }
    
    // Update sort indicators on page load
    document.addEventListener('DOMContentLoaded', function() {
        const sortColumn = '{{ request()->get("sort", "created_at") }}';
        const sortDirection = '{{ request()->get("direction", "desc") }}';
        
        // Update sort icons
        document.querySelectorAll('th[onclick*="sortTable"]').forEach(th => {
            const icon = th.querySelector('i');
            if (th.getAttribute('onclick').includes(`'${sortColumn}'`)) {
                icon.className = `fas fa-sort-${sortDirection === 'asc' ? 'up' : 'down'} ml-1 text-blue-600`;
            }
        });
    });
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateBulkActions();
        loadSavedSearches();
    });
</script>
@endpush
@endsection
