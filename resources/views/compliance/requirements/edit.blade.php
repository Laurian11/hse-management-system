@extends('layouts.app')

@section('title', 'Edit Compliance Requirement')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('compliance.requirements.show', $requirement) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Compliance Requirement</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('compliance.requirements.update', $requirement) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Requirement Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="requirement_title" class="block text-sm font-medium text-black mb-1">Requirement Title *</label>
                    <input type="text" id="requirement_title" name="requirement_title" required value="{{ old('requirement_title', $requirement->requirement_title) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('requirement_title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="regulatory_body" class="block text-sm font-medium text-black mb-1">Regulatory Body *</label>
                    <input type="text" id="regulatory_body" name="regulatory_body" required value="{{ old('regulatory_body', $requirement->regulatory_body) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="compliance_status" class="block text-sm font-medium text-black mb-1">Compliance Status *</label>
                    <select id="compliance_status" name="compliance_status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="compliant" {{ old('compliance_status', $requirement->compliance_status) == 'compliant' ? 'selected' : '' }}>Compliant</option>
                        <option value="non_compliant" {{ old('compliance_status', $requirement->compliance_status) == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
                        <option value="partially_compliant" {{ old('compliance_status', $requirement->compliance_status) == 'partially_compliant' ? 'selected' : '' }}>Partially Compliant</option>
                        <option value="not_applicable" {{ old('compliance_status', $requirement->compliance_status) == 'not_applicable' ? 'selected' : '' }}>Not Applicable</option>
                        <option value="under_review" {{ old('compliance_status', $requirement->compliance_status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    </select>
                </div>

                <div>
                    <label for="compliance_due_date" class="block text-sm font-medium text-black mb-1">Compliance Due Date</label>
                    <input type="date" id="compliance_due_date" name="compliance_due_date" value="{{ old('compliance_due_date', $requirement->compliance_due_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description', $requirement->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('compliance.requirements.show', $requirement) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Requirement
            </button>
        </div>
    </form>
</div>
@endsection

