@extends('layouts.app')

@section('title', $trainingNeedsAnalysis->training_title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-needs.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Training Needs
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $trainingNeedsAnalysis->training_title }}</h1>
                        <p class="text-sm text-gray-500">{{ $trainingNeedsAnalysis->reference_number }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    @if($trainingNeedsAnalysis->status === 'identified')
                        <form action="{{ route('training.training-needs.validate', $trainingNeedsAnalysis) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>Validate
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('training.training-needs.edit', $trainingNeedsAnalysis) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    @if($trainingNeedsAnalysis->trainingPlans->count() === 0)
                        <form action="{{ route('training.training-needs.destroy', $trainingNeedsAnalysis) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this training need?');">
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
        <!-- Status Alert -->
        <div class="mb-6">
            {!! $trainingNeedsAnalysis->getStatusBadge() !!}
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Training Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Training Details</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Description</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $trainingNeedsAnalysis->training_description }}</p>
                        </div>
                        @if($trainingNeedsAnalysis->gap_analysis)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Gap Analysis</h3>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $trainingNeedsAnalysis->gap_analysis }}</p>
                            </div>
                        @endif
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Priority</h3>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                    {{ $trainingNeedsAnalysis->priority === 'critical' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $trainingNeedsAnalysis->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $trainingNeedsAnalysis->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $trainingNeedsAnalysis->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($trainingNeedsAnalysis->priority) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Training Type</h3>
                                <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $trainingNeedsAnalysis->training_type)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trigger Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Trigger Information</h2>
                    <div class="space-y-3">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Trigger Source</h3>
                            <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $trainingNeedsAnalysis->trigger_source)) }}</p>
                        </div>
                        @if($trainingNeedsAnalysis->triggeredByIncident)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Related Incident</h3>
                                <a href="{{ route('incidents.show', $trainingNeedsAnalysis->triggeredByIncident) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $trainingNeedsAnalysis->triggeredByIncident->reference_number }}
                                </a>
                            </div>
                        @endif
                        @if($trainingNeedsAnalysis->triggeredByControlMeasure)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Related Control Measure</h3>
                                <p class="text-gray-900">{{ $trainingNeedsAnalysis->triggeredByControlMeasure->title }}</p>
                            </div>
                        @endif
                        @if($trainingNeedsAnalysis->triggeredByCAPA)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Related CAPA</h3>
                                <a href="{{ route('incidents.capas.show', [$trainingNeedsAnalysis->triggeredByCAPA->incident, $trainingNeedsAnalysis->triggeredByCAPA]) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $trainingNeedsAnalysis->triggeredByCAPA->reference_number }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Training Plans -->
                @if($trainingNeedsAnalysis->trainingPlans->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Training Plans</h2>
                        <div class="space-y-4">
                            @foreach($trainingNeedsAnalysis->trainingPlans as $plan)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $plan->title }}</h3>
                                            <p class="text-sm text-gray-500">{{ $plan->reference_number }}</p>
                                        </div>
                                        <a href="{{ route('training.training-plans.show', $plan) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
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
                            <i class="fas fa-calendar-check text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Training Plans</h3>
                            <p class="text-gray-500 mb-4">Create a training plan to schedule this training need.</p>
                            @if($trainingNeedsAnalysis->status === 'validated')
                                <a href="{{ route('training.training-plans.create', ['training_need_id' => $trainingNeedsAnalysis->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Create Training Plan
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
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            {!! $trainingNeedsAnalysis->getStatusBadge() !!}
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Created By</p>
                            <p class="text-sm font-medium text-gray-900">{{ $trainingNeedsAnalysis->creator->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Created On</p>
                            <p class="text-sm font-medium text-gray-900">{{ $trainingNeedsAnalysis->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                        @if($trainingNeedsAnalysis->validatedBy)
                            <div>
                                <p class="text-sm text-gray-500">Validated By</p>
                                <p class="text-sm font-medium text-gray-900">{{ $trainingNeedsAnalysis->validator->name }}</p>
                                <p class="text-xs text-gray-400">{{ $trainingNeedsAnalysis->validated_at->format('M j, Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Regulatory Info -->
                @if($trainingNeedsAnalysis->is_regulatory || $trainingNeedsAnalysis->is_mandatory)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Regulatory Information</h3>
                        <div class="space-y-3">
                            @if($trainingNeedsAnalysis->is_mandatory)
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                    Mandatory
                                </span>
                            @endif
                            @if($trainingNeedsAnalysis->is_regulatory)
                                <span class="px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-semibold">
                                    Regulatory
                                </span>
                                @if($trainingNeedsAnalysis->regulatory_reference)
                                    <div>
                                        <p class="text-sm text-gray-500">Reference</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $trainingNeedsAnalysis->regulatory_reference }}</p>
                                    </div>
                                @endif
                                @if($trainingNeedsAnalysis->regulatory_deadline)
                                    <div>
                                        <p class="text-sm text-gray-500">Deadline</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $trainingNeedsAnalysis->regulatory_deadline->format('M j, Y') }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
