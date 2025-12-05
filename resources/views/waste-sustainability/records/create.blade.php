@extends('layouts.app')

@section('title', 'Create Waste & Sustainability Record')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('waste-sustainability.records.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Waste & Sustainability Record</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('waste-sustainability.records.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Record Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="record_type" class="block text-sm font-medium text-black mb-1">Record Type *</label>
                    <select id="record_type" name="record_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Type</option>
                        <option value="recycling" {{ old('record_type') == 'recycling' ? 'selected' : '' }}>Recycling</option>
                        <option value="waste_segregation" {{ old('record_type') == 'waste_segregation' ? 'selected' : '' }}>Waste Segregation</option>
                        <option value="composting" {{ old('record_type') == 'composting' ? 'selected' : '' }}>Composting</option>
                        <option value="waste_reduction" {{ old('record_type') == 'waste_reduction' ? 'selected' : '' }}>Waste Reduction</option>
                    </select>
                    @error('record_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="record_date" class="block text-sm font-medium text-black mb-1">Record Date *</label>
                    <input type="date" id="record_date" name="record_date" required value="{{ old('record_date', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('record_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-black mb-1">Quantity</label>
                    <input type="number" id="quantity" name="quantity" step="0.01" min="0" value="{{ old('quantity') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-black mb-1">Unit</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit') }}" placeholder="e.g., kg, tons, liters"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="waste_category" class="block text-sm font-medium text-black mb-1">Waste Category</label>
                    <select id="waste_category" name="waste_category"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Category</option>
                        <option value="plastic" {{ old('waste_category') == 'plastic' ? 'selected' : '' }}>Plastic</option>
                        <option value="paper" {{ old('waste_category') == 'paper' ? 'selected' : '' }}>Paper</option>
                        <option value="metal" {{ old('waste_category') == 'metal' ? 'selected' : '' }}>Metal</option>
                        <option value="organic" {{ old('waste_category') == 'organic' ? 'selected' : '' }}>Organic</option>
                        <option value="hazardous" {{ old('waste_category') == 'hazardous' ? 'selected' : '' }}>Hazardous</option>
                        <option value="other" {{ old('waste_category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="disposal_method" class="block text-sm font-medium text-black mb-1">Disposal Method</label>
                    <select id="disposal_method" name="disposal_method"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Method</option>
                        <option value="recycled" {{ old('disposal_method') == 'recycled' ? 'selected' : '' }}>Recycled</option>
                        <option value="composted" {{ old('disposal_method') == 'composted' ? 'selected' : '' }}>Composted</option>
                        <option value="landfilled" {{ old('disposal_method') == 'landfilled' ? 'selected' : '' }}>Landfilled</option>
                        <option value="incinerated" {{ old('disposal_method') == 'incinerated' ? 'selected' : '' }}>Incinerated</option>
                        <option value="reused" {{ old('disposal_method') == 'reused' ? 'selected' : '' }}>Reused</option>
                    </select>
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
                    <label for="recorded_by" class="block text-sm font-medium text-black mb-1">Recorded By</label>
                    <select id="recorded_by" name="recorded_by"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Person</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('recorded_by', auth()->id()) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
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
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('waste-sustainability.records.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Record
            </button>
        </div>
    </form>
</div>
@endsection

