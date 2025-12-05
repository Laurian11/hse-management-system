@extends('layouts.app')

@section('title', 'Edit Hazard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.hazards.show', $hazard) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Hazard</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('risk-assessment.hazards.update', $hazard) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Hazard Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-black mb-1">Hazard Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $hazard->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description', $hazard->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="hazard_category" class="block text-sm font-medium text-black mb-1">Hazard Category *</label>
                        <select id="hazard_category" name="hazard_category" required
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select Category</option>
                            <option value="physical" {{ old('hazard_category', $hazard->hazard_category) == 'physical' ? 'selected' : '' }}>Physical</option>
                            <option value="chemical" {{ old('hazard_category', $hazard->hazard_category) == 'chemical' ? 'selected' : '' }}>Chemical</option>
                            <option value="biological" {{ old('hazard_category', $hazard->hazard_category) == 'biological' ? 'selected' : '' }}>Biological</option>
                            <option value="ergonomic" {{ old('hazard_category', $hazard->hazard_category) == 'ergonomic' ? 'selected' : '' }}>Ergonomic</option>
                            <option value="psychosocial" {{ old('hazard_category', $hazard->hazard_category) == 'psychosocial' ? 'selected' : '' }}>Psychosocial</option>
                            <option value="mechanical" {{ old('hazard_category', $hazard->hazard_category) == 'mechanical' ? 'selected' : '' }}>Mechanical</option>
                            <option value="electrical" {{ old('hazard_category', $hazard->hazard_category) == 'electrical' ? 'selected' : '' }}>Electrical</option>
                            <option value="fire" {{ old('hazard_category', $hazard->hazard_category) == 'fire' ? 'selected' : '' }}>Fire</option>
                            <option value="environmental" {{ old('hazard_category', $hazard->hazard_category) == 'environmental' ? 'selected' : '' }}>Environmental</option>
                            <option value="other" {{ old('hazard_category', $hazard->hazard_category) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('hazard_category')
                            <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="identified" {{ old('status', $hazard->status) == 'identified' ? 'selected' : '' }}>Identified</option>
                            <option value="assessed" {{ old('status', $hazard->status) == 'assessed' ? 'selected' : '' }}>Assessed</option>
                            <option value="controlled" {{ old('status', $hazard->status) == 'controlled' ? 'selected' : '' }}>Controlled</option>
                            <option value="closed" {{ old('status', $hazard->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="archived" {{ old('status', $hazard->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-black mb-1">Location</label>
                        <input type="text" id="location" name="location" value="{{ old('location', $hazard->location) }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    </div>

                    <div>
                        <label for="process_or_activity" class="block text-sm font-medium text-black mb-1">Process/Activity</label>
                        <input type="text" id="process_or_activity" name="process_or_activity" value="{{ old('process_or_activity', $hazard->process_or_activity) }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    </div>

                    <div>
                        <label for="asset_or_equipment" class="block text-sm font-medium text-black mb-1">Asset/Equipment</label>
                        <input type="text" id="asset_or_equipment" name="asset_or_equipment" value="{{ old('asset_or_equipment', $hazard->asset_or_equipment) }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-medium text-black mb-1">Department</label>
                        <select id="department_id" name="department_id"
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $hazard->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="identification_method" class="block text-sm font-medium text-black mb-1">Identification Method</label>
                    <select id="identification_method" name="identification_method"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Method</option>
                        <option value="hazid_checklist" {{ old('identification_method', $hazard->identification_method) == 'hazid_checklist' ? 'selected' : '' }}>HAZID Checklist</option>
                        <option value="what_if_analysis" {{ old('identification_method', $hazard->identification_method) == 'what_if_analysis' ? 'selected' : '' }}>What-If Analysis</option>
                        <option value="hazop" {{ old('identification_method', $hazard->identification_method) == 'hazop' ? 'selected' : '' }}>HAZOP</option>
                        <option value="job_observation" {{ old('identification_method', $hazard->identification_method) == 'job_observation' ? 'selected' : '' }}>Job Observation</option>
                        <option value="incident_analysis" {{ old('identification_method', $hazard->identification_method) == 'incident_analysis' ? 'selected' : '' }}>Incident Analysis</option>
                        <option value="audit_finding" {{ old('identification_method', $hazard->identification_method) == 'audit_finding' ? 'selected' : '' }}>Audit Finding</option>
                        <option value="employee_report" {{ old('identification_method', $hazard->identification_method) == 'employee_report' ? 'selected' : '' }}>Employee Report</option>
                        <option value="other" {{ old('identification_method', $hazard->identification_method) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="hazard_source" class="block text-sm font-medium text-black mb-1">Hazard Source</label>
                    <select id="hazard_source" name="hazard_source"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Source</option>
                        <option value="routine_activity" {{ old('hazard_source', $hazard->hazard_source) == 'routine_activity' ? 'selected' : '' }}>Routine Activity</option>
                        <option value="non_routine_activity" {{ old('hazard_source', $hazard->hazard_source) == 'non_routine_activity' ? 'selected' : '' }}>Non-Routine Activity</option>
                        <option value="maintenance" {{ old('hazard_source', $hazard->hazard_source) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="change_introduction" {{ old('hazard_source', $hazard->hazard_source) == 'change_introduction' ? 'selected' : '' }}>Change Introduction</option>
                        <option value="emergency_situation" {{ old('hazard_source', $hazard->hazard_source) == 'emergency_situation' ? 'selected' : '' }}>Emergency Situation</option>
                        <option value="contractor_work" {{ old('hazard_source', $hazard->hazard_source) == 'contractor_work' ? 'selected' : '' }}>Contractor Work</option>
                        <option value="other" {{ old('hazard_source', $hazard->hazard_source) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="exposure_description" class="block text-sm font-medium text-black mb-1">Exposure Description</label>
                    <textarea id="exposure_description" name="exposure_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('exposure_description', $hazard->exposure_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('risk-assessment.hazards.show', $hazard) }}" 
               class="px-6 py-2 border border-gray-300 text-black hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                Update Hazard
            </button>
        </div>
    </form>
</div>
@endsection

