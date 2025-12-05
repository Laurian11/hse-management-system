@extends('layouts.app')

@section('title', 'Control Measure: ' . $controlMeasure->title)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.control-measures.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $controlMeasure->title }}</h1>
                    <p class="text-sm text-gray-500">{{ $controlMeasure->reference_number }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.control-measures.edit', $controlMeasure) }}" class="px-4 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Status Badge -->
    <div class="mb-6">
        <span class="px-3 py-1 text-sm font-medium rounded 
            @if($controlMeasure->status === 'verified') bg-[#00CC66] text-white
            @elseif($controlMeasure->status === 'implemented') bg-[#0066CC] text-white
            @elseif($controlMeasure->status === 'in_progress') bg-[#FFCC00] text-black
            @else bg-gray-300 text-black
            @endif">
            {{ ucfirst(str_replace('_', ' ', $controlMeasure->status)) }}
        </span>
        <span class="ml-2 px-3 py-1 text-sm font-medium rounded 
            @if($controlMeasure->priority === 'critical') bg-[#CC0000] text-white
            @elseif($controlMeasure->priority === 'high') bg-[#FF6600] text-white
            @elseif($controlMeasure->priority === 'medium') bg-[#FFCC00] text-black
            @else bg-[#00CC66] text-white
            @endif">
            {{ ucfirst($controlMeasure->priority) }} Priority
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Description</h2>
                <p class="text-gray-700">{{ $controlMeasure->description }}</p>
            </div>

            <!-- Control Details -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Control Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Control Type</label>
                        <p class="text-black mt-1">{{ $controlMeasure->getControlTypeLabel() }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Effectiveness Level</label>
                        <p class="text-black mt-1">{{ ucfirst(str_replace('_', ' ', $controlMeasure->effectiveness_level ?? 'N/A')) }}</p>
                    </div>
                    @if($controlMeasure->target_completion_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Target Completion Date</label>
                        <p class="text-black mt-1">{{ $controlMeasure->target_completion_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($controlMeasure->actual_completion_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Actual Completion Date</label>
                        <p class="text-black mt-1">{{ $controlMeasure->actual_completion_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($controlMeasure->estimated_cost)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Estimated Cost</label>
                        <p class="text-black mt-1">${{ number_format($controlMeasure->estimated_cost, 2) }}</p>
                    </div>
                    @endif
                    @if($controlMeasure->actual_cost)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Actual Cost</label>
                        <p class="text-black mt-1">${{ number_format($controlMeasure->actual_cost, 2) }}</p>
                    </div>
                    @endif
                </div>
                @if($controlMeasure->resources_required)
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">Resources Required</label>
                    <p class="text-black mt-1">{{ $controlMeasure->resources_required }}</p>
                </div>
                @endif
            </div>

            <!-- Verification -->
            @if($controlMeasure->verification_date || $controlMeasure->verified_by)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Verification</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($controlMeasure->verification_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Verification Date</label>
                        <p class="text-black mt-1">{{ $controlMeasure->verification_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($controlMeasure->verifiedBy)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Verified By</label>
                        <p class="text-black mt-1">{{ $controlMeasure->verifiedBy->name }}</p>
                    </div>
                    @endif
                    @if($controlMeasure->verification_method)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Verification Method</label>
                        <p class="text-black mt-1">{{ $controlMeasure->verification_method }}</p>
                    </div>
                    @endif
                    @if($controlMeasure->is_effective !== null)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Effectiveness</label>
                        <p class="text-black mt-1">
                            <span class="px-2 py-1 rounded {{ $controlMeasure->is_effective ? 'bg-[#00CC66] text-white' : 'bg-[#CC0000] text-white' }}">
                                {{ $controlMeasure->is_effective ? 'Effective' : 'Not Effective' }}
                            </span>
                        </p>
                    </div>
                    @endif
                </div>
                @if($controlMeasure->verification_results)
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">Verification Results</label>
                    <p class="text-black mt-1">{{ $controlMeasure->verification_results }}</p>
                </div>
                @endif
                @if($controlMeasure->effectiveness_notes)
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">Effectiveness Notes</label>
                    <p class="text-black mt-1">{{ $controlMeasure->effectiveness_notes }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Maintenance -->
            @if($controlMeasure->maintenance_frequency || $controlMeasure->last_maintenance_date)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Maintenance</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($controlMeasure->maintenance_frequency)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Maintenance Frequency</label>
                        <p class="text-black mt-1">{{ ucfirst(str_replace('_', ' ', $controlMeasure->maintenance_frequency)) }}</p>
                    </div>
                    @endif
                    @if($controlMeasure->last_maintenance_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Last Maintenance Date</label>
                        <p class="text-black mt-1">{{ $controlMeasure->last_maintenance_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($controlMeasure->next_maintenance_date)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Next Maintenance Date</label>
                        <p class="text-black mt-1">{{ $controlMeasure->next_maintenance_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>
                @if($controlMeasure->maintenance_requirements)
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">Maintenance Requirements</label>
                    <p class="text-black mt-1">{{ $controlMeasure->maintenance_requirements }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Assignment -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Assignment</h3>
                <div class="space-y-3">
                    @if($controlMeasure->assignedTo)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Assigned To</label>
                        <p class="text-black mt-1">{{ $controlMeasure->assignedTo->name }}</p>
                    </div>
                    @endif
                    @if($controlMeasure->responsibleParty)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Responsible Party</label>
                        <p class="text-black mt-1">{{ $controlMeasure->responsibleParty->name }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-500">Created At</label>
                        <p class="text-black mt-1">{{ $controlMeasure->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Source Links -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Source</h3>
                <div class="space-y-2">
                    @if($controlMeasure->riskAssessment)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Risk Assessment</label>
                        <a href="{{ route('risk-assessment.risk-assessments.show', $controlMeasure->riskAssessment) }}" 
                           class="text-[#0066CC] hover:underline block mt-1">
                            {{ $controlMeasure->riskAssessment->reference_number }}
                        </a>
                    </div>
                    @endif
                    @if($controlMeasure->hazard)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Hazard</label>
                        <a href="{{ route('risk-assessment.hazards.show', $controlMeasure->hazard) }}" 
                           class="text-[#0066CC] hover:underline block mt-1">
                            {{ $controlMeasure->hazard->name }}
                        </a>
                    </div>
                    @endif
                    @if($controlMeasure->jsa)
                    <div>
                        <label class="text-sm font-medium text-gray-500">JSA</label>
                        <a href="{{ route('risk-assessment.jsas.show', $controlMeasure->jsa) }}" 
                           class="text-[#0066CC] hover:underline block mt-1">
                            {{ $controlMeasure->jsa->reference_number }}
                        </a>
                    </div>
                    @endif
                    @if($controlMeasure->incident)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Incident</label>
                        <a href="{{ route('incidents.show', $controlMeasure->incident) }}" 
                           class="text-[#0066CC] hover:underline block mt-1">
                            {{ $controlMeasure->incident->reference_number }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Related Items -->
            @if($controlMeasure->relatedCAPA || $controlMeasure->relatedTrainingNeed || $controlMeasure->relatedTrainingPlan)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Related Items</h3>
                <div class="space-y-2">
                    @if($controlMeasure->relatedCAPA)
                    <div>
                        <label class="text-sm font-medium text-gray-500">CAPA</label>
                        <a href="{{ route('capas.show', $controlMeasure->relatedCAPA) }}" 
                           class="text-[#0066CC] hover:underline block mt-1">
                            {{ $controlMeasure->relatedCAPA->reference_number }}
                        </a>
                    </div>
                    @endif
                    @if($controlMeasure->relatedTrainingNeed)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Training Need</label>
                        <a href="{{ route('training.training-needs.show', $controlMeasure->relatedTrainingNeed) }}" 
                           class="text-[#0066CC] hover:underline block mt-1">
                            {{ $controlMeasure->relatedTrainingNeed->title }}
                        </a>
                    </div>
                    @endif
                    @if($controlMeasure->relatedTrainingPlan)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Training Plan</label>
                        <a href="{{ route('training.training-plans.show', $controlMeasure->relatedTrainingPlan) }}" 
                           class="text-[#0066CC] hover:underline block mt-1">
                            {{ $controlMeasure->relatedTrainingPlan->title }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

