@extends('layouts.app')

@section('title', 'Schedule Training Session')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-sessions.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Sessions
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Schedule Training Session</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($trainingPlan)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900 mb-1">Scheduling Session for Training Plan</h3>
                        <p class="text-sm text-blue-700">{{ $trainingPlan->title }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('training.training-sessions.store') }}" method="POST">
                @csrf

                @if($trainingPlan)
                    <input type="hidden" name="training_plan_id" value="{{ $trainingPlan->id }}">
                @else
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Training Plan *</label>
                        <select name="training_plan_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Training Plan</option>
                            @foreach(\App\Models\TrainingPlan::where('company_id', auth()->user()->company_id)->where('status', 'approved')->get() as $plan)
                                <option value="{{ $plan->id }}" {{ old('training_plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Title *</label>
                    <input type="text" name="title" value="{{ old('title', $trainingPlan->title ?? '') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Start *</label>
                        <input type="datetime-local" name="scheduled_start" value="{{ old('scheduled_start') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled End *</label>
                        <input type="datetime-local" name="scheduled_end" value="{{ old('scheduled_end') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session Type *</label>
                    <select name="session_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="classroom" {{ old('session_type', $trainingPlan->training_type ?? '') == 'classroom' ? 'selected' : '' }}>Classroom</option>
                        <option value="e_learning" {{ old('session_type') == 'e_learning' ? 'selected' : '' }}>E-Learning</option>
                        <option value="on_job_training" {{ old('session_type') == 'on_job_training' ? 'selected' : '' }}>On-Job Training</option>
                        <option value="workshop" {{ old('session_type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="simulation" {{ old('session_type') == 'simulation' ? 'selected' : '' }}>Simulation</option>
                        <option value="refresher" {{ old('session_type') == 'refresher' ? 'selected' : '' }}>Refresher</option>
                        <option value="certification" {{ old('session_type') == 'certification' ? 'selected' : '' }}>Certification</option>
                        <option value="combination" {{ old('session_type') == 'combination' ? 'selected' : '' }}>Combination</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location Name</label>
                        <input type="text" name="location_name" value="{{ old('location_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Room Number</label>
                        <input type="text" name="room_number" value="{{ old('room_number') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instructor</label>
                    <select name="instructor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Instructor</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('instructor_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Participants</label>
                        <input type="number" name="max_participants" value="{{ old('max_participants') }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Participants</label>
                        <input type="number" name="min_participants" value="{{ old('min_participants', 1) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Virtual Meeting Link</label>
                    <input type="url" name="virtual_meeting_link" value="{{ old('virtual_meeting_link') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="https://...">
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('training.training-sessions.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Schedule Session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
