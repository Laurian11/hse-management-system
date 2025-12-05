@extends('layouts.app')

@section('title', 'Create Control Measure')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.control-measures.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Control Measure</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('risk-assessment.control-measures.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Parent Relationship -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Link to Source</h2>
            <p class="text-sm text-gray-500 mb-4">Control measure must be linked to at least one source</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($riskAssessment)
                <div class="p-3 bg-blue-50 border border-blue-200">
                    <label class="text-sm font-medium text-gray-700">Risk Assessment</label>
                    <p class="text-black mt-1">{{ $riskAssessment->reference_number }} - {{ $riskAssessment->title }}</p>
                    <input type="hidden" name="risk_assessment_id" value="{{ $riskAssessment->id }}">
                </div>
                @else
                <div>
                    <label for="risk_assessment_id" class="block text-sm font-medium text-black mb-1">Risk Assessment</label>
                    <select id="risk_assessment_id" name="risk_assessment_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">None</option>
                        @foreach(\App\Models\RiskAssessment::forCompany(Auth::user()->company_id)->active()->get() as $ra)
                            <option value="{{ $ra->id }}" {{ old('risk_assessment_id') == $ra->id ? 'selected' : '' }}>
                                {{ $ra->reference_number }} - {{ $ra->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($hazard)
                <div class="p-3 bg-blue-50 border border-blue-200">
                    <label class="text-sm font-medium text-gray-700">Hazard</label>
                    <p class="text-black mt-1">{{ $hazard->name }}</p>
                    <input type="hidden" name="hazard_id" value="{{ $hazard->id }}">
                </div>
                @else
                <div>
                    <label for="hazard_id" class="block text-sm font-medium text-black mb-1">Hazard</label>
                    <select id="hazard_id" name="hazard_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">None</option>
                        @foreach(\App\Models\Hazard::forCompany(Auth::user()->company_id)->active()->get() as $h)
                            <option value="{{ $h->id }}" {{ old('hazard_id') == $h->id ? 'selected' : '' }}>
                                {{ $h->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($jsa)
                <div class="p-3 bg-blue-50 border border-blue-200">
                    <label class="text-sm font-medium text-gray-700">JSA</label>
                    <p class="text-black mt-1">{{ $jsa->reference_number }} - {{ $jsa->job_title }}</p>
                    <input type="hidden" name="jsa_id" value="{{ $jsa->id }}">
                </div>
                @else
                <div>
                    <label for="jsa_id" class="block text-sm font-medium text-black mb-1">JSA</label>
                    <select id="jsa_id" name="jsa_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">None</option>
                        @foreach(\App\Models\JSA::forCompany(Auth::user()->company_id)->active()->get() as $j)
                            <option value="{{ $j->id }}" {{ old('jsa_id') == $j->id ? 'selected' : '' }}>
                                {{ $j->reference_number }} - {{ $j->job_title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($incident)
                <div class="p-3 bg-blue-50 border border-blue-200">
                    <label class="text-sm font-medium text-gray-700">Incident</label>
                    <p class="text-black mt-1">{{ $incident->reference_number }} - {{ $incident->title }}</p>
                    <input type="hidden" name="incident_id" value="{{ $incident->id }}">
                </div>
                @else
                <div>
                    <label for="incident_id" class="block text-sm font-medium text-black mb-1">Incident</label>
                    <select id="incident_id" name="incident_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">None</option>
                        @foreach(\App\Models\Incident::where('company_id', Auth::user()->company_id)->latest()->get() as $i)
                            <option value="{{ $i->id }}" {{ old('incident_id') == $i->id ? 'selected' : '' }}>
                                {{ $i->reference_number }} - {{ $i->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </div>

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="control_type" class="block text-sm font-medium text-black mb-1">Control Type *</label>
                        <select id="control_type" name="control_type" required
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select Type</option>
                            <option value="elimination" {{ old('control_type') == 'elimination' ? 'selected' : '' }}>Elimination</option>
                            <option value="substitution" {{ old('control_type') == 'substitution' ? 'selected' : '' }}>Substitution</option>
                            <option value="engineering" {{ old('control_type') == 'engineering' ? 'selected' : '' }}>Engineering Controls</option>
                            <option value="administrative" {{ old('control_type') == 'administrative' ? 'selected' : '' }}>Administrative Controls</option>
                            <option value="ppe" {{ old('control_type') == 'ppe' ? 'selected' : '' }}>Personal Protective Equipment</option>
                            <option value="combination" {{ old('control_type') == 'combination' ? 'selected' : '' }}>Combination</option>
                        </select>
                        @error('control_type')
                            <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-black mb-1">Priority *</label>
                        <select id="priority" name="priority" required
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="effectiveness_level" class="block text-sm font-medium text-black mb-1">Effectiveness Level</label>
                        <select id="effectiveness_level" name="effectiveness_level"
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select Level</option>
                            <option value="low" {{ old('effectiveness_level') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('effectiveness_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('effectiveness_level') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="very_high" {{ old('effectiveness_level') == 'very_high' ? 'selected' : '' }}>Very High</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-black mb-1">Status</label>
                        <select id="status" name="status"
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="planned" {{ old('status', 'planned') == 'planned' ? 'selected' : '' }}>Planned</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="implemented" {{ old('status') == 'implemented' ? 'selected' : '' }}>Implemented</option>
                            <option value="verified" {{ old('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment & Timeline -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Assignment & Timeline</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-black mb-1">Assigned To</label>
                    <select id="assigned_to" name="assigned_to"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="responsible_party" class="block text-sm font-medium text-black mb-1">Responsible Party</label>
                    <select id="responsible_party" name="responsible_party"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('responsible_party') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="target_completion_date" class="block text-sm font-medium text-black mb-1">Target Completion Date</label>
                    <input type="date" id="target_completion_date" name="target_completion_date" 
                           value="{{ old('target_completion_date') }}"
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('target_completion_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-black mb-1">Estimated Cost</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" step="0.01" min="0"
                           value="{{ old('estimated_cost') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>
            </div>

            <div class="mt-4">
                <label for="resources_required" class="block text-sm font-medium text-black mb-1">Resources Required</label>
                <textarea id="resources_required" name="resources_required" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('resources_required') }}</textarea>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('risk-assessment.control-measures.index') }}" 
               class="px-6 py-2 border border-gray-300 text-black hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                Create Control Measure
            </button>
        </div>
    </form>
</div>
@endsection

