@extends('layouts.app')

@section('title', 'Create Audit')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.audits.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Audit</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('inspections.audits.store') }}" method="POST" class="space-y-6">
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
                    <label for="audit_type" class="block text-sm font-medium text-black mb-1">Audit Type *</label>
                    <select id="audit_type" name="audit_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="internal" {{ old('audit_type') == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="external" {{ old('audit_type') == 'external' ? 'selected' : '' }}>External</option>
                        <option value="certification" {{ old('audit_type') == 'certification' ? 'selected' : '' }}>Certification</option>
                        <option value="regulatory" {{ old('audit_type') == 'regulatory' ? 'selected' : '' }}>Regulatory</option>
                        <option value="supplier" {{ old('audit_type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                    @error('audit_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="scope" class="block text-sm font-medium text-black mb-1">Scope *</label>
                    <select id="scope" name="scope" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="full" {{ old('scope') == 'full' ? 'selected' : '' }}>Full</option>
                        <option value="partial" {{ old('scope') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="focused" {{ old('scope') == 'focused' ? 'selected' : '' }}>Focused</option>
                    </select>
                    @error('scope')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="scope_description" class="block text-sm font-medium text-black mb-1">Scope Description</label>
                    <textarea id="scope_description" name="scope_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('scope_description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Schedule</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="planned_start_date" class="block text-sm font-medium text-black mb-1">Planned Start Date *</label>
                    <input type="date" id="planned_start_date" name="planned_start_date" required value="{{ old('planned_start_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('planned_start_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="planned_end_date" class="block text-sm font-medium text-black mb-1">Planned End Date *</label>
                    <input type="date" id="planned_end_date" name="planned_end_date" required value="{{ old('planned_end_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('planned_end_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Team -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Audit Team</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="lead_auditor_id" class="block text-sm font-medium text-black mb-1">Lead Auditor *</label>
                    <select id="lead_auditor_id" name="lead_auditor_id" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Lead Auditor</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('lead_auditor_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('lead_auditor_id')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
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
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('inspections.audits.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Audit
            </button>
        </div>
    </form>
</div>
@endsection

