@extends('layouts.app')

@section('title', 'Edit Emergency Equipment')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.equipment.show', $equipment) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Emergency Equipment</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('emergency.equipment.update', $equipment) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Equipment Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Equipment Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="equipment_name" class="block text-sm font-medium text-black mb-1">Equipment Name *</label>
                    <input type="text" id="equipment_name" name="equipment_name" required value="{{ old('equipment_name', $equipment->equipment_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('equipment_name')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="equipment_type" class="block text-sm font-medium text-black mb-1">Equipment Type *</label>
                    <input type="text" id="equipment_type" name="equipment_type" required value="{{ old('equipment_type', $equipment->equipment_type) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('equipment_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">Location *</label>
                    <input type="text" id="location" name="location" required value="{{ old('location', $equipment->location) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('location')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="serial_number" class="block text-sm font-medium text-black mb-1">Serial Number</label>
                    <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="manufacturer" class="block text-sm font-medium text-black mb-1">Manufacturer</label>
                    <input type="text" id="manufacturer" name="manufacturer" value="{{ old('manufacturer', $equipment->manufacturer) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="model" class="block text-sm font-medium text-black mb-1">Model</label>
                    <input type="text" id="model" name="model" value="{{ old('model', $equipment->model) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="active" {{ old('status', $equipment->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $equipment->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="maintenance" {{ old('status', $equipment->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="retired" {{ old('status', $equipment->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="condition" class="block text-sm font-medium text-black mb-1">Condition</label>
                    <input type="text" id="condition" name="condition" value="{{ old('condition', $equipment->condition) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>
            </div>
        </div>

        <!-- Dates & Inspection -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Dates & Inspection</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-black mb-1">Purchase Date</label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-black mb-1">Expiry Date</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $equipment->expiry_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="last_inspection_date" class="block text-sm font-medium text-black mb-1">Last Inspection Date</label>
                    <input type="date" id="last_inspection_date" name="last_inspection_date" value="{{ old('last_inspection_date', $equipment->last_inspection_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="next_inspection_date" class="block text-sm font-medium text-black mb-1">Next Inspection Date</label>
                    <input type="date" id="next_inspection_date" name="next_inspection_date" value="{{ old('next_inspection_date', $equipment->next_inspection_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="inspection_frequency" class="block text-sm font-medium text-black mb-1">Inspection Frequency</label>
                    <input type="text" id="inspection_frequency" name="inspection_frequency" value="{{ old('inspection_frequency', $equipment->inspection_frequency) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="inspected_by" class="block text-sm font-medium text-black mb-1">Inspected By</label>
                    <select id="inspected_by" name="inspected_by"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('inspected_by', $equipment->inspected_by) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="inspection_notes" class="block text-sm font-medium text-black mb-1">Inspection Notes</label>
                    <textarea id="inspection_notes" name="inspection_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('inspection_notes', $equipment->inspection_notes) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes', $equipment->notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('emergency.equipment.show', $equipment) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Equipment
            </button>
        </div>
    </form>
</div>
@endsection

