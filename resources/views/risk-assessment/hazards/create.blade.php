@extends('layouts.app')

@section('title', 'Identify Hazard')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.hazards.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Hazards
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Identify Hazard (HAZID)</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('risk-assessment.hazards.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Hazard Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Hazard Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                           placeholder="Brief description of the hazard">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                              placeholder="Detailed description of the hazard">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="hazard_category" class="block text-sm font-medium text-gray-700 mb-1">Hazard Category *</label>
                    <select id="hazard_category" name="hazard_category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Select Category</option>
                        <option value="physical" {{ old('hazard_category') == 'physical' ? 'selected' : '' }}>Physical</option>
                        <option value="chemical" {{ old('hazard_category') == 'chemical' ? 'selected' : '' }}>Chemical</option>
                        <option value="biological" {{ old('hazard_category') == 'biological' ? 'selected' : '' }}>Biological</option>
                        <option value="ergonomic" {{ old('hazard_category') == 'ergonomic' ? 'selected' : '' }}>Ergonomic</option>
                        <option value="psychosocial" {{ old('hazard_category') == 'psychosocial' ? 'selected' : '' }}>Psychosocial</option>
                        <option value="mechanical" {{ old('hazard_category') == 'mechanical' ? 'selected' : '' }}>Mechanical</option>
                        <option value="electrical" {{ old('hazard_category') == 'electrical' ? 'selected' : '' }}>Electrical</option>
                        <option value="fire" {{ old('hazard_category') == 'fire' ? 'selected' : '' }}>Fire</option>
                        <option value="environmental" {{ old('hazard_category') == 'environmental' ? 'selected' : '' }}>Environmental</option>
                        <option value="other" {{ old('hazard_category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('hazard_category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="identification_method" class="block text-sm font-medium text-gray-700 mb-1">Identification Method</label>
                    <select id="identification_method" name="identification_method"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Select Method</option>
                        <option value="hazid_checklist" {{ old('identification_method') == 'hazid_checklist' ? 'selected' : '' }}>HAZID Checklist</option>
                        <option value="what_if_analysis" {{ old('identification_method') == 'what_if_analysis' ? 'selected' : '' }}>What-If Analysis</option>
                        <option value="hazop" {{ old('identification_method') == 'hazop' ? 'selected' : '' }}>HAZOP</option>
                        <option value="job_observation" {{ old('identification_method') == 'job_observation' ? 'selected' : '' }}>Job Observation</option>
                        <option value="incident_analysis" {{ old('identification_method') == 'incident_analysis' ? 'selected' : '' }}>Incident Analysis</option>
                        <option value="audit_finding" {{ old('identification_method') == 'audit_finding' ? 'selected' : '' }}>Audit Finding</option>
                        <option value="employee_report" {{ old('identification_method') == 'employee_report' ? 'selected' : '' }}>Employee Report</option>
                        <option value="other" {{ old('identification_method') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                           placeholder="Where is this hazard located?">
                </div>

                <div>
                    <label for="process_or_activity" class="block text-sm font-medium text-gray-700 mb-1">Process/Activity</label>
                    <input type="text" id="process_or_activity" name="process_or_activity" value="{{ old('process_or_activity') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                           placeholder="Related process or activity">
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="hazard_source" class="block text-sm font-medium text-gray-700 mb-1">Hazard Source</label>
                    <select id="hazard_source" name="hazard_source"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Select Source</option>
                        <option value="routine_activity" {{ old('hazard_source') == 'routine_activity' ? 'selected' : '' }}>Routine Activity</option>
                        <option value="non_routine_activity" {{ old('hazard_source') == 'non_routine_activity' ? 'selected' : '' }}>Non-Routine Activity</option>
                        <option value="maintenance" {{ old('hazard_source') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="change_introduction" {{ old('hazard_source') == 'change_introduction' ? 'selected' : '' }}>Change Introduction</option>
                        <option value="emergency_situation" {{ old('hazard_source') == 'emergency_situation' ? 'selected' : '' }}>Emergency Situation</option>
                        <option value="contractor_work" {{ old('hazard_source') == 'contractor_work' ? 'selected' : '' }}>Contractor Work</option>
                        <option value="other" {{ old('hazard_source') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="exposure_description" class="block text-sm font-medium text-gray-700 mb-1">Exposure Description</label>
                    <textarea id="exposure_description" name="exposure_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                              placeholder="Who is at risk and how are they exposed?">{{ old('exposure_description') }}</textarea>
                </div>

                @if($incidents && $incidents->count() > 0)
                <div class="md:col-span-2">
                    <label for="related_incident_id" class="block text-sm font-medium text-gray-700 mb-1">Related Incident (Closed-Loop)</label>
                    <select id="related_incident_id" name="related_incident_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">No Incident Link</option>
                        @foreach($incidents as $incident)
                            <option value="{{ $incident->id }}" {{ old('related_incident_id') == $incident->id ? 'selected' : '' }}>
                                {{ $incident->reference_number }} - {{ $incident->title }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Link this hazard to an incident for closed-loop integration</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('risk-assessment.hazards.index') }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Identify Hazard
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

