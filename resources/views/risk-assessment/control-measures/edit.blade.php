@extends('layouts.app')

@section('title', 'Edit Control Measure')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.control-measures.show', $controlMeasure) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Control Measure</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('risk-assessment.control-measures.update', $controlMeasure) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $controlMeasure->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description', $controlMeasure->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="control_type" class="block text-sm font-medium text-black mb-1">Control Type *</label>
                        <select id="control_type" name="control_type" required
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select Type</option>
                            <option value="elimination" {{ old('control_type', $controlMeasure->control_type) == 'elimination' ? 'selected' : '' }}>Elimination</option>
                            <option value="substitution" {{ old('control_type', $controlMeasure->control_type) == 'substitution' ? 'selected' : '' }}>Substitution</option>
                            <option value="engineering" {{ old('control_type', $controlMeasure->control_type) == 'engineering' ? 'selected' : '' }}>Engineering Controls</option>
                            <option value="administrative" {{ old('control_type', $controlMeasure->control_type) == 'administrative' ? 'selected' : '' }}>Administrative Controls</option>
                            <option value="ppe" {{ old('control_type', $controlMeasure->control_type) == 'ppe' ? 'selected' : '' }}>Personal Protective Equipment</option>
                            <option value="combination" {{ old('control_type', $controlMeasure->control_type) == 'combination' ? 'selected' : '' }}>Combination</option>
                        </select>
                        @error('control_type')
                            <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-black mb-1">Priority *</label>
                        <select id="priority" name="priority" required
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority', $controlMeasure->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $controlMeasure->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $controlMeasure->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ old('priority', $controlMeasure->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="effectiveness_level" class="block text-sm font-medium text-black mb-1">Effectiveness Level</label>
                        <select id="effectiveness_level" name="effectiveness_level"
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select Level</option>
                            <option value="low" {{ old('effectiveness_level', $controlMeasure->effectiveness_level) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('effectiveness_level', $controlMeasure->effectiveness_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('effectiveness_level', $controlMeasure->effectiveness_level) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="very_high" {{ old('effectiveness_level', $controlMeasure->effectiveness_level) == 'very_high' ? 'selected' : '' }}>Very High</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="planned" {{ old('status', $controlMeasure->status) == 'planned' ? 'selected' : '' }}>Planned</option>
                            <option value="in_progress" {{ old('status', $controlMeasure->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="implemented" {{ old('status', $controlMeasure->status) == 'implemented' ? 'selected' : '' }}>Implemented</option>
                            <option value="verified" {{ old('status', $controlMeasure->status) == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="ineffective" {{ old('status', $controlMeasure->status) == 'ineffective' ? 'selected' : '' }}>Ineffective</option>
                            <option value="closed" {{ old('status', $controlMeasure->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="cancelled" {{ old('status', $controlMeasure->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment & Timeline -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Assignment & Timeline</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-black mb-1">Assigned To</label>
                    <select id="assigned_to" name="assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $controlMeasure->assigned_to) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="responsible_party" class="block text-sm font-medium text-black mb-1">Responsible Party</label>
                    <select id="responsible_party" name="responsible_party"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('responsible_party', $controlMeasure->responsible_party) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="target_completion_date" class="block text-sm font-medium text-black mb-1">Target Completion Date</label>
                    <input type="date" id="target_completion_date" name="target_completion_date" 
                           value="{{ old('target_completion_date', $controlMeasure->target_completion_date ? $controlMeasure->target_completion_date->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="actual_completion_date" class="block text-sm font-medium text-black mb-1">Actual Completion Date</label>
                    <input type="date" id="actual_completion_date" name="actual_completion_date" 
                           value="{{ old('actual_completion_date', $controlMeasure->actual_completion_date ? $controlMeasure->actual_completion_date->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-black mb-1">Estimated Cost</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" step="0.01" min="0"
                           value="{{ old('estimated_cost', $controlMeasure->estimated_cost) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="actual_cost" class="block text-sm font-medium text-black mb-1">Actual Cost</label>
                    <input type="number" id="actual_cost" name="actual_cost" step="0.01" min="0"
                           value="{{ old('actual_cost', $controlMeasure->actual_cost) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>
            </div>

            <div class="mt-4">
                <label for="resources_required" class="block text-sm font-medium text-black mb-1">Resources Required</label>
                <textarea id="resources_required" name="resources_required" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('resources_required', $controlMeasure->resources_required) }}</textarea>
            </div>
        </div>

        <!-- Verification -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Verification</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="verification_date" class="block text-sm font-medium text-black mb-1">Verification Date</label>
                    <input type="date" id="verification_date" name="verification_date" 
                           value="{{ old('verification_date', $controlMeasure->verification_date ? $controlMeasure->verification_date->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="verification_method" class="block text-sm font-medium text-black mb-1">Verification Method</label>
                    <input type="text" id="verification_method" name="verification_method" 
                           value="{{ old('verification_method', $controlMeasure->verification_method) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>
            </div>

            <div class="mt-4">
                <label for="verification_results" class="block text-sm font-medium text-black mb-1">Verification Results</label>
                <textarea id="verification_results" name="verification_results" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('verification_results', $controlMeasure->verification_results) }}</textarea>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_effective" value="1" 
                               {{ old('is_effective', $controlMeasure->is_effective) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-sm font-medium text-black">Is Effective</span>
                    </label>
                </div>
            </div>

            <div class="mt-4">
                <label for="effectiveness_notes" class="block text-sm font-medium text-black mb-1">Effectiveness Notes</label>
                <textarea id="effectiveness_notes" name="effectiveness_notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('effectiveness_notes', $controlMeasure->effectiveness_notes) }}</textarea>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('risk-assessment.control-measures.show', $controlMeasure) }}" 
               class="px-6 py-2 border border-gray-300 text-black hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                Update Control Measure
            </button>
        </div>
    </form>
</div>
@endsection

