@extends('layouts.app')

@section('title', 'Create Training Plan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-plans.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Training Plans
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Create Training Plan</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($trainingNeed)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900 mb-1">Creating Plan for Training Need</h3>
                        <p class="text-sm text-blue-700">{{ $trainingNeed->training_title }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('training.training-plans.store') }}" method="POST">
                @csrf

                @if($trainingNeed)
                    <input type="hidden" name="training_need_id" value="{{ $trainingNeed->id }}">
                @else
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Training Need *</label>
                        <select name="training_need_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Training Need</option>
                            @foreach(\App\Models\TrainingNeedsAnalysis::where('company_id', auth()->user()->company_id)->where('status', 'validated')->get() as $tna)
                                <option value="{{ $tna->id }}" {{ old('training_need_id') == $tna->id ? 'selected' : '' }}>
                                    {{ $tna->training_title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plan Title *</label>
                    <input type="text" name="title" value="{{ old('title', $trainingNeed->training_title ?? '') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Training Type *</label>
                        <select name="training_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="classroom" {{ old('training_type', $trainingNeed->training_type ?? '') == 'classroom' ? 'selected' : '' }}>Classroom</option>
                            <option value="e_learning" {{ old('training_type') == 'e_learning' ? 'selected' : '' }}>E-Learning</option>
                            <option value="on_job_training" {{ old('training_type') == 'on_job_training' ? 'selected' : '' }}>On-Job Training</option>
                            <option value="workshop" {{ old('training_type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="simulation" {{ old('training_type') == 'simulation' ? 'selected' : '' }}>Simulation</option>
                            <option value="refresher" {{ old('training_type') == 'refresher' ? 'selected' : '' }}>Refresher</option>
                            <option value="certification" {{ old('training_type') == 'certification' ? 'selected' : '' }}>Certification</option>
                            <option value="combination" {{ old('training_type') == 'combination' ? 'selected' : '' }}>Combination</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method *</label>
                        <select name="delivery_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="internal" {{ old('delivery_method') == 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="external" {{ old('delivery_method') == 'external' ? 'selected' : '' }}>External</option>
                            <option value="mixed" {{ old('delivery_method') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Planned Start Date</label>
                        <input type="date" name="planned_start_date" value="{{ old('planned_start_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Planned End Date</label>
                        <input type="date" name="planned_end_date" value="{{ old('planned_end_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Hours)</label>
                        <input type="number" name="duration_hours" value="{{ old('duration_hours') }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                        <input type="number" name="duration_days" value="{{ old('duration_days') }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
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

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location_name" value="{{ old('location_name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Training location name">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost</label>
                    <input type="number" name="estimated_cost" value="{{ old('estimated_cost') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('training.training-plans.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Training Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
