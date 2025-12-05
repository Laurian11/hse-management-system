@extends('layouts.app')

@section('title', 'Edit Training Session')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-sessions.show', $trainingSession) }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Session
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Training Session</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('training.training-sessions.update', $trainingSession) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Title *</label>
                    <input type="text" name="title" value="{{ old('title', $trainingSession->title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $trainingSession->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Start *</label>
                        <input type="datetime-local" name="scheduled_start" value="{{ old('scheduled_start', $trainingSession->scheduled_start?->format('Y-m-d\TH:i')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled End *</label>
                        <input type="datetime-local" name="scheduled_end" value="{{ old('scheduled_end', $trainingSession->scheduled_end?->format('Y-m-d\TH:i')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Type *</label>
                    <select name="session_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="classroom" {{ old('session_type', $trainingSession->session_type) == 'classroom' ? 'selected' : '' }}>Classroom</option>
                        <option value="e_learning" {{ old('session_type', $trainingSession->session_type) == 'e_learning' ? 'selected' : '' }}>E-Learning</option>
                        <option value="on_job_training" {{ old('session_type', $trainingSession->session_type) == 'on_job_training' ? 'selected' : '' }}>On-Job Training</option>
                        <option value="workshop" {{ old('session_type', $trainingSession->session_type) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="simulation" {{ old('session_type', $trainingSession->session_type) == 'simulation' ? 'selected' : '' }}>Simulation</option>
                        <option value="refresher" {{ old('session_type', $trainingSession->session_type) == 'refresher' ? 'selected' : '' }}>Refresher</option>
                        <option value="certification" {{ old('session_type', $trainingSession->session_type) == 'certification' ? 'selected' : '' }}>Certification</option>
                        <option value="combination" {{ old('session_type', $trainingSession->session_type) == 'combination' ? 'selected' : '' }}>Combination</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location Name</label>
                        <input type="text" name="location_name" value="{{ old('location_name', $trainingSession->location_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Room Number</label>
                        <input type="text" name="room_number" value="{{ old('room_number', $trainingSession->room_number) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instructor</label>
                    <select name="instructor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Instructor</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('instructor_id', $trainingSession->instructor_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Participants</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants', $trainingSession->max_participants) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Participants</label>
                        <input type="number" name="min_participants" value="{{ old('min_participants', $trainingSession->min_participants) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Virtual Meeting Link</label>
                    <input type="url" name="virtual_meeting_link" value="{{ old('virtual_meeting_link', $trainingSession->virtual_meeting_link) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="https://...">
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('training.training-sessions.show', $trainingSession) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
