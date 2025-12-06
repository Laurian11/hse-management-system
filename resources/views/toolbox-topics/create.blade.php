@extends('layouts.app')

@section('title', 'Create Topic')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('toolbox-topics.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Topics
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Create Topic</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('toolbox-topics.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Topic Title *</label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter topic title" value="{{ old('title') }}">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select id="category" name="category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Category</option>
                        <option value="safety" {{ old('category') == 'safety' ? 'selected' : '' }}>Safety</option>
                        <option value="health" {{ old('category') == 'health' ? 'selected' : '' }}>Health</option>
                        <option value="environment" {{ old('category') == 'environment' ? 'selected' : '' }}>Environment</option>
                        <option value="incident_review" {{ old('category') == 'incident_review' ? 'selected' : '' }}>Incident Review</option>
                        <option value="emergency" {{ old('category') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="equipment" {{ old('category') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="procedural" {{ old('category') == 'procedural' ? 'selected' : '' }}>Procedural</option>
                        <option value="custom" {{ old('category') == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level *</label>
                    <select id="difficulty_level" name="difficulty_level" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Difficulty</option>
                        <option value="basic" {{ old('difficulty_level') == 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="intermediate" {{ old('difficulty_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('difficulty_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Estimated Duration (minutes) *</label>
                    <input type="number" id="estimated_duration_minutes" name="estimated_duration_minutes" min="5" max="60" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="15" value="{{ old('estimated_duration_minutes', 15) }}">
                    @error('estimated_duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Detailed description of the topic">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="key_talking_points" class="block text-sm font-medium text-gray-700 mb-1">Key Talking Points</label>
                    <textarea id="key_talking_points" name="key_talking_points" rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Key talking points, main topics to cover, etc.">{{ old('key_talking_points') }}</textarea>
                    @error('key_talking_points')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Settings</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="department_relevance" class="block text-sm font-medium text-gray-700 mb-1">Department Relevance</label>
                    <select id="department_relevance" name="department_relevance[]" multiple
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Departments (leave empty for all)</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ (is_array(old('department_relevance')) && in_array($department->id, old('department_relevance'))) ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple departments. Leave empty for all departments.</p>
                    @error('department_relevance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="seasonal_relevance" class="block text-sm font-medium text-gray-700 mb-1">Seasonal Relevance *</label>
                    <select id="seasonal_relevance" name="seasonal_relevance" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Season</option>
                        <option value="all_year" {{ old('seasonal_relevance', 'all_year') == 'all_year' ? 'selected' : '' }}>All Year</option>
                        <option value="summer" {{ old('seasonal_relevance') == 'summer' ? 'selected' : '' }}>Summer</option>
                        <option value="winter" {{ old('seasonal_relevance') == 'winter' ? 'selected' : '' }}>Winter</option>
                        <option value="monsoon" {{ old('seasonal_relevance') == 'monsoon' ? 'selected' : '' }}>Monsoon</option>
                        <option value="extreme_heat" {{ old('seasonal_relevance') == 'extreme_heat' ? 'selected' : '' }}>Extreme Heat</option>
                        <option value="extreme_cold" {{ old('seasonal_relevance') == 'extreme_cold' ? 'selected' : '' }}>Extreme Cold</option>
                    </select>
                    @error('seasonal_relevance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="representer_id" class="block text-sm font-medium text-gray-700 mb-1">Representer *</label>
                    <select id="representer_id" name="representer_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('representer_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} 
                                @if($employee->employee_id_number)
                                    ({{ $employee->employee_id_number }})
                                @endif
                                @if($employee->department)
                                    - {{ $employee->department->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Select the employee who will represent/present this topic</p>
                    @error('representer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <label for="learning_objectives" class="block text-sm font-medium text-gray-700 mb-1">Learning Objectives</label>
                    <textarea id="learning_objectives" name="learning_objectives" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="What participants will learn from this topic (one per line)">{{ is_array(old('learning_objectives')) ? implode("\n", old('learning_objectives')) : old('learning_objectives', '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Enter one learning objective per line</p>
                    @error('learning_objectives')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="required_materials" class="block text-sm font-medium text-gray-700 mb-1">Required Materials</label>
                    <textarea id="required_materials" name="required_materials" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter one material per line (e.g., PPE samples, handouts, equipment)">{{ is_array(old('required_materials')) ? implode("\n", old('required_materials')) : old('required_materials', '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Enter one material per line</p>
                    @error('required_materials')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_mandatory" value="1" {{ old('is_mandatory') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Mark as mandatory topic</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Mandatory topics must be completed by all relevant staff</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('toolbox-topics.index') }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Topic
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
