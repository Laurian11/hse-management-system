@extends('layouts.app')

@section('title', 'Edit Audit')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.audits.show', $audit) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Audit</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('inspections.audits.update', $audit) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $audit->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="audit_type" class="block text-sm font-medium text-black mb-1">Audit Type *</label>
                    <select id="audit_type" name="audit_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="internal" {{ old('audit_type', $audit->audit_type) == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="external" {{ old('audit_type', $audit->audit_type) == 'external' ? 'selected' : '' }}>External</option>
                        <option value="certification" {{ old('audit_type', $audit->audit_type) == 'certification' ? 'selected' : '' }}>Certification</option>
                        <option value="regulatory" {{ old('audit_type', $audit->audit_type) == 'regulatory' ? 'selected' : '' }}>Regulatory</option>
                        <option value="supplier" {{ old('audit_type', $audit->audit_type) == 'supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                    @error('audit_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="scope" class="block text-sm font-medium text-black mb-1">Scope *</label>
                    <select id="scope" name="scope" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="full" {{ old('scope', $audit->scope) == 'full' ? 'selected' : '' }}>Full</option>
                        <option value="partial" {{ old('scope', $audit->scope) == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="focused" {{ old('scope', $audit->scope) == 'focused' ? 'selected' : '' }}>Focused</option>
                    </select>
                    @error('scope')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description', $audit->description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="scope_description" class="block text-sm font-medium text-black mb-1">Scope Description</label>
                    <textarea id="scope_description" name="scope_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('scope_description', $audit->scope_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Schedule</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="planned_start_date" class="block text-sm font-medium text-black mb-1">Planned Start Date *</label>
                    <input type="date" id="planned_start_date" name="planned_start_date" required value="{{ old('planned_start_date', $audit->planned_start_date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('planned_start_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="planned_end_date" class="block text-sm font-medium text-black mb-1">Planned End Date *</label>
                    <input type="date" id="planned_end_date" name="planned_end_date" required value="{{ old('planned_end_date', $audit->planned_end_date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('planned_end_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="actual_start_date" class="block text-sm font-medium text-black mb-1">Actual Start Date</label>
                    <input type="date" id="actual_start_date" name="actual_start_date" value="{{ old('actual_start_date', $audit->actual_start_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="actual_end_date" class="block text-sm font-medium text-black mb-1">Actual End Date</label>
                    <input type="date" id="actual_end_date" name="actual_end_date" value="{{ old('actual_end_date', $audit->actual_end_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="planned" {{ old('status', $audit->status) == 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="in_progress" {{ old('status', $audit->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $audit->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $audit->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="postponed" {{ old('status', $audit->status) == 'postponed' ? 'selected' : '' }}>Postponed</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="result" class="block text-sm font-medium text-black mb-1">Result</label>
                    <select id="result" name="result"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Result</option>
                        <option value="compliant" {{ old('result', $audit->result) == 'compliant' ? 'selected' : '' }}>Compliant</option>
                        <option value="non_compliant" {{ old('result', $audit->result) == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
                        <option value="partial" {{ old('result', $audit->result) == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="pending" {{ old('result', $audit->result) == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
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
                            <option value="{{ $user->id }}" {{ old('lead_auditor_id', $audit->lead_auditor_id) == $user->id ? 'selected' : '' }}>
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
                            <option value="{{ $department->id }}" {{ old('department_id', $audit->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Summary & Conclusion -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Summary & Conclusion</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="executive_summary" class="block text-sm font-medium text-black mb-1">Executive Summary</label>
                    <textarea id="executive_summary" name="executive_summary" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('executive_summary', $audit->executive_summary) }}</textarea>
                </div>

                <div>
                    <label for="conclusion" class="block text-sm font-medium text-black mb-1">Conclusion</label>
                    <textarea id="conclusion" name="conclusion" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('conclusion', $audit->conclusion) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Follow-up -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Follow-up</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="follow_up_required" value="1" {{ old('follow_up_required', $audit->follow_up_required) ? 'checked' : '' }}
                               class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                        <span class="ml-2 text-sm text-black">Follow-up Required</span>
                    </label>
                </div>

                <div id="follow-up-fields" style="display: {{ old('follow_up_required', $audit->follow_up_required) ? 'block' : 'none' }};">
                    <div>
                        <label for="follow_up_date" class="block text-sm font-medium text-black mb-1">Follow-up Date</label>
                        <input type="date" id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date', $audit->follow_up_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('inspections.audits.show', $audit) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Audit
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const followUpCheckbox = document.querySelector('input[name="follow_up_required"]');
    const followUpFields = document.getElementById('follow-up-fields');
    
    if (followUpCheckbox) {
        followUpCheckbox.addEventListener('change', function() {
            followUpFields.style.display = this.checked ? 'block' : 'none';
        });
    }
});
</script>
@endpush
@endsection

