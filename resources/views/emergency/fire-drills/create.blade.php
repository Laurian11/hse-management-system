@extends('layouts.app')

@section('title', 'Create Fire Drill')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.fire-drills.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Fire Drill</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('emergency.fire-drills.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="drill_date" class="block text-sm font-medium text-black mb-1">Drill Date *</label>
                    <input type="date" id="drill_date" name="drill_date" required value="{{ old('drill_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('drill_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="drill_time" class="block text-sm font-medium text-black mb-1">Drill Time *</label>
                    <input type="time" id="drill_time" name="drill_time" required value="{{ old('drill_time') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('drill_time')
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
                    <label for="drill_type" class="block text-sm font-medium text-black mb-1">Drill Type *</label>
                    <select id="drill_type" name="drill_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="announced" {{ old('drill_type') == 'announced' ? 'selected' : '' }}>Announced</option>
                        <option value="unannounced" {{ old('drill_type') == 'unannounced' ? 'selected' : '' }}>Unannounced</option>
                        <option value="partial" {{ old('drill_type') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="full" {{ old('drill_type') == 'full' ? 'selected' : '' }}>Full</option>
                    </select>
                    @error('drill_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="objectives" class="block text-sm font-medium text-black mb-1">Objectives</label>
                    <textarea id="objectives" name="objectives" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('objectives') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="scenario" class="block text-sm font-medium text-black mb-1">Scenario</label>
                    <textarea id="scenario" name="scenario" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('scenario') }}</textarea>
                </div>

                <div>
                    <label for="expected_participants" class="block text-sm font-medium text-black mb-1">Expected Participants</label>
                    <input type="number" id="expected_participants" name="expected_participants" min="0" value="{{ old('expected_participants') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Drill Results</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="evacuation_time" class="block text-sm font-medium text-black mb-1">Evacuation Time</label>
                    <input type="time" id="evacuation_time" name="evacuation_time" value="{{ old('evacuation_time') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="overall_result" class="block text-sm font-medium text-black mb-1">Overall Result</label>
                    <select id="overall_result" name="overall_result"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Result</option>
                        <option value="excellent" {{ old('overall_result') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                        <option value="good" {{ old('overall_result') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="satisfactory" {{ old('overall_result') == 'satisfactory' ? 'selected' : '' }}>Satisfactory</option>
                        <option value="needs_improvement" {{ old('overall_result') == 'needs_improvement' ? 'selected' : '' }}>Needs Improvement</option>
                        <option value="poor" {{ old('overall_result') == 'poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="observations" class="block text-sm font-medium text-black mb-1">Observations</label>
                    <textarea id="observations" name="observations" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('observations') }}</textarea>
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

                <div id="follow-up-fields" class="space-y-4" style="display: none;">
                    <div>
                        <label for="follow_up_actions" class="block text-sm font-medium text-black mb-1">Follow-up Actions</label>
                        <textarea id="follow_up_actions" name="follow_up_actions" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('follow_up_actions') }}</textarea>
                    </div>

                    <div>
                        <label for="follow_up_due_date" class="block text-sm font-medium text-black mb-1">Follow-up Due Date</label>
                        <input type="date" id="follow_up_due_date" name="follow_up_due_date" value="{{ old('follow_up_due_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('emergency.fire-drills.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Fire Drill
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
            followUpFields.style.display = this.checked ? 'block' : 'none';
        });
        
        if (followUpCheckbox.checked) {
            followUpFields.style.display = 'block';
        }
    }
});
</script>
@endpush
@endsection

