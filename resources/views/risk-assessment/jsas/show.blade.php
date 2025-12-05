@extends('layouts.app')

@section('title', 'JSA: ' . $jsa->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('risk-assessment.jsas.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $jsa->job_title }}</h1>
                    <p class="text-sm text-gray-500">{{ $jsa->reference_number }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @if($jsa->status !== 'approved')
                    <a href="{{ route('risk-assessment.jsas.edit', $jsa) }}" class="px-4 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif
                @if($jsa->status === 'pending_approval' && Auth::user()->id === $jsa->supervisor_id)
                    <form action="{{ route('risk-assessment.jsas.approve', $jsa) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-[#00CC66] text-white hover:bg-[#00A352]">
                            <i class="fas fa-check mr-2"></i>Approve
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Status Badge -->
    <div class="mb-6">
        <span class="px-3 py-1 text-sm font-medium rounded {{ $jsa->getRiskLevelColor() }}">
            {{ strtoupper($jsa->overall_risk_level) }} RISK
        </span>
        <span class="ml-2 px-3 py-1 text-sm font-medium rounded 
            @if($jsa->status === 'approved') bg-[#00CC66] text-white
            @elseif($jsa->status === 'pending_approval') bg-[#FFCC00] text-black
            @elseif($jsa->status === 'in_progress') bg-[#0066CC] text-white
            @else bg-gray-300 text-black
            @endif">
            {{ ucfirst(str_replace('_', ' ', $jsa->status)) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Job Information -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Job Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Job Title</label>
                        <p class="text-black mt-1">{{ $jsa->job_title }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Location</label>
                        <p class="text-black mt-1">{{ $jsa->location ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Work Area</label>
                        <p class="text-black mt-1">{{ $jsa->work_area ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Job Date</label>
                        <p class="text-black mt-1">{{ $jsa->job_date ? $jsa->job_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    @if($jsa->start_time && $jsa->end_time)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Time</label>
                        <p class="text-black mt-1">
                            {{ $jsa->start_time->format('H:i') }} - {{ $jsa->end_time->format('H:i') }}
                        </p>
                    </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-500">Department</label>
                        <p class="text-black mt-1">{{ $jsa->department->name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">Job Description</label>
                    <p class="text-black mt-1">{{ $jsa->job_description }}</p>
                </div>
            </div>

            <!-- Job Steps -->
            @if($jsa->job_steps && count($jsa->job_steps) > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Job Steps</h2>
                <div class="space-y-4">
                    @foreach($jsa->job_steps as $step)
                    <div class="border border-gray-300 p-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-medium text-black">Step {{ $step['step_number'] ?? '' }}</h3>
                        </div>
                        <p class="text-gray-700 mb-3">{{ $step['description'] ?? '' }}</p>
                        @if(isset($step['hazards']) && count($step['hazards']) > 0)
                        <div class="mb-2">
                            <label class="text-sm font-medium text-gray-500">Hazards:</label>
                            <ul class="list-disc list-inside text-gray-700 ml-2">
                                @foreach($step['hazards'] as $hazard)
                                <li>{{ $hazard }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if(isset($step['controls']) && count($step['controls']) > 0)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Controls:</label>
                            <ul class="list-disc list-inside text-gray-700 ml-2">
                                @foreach($step['controls'] as $control)
                                <li>{{ $control }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Risk Summary -->
            @if($jsa->risk_summary)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Risk Summary</h2>
                <p class="text-gray-700">{{ $jsa->risk_summary }}</p>
            </div>
            @endif

            <!-- Emergency Information -->
            @if($jsa->emergency_contacts || $jsa->emergency_procedures || $jsa->first_aid_location || $jsa->evacuation_route)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Emergency Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($jsa->emergency_contacts)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Emergency Contacts</label>
                        <p class="text-black mt-1">{{ $jsa->emergency_contacts }}</p>
                    </div>
                    @endif
                    @if($jsa->first_aid_location)
                    <div>
                        <label class="text-sm font-medium text-gray-500">First Aid Location</label>
                        <p class="text-black mt-1">{{ $jsa->first_aid_location }}</p>
                    </div>
                    @endif
                    @if($jsa->evacuation_route)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Evacuation Route</label>
                        <p class="text-black mt-1">{{ $jsa->evacuation_route }}</p>
                    </div>
                    @endif
                    @if($jsa->emergency_procedures)
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium text-gray-500">Emergency Procedures</label>
                        <p class="text-black mt-1">{{ $jsa->emergency_procedures }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Details Card -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Created By</label>
                        <p class="text-black mt-1">{{ $jsa->creator->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Supervisor</label>
                        <p class="text-black mt-1">{{ $jsa->supervisor->name ?? 'N/A' }}</p>
                    </div>
                    @if($jsa->approved_by)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Approved By</label>
                        <p class="text-black mt-1">{{ $jsa->approvedBy->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $jsa->approved_at ? $jsa->approved_at->format('M d, Y H:i') : '' }}</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-500">Created At</label>
                        <p class="text-black mt-1">{{ $jsa->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            @if($jsa->team_members && count($jsa->team_members) > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Team Members</h3>
                <ul class="space-y-2">
                    @foreach($jsa->team_members as $member)
                    <li class="text-black">{{ $member }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Required Items -->
            @if($jsa->ppe_required && count($jsa->ppe_required) > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Required PPE</h3>
                <ul class="space-y-2">
                    @foreach($jsa->ppe_required as $ppe)
                    <li class="text-black">{{ $ppe }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Related Risk Assessment -->
            @if($jsa->relatedRiskAssessment)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Related Risk Assessment</h3>
                <a href="{{ route('risk-assessment.risk-assessments.show', $jsa->relatedRiskAssessment) }}" 
                   class="text-[#0066CC] hover:underline">
                    {{ $jsa->relatedRiskAssessment->reference_number }}
                </a>
            </div>
            @endif

            <!-- Control Measures -->
            @if($jsa->controlMeasures && $jsa->controlMeasures->count() > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Control Measures</h3>
                <ul class="space-y-2">
                    @foreach($jsa->controlMeasures as $cm)
                    <li>
                        <a href="{{ route('risk-assessment.control-measures.show', $cm) }}" 
                           class="text-[#0066CC] hover:underline">
                            {{ $cm->title }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

