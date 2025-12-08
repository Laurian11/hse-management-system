@extends('layouts.app')

@section('title', 'Safety Communications')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-gray-900">Safety Communications</h1>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('safety-communications.export-all', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                   class="px-4 py-2 text-green-700 bg-white border border-green-300 rounded-lg hover:bg-green-50">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
                <a href="{{ route('safety-communications.export-all', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                   class="px-4 py-2 text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </a>
                <a href="{{ route('safety-communications.reports.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-chart-bar mr-2"></i>Reports
                </a>
                <a href="{{ route('safety-communications.dashboard') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-chart-pie mr-2"></i>Dashboard
                </a>
                <a href="{{ route('safety-communications.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>New Communication
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded flex items-center justify-center">
                    <i class="fas fa-bullhorn text-blue-600 text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-900">Total Communications</h3>
            <p class="text-xs text-gray-500">All messages</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded flex items-center justify-center">
                    <i class="fas fa-paper-plane text-green-600 text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $stats['sent'] }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-900">Sent</h3>
            <p class="text-xs text-gray-500">Delivered messages</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $stats['scheduled'] }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-900">Scheduled</h3>
            <p class="text-xs text-gray-500">Pending delivery</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded flex items-center justify-center">
                    <i class="fas fa-eye text-purple-600 text-lg"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ $stats['draft'] }}</span>
            </div>
            <h3 class="text-sm font-medium text-gray-900">Drafts</h3>
            <p class="text-xs text-gray-500">Unsent messages</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('safety-communications.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Search communications...">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="announcement" {{ request('type') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                    <option value="alert" {{ request('type') == 'alert' ? 'selected' : '' }}>Safety Alert</option>
                    <option value="bulletin" {{ request('type') == 'bulletin' ? 'selected' : '' }}>Bulletin</option>
                    <option value="emergency" {{ request('type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    <option value="reminder" {{ request('type') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                    <option value="policy_update" {{ request('type') == 'policy_update' ? 'selected' : '' }}>Policy Update</option>
                    <option value="training_notice" {{ request('type') == 'training_notice' ? 'selected' : '' }}>Training Notice</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="emergency" {{ request('priority') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('safety-communications.index') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <span id="selectedCount" class="text-sm font-medium text-gray-700">0 selected</span>
                <div class="flex items-center space-x-2">
                    <select id="bulkAction" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">Select Action</option>
                        <option value="export-excel">Export to Excel</option>
                        <option value="export-pdf">Export to PDF</option>
                        <option value="status-draft">Set Status: Draft</option>
                        <option value="status-scheduled">Set Status: Scheduled</option>
                        <option value="status-sent">Set Status: Sent</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button onclick="executeBulkAction()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        Apply
                    </button>
                </div>
            </div>
            <button onclick="clearSelection()" class="text-sm text-gray-600 hover:text-gray-800">
                <i class="fas fa-times mr-1"></i>Clear
            </button>
        </div>
    </div>

    <!-- Communications List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" onchange="toggleAll(this)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipients</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($communications as $communication)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected[]" value="{{ $communication->id }}" 
                                   onchange="updateBulkActionsBar()" 
                                   class="communication-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $communication->title }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($communication->message, 80) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $communication->communication_type === 'alert' ? 'bg-red-100 text-red-800' : 
                                   ($communication->communication_type === 'reminder' ? 'bg-yellow-100 text-yellow-800' :
                                   ($communication->communication_type === 'announcement' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                                {{ ucfirst($communication->communication_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $communication->status === 'sent' ? 'bg-green-100 text-green-800' : 
                                   ($communication->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($communication->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $communication->total_recipients ?? 0 }} recipients
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $communication->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('safety-communications.show', $communication) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            <a href="{{ route('safety-communications.edit', $communication) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No communications found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($communications->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                {{ $communications->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.communication-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateBulkActionsBar();
}

function updateBulkActionsBar() {
    const selected = document.querySelectorAll('.communication-checkbox:checked');
    const bar = document.getElementById('bulkActionsBar');
    const count = document.getElementById('selectedCount');
    
    if (selected.length > 0) {
        bar.classList.remove('hidden');
        count.textContent = selected.length + ' selected';
    } else {
        bar.classList.add('hidden');
    }
}

function clearSelection() {
    document.querySelectorAll('.communication-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateBulkActionsBar();
}

function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selected = Array.from(document.querySelectorAll('.communication-checkbox:checked')).map(cb => cb.value);
    
    if (!action || selected.length === 0) {
        alert('Please select an action and at least one communication.');
        return;
    }
    
    if (action === 'delete' && !confirm('Are you sure you want to delete ' + selected.length + ' communication(s)?')) {
        return;
    }
    
    if (action.startsWith('export-')) {
        const format = action.split('-')[1];
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("safety-communications.bulk-export") }}';
        
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
        
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = format;
        form.appendChild(formatInput);
        
        document.body.appendChild(form);
        form.submit();
    } else if (action.startsWith('status-')) {
        const status = action.split('-')[1];
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("safety-communications.bulk-update") }}';
        
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
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        document.body.appendChild(form);
        form.submit();
    } else if (action === 'delete') {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("safety-communications.bulk-delete") }}';
        
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
}
</script>
@endpush
@endsection
