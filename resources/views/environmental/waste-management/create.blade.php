@extends('layouts.app')

@section('title', 'Create Waste Management Record')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('environmental.waste-management.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Waste Management Record</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('environmental.waste-management.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Waste Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="waste_type" class="block text-sm font-medium text-black mb-1">Waste Type *</label>
                    <input type="text" id="waste_type" name="waste_type" required value="{{ old('waste_type') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('waste_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-black mb-1">Category</label>
                    <input type="text" id="category" name="category" value="{{ old('category') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="segregation_status" class="block text-sm font-medium text-black mb-1">Segregation Status *</label>
                    <select id="segregation_status" name="segregation_status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="properly_segregated" {{ old('segregation_status') == 'properly_segregated' ? 'selected' : '' }}>Properly Segregated</option>
                        <option value="improperly_segregated" {{ old('segregation_status') == 'improperly_segregated' ? 'selected' : '' }}>Improperly Segregated</option>
                        <option value="mixed" {{ old('segregation_status') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                    </select>
                    @error('segregation_status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-black mb-1">Quantity</label>
                    <input type="number" id="quantity" name="quantity" step="0.01" min="0" value="{{ old('quantity') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-black mb-1">Unit</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit') }}" placeholder="kg, liters, etc."
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="storage_location" class="block text-sm font-medium text-black mb-1">Storage Location</label>
                    <input type="text" id="storage_location" name="storage_location" value="{{ old('storage_location') }}"
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

                <div>
                    <label for="collection_date" class="block text-sm font-medium text-black mb-1">Collection Date</label>
                    <input type="date" id="collection_date" name="collection_date" value="{{ old('collection_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="disposal_date" class="block text-sm font-medium text-black mb-1">Disposal Date</label>
                    <input type="date" id="disposal_date" name="disposal_date" value="{{ old('disposal_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="disposal_method" class="block text-sm font-medium text-black mb-1">Disposal Method</label>
                    <select id="disposal_method" name="disposal_method"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Method</option>
                        <option value="landfill" {{ old('disposal_method') == 'landfill' ? 'selected' : '' }}>Landfill</option>
                        <option value="incineration" {{ old('disposal_method') == 'incineration' ? 'selected' : '' }}>Incineration</option>
                        <option value="recycling" {{ old('disposal_method') == 'recycling' ? 'selected' : '' }}>Recycling</option>
                        <option value="treatment" {{ old('disposal_method') == 'treatment' ? 'selected' : '' }}>Treatment</option>
                        <option value="reuse" {{ old('disposal_method') == 'reuse' ? 'selected' : '' }}>Reuse</option>
                        <option value="other" {{ old('disposal_method') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="disposal_contractor_id" class="block text-sm font-medium text-black mb-1">Disposal Contractor</label>
                    <select id="disposal_contractor_id" name="disposal_contractor_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Contractor</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('disposal_contractor_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('environmental.waste-management.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Record
            </button>
        </div>
    </form>
</div>
@endsection

