@extends('layouts.app')

@section('title', 'Create Inspection Checklist')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.checklists.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Inspection Checklist</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('inspections.checklists.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-black mb-1">Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('name')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-black mb-1">Category</label>
                    <input type="text" id="category" name="category" value="{{ old('category') }}"
                           placeholder="e.g., Safety, Equipment, Fire Safety"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                        <span class="ml-2 text-sm text-black">Active</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Checklist Items -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Checklist Items *</h2>
            <div id="checklist-items-container" class="space-y-4">
                <div class="checklist-item border border-gray-300 p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Item</label>
                            <input type="text" name="items[0][item]" required
                                   class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-black mb-1">Type</label>
                            <select name="items[0][type]" required
                                    class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                                <option value="yes_no">Yes/No</option>
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="select">Select</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" onclick="removeItem(this)" class="text-sm text-[#CC0000] hover:underline">Remove</button>
                </div>
            </div>
            <button type="button" onclick="addChecklistItem()" class="mt-4 px-4 py-2 bg-white text-black border border-gray-300 hover:bg-[#F5F5F5]">
                <i class="fas fa-plus mr-2"></i>Add Item
            </button>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('inspections.checklists.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Checklist
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = 1;

function addChecklistItem() {
    const container = document.getElementById('checklist-items-container');
    const newItem = document.createElement('div');
    newItem.className = 'checklist-item border border-gray-300 p-4';
    newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
            <div>
                <label class="block text-sm font-medium text-black mb-1">Item</label>
                <input type="text" name="items[${itemIndex}][item]" required
                       class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            </div>
            <div>
                <label class="block text-sm font-medium text-black mb-1">Type</label>
                <select name="items[${itemIndex}][type]" required
                        class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    <option value="yes_no">Yes/No</option>
                    <option value="text">Text</option>
                    <option value="number">Number</option>
                    <option value="date">Date</option>
                    <option value="select">Select</option>
                </select>
            </div>
        </div>
        <button type="button" onclick="removeItem(this)" class="text-sm text-[#CC0000] hover:underline">Remove</button>
    `;
    container.appendChild(newItem);
    itemIndex++;
}

function removeItem(button) {
    button.closest('.checklist-item').remove();
}
</script>
@endpush
@endsection

