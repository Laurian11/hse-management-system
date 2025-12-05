@extends('layouts.app')

@section('title', $trainingPlan->title)

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
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $trainingPlan->title }}</h1>
                        <p class="text-sm text-gray-500">{{ $trainingPlan->reference_number }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    @if($trainingPlan->status === 'draft')
                        <form action="{{ route('training.training-plans.approve', $trainingPlan) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>Approve Plan
                            </button>
                        </form>
                    @endif
                    @if($trainingPlan->status === 'approved' && !$trainingPlan->budget_approved)
                        <form action="{{ route('training.training-plans.approve-budget', $trainingPlan) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-dollar-sign mr-2"></i>Approve Budget
                            </button>
                        </form>
                    @endif
                    @if($trainingPlan->status === 'draft')
                        <a href="{{ route('training.training-plans.edit', $trainingPlan) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    @endif
                    @if($trainingPlan->sessions->count() === 0)
                        <form action="{{ route('training.training-plans.destroy', $trainingPlan) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this training plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Status Badge -->
        <div class="mb-6">
            {!! $trainingPlan->getStatusBadge() !!}
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Plan Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Plan Details</h2>
                    <div class="space-y-4">
                        @if($trainingPlan->description)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Description</h3>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $trainingPlan->description }}</p>
                            </div>
                        @endif
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Training Type</h3>
                                <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $trainingPlan->training_type)) }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Delivery Method</h3>
                                <p class="text-gray-900">{{ ucfirst($trainingPlan->delivery_method) }}</p>
                            </div>
                            @if($trainingPlan->planned_start_date)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Start Date</h3>
                                    <p class="text-gray-900">{{ $trainingPlan->planned_start_date->format('M j, Y') }}</p>
                                </div>
                            @endif
                            @if($trainingPlan->planned_end_date)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">End Date</h3>
                                    <p class="text-gray-900">{{ $trainingPlan->planned_end_date->format('M j, Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Training Sessions -->
                @if($trainingPlan->sessions->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Training Sessions</h2>
                            <a href="{{ route('training.training-sessions.create', ['training_plan_id' => $trainingPlan->id]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                <i class="fas fa-plus mr-2"></i>Schedule Session
                            </a>
                        </div>
                        <div class="space-y-4">
                            @foreach($trainingPlan->sessions as $session)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $session->title }}</h3>
                                            <p class="text-sm text-gray-500">{{ $session->scheduled_start->format('M j, Y g:i A') }}</p>
                                        </div>
                                        <a href="{{ route('training.training-sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            View <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-center py-8">
                            <i class="fas fa-calendar text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Sessions Scheduled</h3>
                            <p class="text-gray-500 mb-4">Schedule training sessions for this plan.</p>
                            @if($trainingPlan->status === 'approved')
                                <a href="{{ route('training.training-sessions.create', ['training_plan_id' => $trainingPlan->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Schedule Session
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Info</h3>
                    <div class="space-y-4">
                        @if($trainingPlan->trainingNeed)
                            <div>
                                <p class="text-sm text-gray-500">Training Need</p>
                                <a href="{{ route('training.training-needs.show', $trainingPlan->trainingNeed) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    {{ Str::limit($trainingPlan->trainingNeed->training_title, 40) }}
                                </a>
                            </div>
                        @endif
                        @if($trainingPlan->instructor)
                            <div>
                                <p class="text-sm text-gray-500">Instructor</p>
                                <p class="text-sm font-medium text-gray-900">{{ $trainingPlan->instructor->name }}</p>
                            </div>
                        @endif
                        @if($trainingPlan->estimated_cost)
                            <div>
                                <p class="text-sm text-gray-500">Estimated Cost</p>
                                <p class="text-sm font-medium text-gray-900">${{ number_format($trainingPlan->estimated_cost, 2) }}</p>
                            </div>
                        @endif
                        @if($trainingPlan->budget_approved)
                            <div>
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                    Budget Approved
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
