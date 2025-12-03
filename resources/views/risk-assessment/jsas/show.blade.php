@extends('layouts.app')

@section('title', 'JSA Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $jsa->job_title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $jsa->reference_number }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('risk-assessment.jsas.edit', $jsa) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="{{ route('risk-assessment.jsas.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status & Risk Level -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Job Information</h2>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ ucfirst($jsa->status) }}</span>
                            <span class="px-3 py-1 text-xs rounded-full {{ $jsa->getRiskLevelColor() }}">
                                {{ strtoupper($jsa->overall_risk_level) }} RISK
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Job Title</p>
                            <p class="font-medium text-gray-900">{{ $jsa->job_title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Location</p>
                            <p class="font-medium text-gray-900">{{ $jsa->location ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Work Area</p>
                            <p class="font-medium text-gray-900">{{ $jsa->work_area ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Job Date</p>
                            <p class="font-medium text-gray-900">{{ $jsa->job_date ? $jsa->job_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        @if($jsa->start_time && $jsa->end_time)
                        <div>
                            <p class="text-sm text-gray-600">Time</p>
                            <p class="font-medium text-gray-900">
                                {{ $jsa->start_time->format('H:i') }} - {{ $jsa->end_time->format('H:i') }}
                            </p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Department</p>
                            <p class="font-medium text-gray-900">{{ $jsa->department->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Supervisor</p>
                            <p class="font-medium text-gray-900">{{ $jsa->supervisor->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Created By</p>
                            <p class="font-medium text-gray-900">{{ $jsa->creator->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Description</h2>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $jsa->job_description }}</p>
                </div>

                <!-- Job Steps -->
                @if($jsa->job_steps && count($jsa->job_steps) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Steps</h2>
                    <div class="space-y-4">
                        @foreach($jsa->job_steps as $step)
                        <div class="border-l-4 border-yellow-500 bg-yellow-50 p-4 rounded">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="bg-yellow-600 text-white text-xs font-bold px-2 py-1 rounded mr-2">
                                            Step {{ $step['step_number'] ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <p class="font-medium text-gray-900 mb-2">{{ $step['description'] ?? 'N/A' }}</p>
                                    @if(isset($step['hazards']) && is_array($step['hazards']) && count($step['hazards']) > 0)
                                    <div class="mt-2">
                                        <p class="text-xs font-semibold text-red-700 mb-1">Hazards:</p>
                                        <ul class="list-disc list-inside text-sm text-gray-700">
                                            @foreach($step['hazards'] as $hazard)
                                                <li>{{ $hazard }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if(isset($step['controls']) && is_array($step['controls']) && count($step['controls']) > 0)
                                    <div class="mt-2">
                                        <p class="text-xs font-semibold text-green-700 mb-1">Controls:</p>
                                        <ul class="list-disc list-inside text-sm text-gray-700">
                                            @foreach($step['controls'] as $control)
                                                <li>{{ $control }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Team Members -->
                @if($jsa->team_members && count($jsa->team_members) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Team Members</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($jsa->team_members as $memberId)
                            @php
                                $member = \App\Models\User::find($memberId);
                            @endphp
                            @if($member)
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="font-medium text-gray-900">{{ $member->name }}</p>
                                <p class="text-xs text-gray-600">{{ $member->job_title ?? 'Employee' }}</p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Requirements & Resources -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Requirements & Resources</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($jsa->required_qualifications)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Required Qualifications</p>
                            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $jsa->required_qualifications }}</p>
                        </div>
                        @endif
                        @if($jsa->required_training)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Required Training</p>
                            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $jsa->required_training }}</p>
                        </div>
                        @endif
                        @if($jsa->equipment_required && count($jsa->equipment_required) > 0)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Equipment Required</p>
                            <ul class="list-disc list-inside text-sm text-gray-600">
                                @foreach($jsa->equipment_required as $equipment)
                                    <li>{{ $equipment }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if($jsa->materials_required && count($jsa->materials_required) > 0)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-2">Materials Required</p>
                            <ul class="list-disc list-inside text-sm text-gray-600">
                                @foreach($jsa->materials_required as $material)
                                    <li>{{ $material }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        @if($jsa->ppe_required && count($jsa->ppe_required) > 0)
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-700 mb-2">PPE Required</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($jsa->ppe_required as $ppe)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">{{ $ppe }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Environmental Conditions -->
                @if($jsa->weather_conditions || $jsa->site_conditions || $jsa->special_considerations)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Environmental Conditions</h2>
                    <div class="space-y-3">
                        @if($jsa->weather_conditions)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Weather Conditions</p>
                            <p class="text-sm text-gray-600">{{ $jsa->weather_conditions }}</p>
                        </div>
                        @endif
                        @if($jsa->site_conditions)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Site Conditions</p>
                            <p class="text-sm text-gray-600">{{ $jsa->site_conditions }}</p>
                        </div>
                        @endif
                        @if($jsa->special_considerations)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Special Considerations</p>
                            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $jsa->special_considerations }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Emergency Information -->
                @if($jsa->emergency_contacts || $jsa->emergency_procedures || $jsa->first_aid_location || $jsa->evacuation_route)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Emergency Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($jsa->emergency_contacts)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Emergency Contacts</p>
                            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $jsa->emergency_contacts }}</p>
                        </div>
                        @endif
                        @if($jsa->first_aid_location)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">First Aid Location</p>
                            <p class="text-sm text-gray-600">{{ $jsa->first_aid_location }}</p>
                        </div>
                        @endif
                        @if($jsa->emergency_procedures)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Emergency Procedures</p>
                            <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $jsa->emergency_procedures }}</p>
                        </div>
                        @endif
                        @if($jsa->evacuation_route)
                        <div>
                            <p class="text-sm font-medium text-gray-700 mb-1">Evacuation Route</p>
                            <p class="text-sm text-gray-600">{{ $jsa->evacuation_route }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Risk Summary -->
                @if($jsa->risk_summary)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Risk Summary</h2>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $jsa->risk_summary }}</p>
                </div>
                @endif

                <!-- Control Measures -->
                @if($jsa->controlMeasures->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Control Measures</h2>
                        <a href="{{ route('risk-assessment.control-measures.create', ['jsa_id' => $jsa->id]) }}" class="text-blue-600 hover:text-blue-700 text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Control
                        </a>
                    </div>
                    <div class="space-y-3">
                        @foreach($jsa->controlMeasures as $control)
                            <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <a href="{{ route('risk-assessment.control-measures.show', $control) }}" class="font-semibold text-gray-900 hover:text-green-700">
                                            {{ $control->title }}
                                        </a>
                                        <p class="text-sm text-gray-600 mt-1">{{ $control->getControlTypeLabel() }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-white">{{ ucfirst($control->status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Information</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500">Reference Number</p>
                            <p class="text-sm font-medium text-gray-900">{{ $jsa->reference_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <p class="text-sm font-medium text-gray-900">{{ ucfirst($jsa->status) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Overall Risk Level</p>
                            <span class="inline-block px-2 py-1 text-xs rounded-full {{ $jsa->getRiskLevelColor() }}">
                                {{ strtoupper($jsa->overall_risk_level) }}
                            </span>
                        </div>
                        @if($jsa->approvedBy)
                        <div>
                            <p class="text-xs text-gray-500">Approved By</p>
                            <p class="text-sm font-medium text-gray-900">{{ $jsa->approvedBy->name }}</p>
                            <p class="text-xs text-gray-500">{{ $jsa->approved_at ? $jsa->approved_at->format('M d, Y') : '' }}</p>
                        </div>
                        @endif
                        @if($jsa->relatedRiskAssessment)
                        <div>
                            <p class="text-xs text-gray-500">Related Risk Assessment</p>
                            <a href="{{ route('risk-assessment.risk-assessments.show', $jsa->relatedRiskAssessment) }}" class="text-sm text-blue-600 hover:text-blue-700">
                                {{ $jsa->relatedRiskAssessment->reference_number }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-2">
                        @if($jsa->status !== 'completed' && $jsa->status !== 'approved')
                            @can('approve', $jsa)
                            <form action="{{ route('risk-assessment.jsas.approve', $jsa) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                                    <i class="fas fa-check mr-2"></i>Approve JSA
                                </button>
                            </form>
                            @endcan
                        @endif
                        <a href="{{ route('risk-assessment.control-measures.create', ['jsa_id' => $jsa->id]) }}" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i>Add Control Measure
                        </a>
                        @if($jsa->status !== 'completed')
                        <a href="{{ route('risk-assessment.jsas.edit', $jsa) }}" class="block w-full text-left px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit JSA
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

