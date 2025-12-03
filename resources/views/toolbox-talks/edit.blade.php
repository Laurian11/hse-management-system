@extends('layouts.app')

@section('title', 'Edit Toolbox Talk')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('toolbox-talks.show', $toolboxTalk) }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Talk
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Toolbox Talk</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($toolboxTalk->status === 'completed')
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-yellow-800"><i class="fas fa-exclamation-triangle mr-2"></i>This talk is completed and cannot be edited.</p>
            </div>
        @endif

        <form action="{{ route('toolbox-talks.update', $toolboxTalk) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" id="title" name="title" required value="{{ old('title', $toolboxTalk->title) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter toolbox talk title">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select id="department_id" name="department_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $toolboxTalk->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-1">Supervisor</label>
                        <select id="supervisor_id" name="supervisor_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Supervisor</option>
                            @foreach($supervisors as $supervisor)
                                <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $toolboxTalk->supervisor_id) == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                            @endforeach
                        </select>
                        @error('supervisor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter talk description">{{ old('description', $toolboxTalk->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Topic Selection -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Topic Selection</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="topic_id" class="block text-sm font-medium text-gray-700 mb-1">Topic</label>
                        <select id="topic_id" name="topic_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Topic (Optional)</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ old('topic_id', $toolboxTalk->topic_id) == $topic->id ? 'selected' : '' }}>{{ $topic->title }} - {{ $topic->category }}</option>
                            @endforeach
                        </select>
                        @error('topic_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="talk_type" class="block text-sm font-medium text-gray-700 mb-1">Talk Type *</label>
                        <select id="talk_type" name="talk_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Type</option>
                            <option value="safety" {{ old('talk_type', $toolboxTalk->talk_type) == 'safety' ? 'selected' : '' }}>Safety</option>
                            <option value="health" {{ old('talk_type', $toolboxTalk->talk_type) == 'health' ? 'selected' : '' }}>Health</option>
                            <option value="environment" {{ old('talk_type', $toolboxTalk->talk_type) == 'environment' ? 'selected' : '' }}>Environment</option>
                            <option value="incident_review" {{ old('talk_type', $toolboxTalk->talk_type) == 'incident_review' ? 'selected' : '' }}>Incident Review</option>
                            <option value="custom" {{ old('talk_type', $toolboxTalk->talk_type) == 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                        @error('talk_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Scheduling -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Scheduling</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input type="date" id="scheduled_date" name="scheduled_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('scheduled_date', $toolboxTalk->scheduled_date ? $toolboxTalk->scheduled_date->format('Y-m-d') : '') }}">
                        @error('scheduled_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                        <input type="time" id="start_time" name="start_time" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('start_time', $toolboxTalk->start_time ? \Carbon\Carbon::parse($toolboxTalk->start_time)->format('H:i') : '') }}">
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                        <input type="number" id="duration_minutes" name="duration_minutes" required min="5" max="60"
                               value="{{ old('duration_minutes', $toolboxTalk->duration_minutes ?? 15) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('duration_minutes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-3">
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location *</label>
                        <input type="text" id="location" name="location" required
                               value="{{ old('location', $toolboxTalk->location) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter location">
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Biometric Settings -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Attendance Settings</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="biometric_required" name="biometric_required" value="1"
                               {{ old('biometric_required', $toolboxTalk->biometric_required) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="biometric_required" class="ml-3 text-sm text-gray-700">
                            Require biometric attendance (ZKTeco K40)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('toolbox-talks.show', $toolboxTalk) }}" 
                       class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            {{ $toolboxTalk->status === 'completed' ? 'disabled' : '' }}>
                        <i class="fas fa-save mr-2"></i>Update Talk
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Set minimum date to today
    document.getElementById('scheduled_date').min = new Date().toISOString().split('T')[0];

    // Auto-populate duration based on topic selection
    document.getElementById('topic_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const duration = selectedOption.getAttribute('data-duration');
        if (duration) {
            document.getElementById('duration_minutes').value = duration;
        }
    });
</script>
@endpush
@endsection

