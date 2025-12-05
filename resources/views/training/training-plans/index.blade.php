@extends('layouts.app')

@section('title', 'Training Plans')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">Training Plans</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-plans.export', ['format' => 'excel']) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                    <a href="{{ route('training.training-plans.export', ['format' => 'csv']) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>Export CSV
                    </a>
                    <a href="{{ route('training.training-plans.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create Training Plan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('training.training-plans.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Training Plans List -->
        <div class="bg-white rounded-lg shadow">
            @if($plans->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($plans as $plan)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ route('training.training-plans.show', $plan) }}" class="hover:text-indigo-600">
                                                {{ $plan->title }}
                                            </a>
                                        </h3>
                                        {!! $plan->getStatusBadge() !!}
                                    </div>
                                    @if($plan->trainingNeed)
                                        <p class="text-sm text-gray-600 mb-2">
                                            <i class="fas fa-link mr-1"></i>
                                            <a href="{{ route('training.training-needs.show', $plan->trainingNeed) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $plan->trainingNeed->training_title }}
                                            </a>
                                        </p>
                                    @endif
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>
                                            <i class="fas fa-chalkboard-teacher mr-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $plan->training_type)) }}
                                        </span>
                                        @if($plan->planned_start_date)
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>
                                                {{ $plan->planned_start_date->format('M j, Y') }}
                                            </span>
                                        @endif
                                        @if($plan->instructor)
                                            <span>
                                                <i class="fas fa-user mr-1"></i>
                                                {{ $plan->instructor->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('training.training-plans.show', $plan) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        View <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $plans->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-calendar-check text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Training Plans Found</h3>
                    <p class="text-gray-500 mb-6">Create a training plan to schedule training sessions.</p>
                    <a href="{{ route('training.training-plans.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create Training Plan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
