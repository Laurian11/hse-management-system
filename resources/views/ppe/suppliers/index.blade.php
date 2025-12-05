@extends('layouts.app')

@section('title', 'PPE Suppliers')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">PPE Suppliers</h1>
                <p class="text-sm text-gray-600 mt-1">Manage PPE suppliers and procurement</p>
            </div>
            <a href="{{ route('ppe.suppliers.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>New Supplier
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Total Suppliers</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Preferred</p>
            <p class="text-2xl font-bold text-teal-600">{{ $stats['preferred'] }}</p>
        </div>
    </div>

    <!-- Suppliers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($suppliers as $supplier)
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $supplier->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $supplier->reference_number }}</p>
                    </div>
                    @if($supplier->is_preferred)
                        <span class="px-2 py-1 text-xs rounded-full bg-teal-100 text-teal-800">Preferred</span>
                    @endif
                </div>
                <dl class="space-y-2 mb-4">
                    @if($supplier->contact_person)
                    <div>
                        <dt class="text-xs text-gray-500">Contact</dt>
                        <dd class="text-sm text-gray-900">{{ $supplier->contact_person }}</dd>
                    </div>
                    @endif
                    @if($supplier->email)
                    <div>
                        <dt class="text-xs text-gray-500">Email</dt>
                        <dd class="text-sm text-gray-900">{{ $supplier->email }}</dd>
                    </div>
                    @endif
                    @if($supplier->phone)
                    <div>
                        <dt class="text-xs text-gray-500">Phone</dt>
                        <dd class="text-sm text-gray-900">{{ $supplier->phone }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs text-gray-500">Items</dt>
                        <dd class="text-sm text-gray-900">{{ $supplier->ppe_items_count ?? 0 }} items</dd>
                    </div>
                </dl>
                <div class="flex space-x-2">
                    <a href="{{ route('ppe.suppliers.show', $supplier) }}" class="flex-1 text-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">View</a>
                    <a href="{{ route('ppe.suppliers.edit', $supplier) }}" class="flex-1 text-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Edit</a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500">No suppliers found</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection

