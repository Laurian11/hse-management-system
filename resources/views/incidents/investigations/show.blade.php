@extends('layouts.app')

@section('title', 'Investigation Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('incidents.show', $incident) }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Incident
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Investigation: {{ $incident->reference_number }}</h1>
                </div>
                <div class="flex space-x-3">
                    {!! $investigation->getStatusBadge() !!}
                    @if($investigation->status !== 'completed')
                        <a href="{{ route('incidents.investigations.edit', [$incident, $investigation]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Investigation Facts -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Investigation Facts</h2>
            <div class="space-y-6">
                @if($investigation->what_happened)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">What Happened?</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->what_happened }}</p>
                    </div>
                @endif
                @if($investigation->when_occurred)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">When Did It Occur?</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->when_occurred }}</p>
                    </div>
                @endif
                @if($investigation->where_occurred)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Where Did It Occur?</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->where_occurred }}</p>
                    </div>
                @endif
                @if($investigation->who_involved)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Who Was Involved?</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->who_involved }}</p>
                    </div>
                @endif
                @if($investigation->how_occurred)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">How Did It Occur?</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->how_occurred }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Causes -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Causes</h2>
            <div class="space-y-6">
                @if($investigation->immediate_causes)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Immediate Causes</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->immediate_causes }}</p>
                    </div>
                @endif
                @if($investigation->contributing_factors)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Contributing Factors</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->contributing_factors }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Conditions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Conditions at Time of Incident</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($investigation->environmental_conditions)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Environmental Conditions</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->environmental_conditions }}</p>
                    </div>
                @endif
                @if($investigation->equipment_conditions)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Equipment Conditions</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->equipment_conditions }}</p>
                    </div>
                @endif
                @if($investigation->procedures_followed)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Procedures Followed</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->procedures_followed }}</p>
                    </div>
                @endif
                @if($investigation->training_received)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Training Received</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->training_received }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Findings & Recommendations -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Findings & Recommendations</h2>
            <div class="space-y-6">
                @if($investigation->key_findings)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Key Findings</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->key_findings }}</p>
                    </div>
                @endif
                @if($investigation->recommendations)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Recommendations</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $investigation->recommendations }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-3">
                @if($investigation->status === 'pending')
                    <form action="{{ route('incidents.investigations.start', [$incident, $investigation]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-play mr-2"></i>Start Investigation
                        </button>
                    </form>
                @endif
                @if($investigation->status === 'in_progress')
                    <form action="{{ route('incidents.investigations.complete', [$incident, $investigation]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Complete Investigation
                        </button>
                    </form>
                @endif
                @if($investigation->isCompleted() && !$incident->rootCauseAnalysis)
                    <a href="{{ route('incidents.rca.create', $incident) }}" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-project-diagram mr-2"></i>Perform Root Cause Analysis
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

