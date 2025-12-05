@extends('layouts.app')

@section('title', 'Risk Review: ' . $riskReview->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.risk-reviews.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">Risk Review</h1>
                    <p class="text-sm text-gray-500">{{ $riskReview->reference_number }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @if($riskReview->status !== 'completed')
                    <a href="{{ route('risk-assessment.risk-reviews.edit', $riskReview) }}" class="px-4 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Status Badge -->
    <div class="mb-6">
        <span class="px-3 py-1 text-sm font-medium rounded 
            @if($riskReview->status === 'completed') bg-[#00CC66] text-white
            @elseif($riskReview->status === 'in_progress') bg-[#0066CC] text-white
            @elseif($riskReview->status === 'overdue') bg-[#CC0000] text-white
            @else bg-gray-300 text-black
            @endif">
            {{ ucfirst(str_replace('_', ' ', $riskReview->status)) }}
        </span>
        <span class="ml-2 px-3 py-1 text-sm font-medium rounded bg-gray-200 text-black">
            {{ ucfirst(str_replace('_', ' ', $riskReview->review_type)) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Risk Assessment Info -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Risk Assessment</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Assessment</label>
                        <a href="{{ route('risk-assessment.risk-assessments.show', $riskReview->riskAssessment) }}" 
                           class="text-[#0066CC] hover:underline block mt-1">
                            {{ $riskReview->riskAssessment->reference_number }} - {{ $riskReview->riskAssessment->title }}
                        </a>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Current Risk Level</label>
                        <p class="text-black mt-1">{{ strtoupper($riskReview->riskAssessment->risk_level) }} (Score: {{ $riskReview->riskAssessment->risk_score }})</p>
                    </div>
                </div>
            </div>

            <!-- Review Details -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Review Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Due Date</label>
                        <p class="text-black mt-1">{{ $riskReview->due_date->format('M d, Y') }}</p>
                    </div>
                    @if($riskReview->scheduled_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Scheduled Date</label>
                        <p class="text-black mt-1">{{ $riskReview->scheduled_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($riskReview->review_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Review Date</label>
                        <p class="text-black mt-1">{{ $riskReview->review_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($riskReview->trigger_description)
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-500">Trigger Description</label>
                        <p class="text-black mt-1">{{ $riskReview->trigger_description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Review Findings -->
            @if($riskReview->review_findings)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Review Findings</h2>
                <p class="text-gray-700">{{ $riskReview->review_findings }}</p>
            </div>
            @endif

            <!-- Updated Risk Assessment -->
            @if($riskReview->status === 'completed' && $riskReview->updated_risk_score)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Updated Risk Assessment</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Updated Severity</label>
                        <p class="text-black mt-1">{{ ucfirst($riskReview->updated_severity) }} (Score: {{ $riskReview->updated_severity_score }})</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Updated Likelihood</label>
                        <p class="text-black mt-1">{{ ucfirst($riskReview->updated_likelihood) }} (Score: {{ $riskReview->updated_likelihood_score }})</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Updated Risk Score</label>
                        <p class="text-black mt-1 font-bold">{{ $riskReview->updated_risk_score }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Updated Risk Level</label>
                        <p class="text-black mt-1 font-bold">{{ strtoupper($riskReview->updated_risk_level) }}</p>
                    </div>
                    @if($riskReview->risk_change)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Risk Change</label>
                        <p class="text-black mt-1">{{ ucfirst($riskReview->risk_change) }}</p>
                    </div>
                    @endif
                    @if($riskReview->risk_change_reason)
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-500">Risk Change Reason</label>
                        <p class="text-black mt-1">{{ $riskReview->risk_change_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Changes & Recommendations -->
            @if($riskReview->changes_identified || $riskReview->recommended_actions)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Changes & Recommendations</h2>
                @if($riskReview->changes_identified)
                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-500">Changes Identified</label>
                    <p class="text-gray-700 mt-1">{{ $riskReview->changes_identified }}</p>
                </div>
                @endif
                @if($riskReview->recommended_actions)
                <div>
                    <label class="text-sm font-medium text-gray-500">Recommended Actions</label>
                    <p class="text-gray-700 mt-1">{{ $riskReview->recommended_actions }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Complete Review Form -->
            @if($riskReview->status !== 'completed')
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Complete Review</h2>
                <form action="{{ route('risk-assessment.risk-reviews.complete', $riskReview) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="review_findings" class="block text-sm font-medium text-black mb-1">Review Findings *</label>
                            <textarea id="review_findings" name="review_findings" rows="4" required
                                      class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="updated_severity_score" class="block text-sm font-medium text-black mb-1">Updated Severity Score</label>
                                <input type="number" id="updated_severity_score" name="updated_severity_score" min="1" max="5"
                                       class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            </div>
                            <div>
                                <label for="updated_likelihood_score" class="block text-sm font-medium text-black mb-1">Updated Likelihood Score</label>
                                <input type="number" id="updated_likelihood_score" name="updated_likelihood_score" min="1" max="5"
                                       class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            </div>
                        </div>
                        <div>
                            <label for="recommended_actions" class="block text-sm font-medium text-black mb-1">Recommended Actions</label>
                            <textarea id="recommended_actions" name="recommended_actions" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]"></textarea>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-[#00CC66] text-white hover:bg-[#00A352]">
                            Complete Review
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Assignment -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Assignment</h3>
                <div class="space-y-3">
                    @if($riskReview->assignedTo)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Assigned To</label>
                        <p class="text-black mt-1">{{ $riskReview->assignedTo->name }}</p>
                    </div>
                    @endif
                    @if($riskReview->reviewedBy)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Reviewed By</label>
                        <p class="text-black mt-1">{{ $riskReview->reviewedBy->name }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-500">Created At</label>
                        <p class="text-black mt-1">{{ $riskReview->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Triggering Incident -->
            @if($riskReview->triggeringIncident)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Triggering Incident</h3>
                <a href="{{ route('incidents.show', $riskReview->triggeringIncident) }}" 
                   class="text-[#0066CC] hover:underline">
                    {{ $riskReview->triggeringIncident->reference_number }}
                </a>
            </div>
            @endif

            <!-- Next Review -->
            @if($riskReview->next_review_date)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Next Review</h3>
                <div class="space-y-2">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Next Review Date</label>
                        <p class="text-black mt-1">{{ $riskReview->next_review_date->format('M d, Y') }}</p>
                    </div>
                    @if($riskReview->next_review_frequency)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Frequency</label>
                        <p class="text-black mt-1">{{ ucfirst(str_replace('_', ' ', $riskReview->next_review_frequency)) }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

