@extends('layouts.app')

@section('title', isset($jsa) ? 'Edit Job Safety Analysis' : 'Create Job Safety Analysis')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ isset($jsa) ? route('risk-assessment.jsas.show', $jsa) : route('risk-assessment.jsas.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ isset($jsa) ? 'Edit Job Safety Analysis' : 'Create Job Safety Analysis (JSA)' }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ isset($jsa) ? route('risk-assessment.jsas.update', $jsa) : route('risk-assessment.jsas.store') }}" method="POST" class="space-y-6" id="jsaForm">
        @csrf
        @if(isset($jsa))
            @method('PUT')
        @endif

        <!-- Basic Job Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">Job Title *</label>
                    <input type="text" id="job_title" name="job_title" required value="{{ old('job_title', $jsa->job_title ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="e.g., Welding Operations, Confined Space Entry">
                    @error('job_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="job_description" class="block text-sm font-medium text-gray-700 mb-1">Job Description *</label>
                    <textarea id="job_description" name="job_description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="Detailed description of the job/task">{{ old('job_description', $jsa->job_description ?? '') }}</textarea>
                    @error('job_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $jsa->location ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="e.g., Building A, Floor 3">
                </div>

                <div>
                    <label for="work_area" class="block text-sm font-medium text-gray-700 mb-1">Work Area</label>
                    <input type="text" id="work_area" name="work_area" value="{{ old('work_area', $jsa->work_area ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="e.g., Production Line 2">
                </div>

                <div>
                    <label for="job_date" class="block text-sm font-medium text-gray-700 mb-1">Job Date</label>
                    <input type="date" id="job_date" name="job_date" value="{{ old('job_date', $jsa->job_date ? $jsa->job_date->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', $jsa->start_time ? $jsa->start_time->format('H:i') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time', $jsa->end_time ? $jsa->end_time->format('H:i') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $jsa->department_id ?? '') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="supervisor_id" class="block text-sm font-medium text-gray-700 mb-1">Supervisor</label>
                    <select id="supervisor_id" name="supervisor_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Select Supervisor</option>
                        @foreach($supervisors as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ old('supervisor_id', $jsa->supervisor_id ?? '') == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="related_risk_assessment_id" class="block text-sm font-medium text-gray-700 mb-1">Related Risk Assessment</label>
                    <select id="related_risk_assessment_id" name="related_risk_assessment_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Select Risk Assessment (Optional)</option>
                        @foreach($riskAssessments as $assessment)
                            <option value="{{ $assessment->id }}" {{ old('related_risk_assessment_id', $jsa->related_risk_assessment_id ?? $selectedRiskAssessment?->id ?? '') == $assessment->id ? 'selected' : '' }}>
                                {{ $assessment->reference_number }} - {{ $assessment->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Job Steps -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Job Steps *</h2>
                <button type="button" onclick="addJobStep()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                    <i class="fas fa-plus mr-2"></i>Add Step
                </button>
            </div>
            
            <div id="jobStepsContainer" class="space-y-4">
                @if(isset($jsa) && $jsa->job_steps && count($jsa->job_steps) > 0)
                    @foreach($jsa->job_steps as $index => $step)
                        <div class="border border-gray-300 rounded-lg p-4" data-step-index="{{ $index }}">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="font-medium text-gray-900">Step {{ $step['step_number'] ?? ($index + 1) }}</h3>
                                <button type="button" onclick="this.closest('.border').remove()" class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Step Number</label>
                                    <input type="number" name="job_steps[{{ $index }}][step_number]" value="{{ $step['step_number'] ?? ($index + 1) }}" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                                    <textarea name="job_steps[{{ $index }}][description]" rows="2" required
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                                              placeholder="Describe this step">{{ $step['description'] ?? '' }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hazards</label>
                                    <textarea name="job_steps[{{ $index }}][hazards][]" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                                              placeholder="List potential hazards">{{ is_array($step['hazards'] ?? []) ? implode("\n", $step['hazards']) : ($step['hazards'] ?? '') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Controls</label>
                                    <textarea name="job_steps[{{ $index }}][controls][]" rows="2"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                                              placeholder="List control measures">{{ is_array($step['controls'] ?? []) ? implode("\n", $step['controls']) : ($step['controls'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            @error('job_steps')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Team Members -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Team Members</h2>
                <button type="button" onclick="addTeamMember()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                    <i class="fas fa-plus mr-2"></i>Add Member
                </button>
            </div>
            
            <div id="teamMembersContainer" class="space-y-2">
                @if(isset($jsa) && $jsa->team_members && count($jsa->team_members) > 0)
                    @foreach($jsa->team_members as $memberId)
                        <div class="flex items-center space-x-2">
                            <select name="team_members[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                <option value="">Select Team Member</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $memberId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="this.closest('.flex').remove()" class="text-red-600 hover:text-red-700 px-2">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Requirements -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Requirements & Resources</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="required_qualifications" class="block text-sm font-medium text-gray-700 mb-1">Required Qualifications</label>
                    <textarea id="required_qualifications" name="required_qualifications" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="e.g., Certified Welder, Confined Space Entry Permit">{{ old('required_qualifications', $jsa->required_qualifications ?? '') }}</textarea>
                </div>

                <div>
                    <label for="required_training" class="block text-sm font-medium text-gray-700 mb-1">Required Training</label>
                    <textarea id="required_training" name="required_training" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="e.g., Lockout/Tagout, Fall Protection">{{ old('required_training', $jsa->required_training ?? '') }}</textarea>
                </div>

                <div>
                    <label for="equipment_required" class="block text-sm font-medium text-gray-700 mb-1">Equipment Required</label>
                    <div id="equipmentContainer" class="space-y-2">
                        @if(isset($jsa) && $jsa->equipment_required && count($jsa->equipment_required) > 0)
                            @foreach($jsa->equipment_required as $equipment)
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="equipment_required[]" value="{{ $equipment }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                    <button type="button" onclick="this.closest('.flex').remove()" class="text-red-600 hover:text-red-700 px-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" onclick="addEquipment()" class="mt-2 text-sm text-yellow-600 hover:text-yellow-700">
                        <i class="fas fa-plus mr-1"></i>Add Equipment
                    </button>
                </div>

                <div>
                    <label for="materials_required" class="block text-sm font-medium text-gray-700 mb-1">Materials Required</label>
                    <div id="materialsContainer" class="space-y-2">
                        @if(isset($jsa) && $jsa->materials_required && count($jsa->materials_required) > 0)
                            @foreach($jsa->materials_required as $material)
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="materials_required[]" value="{{ $material }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                    <button type="button" onclick="this.closest('.flex').remove()" class="text-red-600 hover:text-red-700 px-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" onclick="addMaterial()" class="mt-2 text-sm text-yellow-600 hover:text-yellow-700">
                        <i class="fas fa-plus mr-1"></i>Add Material
                    </button>
                </div>

                <div class="md:col-span-2">
                    <label for="ppe_required" class="block text-sm font-medium text-gray-700 mb-1">PPE Required</label>
                    <div id="ppeContainer" class="space-y-2">
                        @if(isset($jsa) && $jsa->ppe_required && count($jsa->ppe_required) > 0)
                            @foreach($jsa->ppe_required as $ppe)
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="ppe_required[]" value="{{ $ppe }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                                    <button type="button" onclick="this.closest('.flex').remove()" class="text-red-600 hover:text-red-700 px-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" onclick="addPPE()" class="mt-2 text-sm text-yellow-600 hover:text-yellow-700">
                        <i class="fas fa-plus mr-1"></i>Add PPE
                    </button>
                </div>
            </div>
        </div>

        <!-- Environmental Conditions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Environmental Conditions</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="weather_conditions" class="block text-sm font-medium text-gray-700 mb-1">Weather Conditions</label>
                    <textarea id="weather_conditions" name="weather_conditions" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="e.g., Clear, 25°C, Light wind">{{ old('weather_conditions', $jsa->weather_conditions ?? '') }}</textarea>
                </div>

                <div>
                    <label for="site_conditions" class="block text-sm font-medium text-gray-700 mb-1">Site Conditions</label>
                    <textarea id="site_conditions" name="site_conditions" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="e.g., Dry, Well-lit, Adequate ventilation">{{ old('site_conditions', $jsa->site_conditions ?? '') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="special_considerations" class="block text-sm font-medium text-gray-700 mb-1">Special Considerations</label>
                    <textarea id="special_considerations" name="special_considerations" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="Any special considerations or precautions">{{ old('special_considerations', $jsa->special_considerations ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Emergency Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Emergency Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="emergency_contacts" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contacts</label>
                    <textarea id="emergency_contacts" name="emergency_contacts" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="Emergency contact numbers and names">{{ old('emergency_contacts', $jsa->emergency_contacts ?? '') }}</textarea>
                </div>

                <div>
                    <label for="first_aid_location" class="block text-sm font-medium text-gray-700 mb-1">First Aid Location</label>
                    <input type="text" id="first_aid_location" name="first_aid_location" value="{{ old('first_aid_location') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Location of first aid kit/station" value="{{ old('first_aid_location', $jsa->first_aid_location ?? '') }}">
                </div>

                <div>
                    <label for="emergency_procedures" class="block text-sm font-medium text-gray-700 mb-1">Emergency Procedures</label>
                    <textarea id="emergency_procedures" name="emergency_procedures" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="Emergency response procedures">{{ old('emergency_procedures', $jsa->emergency_procedures ?? '') }}</textarea>
                </div>

                <div>
                    <label for="evacuation_route" class="block text-sm font-medium text-gray-700 mb-1">Evacuation Route</label>
                    <input type="text" id="evacuation_route" name="evacuation_route" value="{{ old('evacuation_route') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           placeholder="Primary evacuation route" value="{{ old('evacuation_route', $jsa->evacuation_route ?? '') }}">
                </div>
            </div>
        </div>

        <!-- Risk Assessment -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Overall Risk Assessment</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="overall_risk_level" class="block text-sm font-medium text-gray-700 mb-1">Overall Risk Level *</label>
                    <select id="overall_risk_level" name="overall_risk_level" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        <option value="">Select Risk Level</option>
                        <option value="low" {{ old('overall_risk_level', $jsa->overall_risk_level ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('overall_risk_level', $jsa->overall_risk_level ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('overall_risk_level', $jsa->overall_risk_level ?? '') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('overall_risk_level', $jsa->overall_risk_level ?? '') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('overall_risk_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="risk_summary" class="block text-sm font-medium text-gray-700 mb-1">Risk Summary</label>
                    <textarea id="risk_summary" name="risk_summary" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              placeholder="Summary of overall risks and key control measures">{{ old('risk_summary', $jsa->risk_summary ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                    <option value="draft" {{ old('status', $jsa->status ?? 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending_approval" {{ old('status', $jsa->status ?? '') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ old('status', $jsa->status ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="in_progress" {{ old('status', $jsa->status ?? '') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status', $jsa->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', $jsa->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('risk-assessment.jsas.index') }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($jsa) ? 'Update JSA' : 'Create JSA' }}
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let jobStepCounter = 0;
let teamMemberCounter = 0;
let equipmentCounter = 0;
let materialCounter = 0;
let ppeCounter = 0;

function addJobStep() {
    jobStepCounter++;
    const container = document.getElementById('jobStepsContainer');
    const stepDiv = document.createElement('div');
    stepDiv.className = 'border border-gray-300 rounded-lg p-4';
    stepDiv.innerHTML = `
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-medium text-gray-900">Step ${jobStepCounter}</h3>
            <button type="button" onclick="this.closest('.border').remove()" class="text-red-600 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Step Number</label>
                <input type="number" name="job_steps[${jobStepCounter}][step_number]" value="${jobStepCounter}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="job_steps[${jobStepCounter}][description]" rows="2" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                          placeholder="Describe this step"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hazards</label>
                <textarea name="job_steps[${jobStepCounter}][hazards][]" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                          placeholder="List potential hazards"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Controls</label>
                <textarea name="job_steps[${jobStepCounter}][controls][]" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                          placeholder="List control measures"></textarea>
            </div>
        </div>
    `;
    container.appendChild(stepDiv);
}

function addTeamMember() {
    teamMemberCounter++;
    const container = document.getElementById('teamMembersContainer');
    const memberDiv = document.createElement('div');
    memberDiv.className = 'flex items-center space-x-2';
    memberDiv.innerHTML = `
        <select name="team_members[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
            <option value="">Select Team Member</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        <button type="button" onclick="this.closest('.flex').remove()" class="text-red-600 hover:text-red-700 px-2">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(memberDiv);
}

function addEquipment() {
    equipmentCounter++;
    const container = document.getElementById('equipmentContainer');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'flex items-center space-x-2';
    itemDiv.innerHTML = `
        <input type="text" name="equipment_required[]" placeholder="Equipment name"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
        <button type="button" onclick="this.closest('.flex').remove()" class="text-red-600 hover:text-red-700 px-2">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(itemDiv);
}

function addMaterial() {
    materialCounter++;
    const container = document.getElementById('materialsContainer');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'flex items-center space-x-2';
    itemDiv.innerHTML = `
        <input type="text" name="materials_required[]" placeholder="Material name"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
        <button type="button" onclick="this.closest('.flex').remove()" class="text-red-600 hover:text-red-700 px-2">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(itemDiv);
}

function addPPE() {
    ppeCounter++;
    const container = document.getElementById('ppeContainer');
    const itemDiv = document.createElement('div');
    itemDiv.className = 'flex items-center space-x-2';
    itemDiv.innerHTML = `
        <input type="text" name="ppe_required[]" placeholder="PPE item (e.g., Safety glasses, Hard hat)"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
        <button type="button" onclick="this.closest('.flex').remove()" class="text-red-600 hover:text-red-700 px-2">
            <i class="fas fa-trash"></i>
        </button>
    `;
    container.appendChild(itemDiv);
}

// Initialize with one job step if no existing steps
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('jobStepsContainer');
    if (container.children.length === 0) {
        addJobStep();
    }
    
    // Update counters based on existing items
    jobStepCounter = container.children.length;
    const teamContainer = document.getElementById('teamMembersContainer');
    teamMemberCounter = teamContainer ? teamContainer.children.length : 0;
    const equipmentContainer = document.getElementById('equipmentContainer');
    equipmentCounter = equipmentContainer ? equipmentContainer.children.length : 0;
    const materialsContainer = document.getElementById('materialsContainer');
    materialCounter = materialsContainer ? materialsContainer.children.length : 0;
    const ppeContainer = document.getElementById('ppeContainer');
    ppeCounter = ppeContainer ? ppeContainer.children.length : 0;
});
</script>
@endsection

