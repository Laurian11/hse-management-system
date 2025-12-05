@extends('layouts.app')

@section('title', 'Create Inspection')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Inspection</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('inspections.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="inspection_date" class="block text-sm font-medium text-black mb-1">Inspection Date *</label>
                    <input type="date" id="inspection_date" name="inspection_date" required value="{{ old('inspection_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('inspection_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="inspection_schedule_id" class="block text-sm font-medium text-black mb-1">Schedule</label>
                    <select id="inspection_schedule_id" name="inspection_schedule_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">None</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}" {{ old('inspection_schedule_id', $selectedScheduleId) == $schedule->id ? 'selected' : '' }}>
                                {{ $schedule->title }} ({{ $schedule->reference_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="inspection_checklist_id" class="block text-sm font-medium text-black mb-1">Checklist</label>
                    <select id="inspection_checklist_id" name="inspection_checklist_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">None</option>
                        @foreach($checklists as $checklist)
                            <option value="{{ $checklist->id }}" {{ old('inspection_checklist_id') == $checklist->id ? 'selected' : '' }}>
                                {{ $checklist->name }}
                            </option>
                        @endforeach
                    </select>
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

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="overall_status" class="block text-sm font-medium text-black mb-1">Overall Status *</label>
                    <select id="overall_status" name="overall_status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="pending" {{ old('overall_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="compliant" {{ old('overall_status') == 'compliant' ? 'selected' : '' }}>Compliant</option>
                        <option value="non_compliant" {{ old('overall_status') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
                        <option value="partial" {{ old('overall_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    </select>
                    @error('overall_status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Checklist Responses -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Checklist Responses</h2>
            <div id="checklist-responses-container">
                <p class="text-sm text-gray-500 mb-4">Select a checklist above to load items, or add items manually.</p>
                <div class="space-y-3" id="checklist-items">
                    <!-- Dynamic checklist items will be added here -->
                </div>
            </div>
            <input type="hidden" name="checklist_responses" id="checklist_responses" value="{{ old('checklist_responses', '[]') }}">
        </div>

        <!-- Findings & Recommendations -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Findings & Recommendations</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="observations" class="block text-sm font-medium text-black mb-1">Observations</label>
                    <textarea id="observations" name="observations" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('observations') }}</textarea>
                </div>

                <div>
                    <label for="recommendations" class="block text-sm font-medium text-black mb-1">Recommendations</label>
                    <textarea id="recommendations" name="recommendations" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('recommendations') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Follow-up -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Follow-up</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="requires_follow_up" value="1" {{ old('requires_follow_up') ? 'checked' : '' }}
                               class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                        <span class="ml-2 text-sm text-black">Requires Follow-up</span>
                    </label>
                </div>

                <div id="follow-up-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;">
                    <div>
                        <label for="follow_up_date" class="block text-sm font-medium text-black mb-1">Follow-up Date</label>
                        <input type="date" id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    </div>

                    <div>
                        <label for="follow_up_assigned_to" class="block text-sm font-medium text-black mb-1">Assign To</label>
                        <select id="follow_up_assigned_to" name="follow_up_assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('follow_up_assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('inspections.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Inspection
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const followUpCheckbox = document.querySelector('input[name="requires_follow_up"]');
    const followUpFields = document.getElementById('follow-up-fields');
    
    if (followUpCheckbox) {
        followUpCheckbox.addEventListener('change', function() {
            followUpFields.style.display = this.checked ? 'grid' : 'none';
        });
        
        if (followUpCheckbox.checked) {
            followUpFields.style.display = 'grid';
        }
    }
});
</script>
@endpush
@endsection

