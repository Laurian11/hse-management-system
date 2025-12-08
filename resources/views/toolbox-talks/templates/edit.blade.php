@extends('layouts.app')

@section('title', 'Edit Template')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('toolbox-talks.templates.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Templates
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Template</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('toolbox-talks.templates.update', $template) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Template Name *</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('name', $template->name) }}"
                               placeholder="e.g., Weekly Safety Briefing">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Brief description of this template">{{ old('description', $template->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="talk_type" class="block text-sm font-medium text-gray-700 mb-1">Talk Type *</label>
                            <select id="talk_type" name="talk_type" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Type</option>
                                <option value="safety" {{ old('talk_type', $template->talk_type) == 'safety' ? 'selected' : '' }}>Safety</option>
                                <option value="health" {{ old('talk_type', $template->talk_type) == 'health' ? 'selected' : '' }}>Health</option>
                                <option value="environment" {{ old('talk_type', $template->talk_type) == 'environment' ? 'selected' : '' }}>Environment</option>
                                <option value="incident_review" {{ old('talk_type', $template->talk_type) == 'incident_review' ? 'selected' : '' }}>Incident Review</option>
                                <option value="custom" {{ old('talk_type', $template->talk_type) == 'custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                            @error('talk_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                            <input type="number" id="duration_minutes" name="duration_minutes" required min="5" max="60"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('duration_minutes', $template->duration_minutes) }}">
                            @error('duration_minutes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="topic_id" class="block text-sm font-medium text-gray-700 mb-1">Topic (Optional)</label>
                        <select id="topic_id" name="topic_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Topic (Optional)</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id', $template->topic_id) == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->title }} - {{ $topic->category }}
                                </option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Template Content -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Template Content</h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Default Title</label>
                        <input type="text" id="title" name="title"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('title', $template->title) }}"
                               placeholder="Default title for talks created from this template">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description_content" class="block text-sm font-medium text-gray-700 mb-1">Default Description</label>
                        <textarea id="description_content" name="description_content" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Default description content">{{ old('description_content', $template->description_content) }}</textarea>
                        @error('description_content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="key_points" class="block text-sm font-medium text-gray-700 mb-1">Key Points</label>
                        <textarea id="key_points" name="key_points" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Key talking points (one per line)">{{ old('key_points', $template->key_points) }}</textarea>
                        @error('key_points')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="regulatory_references" class="block text-sm font-medium text-gray-700 mb-1">Regulatory References</label>
                        <textarea id="regulatory_references" name="regulatory_references" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Relevant regulations, standards, or guidelines">{{ old('regulatory_references', $template->regulatory_references) }}</textarea>
                        @error('regulatory_references')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-3 text-sm text-gray-700">
                        Template is active (can be used to create talks)
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('toolbox-talks.templates.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="btn-primary rounded">
                    <i class="fas fa-save mr-2"></i>Update Template
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

