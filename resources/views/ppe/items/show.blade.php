@extends('layouts.app')

@section('title', 'PPE Item Details')

@section('content')
<script>
    // Track recent item view
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route("recent-items.track") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                title: '{{ $item->name }}',
                url: '{{ route("ppe.items.show", $item) }}',
                module: 'PPE Management',
                icon: 'fa-hard-hat'
            })
        });
    });
</script>
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('ppe.items.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('ppe.items.create', ['copy_from' => $item->id]) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700" title="Copy this item">
                    <i class="fas fa-copy mr-2"></i>Copy
                </a>
                <a href="{{ route('ppe.items.edit', $item) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('ppe.issuances.create', ['ppe_item_id' => $item->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-hand-holding mr-2"></i>Issue
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->category }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->type ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs rounded-full {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($item->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Inventory Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Inventory Status</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Total Quantity</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $item->total_quantity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Available</p>
                        <p class="text-2xl font-bold text-green-600">{{ $item->available_quantity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Issued</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $item->issued_quantity }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Minimum Level</p>
                        <p class="text-2xl font-bold {{ $item->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">{{ $item->minimum_stock_level }}</p>
                    </div>
                </div>
                @if($item->isLowStock())
                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-800"><i class="fas fa-exclamation-triangle mr-2"></i>Low Stock Alert! Reorder needed.</p>
                    </div>
                @endif
            </div>

            <!-- Recent Issuances -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Issuances</h2>
                @if($item->issuances->count() > 0)
                    <div class="space-y-3">
                        @foreach($item->issuances->take(5) as $issuance)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $issuance->issuedTo->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $issuance->issue_date->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $issuance->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($issuance->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No issuances yet</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Info</h3>
                <dl class="space-y-3">
                    @if($item->supplier)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->supplier->name }}</dd>
                    </div>
                    @endif
                    @if($item->unit_cost)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Unit Cost</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->currency }} {{ number_format($item->unit_cost, 2) }}</dd>
                    </div>
                    @endif
                    @if($item->has_expiry)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Expiry Days</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->expiry_days }} days</dd>
                    </div>
                    @endif
                    @if($item->requires_inspection)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Inspection Frequency</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $item->inspection_frequency_days }} days</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Stock Adjustment -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Stock Adjustment</h3>
                <form action="{{ route('ppe.items.adjust-stock', $item) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label for="adjustment_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select id="adjustment_type" name="adjustment_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="add">Add Stock</option>
                            <option value="remove">Remove Stock</option>
                            <option value="set">Set Stock</option>
                        </select>
                    </div>
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" id="quantity" name="quantity" required min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <input type="text" id="reason" name="reason" placeholder="e.g., New purchase, Damaged, etc."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <button type="submit" class="w-full bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700">
                        Adjust Stock
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


