@extends('layouts.app')

@section('title', 'Edit Training Need')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-needs.show', $trainingNeedsAnalysis) }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Training Need
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Training Need</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('training.training-needs.update', $trainingNeedsAnalysis) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Training Details -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Training Title *</label>
                    <input type="text" name="training_title" value="{{ old('training_title', $trainingNeedsAnalysis->training_title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Training Description *</label>
                    <textarea name="training_description" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('training_description', $trainingNeedsAnalysis->training_description) }}</textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gap Analysis</label>
                    <textarea name="gap_analysis" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('gap_analysis', $trainingNeedsAnalysis->gap_analysis) }}</textarea>
                </div>

                <!-- Priority and Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                        <select name="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="low" {{ old('priority', $trainingNeedsAnalysis->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $trainingNeedsAnalysis->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $trainingNeedsAnalysis->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ old('priority', $trainingNeedsAnalysis->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Training Type *</label>
                        <select name="training_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="classroom" {{ old('training_type', $trainingNeedsAnalysis->training_type) == 'classroom' ? 'selected' : '' }}>Classroom</option>
                            <option value="e_learning" {{ old('training_type', $trainingNeedsAnalysis->training_type) == 'e_learning' ? 'selected' : '' }}>E-Learning</option>
                            <option value="on_job_training" {{ old('training_type', $trainingNeedsAnalysis->training_type) == 'on_job_training' ? 'selected' : '' }}>On-Job Training</option>
                            <option value="workshop" {{ old('training_type', $trainingNeedsAnalysis->training_type) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="simulation" {{ old('training_type', $trainingNeedsAnalysis->training_type) == 'simulation' ? 'selected' : '' }}>Simulation</option>
                            <option value="refresher" {{ old('training_type', $trainingNeedsAnalysis->training_type) == 'refresher' ? 'selected' : '' }}>Refresher</option>
                            <option value="certification" {{ old('training_type', $trainingNeedsAnalysis->training_type) == 'certification' ? 'selected' : '' }}>Certification</option>
                            <option value="combination" {{ old('training_type', $trainingNeedsAnalysis->training_type) == 'combination' ? 'selected' : '' }}>Combination</option>
                        </select>
                    </div>
                </div>

                <!-- Target Audience -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Departments</label>
                    <select name="target_departments[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ in_array($department->id, old('target_departments', $trainingNeedsAnalysis->target_departments ?? [])) ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                </div>

                <!-- Regulatory Information -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" name="is_mandatory" id="is_mandatory" value="1" {{ old('is_mandatory', $trainingNeedsAnalysis->is_mandatory) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_mandatory" class="ml-2 block text-sm font-medium text-gray-700">Mandatory Training</label>
                    </div>
                    <div class="flex items-center mb-4">
                        <input type="checkbox" name="is_regulatory" id="is_regulatory" value="1" {{ old('is_regulatory', $trainingNeedsAnalysis->is_regulatory) ? 'checked' : '' }} data-initial-state="{{ old('is_regulatory', $trainingNeedsAnalysis->is_regulatory) ? 'true' : 'false' }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_regulatory" class="ml-2 block text-sm font-medium text-gray-700">Regulatory Requirement</label>
                    </div>
                    <div id="regulatory_fields" class="hidden">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Regulatory Reference</label>
                            <input type="text" name="regulatory_reference" value="{{ old('regulatory_reference', $trainingNeedsAnalysis->regulatory_reference) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Regulatory Deadline</label>
                            <input type="date" name="regulatory_deadline" value="{{ old('regulatory_deadline', $trainingNeedsAnalysis->regulatory_deadline?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('training.training-needs.show', $trainingNeedsAnalysis) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Training Need
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const regulatoryCheckbox = document.getElementById('is_regulatory');
        const regulatoryFields = document.getElementById('regulatory_fields');
        
        // Set initial state based on data attribute
        const initialState = regulatoryCheckbox.getAttribute('data-initial-state') === 'true';
        if (initialState || regulatoryCheckbox.checked) {
            regulatoryFields.classList.remove('hidden');
        }
        
        // Handle toggle
        regulatoryCheckbox.addEventListener('change', function() {
            if (this.checked) {
                regulatoryFields.classList.remove('hidden');
            } else {
                regulatoryFields.classList.add('hidden');
            }
        });
    });
</script>
@endsection
