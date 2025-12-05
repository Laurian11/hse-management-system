@extends('layouts.app')

@section('title', 'Create Procurement Request')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('procurement.requests.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Procurement Request</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('procurement.requests.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Item Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Item Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="item_name" class="block text-sm font-medium text-black mb-1">Item Name *</label>
                    <input type="text" id="item_name" name="item_name" required value="{{ old('item_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('item_name')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="item_category" class="block text-sm font-medium text-black mb-1">Category *</label>
                    <select id="item_category" name="item_category" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Category</option>
                        <option value="safety_equipment" {{ old('item_category') == 'safety_equipment' ? 'selected' : '' }}>Safety Equipment</option>
                        <option value="ppe" {{ old('item_category') == 'ppe' ? 'selected' : '' }}>PPE</option>
                        <option value="tools" {{ old('item_category') == 'tools' ? 'selected' : '' }}>Tools</option>
                        <option value="materials" {{ old('item_category') == 'materials' ? 'selected' : '' }}>Materials</option>
                        <option value="services" {{ old('item_category') == 'services' ? 'selected' : '' }}>Services</option>
                        <option value="other" {{ old('item_category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('item_category')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-black mb-1">Quantity *</label>
                    <input type="number" id="quantity" name="quantity" required min="1" value="{{ old('quantity') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('quantity')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-black mb-1">Unit</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit') }}" placeholder="e.g., pieces, kg, liters"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-black mb-1">Estimated Cost</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" step="0.01" min="0" value="{{ old('estimated_cost') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-black mb-1">Priority *</label>
                    <select id="priority" name="priority" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="required_date" class="block text-sm font-medium text-black mb-1">Required By Date</label>
                    <input type="date" id="required_date" name="required_date" value="{{ old('required_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-black mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="justification" class="block text-sm font-medium text-black mb-1">Justification</label>
                    <textarea id="justification" name="justification" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('justification') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('procurement.requests.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Request
            </button>
        </div>
    </form>
</div>
@endsection

