@extends('layouts.app')

@section('title', 'Risk Assessment Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $riskAssessment->title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $riskAssessment->reference_number }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    @can('risk_assessments.print')
                    <a href="{{ route('risk-assessment.risk-assessments.export-pdf', $riskAssessment) }}" class="px-4 py-2 text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                    <x-print-button />
                    @endcan
                    @can('risk_assessments.create')
                    <a href="{{ route('risk-assessment.risk-assessments.create', ['copy_from' => $riskAssessment->id]) }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" title="Copy this risk assessment">
                        <i class="fas fa-copy mr-2"></i>Copy
                    </a>
                    @endcan
                    @can('risk_assessments.edit')
                    <a href="{{ route('risk-assessment.risk-assessments.edit', $riskAssessment) }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    @endcan
                    @can('risk_assessments.write')
                    <a href="{{ route('risk-assessment.control-measures.create', ['risk_assessment_id' => $riskAssessment->id]) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fas fa-shield-alt mr-2"></i>Add Control
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Risk Matrix Summary -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Risk Assessment Summary</h2>
                    <div class="grid grid-cols-3 gap-4 text-center mb-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600">Initial Risk Score</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $riskAssessment->risk_score ?? 'N/A' }}</p>
                            <p class="text-sm mt-1">
                                <span class="px-2 py-1 rounded-full {{ $riskAssessment->getRiskLevelColor() }}">
                                    {{ strtoupper($riskAssessment->risk_level ?? 'N/A') }}
                                </span>
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600">Severity</p>
                            <p class="text-xl font-bold text-gray-900">{{ ucfirst($riskAssessment->severity ?? 'N/A') }}</p>
                            <p class="text-sm text-gray-500">Score: {{ $riskAssessment->severity_score ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600">Likelihood</p>
                            <p class="text-xl font-bold text-gray-900">{{ ucfirst($riskAssessment->likelihood ?? 'N/A') }}</p>
                            <p class="text-sm text-gray-500">Score: {{ $riskAssessment->likelihood_score ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if($riskAssessment->residual_risk_score)
                    <div class="border-t pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Residual Risk (After Controls)</p>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600">Residual Score</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $riskAssessment->residual_risk_score }}</p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600">Residual Level</p>
                                <p class="text-xl font-bold text-blue-900">{{ strtoupper($riskAssessment->residual_risk_level) }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                    <p class="text-gray-700">{{ $riskAssessment->description }}</p>
                </div>

                <!-- Control Measures -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Control Measures</h2>
                        <a href="{{ route('risk-assessment.control-measures.create', ['risk_assessment_id' => $riskAssessment->id]) }}" class="text-blue-600 hover:text-blue-700 text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Control
                        </a>
                    </div>
                    @if($riskAssessment->controlMeasures->count() > 0)
                        <div class="space-y-3">
                            @foreach($riskAssessment->controlMeasures as $control)
                                <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <a href="{{ route('risk-assessment.control-measures.show', $control) }}" class="font-semibold text-gray-900 hover:text-green-700">
                                                {{ $control->title }}
                                            </a>
                                            <p class="text-sm text-gray-600 mt-1">{{ $control->getControlTypeLabel() }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-white">{{ ucfirst($control->status) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No control measures defined yet.</p>
                    @endif
                </div>

                <!-- Reviews -->
                @if($riskAssessment->reviews->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Review History</h2>
                    <div class="space-y-3">
                        @foreach($riskAssessment->reviews as $review)
                            <div class="border-l-4 border-purple-500 bg-purple-50 p-4 rounded">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <a href="{{ route('risk-assessment.risk-reviews.show', $review) }}" class="font-semibold text-gray-900 hover:text-purple-700">
                                            {{ $review->reference_number }}
                                        </a>
                                        <p class="text-sm text-gray-600 mt-1">{{ ucfirst(str_replace('_', ' ', $review->review_type)) }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-white">{{ ucfirst($review->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('risk-assessment.control-measures.create', ['risk_assessment_id' => $riskAssessment->id]) }}" class="block w-full text-left px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100">
                            <i class="fas fa-shield-alt mr-2"></i>Add Control Measure
                        </a>
                        <a href="{{ route('risk-assessment.risk-reviews.create', ['risk_assessment_id' => $riskAssessment->id]) }}" class="block w-full text-left px-4 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100">
                            <i class="fas fa-sync-alt mr-2"></i>Schedule Review
                        </a>
                        @if($riskAssessment->hazard)
                        <a href="{{ route('risk-assessment.hazards.show', $riskAssessment->hazard) }}" class="block w-full text-left px-4 py-2 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100">
                            <i class="fas fa-exclamation-triangle mr-2"></i>View Hazard
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Information</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500">Status:</span>
                            <p class="text-gray-900">{{ ucfirst($riskAssessment->status) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Assessment Type:</span>
                            <p class="text-gray-900">{{ ucfirst($riskAssessment->assessment_type) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Next Review:</span>
                            <p class="text-gray-900">
                                @if($riskAssessment->next_review_date)
                                    {{ $riskAssessment->next_review_date->format('M d, Y') }}
                                    @if($riskAssessment->isOverdueForReview())
                                        <span class="text-red-600">(Overdue)</span>
                                    @endif
                                @else
                                    Not scheduled
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-500">Created by:</span>
                            <p class="text-gray-900">{{ $riskAssessment->creator->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

