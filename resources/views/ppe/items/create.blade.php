@extends('layouts.app')

@section('title', 'Create PPE Item')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('ppe.items.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isset($copyFrom) ? 'Copy PPE Item' : 'Create PPE Item' }}
                    @if(isset($copyFrom))
                        <span class="text-sm font-normal text-gray-500">(from {{ $copyFrom->reference_number }})</span>
                    @endif
                </h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('ppe.items.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', isset($copyFrom) ? $copyFrom->name : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <input type="text" id="category" name="category" required value="{{ old('category', isset($copyFrom) ? $copyFrom->category : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                           placeholder="e.g., Helmet, Safety Glasses, Gloves">
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <input type="text" id="type" name="type" value="{{ old('type', isset($copyFrom) ? $copyFrom->type : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">{{ old('description', isset($copyFrom) ? $copyFrom->description : '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Inventory Management -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Inventory Management</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="total_quantity" class="block text-sm font-medium text-gray-700 mb-1">Total Quantity *</label>
                    <input type="number" id="total_quantity" name="total_quantity" required value="{{ old('total_quantity', 0) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('total_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="minimum_stock_level" class="block text-sm font-medium text-gray-700 mb-1">Minimum Stock Level *</label>
                    <input type="number" id="minimum_stock_level" name="minimum_stock_level" required value="{{ old('minimum_stock_level', 0) }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    @error('minimum_stock_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reorder_quantity" class="block text-sm font-medium text-gray-700 mb-1">Reorder Quantity</label>
                    <input type="number" id="reorder_quantity" name="reorder_quantity" value="{{ old('reorder_quantity') }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        <!-- Expiry & Inspection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Expiry & Inspection</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-center">
                    <input type="checkbox" id="has_expiry" name="has_expiry" value="1" {{ old('has_expiry') ? 'checked' : '' }}
                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                    <label for="has_expiry" class="ml-2 block text-sm text-gray-900">Has Expiry Date</label>
                </div>

                <div id="expiry_fields" style="display: none;">
                    <label for="expiry_days" class="block text-sm font-medium text-gray-700 mb-1">Expiry Days</label>
                    <input type="number" id="expiry_days" name="expiry_days" value="{{ old('expiry_days') }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div id="replacement_alert_fields" style="display: none;">
                    <label for="replacement_alert_days" class="block text-sm font-medium text-gray-700 mb-1">Replacement Alert Days</label>
                    <input type="number" id="replacement_alert_days" name="replacement_alert_days" value="{{ old('replacement_alert_days', 30) }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="requires_inspection" name="requires_inspection" value="1" {{ old('requires_inspection', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                    <label for="requires_inspection" class="ml-2 block text-sm text-gray-900">Requires Inspection</label>
                </div>

                <div id="inspection_fields">
                    <label for="inspection_frequency_days" class="block text-sm font-medium text-gray-700 mb-1">Inspection Frequency (Days)</label>
                    <input type="number" id="inspection_frequency_days" name="inspection_frequency_days" value="{{ old('inspection_frequency_days', 90) }}" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        <!-- Supplier & Procurement -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Supplier & Procurement</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <select id="supplier_id" name="supplier_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-1">Unit Cost</label>
                    <input type="number" id="unit_cost" name="unit_cost" step="0.01" value="{{ old('unit_cost') }}" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select id="status" name="status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                </select>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('ppe.items.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">Create Item</button>
        </div>
    </form>
</div>

<script>
document.getElementById('has_expiry').addEventListener('change', function() {
    const expiryFields = document.getElementById('expiry_fields');
    const replacementFields = document.getElementById('replacement_alert_fields');
    if (this.checked) {
        expiryFields.style.display = 'block';
        replacementFields.style.display = 'block';
    } else {
        expiryFields.style.display = 'none';
        replacementFields.style.display = 'none';
    }
});

document.getElementById('requires_inspection').addEventListener('change', function() {
    const inspectionFields = document.getElementById('inspection_fields');
    if (this.checked) {
        inspectionFields.style.display = 'block';
    } else {
        inspectionFields.style.display = 'none';
    }
});
</script>
@endsection

