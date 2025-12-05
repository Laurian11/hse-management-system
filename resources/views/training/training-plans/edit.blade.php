@extends('layouts.app')

@section('title', 'Edit Training Plan')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-plans.show', $trainingPlan) }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Training Plan
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Training Plan</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('training.training-plans.update', $trainingPlan) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plan Title *</label>
                    <input type="text" name="title" value="{{ old('title', $trainingPlan->title) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $trainingPlan->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Training Type *</label>
                        <select name="training_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="classroom" {{ old('training_type', $trainingPlan->training_type) == 'classroom' ? 'selected' : '' }}>Classroom</option>
                            <option value="e_learning" {{ old('training_type', $trainingPlan->training_type) == 'e_learning' ? 'selected' : '' }}>E-Learning</option>
                            <option value="on_job_training" {{ old('training_type', $trainingPlan->training_type) == 'on_job_training' ? 'selected' : '' }}>On-Job Training</option>
                            <option value="workshop" {{ old('training_type', $trainingPlan->training_type) == 'workshop' ? 'selected' : '' }}>Workshop</option>
                            <option value="simulation" {{ old('training_type', $trainingPlan->training_type) == 'simulation' ? 'selected' : '' }}>Simulation</option>
                            <option value="refresher" {{ old('training_type', $trainingPlan->training_type) == 'refresher' ? 'selected' : '' }}>Refresher</option>
                            <option value="certification" {{ old('training_type', $trainingPlan->training_type) == 'certification' ? 'selected' : '' }}>Certification</option>
                            <option value="combination" {{ old('training_type', $trainingPlan->training_type) == 'combination' ? 'selected' : '' }}>Combination</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method *</label>
                        <select name="delivery_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="internal" {{ old('delivery_method', $trainingPlan->delivery_method) == 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="external" {{ old('delivery_method', $trainingPlan->delivery_method) == 'external' ? 'selected' : '' }}>External</option>
                            <option value="mixed" {{ old('delivery_method', $trainingPlan->delivery_method) == 'mixed' ? 'selected' : '' }}>Mixed</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Planned Start Date</label>
                        <input type="date" name="planned_start_date" value="{{ old('planned_start_date', $trainingPlan->planned_start_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Planned End Date</label>
                        <input type="date" name="planned_end_date" value="{{ old('planned_end_date', $trainingPlan->planned_end_date?->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Hours)</label>
                        <input type="number" name="duration_hours" value="{{ old('duration_hours', $trainingPlan->duration_hours) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                        <input type="number" name="duration_days" value="{{ old('duration_days', $trainingPlan->duration_days) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instructor</label>
                    <select name="instructor_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Instructor</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('instructor_id', $trainingPlan->instructor_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location_name" value="{{ old('location_name', $trainingPlan->location_name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Training location name">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost</label>
                    <input type="number" name="estimated_cost" value="{{ old('estimated_cost', $trainingPlan->estimated_cost) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00">
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('training.training-plans.show', $trainingPlan) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Training Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
