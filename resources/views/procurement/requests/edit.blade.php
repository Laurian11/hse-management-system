@extends('layouts.app')

@section('title', 'Edit Procurement Request')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('procurement.requests.show', $procurementRequest) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Procurement Request</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('procurement.requests.update', $procurementRequest) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Item Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Item Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="item_name" class="block text-sm font-medium text-black mb-1">Item Name *</label>
                    <input type="text" id="item_name" name="item_name" required value="{{ old('item_name', $procurementRequest->item_name) }}"
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
                        <option value="safety_equipment" {{ old('item_category', $procurementRequest->item_category) == 'safety_equipment' ? 'selected' : '' }}>Safety Equipment</option>
                        <option value="ppe" {{ old('item_category', $procurementRequest->item_category) == 'ppe' ? 'selected' : '' }}>PPE</option>
                        <option value="tools" {{ old('item_category', $procurementRequest->item_category) == 'tools' ? 'selected' : '' }}>Tools</option>
                        <option value="materials" {{ old('item_category', $procurementRequest->item_category) == 'materials' ? 'selected' : '' }}>Materials</option>
                        <option value="services" {{ old('item_category', $procurementRequest->item_category) == 'services' ? 'selected' : '' }}>Services</option>
                        <option value="other" {{ old('item_category', $procurementRequest->item_category) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('item_category')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-black mb-1">Quantity *</label>
                    <input type="number" id="quantity" name="quantity" required min="1" value="{{ old('quantity', $procurementRequest->quantity) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('quantity')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-black mb-1">Unit</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $procurementRequest->unit) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-black mb-1">Estimated Cost</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" step="0.01" min="0" value="{{ old('estimated_cost', $procurementRequest->estimated_cost) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-black mb-1">Priority *</label>
                    <select id="priority" name="priority" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="low" {{ old('priority', $procurementRequest->priority) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $procurementRequest->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $procurementRequest->priority) == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority', $procurementRequest->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="required_date" class="block text-sm font-medium text-black mb-1">Required By Date</label>
                    <input type="date" id="required_date" name="required_date" value="{{ old('required_date', $procurementRequest->required_date ? $procurementRequest->required_date->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-black mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $procurementRequest->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="draft" {{ old('status', $procurementRequest->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ old('status', $procurementRequest->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="under_review" {{ old('status', $procurementRequest->status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ old('status', $procurementRequest->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status', $procurementRequest->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="purchased" {{ old('status', $procurementRequest->status) == 'purchased' ? 'selected' : '' }}>Purchased</option>
                        <option value="received" {{ old('status', $procurementRequest->status) == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="cancelled" {{ old('status', $procurementRequest->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description', $procurementRequest->description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="justification" class="block text-sm font-medium text-black mb-1">Justification</label>
                    <textarea id="justification" name="justification" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('justification', $procurementRequest->justification) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes', $procurementRequest->notes) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('procurement.requests.show', $procurementRequest) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Request
            </button>
        </div>
    </form>
</div>
@endsection

