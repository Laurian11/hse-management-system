@extends('layouts.app')

@section('title', 'Create Risk Assessment')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.risk-assessments.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Risk Register
                </a>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ isset($copyFrom) ? 'Copy Risk Assessment' : 'Create Risk Assessment' }}
                    @if(isset($copyFrom))
                        <span class="text-sm font-normal text-gray-500">(from {{ $copyFrom->reference_number }})</span>
                    @endif
                </h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('risk-assessment.risk-assessments.store') }}" method="POST" class="space-y-6" id="riskAssessmentForm">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Assessment Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Assessment Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', isset($copyFrom) ? $copyFrom->title : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Brief title for this risk assessment">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Detailed description of the risk">{{ old('description', isset($copyFrom) ? $copyFrom->description : '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hazard_id" class="block text-sm font-medium text-gray-700 mb-1">Related Hazard</label>
                    <select id="hazard_id" name="hazard_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Hazard (Optional)</option>
                        @foreach($hazards as $hazard)
                            <option value="{{ $hazard->id }}" {{ old('hazard_id', isset($copyFrom) ? $copyFrom->hazard_id : $selectedHazard?->id) == $hazard->id ? 'selected' : '' }}>
                                {{ $hazard->reference_number }} - {{ $hazard->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assessment_type" class="block text-sm font-medium text-gray-700 mb-1">Assessment Type *</label>
                    <select id="assessment_type" name="assessment_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Type</option>
                        <option value="general" {{ old('assessment_type', isset($copyFrom) ? $copyFrom->assessment_type : '') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="process" {{ old('assessment_type', isset($copyFrom) ? $copyFrom->assessment_type : '') == 'process' ? 'selected' : '' }}>Process</option>
                        <option value="task" {{ old('assessment_type', isset($copyFrom) ? $copyFrom->assessment_type : '') == 'task' ? 'selected' : '' }}>Task</option>
                        <option value="equipment" {{ old('assessment_type', isset($copyFrom) ? $copyFrom->assessment_type : '') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="chemical" {{ old('assessment_type', isset($copyFrom) ? $copyFrom->assessment_type : '') == 'chemical' ? 'selected' : '' }}>Chemical</option>
                        <option value="workplace" {{ old('assessment_type', isset($copyFrom) ? $copyFrom->assessment_type : '') == 'workplace' ? 'selected' : '' }}>Workplace</option>
                        <option value="environmental" {{ old('assessment_type', isset($copyFrom) ? $copyFrom->assessment_type : '') == 'environmental' ? 'selected' : '' }}>Environmental</option>
                    </select>
                    @error('assessment_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', isset($copyFrom) ? $copyFrom->department_id : '') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                    <select id="assigned_to" name="assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Person</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', isset($copyFrom) ? $copyFrom->assigned_to : '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($selectedIncident)
                <div class="md:col-span-2">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Creating risk assessment from incident: <strong>{{ $selectedIncident->reference_number }}</strong>
                        </p>
                        <input type="hidden" name="related_incident_id" value="{{ $selectedIncident->id }}">
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Risk Matrix Scoring -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Risk Matrix Scoring</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="severity" class="block text-sm font-medium text-gray-700 mb-1">Severity *</label>
                    <select id="severity" name="severity" required onchange="updateRiskScore()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Severity</option>
                        <option value="negligible" data-score="1" {{ old('severity', isset($copyFrom) ? $copyFrom->severity : '') == 'negligible' ? 'selected' : '' }}>1 - Negligible</option>
                        <option value="minor" data-score="2" {{ old('severity', isset($copyFrom) ? $copyFrom->severity : '') == 'minor' ? 'selected' : '' }}>2 - Minor</option>
                        <option value="moderate" data-score="3" {{ old('severity', isset($copyFrom) ? $copyFrom->severity : '') == 'moderate' ? 'selected' : '' }}>3 - Moderate</option>
                        <option value="major" data-score="4" {{ old('severity', isset($copyFrom) ? $copyFrom->severity : '') == 'major' ? 'selected' : '' }}>4 - Major</option>
                        <option value="catastrophic" data-score="5" {{ old('severity', isset($copyFrom) ? $copyFrom->severity : '') == 'catastrophic' ? 'selected' : '' }}>5 - Catastrophic</option>
                    </select>
                    <input type="hidden" id="severity_score" name="severity_score" value="{{ old('severity_score', isset($copyFrom) ? $copyFrom->severity_score : 1) }}">
                    @error('severity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="likelihood" class="block text-sm font-medium text-gray-700 mb-1">Likelihood *</label>
                    <select id="likelihood" name="likelihood" required onchange="updateRiskScore()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Likelihood</option>
                        <option value="rare" data-score="1" {{ old('likelihood', isset($copyFrom) ? $copyFrom->likelihood : '') == 'rare' ? 'selected' : '' }}>1 - Rare</option>
                        <option value="unlikely" data-score="2" {{ old('likelihood', isset($copyFrom) ? $copyFrom->likelihood : '') == 'unlikely' ? 'selected' : '' }}>2 - Unlikely</option>
                        <option value="possible" data-score="3" {{ old('likelihood', isset($copyFrom) ? $copyFrom->likelihood : '') == 'possible' ? 'selected' : '' }}>3 - Possible</option>
                        <option value="likely" data-score="4" {{ old('likelihood', isset($copyFrom) ? $copyFrom->likelihood : '') == 'likely' ? 'selected' : '' }}>4 - Likely</option>
                        <option value="almost_certain" data-score="5" {{ old('likelihood', isset($copyFrom) ? $copyFrom->likelihood : '') == 'almost_certain' ? 'selected' : '' }}>5 - Almost Certain</option>
                    </select>
                    <input type="hidden" id="likelihood_score" name="likelihood_score" value="{{ old('likelihood_score', isset($copyFrom) ? $copyFrom->likelihood_score : 1) }}">
                    @error('likelihood')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Risk Score Display -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-sm text-gray-600">Risk Score</p>
                        <p class="text-2xl font-bold text-gray-900" id="riskScoreDisplay">0</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Risk Level</p>
                        <p class="text-2xl font-bold" id="riskLevelDisplay">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Matrix Position</p>
                        <p class="text-sm font-medium text-gray-700" id="matrixPosition">-</p>
                    </div>
                </div>
            </div>

            <!-- Risk Matrix Visualization -->
            <div class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-3">Risk Matrix (5x5)</h3>
                <div class="bg-white border border-gray-300 rounded-lg p-4 overflow-x-auto">
                    <table class="w-full text-xs" id="riskMatrix">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 p-2 bg-gray-100"></th>
                                <th class="border border-gray-300 p-2 bg-gray-100">1 - Rare</th>
                                <th class="border border-gray-300 p-2 bg-gray-100">2 - Unlikely</th>
                                <th class="border border-gray-300 p-2 bg-gray-100">3 - Possible</th>
                                <th class="border border-gray-300 p-2 bg-gray-100">4 - Likely</th>
                                <th class="border border-gray-300 p-2 bg-gray-100">5 - Almost Certain</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border border-gray-300 p-2 bg-gray-100 font-medium">5 - Catastrophic</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="5" data-likelihood="1" data-score="5">5</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="5" data-likelihood="2" data-score="10">10</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="5" data-likelihood="3" data-score="15">15</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="5" data-likelihood="4" data-score="20">20</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="5" data-likelihood="5" data-score="25">25</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 p-2 bg-gray-100 font-medium">4 - Major</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="4" data-likelihood="1" data-score="4">4</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="4" data-likelihood="2" data-score="8">8</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="4" data-likelihood="3" data-score="12">12</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="4" data-likelihood="4" data-score="16">16</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="4" data-likelihood="5" data-score="20">20</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 p-2 bg-gray-100 font-medium">3 - Moderate</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="3" data-likelihood="1" data-score="3">3</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="3" data-likelihood="2" data-score="6">6</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="3" data-likelihood="3" data-score="9">9</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="3" data-likelihood="4" data-score="12">12</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="3" data-likelihood="5" data-score="15">15</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 p-2 bg-gray-100 font-medium">2 - Minor</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="2" data-likelihood="1" data-score="2">2</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="2" data-likelihood="2" data-score="4">4</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="2" data-likelihood="3" data-score="6">6</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="2" data-likelihood="4" data-score="8">8</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="2" data-likelihood="5" data-score="10">10</td>
                            </tr>
                            <tr>
                                <td class="border border-gray-300 p-2 bg-gray-100 font-medium">1 - Negligible</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="1" data-likelihood="1" data-score="1">1</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="1" data-likelihood="2" data-score="2">2</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="1" data-likelihood="3" data-score="3">3</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="1" data-likelihood="4" data-score="4">4</td>
                                <td class="border border-gray-300 p-2 text-center" data-severity="1" data-likelihood="5" data-score="5">5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Existing Controls -->
            <div class="mb-6">
                <label for="existing_controls" class="block text-sm font-medium text-gray-700 mb-1">Existing Controls</label>
                <textarea id="existing_controls" name="existing_controls" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Describe any existing controls in place">{{ old('existing_controls') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="existing_controls_effectiveness" class="block text-sm font-medium text-gray-700 mb-1">Control Effectiveness</label>
                    <select id="existing_controls_effectiveness" name="existing_controls_effectiveness"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Effectiveness</option>
                        <option value="none" {{ old('existing_controls_effectiveness') == 'none' ? 'selected' : '' }}>None</option>
                        <option value="poor" {{ old('existing_controls_effectiveness') == 'poor' ? 'selected' : '' }}>Poor</option>
                        <option value="adequate" {{ old('existing_controls_effectiveness') == 'adequate' ? 'selected' : '' }}>Adequate</option>
                        <option value="good" {{ old('existing_controls_effectiveness') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="excellent" {{ old('existing_controls_effectiveness') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                    </select>
                </div>

                <div>
                    <label for="review_frequency" class="block text-sm font-medium text-gray-700 mb-1">Review Frequency</label>
                    <select id="review_frequency" name="review_frequency"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Frequency</option>
                        <option value="monthly" {{ old('review_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('review_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="semi_annually" {{ old('review_frequency') == 'semi_annually' ? 'selected' : '' }}>Semi-Annually</option>
                        <option value="annually" {{ old('review_frequency') == 'annually' ? 'selected' : '' }}>Annually</option>
                        <option value="biannually" {{ old('review_frequency') == 'biannually' ? 'selected' : '' }}>Biannually</option>
                        <option value="on_change" {{ old('review_frequency') == 'on_change' ? 'selected' : '' }}>On Change</option>
                        <option value="on_incident" {{ old('review_frequency') == 'on_incident' ? 'selected' : '' }}>On Incident</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Residual Risk (Optional) -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Residual Risk (After Controls)</h2>
            <p class="text-sm text-gray-600 mb-4">Assess the risk level after implementing control measures</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="residual_severity" class="block text-sm font-medium text-gray-700 mb-1">Residual Severity</label>
                    <select id="residual_severity" name="residual_severity" onchange="updateResidualRiskScore()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Severity</option>
                        <option value="negligible" data-score="1" {{ old('residual_severity') == 'negligible' ? 'selected' : '' }}>1 - Negligible</option>
                        <option value="minor" data-score="2" {{ old('residual_severity') == 'minor' ? 'selected' : '' }}>2 - Minor</option>
                        <option value="moderate" data-score="3" {{ old('residual_severity') == 'moderate' ? 'selected' : '' }}>3 - Moderate</option>
                        <option value="major" data-score="4" {{ old('residual_severity') == 'major' ? 'selected' : '' }}>4 - Major</option>
                        <option value="catastrophic" data-score="5" {{ old('residual_severity') == 'catastrophic' ? 'selected' : '' }}>5 - Catastrophic</option>
                    </select>
                    <input type="hidden" id="residual_severity_score" name="residual_severity_score" value="{{ old('residual_severity_score') }}">
                </div>

                <div>
                    <label for="residual_likelihood" class="block text-sm font-medium text-gray-700 mb-1">Residual Likelihood</label>
                    <select id="residual_likelihood" name="residual_likelihood" onchange="updateResidualRiskScore()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Likelihood</option>
                        <option value="rare" data-score="1" {{ old('residual_likelihood') == 'rare' ? 'selected' : '' }}>1 - Rare</option>
                        <option value="unlikely" data-score="2" {{ old('residual_likelihood') == 'unlikely' ? 'selected' : '' }}>2 - Unlikely</option>
                        <option value="possible" data-score="3" {{ old('residual_likelihood') == 'possible' ? 'selected' : '' }}>3 - Possible</option>
                        <option value="likely" data-score="4" {{ old('residual_likelihood') == 'likely' ? 'selected' : '' }}>4 - Likely</option>
                        <option value="almost_certain" data-score="5" {{ old('residual_likelihood') == 'almost_certain' ? 'selected' : '' }}>5 - Almost Certain</option>
                    </select>
                    <input type="hidden" id="residual_likelihood_score" name="residual_likelihood_score" value="{{ old('residual_likelihood_score') }}">
                </div>

                <div class="md:col-span-2">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-sm text-gray-600">Residual Risk Score</p>
                                <p class="text-2xl font-bold text-gray-900" id="residualRiskScoreDisplay">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Residual Risk Level</p>
                                <p class="text-2xl font-bold" id="residualRiskLevelDisplay">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALARP Assessment -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">ALARP Assessment</h2>
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" id="is_alarp" name="is_alarp" value="1" {{ old('is_alarp') ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_alarp" class="ml-3 text-sm text-gray-700">
                        Risk is As Low As Reasonably Practicable (ALARP)
                    </label>
                </div>
                <div>
                    <label for="alarp_justification" class="block text-sm font-medium text-gray-700 mb-1">ALARP Justification</label>
                    <textarea id="alarp_justification" name="alarp_justification" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Justify why the risk is ALARP or why further reduction is not reasonably practicable">{{ old('alarp_justification') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('risk-assessment.risk-assessments.index') }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Risk Assessment
                </button>
            </div>
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
    let riskLevelColor = 'text-gray-900';
    if (riskScore >= 20) {
        riskLevel = 'EXTREME';
        riskLevelColor = 'text-red-800';
    } else if (riskScore >= 15) {
        riskLevel = 'CRITICAL';
        riskLevelColor = 'text-red-600';
    } else if (riskScore >= 10) {
        riskLevel = 'HIGH';
        riskLevelColor = 'text-orange-600';
    } else if (riskScore >= 5) {
        riskLevel = 'MEDIUM';
        riskLevelColor = 'text-yellow-600';
    } else if (riskScore > 0) {
        riskLevel = 'LOW';
        riskLevelColor = 'text-green-600';
    }
    
    document.getElementById('riskLevelDisplay').textContent = riskLevel;
    document.getElementById('riskLevelDisplay').className = 'text-2xl font-bold ' + riskLevelColor;
    
    // Update matrix position
    if (severityScore > 0 && likelihoodScore > 0) {
        document.getElementById('matrixPosition').textContent = `S${severityScore} x L${likelihoodScore}`;
        
        // Highlight matrix cell
        document.querySelectorAll('#riskMatrix td[data-severity][data-likelihood]').forEach(cell => {
            cell.classList.remove('bg-blue-200', 'border-blue-500', 'border-2');
        });
        const cell = document.querySelector(`#riskMatrix td[data-severity="${severityScore}"][data-likelihood="${likelihoodScore}"]`);
        if (cell) {
            cell.classList.add('bg-blue-200', 'border-blue-500', 'border-2');
        }
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
    let riskLevelColor = 'text-gray-900';
    if (riskScore >= 20) {
        riskLevel = 'EXTREME';
        riskLevelColor = 'text-red-800';
    } else if (riskScore >= 15) {
        riskLevel = 'CRITICAL';
        riskLevelColor = 'text-red-600';
    } else if (riskScore >= 10) {
        riskLevel = 'HIGH';
        riskLevelColor = 'text-orange-600';
    } else if (riskScore >= 5) {
        riskLevel = 'MEDIUM';
        riskLevelColor = 'text-yellow-600';
    } else if (riskScore > 0) {
        riskLevel = 'LOW';
        riskLevelColor = 'text-green-600';
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

