@extends('layouts.app')

@section('title', 'Edit Risk Assessment')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.risk-assessments.show', $riskAssessment) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Risk Assessment</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('risk-assessment.risk-assessments.update', $riskAssessment) }}" method="POST" class="space-y-6" id="riskAssessmentForm">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Assessment Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-black mb-1">Assessment Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $riskAssessment->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description', $riskAssessment->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hazard_id" class="block text-sm font-medium text-black mb-1">Related Hazard</label>
                    <select id="hazard_id" name="hazard_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Hazard (Optional)</option>
                        @foreach($hazards as $hazard)
                            <option value="{{ $hazard->id }}" {{ old('hazard_id', $riskAssessment->hazard_id) == $hazard->id ? 'selected' : '' }}>
                                {{ $hazard->reference_number }} - {{ $hazard->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assessment_type" class="block text-sm font-medium text-black mb-1">Assessment Type *</label>
                    <select id="assessment_type" name="assessment_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Type</option>
                        <option value="general" {{ old('assessment_type', $riskAssessment->assessment_type) == 'general' ? 'selected' : '' }}>General</option>
                        <option value="process" {{ old('assessment_type', $riskAssessment->assessment_type) == 'process' ? 'selected' : '' }}>Process</option>
                        <option value="task" {{ old('assessment_type', $riskAssessment->assessment_type) == 'task' ? 'selected' : '' }}>Task</option>
                        <option value="equipment" {{ old('assessment_type', $riskAssessment->assessment_type) == 'equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="chemical" {{ old('assessment_type', $riskAssessment->assessment_type) == 'chemical' ? 'selected' : '' }}>Chemical</option>
                        <option value="workplace" {{ old('assessment_type', $riskAssessment->assessment_type) == 'workplace' ? 'selected' : '' }}>Workplace</option>
                        <option value="environmental" {{ old('assessment_type', $riskAssessment->assessment_type) == 'environmental' ? 'selected' : '' }}>Environmental</option>
                        <option value="other" {{ old('assessment_type', $riskAssessment->assessment_type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('assessment_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-black mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $riskAssessment->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-black mb-1">Assigned To</label>
                    <select id="assigned_to" name="assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Person</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $riskAssessment->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assessment_date" class="block text-sm font-medium text-black mb-1">Assessment Date</label>
                    <input type="date" id="assessment_date" name="assessment_date" 
                           value="{{ old('assessment_date', $riskAssessment->assessment_date ? $riskAssessment->assessment_date->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="draft" {{ old('status', $riskAssessment->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="under_review" {{ old('status', $riskAssessment->status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ old('status', $riskAssessment->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="implementation" {{ old('status', $riskAssessment->status) == 'implementation' ? 'selected' : '' }}>Implementation</option>
                        <option value="monitoring" {{ old('status', $riskAssessment->status) == 'monitoring' ? 'selected' : '' }}>Monitoring</option>
                        <option value="closed" {{ old('status', $riskAssessment->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="archived" {{ old('status', $riskAssessment->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Risk Matrix Scoring -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Risk Matrix Scoring</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="severity" class="block text-sm font-medium text-black mb-1">Severity *</label>
                    <select id="severity" name="severity" required onchange="updateRiskScore()"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Severity</option>
                        <option value="negligible" data-score="1" {{ old('severity', $riskAssessment->severity) == 'negligible' ? 'selected' : '' }}>1 - Negligible</option>
                        <option value="minor" data-score="2" {{ old('severity', $riskAssessment->severity) == 'minor' ? 'selected' : '' }}>2 - Minor</option>
                        <option value="moderate" data-score="3" {{ old('severity', $riskAssessment->severity) == 'moderate' ? 'selected' : '' }}>3 - Moderate</option>
                        <option value="major" data-score="4" {{ old('severity', $riskAssessment->severity) == 'major' ? 'selected' : '' }}>4 - Major</option>
                        <option value="catastrophic" data-score="5" {{ old('severity', $riskAssessment->severity) == 'catastrophic' ? 'selected' : '' }}>5 - Catastrophic</option>
                    </select>
                    <input type="hidden" id="severity_score" name="severity_score" value="{{ old('severity_score', $riskAssessment->severity_score) }}">
                    @error('severity')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="likelihood" class="block text-sm font-medium text-black mb-1">Likelihood *</label>
                    <select id="likelihood" name="likelihood" required onchange="updateRiskScore()"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Likelihood</option>
                        <option value="rare" data-score="1" {{ old('likelihood', $riskAssessment->likelihood) == 'rare' ? 'selected' : '' }}>1 - Rare</option>
                        <option value="unlikely" data-score="2" {{ old('likelihood', $riskAssessment->likelihood) == 'unlikely' ? 'selected' : '' }}>2 - Unlikely</option>
                        <option value="possible" data-score="3" {{ old('likelihood', $riskAssessment->likelihood) == 'possible' ? 'selected' : '' }}>3 - Possible</option>
                        <option value="likely" data-score="4" {{ old('likelihood', $riskAssessment->likelihood) == 'likely' ? 'selected' : '' }}>4 - Likely</option>
                        <option value="almost_certain" data-score="5" {{ old('likelihood', $riskAssessment->likelihood) == 'almost_certain' ? 'selected' : '' }}>5 - Almost Certain</option>
                    </select>
                    <input type="hidden" id="likelihood_score" name="likelihood_score" value="{{ old('likelihood_score', $riskAssessment->likelihood_score) }}">
                    @error('likelihood')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Risk Score Display -->
            <div class="bg-gray-50 border border-gray-300 p-4 mb-6">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-sm text-gray-600">Risk Score</p>
                        <p class="text-2xl font-bold text-black" id="riskScoreDisplay">{{ $riskAssessment->risk_score ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Risk Level</p>
                        <p class="text-2xl font-bold" id="riskLevelDisplay">{{ strtoupper($riskAssessment->risk_level ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Matrix Position</p>
                        <p class="text-sm font-medium text-gray-700" id="matrixPosition">-</p>
                    </div>
                </div>
            </div>

            <!-- Existing Controls -->
            <div class="mb-6">
                <label for="existing_controls" class="block text-sm font-medium text-black mb-1">Existing Controls</label>
                <textarea id="existing_controls" name="existing_controls" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('existing_controls', $riskAssessment->existing_controls) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="existing_controls_effectiveness" class="block text-sm font-medium text-black mb-1">Control Effectiveness</label>
                    <select id="existing_controls_effectiveness" name="existing_controls_effectiveness"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Effectiveness</option>
                        <option value="none" {{ old('existing_controls_effectiveness', $riskAssessment->existing_controls_effectiveness) == 'none' ? 'selected' : '' }}>None</option>
                        <option value="poor" {{ old('existing_controls_effectiveness', $riskAssessment->existing_controls_effectiveness) == 'poor' ? 'selected' : '' }}>Poor</option>
                        <option value="adequate" {{ old('existing_controls_effectiveness', $riskAssessment->existing_controls_effectiveness) == 'adequate' ? 'selected' : '' }}>Adequate</option>
                        <option value="good" {{ old('existing_controls_effectiveness', $riskAssessment->existing_controls_effectiveness) == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="excellent" {{ old('existing_controls_effectiveness', $riskAssessment->existing_controls_effectiveness) == 'excellent' ? 'selected' : '' }}>Excellent</option>
                    </select>
                </div>

                <div>
                    <label for="review_frequency" class="block text-sm font-medium text-black mb-1">Review Frequency</label>
                    <select id="review_frequency" name="review_frequency"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Frequency</option>
                        <option value="monthly" {{ old('review_frequency', $riskAssessment->review_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('review_frequency', $riskAssessment->review_frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="semi_annually" {{ old('review_frequency', $riskAssessment->review_frequency) == 'semi_annually' ? 'selected' : '' }}>Semi-Annually</option>
                        <option value="annually" {{ old('review_frequency', $riskAssessment->review_frequency) == 'annually' ? 'selected' : '' }}>Annually</option>
                        <option value="biannually" {{ old('review_frequency', $riskAssessment->review_frequency) == 'biannually' ? 'selected' : '' }}>Biannually</option>
                        <option value="on_change" {{ old('review_frequency', $riskAssessment->review_frequency) == 'on_change' ? 'selected' : '' }}>On Change</option>
                        <option value="on_incident" {{ old('review_frequency', $riskAssessment->review_frequency) == 'on_incident' ? 'selected' : '' }}>On Incident</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Residual Risk (Optional) -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Residual Risk (After Controls)</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="residual_severity" class="block text-sm font-medium text-black mb-1">Residual Severity</label>
                    <select id="residual_severity" name="residual_severity" onchange="updateResidualRiskScore()"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Severity</option>
                        <option value="negligible" data-score="1" {{ old('residual_severity', $riskAssessment->residual_severity) == 'negligible' ? 'selected' : '' }}>1 - Negligible</option>
                        <option value="minor" data-score="2" {{ old('residual_severity', $riskAssessment->residual_severity) == 'minor' ? 'selected' : '' }}>2 - Minor</option>
                        <option value="moderate" data-score="3" {{ old('residual_severity', $riskAssessment->residual_severity) == 'moderate' ? 'selected' : '' }}>3 - Moderate</option>
                        <option value="major" data-score="4" {{ old('residual_severity', $riskAssessment->residual_severity) == 'major' ? 'selected' : '' }}>4 - Major</option>
                        <option value="catastrophic" data-score="5" {{ old('residual_severity', $riskAssessment->residual_severity) == 'catastrophic' ? 'selected' : '' }}>5 - Catastrophic</option>
                    </select>
                    <input type="hidden" id="residual_severity_score" name="residual_severity_score" value="{{ old('residual_severity_score', $riskAssessment->residual_severity_score) }}">
                </div>

                <div>
                    <label for="residual_likelihood" class="block text-sm font-medium text-black mb-1">Residual Likelihood</label>
                    <select id="residual_likelihood" name="residual_likelihood" onchange="updateResidualRiskScore()"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Likelihood</option>
                        <option value="rare" data-score="1" {{ old('residual_likelihood', $riskAssessment->residual_likelihood) == 'rare' ? 'selected' : '' }}>1 - Rare</option>
                        <option value="unlikely" data-score="2" {{ old('residual_likelihood', $riskAssessment->residual_likelihood) == 'unlikely' ? 'selected' : '' }}>2 - Unlikely</option>
                        <option value="possible" data-score="3" {{ old('residual_likelihood', $riskAssessment->residual_likelihood) == 'possible' ? 'selected' : '' }}>3 - Possible</option>
                        <option value="likely" data-score="4" {{ old('residual_likelihood', $riskAssessment->residual_likelihood) == 'likely' ? 'selected' : '' }}>4 - Likely</option>
                        <option value="almost_certain" data-score="5" {{ old('residual_likelihood', $riskAssessment->residual_likelihood) == 'almost_certain' ? 'selected' : '' }}>5 - Almost Certain</option>
                    </select>
                    <input type="hidden" id="residual_likelihood_score" name="residual_likelihood_score" value="{{ old('residual_likelihood_score', $riskAssessment->residual_likelihood_score) }}">
                </div>

                <div class="md:col-span-2">
                    <div class="bg-gray-50 border border-gray-300 p-4">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-sm text-gray-600">Residual Risk Score</p>
                                <p class="text-2xl font-bold text-black" id="residualRiskScoreDisplay">{{ $riskAssessment->residual_risk_score ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Residual Risk Level</p>
                                <p class="text-2xl font-bold" id="residualRiskLevelDisplay">{{ strtoupper($riskAssessment->residual_risk_level ?? '-') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALARP Assessment -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">ALARP Assessment</h2>
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" id="is_alarp" name="is_alarp" value="1" {{ old('is_alarp', $riskAssessment->is_alarp) ? 'checked' : '' }}
                           class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                    <label for="is_alarp" class="ml-3 text-sm text-black">
                        Risk is As Low As Reasonably Practicable (ALARP)
                    </label>
                </div>
                <div>
                    <label for="alarp_justification" class="block text-sm font-medium text-black mb-1">ALARP Justification</label>
                    <textarea id="alarp_justification" name="alarp_justification" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('alarp_justification', $riskAssessment->alarp_justification) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('risk-assessment.risk-assessments.show', $riskAssessment) }}" 
               class="px-6 py-2 border border-gray-300 text-black hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                <i class="fas fa-save mr-2"></i>Update Risk Assessment
            </button>
        </div>
    </form>
</div>

<script>
function updateRiskScore() {
    const severitySelect = document.getElementById('severity');
    const likelihoodSelect = document.getElementById('likelihood');
    
    const severityScore = parseInt(severitySelect.options[severitySelect.selectedIndex]?.dataset.score || 0);
    const likelihoodScore = parseInt(likelihoodSelect.options[likelihoodSelect.selectedIndex]?.dataset.score || 0);
    
    document.getElementById('severity_score').value = severityScore;
    document.getElementById('likelihood_score').value = likelihoodScore;
    
    const riskScore = severityScore * likelihoodScore;
    document.getElementById('riskScoreDisplay').textContent = riskScore || '0';
    
    // Calculate risk level
    let riskLevel = '-';
    let riskLevelColor = 'text-black';
    if (riskScore >= 20) {
        riskLevel = 'EXTREME';
        riskLevelColor = 'text-[#CC0000]';
    } else if (riskScore >= 15) {
        riskLevel = 'CRITICAL';
        riskLevelColor = 'text-[#CC0000]';
    } else if (riskScore >= 10) {
        riskLevel = 'HIGH';
        riskLevelColor = 'text-[#FF6600]';
    } else if (riskScore >= 5) {
        riskLevel = 'MEDIUM';
        riskLevelColor = 'text-[#FFCC00]';
    } else if (riskScore > 0) {
        riskLevel = 'LOW';
        riskLevelColor = 'text-[#00CC66]';
    }
    
    document.getElementById('riskLevelDisplay').textContent = riskLevel;
    document.getElementById('riskLevelDisplay').className = 'text-2xl font-bold ' + riskLevelColor;
    
    if (severityScore > 0 && likelihoodScore > 0) {
        document.getElementById('matrixPosition').textContent = `S${severityScore} x L${likelihoodScore}`;
    } else {
        document.getElementById('matrixPosition').textContent = '-';
    }
}

function updateResidualRiskScore() {
    const severitySelect = document.getElementById('residual_severity');
    const likelihoodSelect = document.getElementById('residual_likelihood');
    
    const severityScore = parseInt(severitySelect.options[severitySelect.selectedIndex]?.dataset.score || 0);
    const likelihoodScore = parseInt(likelihoodSelect.options[likelihoodSelect.selectedIndex]?.dataset.score || 0);
    
    document.getElementById('residual_severity_score').value = severityScore;
    document.getElementById('residual_likelihood_score').value = likelihoodScore;
    
    const riskScore = severityScore * likelihoodScore;
    document.getElementById('residualRiskScoreDisplay').textContent = riskScore || '-';
    
    // Calculate risk level
    let riskLevel = '-';
    let riskLevelColor = 'text-black';
    if (riskScore >= 20) {
        riskLevel = 'EXTREME';
        riskLevelColor = 'text-[#CC0000]';
    } else if (riskScore >= 15) {
        riskLevel = 'CRITICAL';
        riskLevelColor = 'text-[#CC0000]';
    } else if (riskScore >= 10) {
        riskLevel = 'HIGH';
        riskLevelColor = 'text-[#FF6600]';
    } else if (riskScore >= 5) {
        riskLevel = 'MEDIUM';
        riskLevelColor = 'text-[#FFCC00]';
    } else if (riskScore > 0) {
        riskLevel = 'LOW';
        riskLevelColor = 'text-[#00CC66]';
    }
    
    document.getElementById('residualRiskLevelDisplay').textContent = riskLevel;
    document.getElementById('residualRiskLevelDisplay').className = 'text-2xl font-bold ' + riskLevelColor;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateRiskScore();
    updateResidualRiskScore();
});
</script>
@endsection

