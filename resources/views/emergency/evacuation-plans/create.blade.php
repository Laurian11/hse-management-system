@extends('layouts.app')

@section('title', 'Create Evacuation Plan')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.evacuation-plans.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Evacuation Plan</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('emergency.evacuation-plans.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">Location *</label>
                    <input type="text" id="location" name="location" required value="{{ old('location') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('location')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="plan_type" class="block text-sm font-medium text-black mb-1">Plan Type *</label>
                    <select id="plan_type" name="plan_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="building" {{ old('plan_type') == 'building' ? 'selected' : '' }}>Building</option>
                        <option value="floor" {{ old('plan_type') == 'floor' ? 'selected' : '' }}>Floor</option>
                        <option value="area" {{ old('plan_type') == 'area' ? 'selected' : '' }}>Area</option>
                        <option value="site" {{ old('plan_type') == 'site' ? 'selected' : '' }}>Site</option>
                        <option value="general" {{ old('plan_type') == 'general' ? 'selected' : '' }}>General</option>
                    </select>
                    @error('plan_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="next_review_date" class="block text-sm font-medium text-black mb-1">Next Review Date</label>
                    <input type="date" id="next_review_date" name="next_review_date" value="{{ old('next_review_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
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

        <!-- Procedures -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Procedures</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="evacuation_procedures" class="block text-sm font-medium text-black mb-1">Evacuation Procedures</label>
                    <textarea id="evacuation_procedures" name="evacuation_procedures" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('evacuation_procedures') }}</textarea>
                </div>

                <div>
                    <label for="accountability_procedures" class="block text-sm font-medium text-black mb-1">Accountability Procedures</label>
                    <textarea id="accountability_procedures" name="accountability_procedures" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('accountability_procedures') }}</textarea>
                </div>

                <div>
                    <label for="special_needs_procedures" class="block text-sm font-medium text-black mb-1">Special Needs Procedures</label>
                    <textarea id="special_needs_procedures" name="special_needs_procedures" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('special_needs_procedures') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('emergency.evacuation-plans.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Plan
            </button>
        </div>
    </form>
</div>
@endsection

