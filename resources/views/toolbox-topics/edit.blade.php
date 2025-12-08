@extends('layouts.app')

@section('title', 'Edit Topic')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('toolbox-topics.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Topics
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Edit Topic</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('toolbox-topics.update', $topic) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Topic Title *</label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter topic title" value="{{ old('title', $topic->title) }}">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select id="category" name="category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Category</option>
                        <option value="safety" {{ old('category', $topic->category) == 'safety' ? 'selected' : '' }}>Safety</option>
                        <option value="health" {{ old('category', $topic->category) == 'health' ? 'selected' : '' }}>Health</option>
                        <option value="environment" {{ old('category', $topic->category) == 'environment' ? 'selected' : '' }}>Environment</option>
                        <option value="incident_review" {{ old('category', $topic->category) == 'incident_review' ? 'selected' : '' }}>Incident Review</option>
                        <option value="emergency" {{ old('category', $topic->category) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="equipment" {{ old('category', $topic->category) == 'equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="procedural" {{ old('category', $topic->category) == 'procedural' ? 'selected' : '' }}>Procedural</option>
                        <option value="custom" {{ old('category', $topic->category) == 'custom' ? 'selected' : '' }}>Custom</option>
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
                        <option value="basic" {{ old('difficulty_level', $topic->difficulty_level) == 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="intermediate" {{ old('difficulty_level', $topic->difficulty_level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ old('difficulty_level', $topic->difficulty_level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('difficulty_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Estimated Duration (minutes) *</label>
                    <input type="number" id="estimated_duration_minutes" name="estimated_duration_minutes" min="5" max="60" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="15" value="{{ old('estimated_duration_minutes', $topic->estimated_duration_minutes) }}">
                    @error('estimated_duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Detailed description of the topic">{{ old('description', $topic->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="key_talking_points" class="block text-sm font-medium text-gray-700 mb-1">Key Talking Points</label>
                    <textarea id="key_talking_points" name="key_talking_points" rows="6"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Key talking points, main topics to cover, etc.">{{ old('key_talking_points', $topic->key_talking_points) }}</textarea>
                    @error('key_talking_points')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="subcategory" class="block text-sm font-medium text-gray-700 mb-1">Subcategory</label>
                    <select id="subcategory" name="subcategory"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Subcategory (Optional)</option>
                        <option value="equipment_safety" {{ old('subcategory', $topic->subcategory) == 'equipment_safety' ? 'selected' : '' }}>Equipment Safety</option>
                        <option value="hazard_recognition" {{ old('subcategory', $topic->subcategory) == 'hazard_recognition' ? 'selected' : '' }}>Hazard Recognition</option>
                        <option value="emergency_procedures" {{ old('subcategory', $topic->subcategory) == 'emergency_procedures' ? 'selected' : '' }}>Emergency Procedures</option>
                        <option value="ergonomics" {{ old('subcategory', $topic->subcategory) == 'ergonomics' ? 'selected' : '' }}>Ergonomics</option>
                        <option value="waste_management" {{ old('subcategory', $topic->subcategory) == 'waste_management' ? 'selected' : '' }}>Waste Management</option>
                        <option value="incident_learnings" {{ old('subcategory', $topic->subcategory) == 'incident_learnings' ? 'selected' : '' }}>Incident Learnings</option>
                        <option value="ppe" {{ old('subcategory', $topic->subcategory) == 'ppe' ? 'selected' : '' }}>PPE</option>
                        <option value="lockout_tagout" {{ old('subcategory', $topic->subcategory) == 'lockout_tagout' ? 'selected' : '' }}>Lockout/Tagout</option>
                        <option value="chemical_safety" {{ old('subcategory', $topic->subcategory) == 'chemical_safety' ? 'selected' : '' }}>Chemical Safety</option>
                        <option value="electrical_safety" {{ old('subcategory', $topic->subcategory) == 'electrical_safety' ? 'selected' : '' }}>Electrical Safety</option>
                        <option value="fall_protection" {{ old('subcategory', $topic->subcategory) == 'fall_protection' ? 'selected' : '' }}>Fall Protection</option>
                        <option value="fire_safety" {{ old('subcategory', $topic->subcategory) == 'fire_safety' ? 'selected' : '' }}>Fire Safety</option>
                        <option value="first_aid" {{ old('subcategory', $topic->subcategory) == 'first_aid' ? 'selected' : '' }}>First Aid</option>
                        <option value="wellness" {{ old('subcategory', $topic->subcategory) == 'wellness' ? 'selected' : '' }}>Wellness</option>
                        <option value="mental_health" {{ old('subcategory', $topic->subcategory) == 'mental_health' ? 'selected' : '' }}>Mental Health</option>
                        <option value="other" {{ old('subcategory', $topic->subcategory) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('subcategory')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="seasonal_relevance" class="block text-sm font-medium text-gray-700 mb-1">Seasonal Relevance *</label>
                    <select id="seasonal_relevance" name="seasonal_relevance" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all_year" {{ old('seasonal_relevance', $topic->seasonal_relevance) == 'all_year' ? 'selected' : '' }}>All Year</option>
                        <option value="summer" {{ old('seasonal_relevance', $topic->seasonal_relevance) == 'summer' ? 'selected' : '' }}>Summer</option>
                        <option value="winter" {{ old('seasonal_relevance', $topic->seasonal_relevance) == 'winter' ? 'selected' : '' }}>Winter</option>
                        <option value="monsoon" {{ old('seasonal_relevance', $topic->seasonal_relevance) == 'monsoon' ? 'selected' : '' }}>Monsoon</option>
                        <option value="extreme_heat" {{ old('seasonal_relevance', $topic->seasonal_relevance) == 'extreme_heat' ? 'selected' : '' }}>Extreme Heat</option>
                        <option value="extreme_cold" {{ old('seasonal_relevance', $topic->seasonal_relevance) == 'extreme_cold' ? 'selected' : '' }}>Extreme Cold</option>
                    </select>
                    @error('seasonal_relevance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="learning_objectives" class="block text-sm font-medium text-gray-700 mb-1">Learning Objectives</label>
                    <textarea id="learning_objectives" name="learning_objectives" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter learning objectives (one per line)">{{ old('learning_objectives', is_array($topic->learning_objectives) ? implode("\n", $topic->learning_objectives) : $topic->learning_objectives) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Enter one objective per line</p>
                    @error('learning_objectives')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="required_materials" class="block text-sm font-medium text-gray-700 mb-1">Required Materials</label>
                    <textarea id="required_materials" name="required_materials" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter required materials (one per line)">{{ old('required_materials', is_array($topic->required_materials) ? implode("\n", $topic->required_materials) : '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Enter one material per line</p>
                    @error('required_materials')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="real_world_examples" class="block text-sm font-medium text-gray-700 mb-1">Real World Examples</label>
                    <textarea id="real_world_examples" name="real_world_examples" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Real-world examples, case studies, etc.">{{ old('real_world_examples', $topic->real_world_examples) }}</textarea>
                    @error('real_world_examples')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="regulatory_references" class="block text-sm font-medium text-gray-700 mb-1">Regulatory References</label>
                    <textarea id="regulatory_references" name="regulatory_references" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Relevant regulations, standards, guidelines">{{ old('regulatory_references', $topic->regulatory_references) }}</textarea>
                    @error('regulatory_references')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="representer_id" class="block text-sm font-medium text-gray-700 mb-1">Representer</label>
                    <select id="representer_id" name="representer_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Representer (Optional)</option>
                        @foreach($employees ?? [] as $employee)
                            <option value="{{ $employee->id }}" {{ old('representer_id', $topic->representer_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('representer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_mandatory" name="is_mandatory" value="1"
                           {{ old('is_mandatory', $topic->is_mandatory) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_mandatory" class="ml-3 text-sm text-gray-700">
                        This is a mandatory topic
                    </label>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('toolbox-topics.index') }}" 
               class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Update Topic
            </button>
        </div>
    </form>
</div>
@endsection

