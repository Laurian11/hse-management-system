@extends('layouts.app')

@section('title', 'Root Cause Analysis')

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
                    <h1 class="text-2xl font-bold text-gray-900">Root Cause Analysis: {{ $incident->reference_number }}</h1>
                </div>
                <div class="flex space-x-3">
                    {!! $rootCauseAnalysis->getStatusBadge() !!}
                    @if($rootCauseAnalysis->status !== 'reviewed')
                        <a href="{{ route('incidents.rca.edit', [$incident, $rootCauseAnalysis]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Analysis Type -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Analysis Type</h2>
            <span class="px-4 py-2 bg-purple-100 text-purple-800 rounded-lg text-sm font-semibold">
                {{ ucfirst(str_replace('_', ' ', $rootCauseAnalysis->analysis_type)) }}
            </span>
        </div>

        <!-- 5 Whys Analysis -->
        @if($rootCauseAnalysis->isFiveWhys())
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">5 Whys Analysis</h2>
                <div class="space-y-4">
                    @if($rootCauseAnalysis->why_1)
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Why 1 (First Level)</h3>
                            <p class="text-gray-900">{{ $rootCauseAnalysis->why_1 }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->why_2)
                        <div class="border-l-4 border-blue-400 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Why 2 (Second Level)</h3>
                            <p class="text-gray-900">{{ $rootCauseAnalysis->why_2 }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->why_3)
                        <div class="border-l-4 border-blue-300 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Why 3 (Third Level)</h3>
                            <p class="text-gray-900">{{ $rootCauseAnalysis->why_3 }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->why_4)
                        <div class="border-l-4 border-purple-300 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Why 4 (Fourth Level)</h3>
                            <p class="text-gray-900">{{ $rootCauseAnalysis->why_4 }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->why_5)
                        <div class="border-l-4 border-purple-400 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Why 5 (Fifth Level)</h3>
                            <p class="text-gray-900">{{ $rootCauseAnalysis->why_5 }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->root_cause)
                        <div class="bg-purple-50 border-2 border-purple-500 rounded-lg p-4 mt-6">
                            <h3 class="text-sm font-semibold text-purple-900 mb-2">Root Cause Identified</h3>
                            <p class="text-lg font-medium text-purple-900">{{ $rootCauseAnalysis->root_cause }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Fishbone Analysis -->
        @if($rootCauseAnalysis->isFishbone())
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Fishbone (Ishikawa) Analysis</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($rootCauseAnalysis->human_factors)
                        <div class="border-l-4 border-red-500 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Human Factors</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->human_factors }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->organizational_factors)
                        <div class="border-l-4 border-orange-500 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Organizational Factors</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->organizational_factors }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->technical_factors)
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Technical Factors</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->technical_factors }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->environmental_factors)
                        <div class="border-l-4 border-green-500 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Environmental Factors</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->environmental_factors }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->procedural_factors)
                        <div class="border-l-4 border-yellow-500 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Procedural Factors</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->procedural_factors }}</p>
                        </div>
                    @endif
                    @if($rootCauseAnalysis->equipment_factors)
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Equipment Factors</h3>
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->equipment_factors }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Comprehensive Analysis -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Comprehensive Analysis</h2>
            <div class="space-y-6">
                @if($rootCauseAnalysis->direct_cause)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Direct Cause</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->direct_cause }}</p>
                    </div>
                @endif
                @if($rootCauseAnalysis->contributing_causes)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Contributing Causes</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->contributing_causes }}</p>
                    </div>
                @endif
                @if($rootCauseAnalysis->root_causes)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Root Causes</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->root_causes }}</p>
                    </div>
                @endif
                @if($rootCauseAnalysis->systemic_failures)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Systemic Failures</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->systemic_failures }}</p>
                    </div>
                @endif
                @if($rootCauseAnalysis->prevention_possible)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Could This Have Been Prevented?</h3>
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->prevention_possible }}</p>
                    </div>
                @endif
                @if($rootCauseAnalysis->lessons_learned)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">Lessons Learned</h3>
                        <p class="text-blue-900 whitespace-pre-wrap">{{ $rootCauseAnalysis->lessons_learned }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-3">
                @if($rootCauseAnalysis->status === 'draft' || $rootCauseAnalysis->status === 'in_progress')
                    <form action="{{ route('incidents.rca.complete', [$incident, $rootCauseAnalysis]) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Complete Analysis
                        </button>
                    </form>
                @endif
                @if($rootCauseAnalysis->isCompleted() && $rootCauseAnalysis->capas->count() == 0)
                    <a href="{{ route('incidents.capas.create', $incident) }}" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-tasks mr-2"></i>Create CAPAs
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

