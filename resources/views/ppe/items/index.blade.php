@extends('layouts.app')

@section('title', 'PPE Inventory')

@php
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => route('dashboard'), 'icon' => 'fa-home'],
    ['label' => 'PPE Management', 'url' => route('ppe.dashboard'), 'icon' => 'fa-hard-hat'],
    ['label' => 'Inventory', 'url' => route('ppe.items.index'), 'active' => true]
];
@endphp

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">PPE Inventory</h1>
                <p class="text-sm text-gray-600 mt-1">Manage Personal Protective Equipment items</p>
            </div>
            <div class="flex space-x-3">
                <x-print-button />
                <a href="{{ route('ppe.items.export', request()->all()) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Export
                </a>
                <a href="{{ route('ppe.items.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>New Item
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Items</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Low Stock</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['low_stock'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Need Reorder</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['needs_reorder'] }}</p>
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
                            <button onclick="saveCurrentSearch()" class="w-full mt-2 px-3 py-2 text-sm bg-teal-600 text-white rounded hover:bg-teal-700 transition-colors">
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
        <form method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items..." 
                   class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
            <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
            <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
            </select>
            <select name="low_stock" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                <option value="">All Stock Levels</option>
                <option value="1" {{ request('low_stock') == '1' ? 'selected' : '' }}>Low Stock Only</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700">Filter</button>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Bar (hidden by default) -->
    <div id="bulkActionsBar" class="hidden bg-teal-50 border border-teal-200 rounded-lg p-4 mb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span id="selectedCount" class="text-sm font-medium text-teal-900">0 items selected</span>
                <div class="flex items-center space-x-2">
                    <button onclick="bulkExport()" class="px-3 py-1 bg-teal-600 text-white text-sm rounded hover:bg-teal-700 transition-colors">
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
            <button onclick="clearSelection()" class="text-teal-600 hover:text-teal-800 text-sm font-medium">
                <i class="fas fa-times mr-1"></i>Clear Selection
            </button>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('name')">
                        Item <i class="fas fa-sort ml-1 text-gray-400"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('category')">
                        Category <i class="fas fa-sort ml-1 text-gray-400"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('available_quantity')">
                        Stock <i class="fas fa-sort ml-1 text-gray-400"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('supplier_id')">
                        Supplier <i class="fas fa-sort ml-1 text-gray-400"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('status')">
                        Status <i class="fas fa-sort ml-1 text-gray-400"></i>
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" class="item-checkbox w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500" onchange="updateBulkActions()">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                <div class="text-sm text-gray-500">{{ $item->reference_number }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $item->category }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <span class="font-medium {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ $item->available_quantity }} / {{ $item->total_quantity }}
                                </span>
                                <span class="text-gray-500">available</span>
                            </div>
                            @if($item->isLowStock())
                                <div class="text-xs text-red-600 mt-1">Low Stock!</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->supplier->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('ppe.items.show', $item) }}" class="text-teal-600 hover:text-teal-900 mr-3">View</a>
                            <a href="{{ route('ppe.items.edit', $item) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No items found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $items->appends(request()->query())->links() }}
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
        form.action = '{{ route("ppe.items.bulk-export") }}';
        
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
        form.action = '{{ route("ppe.items.bulk-delete") }}';
        
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
        
        const newStatus = prompt('Enter new status (active, inactive, discontinued):');
        if (!newStatus) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("ppe.items.bulk-update") }}';
        
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
    let currentSort = {
        column: '{{ request()->get("sort", "created_at") }}',
        direction: '{{ request()->get("direction", "desc") }}'
    };
    
    function sortTable(column) {
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }
        
        const url = new URL(window.location.href);
        url.searchParams.set('sort', currentSort.column);
        url.searchParams.set('direction', currentSort.direction);
        window.location.href = url.toString();
    }
    
    function clearFilters() {
        window.location.href = '{{ route("ppe.items.index") }}';
    }
    
    // Saved Searches Functions
    const STORAGE_KEY_PPE = 'ppe_items_saved_searches';
    
    function getSavedSearches() {
        const saved = localStorage.getItem(STORAGE_KEY_PPE);
        return saved ? JSON.parse(saved) : [];
    }
    
    function saveSavedSearches(searches) {
        localStorage.setItem(STORAGE_KEY_PPE, JSON.stringify(searches));
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
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('savedSearchesContainer');
        if (container && !container.contains(event.target)) {
            document.getElementById('savedSearchesDropdown').classList.add('hidden');
        }
    });
    
    // Update sort indicators on page load
    document.addEventListener('DOMContentLoaded', function() {
        const sortColumn = '{{ request()->get("sort", "created_at") }}';
        const sortDirection = '{{ request()->get("direction", "desc") }}';
        
        document.querySelectorAll('th[onclick*="sortTable"]').forEach(th => {
            const icon = th.querySelector('i');
            if (th.getAttribute('onclick').includes(`'${sortColumn}'`)) {
                icon.className = `fas fa-sort-${sortDirection === 'asc' ? 'up' : 'down'} ml-1 text-teal-600`;
            }
        });
        
        updateBulkActions();
        loadSavedSearches();
    });
</script>
@endpush
@endsection

