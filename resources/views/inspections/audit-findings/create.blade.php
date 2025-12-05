@extends('layouts.app')

@section('title', 'Create Audit Finding')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.audit-findings.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Audit Finding</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('inspections.audit-findings.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="audit_id" class="block text-sm font-medium text-black mb-1">Audit *</label>
                    <select id="audit_id" name="audit_id" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Audit</option>
                        @foreach($audits as $audit)
                            <option value="{{ $audit->id }}" {{ old('audit_id', $selectedAuditId) == $audit->id ? 'selected' : '' }}>
                                {{ $audit->title }} ({{ $audit->reference_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('audit_id')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="finding_type" class="block text-sm font-medium text-black mb-1">Finding Type *</label>
                    <select id="finding_type" name="finding_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="non_conformance" {{ old('finding_type') == 'non_conformance' ? 'selected' : '' }}>Non-Conformance</option>
                        <option value="observation" {{ old('finding_type') == 'observation' ? 'selected' : '' }}>Observation</option>
                        <option value="opportunity_for_improvement" {{ old('finding_type') == 'opportunity_for_improvement' ? 'selected' : '' }}>Opportunity for Improvement</option>
                        <option value="strength" {{ old('finding_type') == 'strength' ? 'selected' : '' }}>Strength</option>
                    </select>
                    @error('finding_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="severity" class="block text-sm font-medium text-black mb-1">Severity *</label>
                    <select id="severity" name="severity" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="major" {{ old('severity') == 'major' ? 'selected' : '' }}>Major</option>
                        <option value="minor" {{ old('severity') == 'minor' ? 'selected' : '' }}>Minor</option>
                        <option value="observation" {{ old('severity') == 'observation' ? 'selected' : '' }}>Observation</option>
                    </select>
                    @error('severity')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="clause_reference" class="block text-sm font-medium text-black mb-1">Clause Reference</label>
                    <input type="text" id="clause_reference" name="clause_reference" value="{{ old('clause_reference') }}"
                           placeholder="e.g., ISO 45001:2018, Clause 8.1.2"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="evidence" class="block text-sm font-medium text-black mb-1">Evidence</label>
                    <input type="text" id="evidence" name="evidence" value="{{ old('evidence') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>
            </div>
        </div>

        <!-- Root Cause & Corrective Action -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Root Cause & Corrective Action</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="root_cause" class="block text-sm font-medium text-black mb-1">Root Cause</label>
                    <textarea id="root_cause" name="root_cause" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('root_cause') }}</textarea>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="corrective_action_required" value="1" {{ old('corrective_action_required', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                        <span class="ml-2 text-sm text-black">Corrective Action Required</span>
                    </label>
                </div>

                <div id="corrective-action-fields" class="space-y-4">
                    <div>
                        <label for="corrective_action_plan" class="block text-sm font-medium text-black mb-1">Corrective Action Plan</label>
                        <textarea id="corrective_action_plan" name="corrective_action_plan" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('corrective_action_plan') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="corrective_action_due_date" class="block text-sm font-medium text-black mb-1">Due Date</label>
                            <input type="date" id="corrective_action_due_date" name="corrective_action_due_date" value="{{ old('corrective_action_due_date') }}"
                                   class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        </div>

                        <div>
                            <label for="corrective_action_assigned_to" class="block text-sm font-medium text-black mb-1">Assign To</label>
                            <select id="corrective_action_assigned_to" name="corrective_action_assigned_to"
                                    class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('corrective_action_assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('inspections.audit-findings.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Finding
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const correctiveActionCheckbox = document.querySelector('input[name="corrective_action_required"]');
    const correctiveActionFields = document.getElementById('corrective-action-fields');
    
    if (correctiveActionCheckbox) {
        correctiveActionCheckbox.addEventListener('change', function() {
            correctiveActionFields.style.display = this.checked ? 'block' : 'none';
        });
        
        if (correctiveActionCheckbox.checked) {
            correctiveActionFields.style.display = 'block';
        }
    }
});
</script>
@endpush
@endsection

