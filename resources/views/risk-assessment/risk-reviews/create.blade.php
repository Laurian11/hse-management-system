@extends('layouts.app')

@section('title', 'Create Risk Review')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.risk-reviews.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Risk Reviews
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Create Risk Review</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('risk-assessment.risk-reviews.store') }}" method="POST" class="space-y-6" id="riskReviewForm">
        @csrf

        <!-- Risk Assessment Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Risk Assessment</h2>
            
            <div>
                <label for="risk_assessment_id" class="block text-sm font-medium text-gray-700 mb-1">Risk Assessment *</label>
                <select id="risk_assessment_id" name="risk_assessment_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Select Risk Assessment</option>
                    @foreach($riskAssessments as $assessment)
                        <option value="{{ $assessment->id }}" 
                                {{ old('risk_assessment_id', $selectedRiskAssessment?->id) == $assessment->id ? 'selected' : '' }}
                                data-risk-level="{{ $assessment->risk_level }}"
                                data-risk-score="{{ $assessment->risk_score }}">
                            {{ $assessment->reference_number }} - {{ $assessment->title }} 
                            ({{ strtoupper($assessment->risk_level) }} - Score: {{ $assessment->risk_score }})
                        </option>
                    @endforeach
                </select>
                @error('risk_assessment_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                @if($selectedRiskAssessment)
                <div class="mt-3 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                    <p class="text-sm text-purple-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Selected Assessment:</strong> {{ $selectedRiskAssessment->title }}
                        <br>
                        <span class="text-xs">Current Risk Level: <span class="font-semibold">{{ strtoupper($selectedRiskAssessment->risk_level) }}</span> | Risk Score: {{ $selectedRiskAssessment->risk_score }}</span>
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Review Type & Trigger Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Review Type & Trigger</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="review_type" class="block text-sm font-medium text-gray-700 mb-1">Review Type *</label>
                    <select id="review_type" name="review_type" required onchange="toggleTriggerFields()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Select Review Type</option>
                        <option value="scheduled" {{ old('review_type') == 'scheduled' ? 'selected' : '' }}>Scheduled Review</option>
                        <option value="triggered_by_incident" {{ old('review_type') == 'triggered_by_incident' ? 'selected' : '' }}>Triggered by Incident</option>
                        <option value="triggered_by_change" {{ old('review_type') == 'triggered_by_change' ? 'selected' : '' }}>Triggered by Change</option>
                        <option value="triggered_by_audit" {{ old('review_type') == 'triggered_by_audit' ? 'selected' : '' }}>Triggered by Audit</option>
                        <option value="triggered_by_regulation" {{ old('review_type') == 'triggered_by_regulation' ? 'selected' : '' }}>Triggered by Regulation</option>
                        <option value="triggered_by_control_failure" {{ old('review_type') == 'triggered_by_control_failure' ? 'selected' : '' }}>Triggered by Control Failure</option>
                        <option value="manual" {{ old('review_type') == 'manual' ? 'selected' : '' }}>Manual Review</option>
                        <option value="other" {{ old('review_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('review_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="scheduledDateField" style="display: none;">
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                    <input type="date" id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <div id="triggerDescriptionField" style="display: none;" class="md:col-span-2">
                    <label for="trigger_description" class="block text-sm font-medium text-gray-700 mb-1">Trigger Description</label>
                    <textarea id="trigger_description" name="trigger_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Describe what triggered this review">{{ old('trigger_description') }}</textarea>
                </div>

                <div id="triggeringIncidentField" style="display: none;" class="md:col-span-2">
                    <label for="triggering_incident_id" class="block text-sm font-medium text-gray-700 mb-1">Triggering Incident</label>
                    @if($incidents && $incidents->count() > 0)
                        <select id="triggering_incident_id" name="triggering_incident_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">Select Incident</option>
                            @foreach($incidents as $incident)
                                <option value="{{ $incident->id }}" {{ old('triggering_incident_id') == $incident->id ? 'selected' : '' }}>
                                    {{ $incident->reference_number }} - {{ $incident->title }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" id="triggering_incident_id" name="triggering_incident_id" 
                               value="{{ old('triggering_incident_id') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                               placeholder="Incident ID or Reference Number">
                        <p class="mt-1 text-xs text-gray-500">Enter the incident reference number or ID</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Review Schedule & Assignment -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedule & Assignment</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                    <input type="date" id="due_date" name="due_date" required 
                           value="{{ old('due_date') }}" min="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                    <select id="assigned_to" name="assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Select Reviewer</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Review Information (Optional - can be filled later) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Review Information</h2>
            <p class="text-sm text-gray-600 mb-4">This information can be filled when completing the review. You can leave it blank for now.</p>
            
            <div class="space-y-4">
                <div>
                    <label for="review_findings" class="block text-sm font-medium text-gray-700 mb-1">Review Findings</label>
                    <textarea id="review_findings" name="review_findings" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Findings from the review (can be completed later)">{{ old('review_findings') }}</textarea>
                </div>

                <div>
                    <label for="recommended_actions" class="block text-sm font-medium text-gray-700 mb-1">Recommended Actions</label>
                    <textarea id="recommended_actions" name="recommended_actions" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Recommended actions based on the review (can be completed later)">{{ old('recommended_actions') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('risk-assessment.risk-reviews.index') }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Risk Review
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleTriggerFields() {
    const reviewType = document.getElementById('review_type').value;
    const scheduledDateField = document.getElementById('scheduledDateField');
    const triggerDescriptionField = document.getElementById('triggerDescriptionField');
    const triggeringIncidentField = document.getElementById('triggeringIncidentField');
    
    // Reset visibility
    scheduledDateField.style.display = 'none';
    triggerDescriptionField.style.display = 'none';
    triggeringIncidentField.style.display = 'none';
    
    // Show appropriate fields based on review type
    if (reviewType === 'scheduled') {
        scheduledDateField.style.display = 'block';
    } else if (reviewType && reviewType !== 'scheduled' && reviewType !== 'manual') {
        triggerDescriptionField.style.display = 'block';
        
        // Show incident field only for incident-triggered reviews
        if (reviewType === 'triggered_by_incident') {
            triggeringIncidentField.style.display = 'block';
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleTriggerFields();
});
</script>
@endsection

