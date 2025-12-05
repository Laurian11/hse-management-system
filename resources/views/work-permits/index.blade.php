@extends('layouts.app')

@section('title', 'Work Permits')

@php
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'fa-home'],
    ['label' => 'Work Permits', 'url' => route('work-permits.index'), 'active' => true]
];
@endphp

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Work Permits</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage and track work permits</p>
                </div>
                <div class="flex items-center space-x-3">
                    <x-print-button />
                    <a href="{{ route('work-permits.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-chart-pie mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('work-permits.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>New Permit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Total</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Active</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['active'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm font-medium text-gray-600">Expired</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['expired'] }}</p>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                <div class="flex items-center space-x-2">
                    <button onclick="toggleSavedSearches()" class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200 transition-colors relative">
                        <i class="fas fa-bookmark mr-1"></i>Saved Searches
                        <div id="savedSearchesDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg z-10 border border-gray-200" style="top: 100%;">
                            <div class="p-2">
                                <div id="savedSearchesList" class="max-h-64 overflow-y-auto"></div>
                                <div class="border-t border-gray-200 mt-2 pt-2">
                                    <button onclick="saveCurrentSearch()" class="w-full text-left px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded">
                                        <i class="fas fa-plus mr-2"></i>Save Current Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </button>
                    <a href="{{ route('work-permits.index') }}" class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200 transition-colors">
                        <i class="fas fa-redo mr-1"></i>Clear All
                    </a>
                </div>
            </div>
            <form method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search permits..."
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <select name="work_permit_type_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($permitTypes as $type)
                        <option value="{{ $type->id }}" {{ request('work_permit_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                <select name="department_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From Date"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To Date"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-full flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
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

        <!-- Permits Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('reference_number')">
                                Reference
                                @if(request('sort') == 'reference_number')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1 text-blue-600"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('work_title')">
                                Work Title
                                @if(request('sort') == 'work_title')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1 text-blue-600"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('work_start_date')">
                                Start Date
                                @if(request('sort') == 'work_start_date')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1 text-blue-600"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('status')">
                                Status
                                @if(request('sort') == 'status')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1 text-blue-600"></i>
                                @else
                                    <i class="fas fa-sort ml-1 text-gray-400"></i>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($permits as $permit)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_items[]" value="{{ $permit->id }}" class="item-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" onchange="updateBulkActions()">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $permit->reference_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $permit->work_title }}</div>
                                <div class="text-sm text-gray-500">{{ $permit->requestedBy->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $permit->workPermitType->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $permit->work_location }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $permit->work_start_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $permit->work_start_date->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $permit->status == 'active' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $permit->status == 'submitted' || $permit->status == 'under_review' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $permit->status == 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $permit->status == 'closed' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $permit->status == 'draft' ? 'bg-gray-100 text-gray-500' : '' }}
                                    {{ $permit->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $permit->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('work-permits.show', $permit) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($permit->status, ['draft', 'rejected']))
                                        <a href="{{ route('work-permits.edit', $permit) }}" 
                                           class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-file-alt text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-500 text-lg font-medium">No work permits found</p>
                                    <p class="text-gray-400 text-sm mt-2">Get started by creating a new work permit</p>
                                    <a href="{{ route('work-permits.create') }}" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>New Permit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($permits->hasPages())
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    {{ $permits->links() }}
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
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("work-permits.bulk-export") }}';
        
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
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("work-permits.bulk-delete") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);
        
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
        
        const newStatus = prompt('Enter new status (draft, submitted, approved, active, expired, closed, rejected, cancelled):');
        if (!newStatus) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("work-permits.bulk-update") }}';
        
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
    
    // Table Sorting
    function sortTable(column) {
        const currentSort = '{{ request("sort", "created_at") }}';
        const currentDirection = '{{ request("direction", "desc") }}';
        const newDirection = (currentSort === column && currentDirection === 'asc') ? 'desc' : 'asc';
        
        const url = new URL(window.location.href);
        url.searchParams.set('sort', column);
        url.searchParams.set('direction', newDirection);
        window.location.href = url.toString();
    }
    
    // Saved Searches Functions
    const STORAGE_KEY = 'work_permits_saved_searches';
    
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
        
        for (let [key, value] of formData.entries()) {
            if (value) {
                params[key] = value;
            }
        }
        
        const searches = getSavedSearches();
        searches.push({ name, params });
        saveSavedSearches(searches);
        
        alert('Search saved successfully!');
        loadSavedSearches();
    }
    
    function loadSavedSearch(index) {
        const searches = getSavedSearches();
        if (index >= searches.length) return;
        
        const search = searches[index];
        const form = document.getElementById('filterForm');
        
        // Clear all fields first
        form.reset();
        
        // Set saved values
        Object.keys(search.params).forEach(key => {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
                field.value = search.params[key];
            }
        });
        
        // Submit form
        form.submit();
    }
    
    function deleteSavedSearch(index) {
        if (!confirm('Are you sure you want to delete this saved search?')) return;
        
        const searches = getSavedSearches();
        searches.splice(index, 1);
        saveSavedSearches(searches);
        loadSavedSearches();
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('savedSearchesDropdown');
        const button = event.target.closest('button');
        if (!dropdown.contains(event.target) && button && !button.onclick.toString().includes('toggleSavedSearches')) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
