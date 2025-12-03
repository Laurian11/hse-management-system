@extends('layouts.app')

@section('title', $incident->reference_number)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('incidents.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Incidents
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $incident->title ?? $incident->reference_number }}</h1>
                        <p class="text-sm text-gray-500">{{ $incident->reference_number }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    @if($incident->status !== 'closed' && $incident->status !== 'resolved')
                        <a href="{{ route('incidents.edit', $incident) }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    @endif
                    @if(!$incident->investigation && $incident->status !== 'closed')
                        <a href="{{ route('incidents.investigations.create', $incident) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Start Investigation
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Status Alert -->
        <div class="mb-6">
            @if($incident->status === 'open' || $incident->status === 'reported')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-600 mr-3 text-xl"></i>
                            <div>
                                <h3 class="text-sm font-medium text-red-800">Open Incident</h3>
                                <p class="text-sm text-red-700">This incident requires attention and investigation.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                            {{ ucfirst($incident->status) }}
                        </span>
                    </div>
                </div>
            @elseif($incident->status === 'investigating')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-search text-yellow-600 mr-3 text-xl"></i>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-800">Under Investigation</h3>
                                <p class="text-sm text-yellow-700">Investigation is in progress.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                            Investigating
                        </span>
                    </div>
                </div>
            @elseif($incident->status === 'closed' || $incident->status === 'resolved')
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                            <div>
                                <h3 class="text-sm font-medium text-green-800">Incident Closed</h3>
                                <p class="text-sm text-green-700">This incident has been resolved and closed.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            {{ ucfirst($incident->status) }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px" id="tabs">
                    <button onclick="switchTab('overview')" id="tab-overview" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-blue-500 text-blue-600">
                        <i class="fas fa-info-circle mr-2"></i>Overview
                    </button>
                    <button onclick="switchTab('investigation')" id="tab-investigation" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-search mr-2"></i>Investigation
                        @if($incident->investigation)
                            <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full">{{ $incident->investigation->status }}</span>
                        @endif
                    </button>
                    <button onclick="switchTab('rca')" id="tab-rca" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-project-diagram mr-2"></i>Root Cause Analysis
                        @if($incident->rootCauseAnalysis)
                            <span class="ml-2 px-2 py-0.5 text-xs bg-purple-100 text-purple-800 rounded-full">Done</span>
                        @endif
                    </button>
                    <button onclick="switchTab('capas')" id="tab-capas" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-tasks mr-2"></i>CAPAs
                        @if($incident->capas->count() > 0)
                            <span class="ml-2 px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">{{ $incident->capas->count() }}</span>
                        @endif
                    </button>
                    <button onclick="switchTab('attachments')" id="tab-attachments" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-paperclip mr-2"></i>Attachments
                        @if($incident->attachments->count() > 0)
                            <span class="ml-2 px-2 py-0.5 text-xs bg-gray-100 text-gray-800 rounded-full">{{ $incident->attachments->count() }}</span>
                        @endif
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="space-y-6">
            <!-- Overview Tab -->
            <div id="content-overview" class="tab-content">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Incident Details -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Incident Details</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Event Type</h3>
                                    @if($incident->event_type)
                                        @php
                                            $eventTypeIcons = [
                                                'injury_illness' => ['icon' => 'fa-user-injured', 'color' => 'red', 'label' => 'Injury/Illness'],
                                                'property_damage' => ['icon' => 'fa-tools', 'color' => 'orange', 'label' => 'Property Damage'],
                                                'near_miss' => ['icon' => 'fa-exclamation-triangle', 'color' => 'yellow', 'label' => 'Near Miss'],
                                                'environmental' => ['icon' => 'fa-leaf', 'color' => 'green', 'label' => 'Environmental'],
                                            ];
                                            $eventType = $eventTypeIcons[$incident->event_type] ?? ['icon' => 'fa-circle', 'color' => 'gray', 'label' => 'Other'];
                                        @endphp
                                        <div class="flex items-center">
                                            <i class="fas {{ $eventType['icon'] }} text-{{ $eventType['color'] }}-600 mr-2"></i>
                                            <span class="text-lg font-medium text-gray-900">{{ $eventType['label'] }}</span>
                                        </div>
                                    @else
                                        <p class="text-lg font-medium text-gray-900">N/A</p>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Severity</h3>
                                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                                        {{ $incident->severity === 'critical' ? 'bg-red-100 text-red-800' : 
                                           ($incident->severity === 'high' ? 'bg-orange-100 text-orange-800' : 
                                           ($incident->severity === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) }}">
                                        <i class="fas fa-{{ $incident->severity === 'critical' ? 'exclamation-circle' : ($incident->severity === 'high' ? 'exclamation-triangle' : 'info-circle') }} mr-1"></i>
                                        {{ ucfirst($incident->severity) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Date & Time</h3>
                                    <p class="text-lg font-medium text-gray-900">
                                        {{ $incident->incident_date->format('F j, Y') }}<br>
                                        <span class="text-sm text-gray-600">{{ $incident->incident_date->format('g:i A') }}</span>
                                    </p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Location</h3>
                                    <p class="text-lg font-medium text-gray-900">{{ $incident->location }}</p>
                                    @if($incident->location_specific)
                                        <p class="text-sm text-gray-600 mt-1">{{ $incident->location_specific }}</p>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Department</h3>
                                    <p class="text-lg font-medium text-gray-900">{{ $incident->department->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Assigned To</h3>
                                    <p class="text-lg font-medium text-gray-900">{{ $incident->assignedTo->name ?? 'Unassigned' }}</p>
                                </div>
                            </div>
                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $incident->description }}</p>
                            </div>
                        </div>

                        <!-- Event Type Specific Details -->
                        @if($incident->isInjuryIllness())
                            <div class="bg-white rounded-lg shadow p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Injury/Illness Details</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if($incident->injury_type)
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Injury Type</h3>
                                            <p class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $incident->injury_type)) }}</p>
                                        </div>
                                    @endif
                                    @if($incident->body_part_affected)
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Body Part Affected</h3>
                                            <p class="text-gray-900">{{ $incident->body_part_affected }}</p>
                                        </div>
                                    @endif
                                    @if($incident->nature_of_injury)
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Nature of Injury</h3>
                                            <p class="text-gray-900">{{ $incident->nature_of_injury }}</p>
                                        </div>
                                    @endif
                                    @if($incident->lost_time_injury)
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Lost Time Injury</h3>
                                            <p class="text-gray-900">Yes @if($incident->days_lost) ({{ $incident->days_lost }} days) @endif</p>
                                        </div>
                                    @endif
                                    @if($incident->medical_treatment_details)
                                        <div class="md:col-span-2">
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Medical Treatment</h3>
                                            <p class="text-gray-900 whitespace-pre-wrap">{{ $incident->medical_treatment_details }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @elseif($incident->isPropertyDamage())
                            <div class="bg-white rounded-lg shadow p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Property Damage Details</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if($incident->asset_damaged)
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Asset Damaged</h3>
                                            <p class="text-gray-900">{{ $incident->asset_damaged }}</p>
                                        </div>
                                    @endif
                                    @if($incident->estimated_cost)
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Estimated Cost</h3>
                                            <p class="text-gray-900">${{ number_format($incident->estimated_cost, 2) }}</p>
                                        </div>
                                    @endif
                                    @if($incident->insurance_claim_filed)
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Insurance Claim</h3>
                                            <p class="text-gray-900">Filed</p>
                                        </div>
                                    @endif
                                    @if($incident->damage_description)
                                        <div class="md:col-span-2">
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Damage Description</h3>
                                            <p class="text-gray-900 whitespace-pre-wrap">{{ $incident->damage_description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @elseif($incident->isNearMiss())
                            <div class="bg-white rounded-lg shadow p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Near Miss Details</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if($incident->potential_severity)
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Potential Severity</h3>
                                            <p class="text-gray-900">{{ ucfirst($incident->potential_severity) }}</p>
                                        </div>
                                    @endif
                                    @if($incident->potential_consequences)
                                        <div class="md:col-span-2">
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Potential Consequences</h3>
                                            <p class="text-gray-900 whitespace-pre-wrap">{{ $incident->potential_consequences }}</p>
                                        </div>
                                    @endif
                                    @if($incident->preventive_measures_taken)
                                        <div class="md:col-span-2">
                                            <h3 class="text-sm font-medium text-gray-500 mb-1">Preventive Measures Taken</h3>
                                            <p class="text-gray-900 whitespace-pre-wrap">{{ $incident->preventive_measures_taken }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Images -->
                        @if($incident->hasImages())
                            <div class="bg-white rounded-lg shadow p-6">
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Images</h2>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($incident->images as $image)
                                        <a href="{{ Storage::url($image) }}" target="_blank" class="block">
                                            <img src="{{ Storage::url($image) }}" alt="Incident image" class="w-full h-32 object-cover rounded-lg hover:opacity-75 transition-opacity">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Quick Info -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Info</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-500">Reported By</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $incident->reporter->name ?? $incident->reporter_name ?? 'Anonymous' }}</p>
                                    @if($incident->reporter)
                                        <p class="text-xs text-gray-400">{{ $incident->reporter->email }}</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Reported On</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $incident->created_at->format('M j, Y g:i A') }}</p>
                                </div>
                                @if($incident->assignedTo)
                                    <div>
                                        <p class="text-sm text-gray-500">Assigned To</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $incident->assignedTo->name }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            
                            <!-- Risk Assessment Integration (Closed-Loop) -->
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Risk Assessment Integration</h4>
                                <div class="space-y-2">
                                    @if(!$incident->related_hazard_id)
                                        <a href="{{ route('risk-assessment.hazards.create', ['incident_id' => $incident->id]) }}" class="block w-full text-left px-4 py-2 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>Create Hazard from Incident
                                        </a>
                                    @else
                                        <a href="{{ route('risk-assessment.hazards.show', $incident->relatedHazard) }}" class="block w-full text-left px-4 py-2 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>View Related Hazard
                                        </a>
                                    @endif
                                    
                                    @if(!$incident->related_risk_assessment_id)
                                        <a href="{{ route('risk-assessment.risk-assessments.create', ['incident_id' => $incident->id]) }}" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-clipboard-list mr-2"></i>Create Risk Assessment
                                        </a>
                                    @else
                                        <a href="{{ route('risk-assessment.risk-assessments.show', $incident->relatedRiskAssessment) }}" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                            <i class="fas fa-clipboard-list mr-2"></i>View Related Risk Assessment
                                        </a>
                                    @endif
                                    
                                    @if($incident->rootCauseAnalysis && $incident->related_risk_assessment_id)
                                        <a href="{{ route('risk-assessment.risk-reviews.create', ['incident_id' => $incident->id, 'risk_assessment_id' => $incident->related_risk_assessment_id]) }}" class="block w-full text-left px-4 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                            <i class="fas fa-sync-alt mr-2"></i>Trigger Risk Review
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="space-y-3">
                                @if(!$incident->investigation && $incident->status !== 'closed')
                                    <a href="{{ route('incidents.investigations.create', $incident) }}" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-search mr-2"></i>Start Investigation
                                    </a>
                                @endif
                                @if($incident->investigation && !$incident->rootCauseAnalysis)
                                    <a href="{{ route('incidents.rca.create', $incident) }}" class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                        <i class="fas fa-project-diagram mr-2"></i>Perform RCA
                                    </a>
                                @endif
                                @if($incident->rootCauseAnalysis && $incident->capas->count() == 0)
                                    <a href="{{ route('incidents.capas.create', $incident) }}" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-tasks mr-2"></i>Create CAPA
                                    </a>
                                @endif
                                @if($incident->canBeClosed() && $incident->closure_status !== 'approved')
                                    <form action="{{ route('incidents.request-closure', $incident) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                            <i class="fas fa-check-circle mr-2"></i>Request Closure
                                        </button>
                                    </form>
                                @endif
                                @if($incident->closure_status === 'pending_approval')
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                        <p class="text-sm text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Pending Approval
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Risk Assessment Integration Status -->
                        @if($incident->related_hazard_id || $incident->related_risk_assessment_id || $incident->hazard_was_identified)
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Risk Assessment Integration</h3>
                            <div class="space-y-3">
                                @if($incident->hazard_was_identified)
                                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                            <span class="text-sm text-gray-700">Hazard was identified</span>
                                        </div>
                                    </div>
                                @endif
                                @if($incident->controls_were_in_place)
                                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                                            <span class="text-sm text-gray-700">Controls were in place</span>
                                        </div>
                                        @if($incident->controls_were_effective !== null)
                                            <span class="text-xs {{ $incident->controls_were_effective ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $incident->controls_were_effective ? 'Effective' : 'Ineffective' }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                @if($incident->related_hazard_id)
                                    <div class="p-3 bg-orange-50 rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1">Related Hazard</p>
                                        <a href="{{ route('risk-assessment.hazards.show', $incident->relatedHazard) }}" class="text-sm font-medium text-orange-700 hover:text-orange-800">
                                            {{ $incident->relatedHazard->reference_number }} - {{ $incident->relatedHazard->title }}
                                        </a>
                                    </div>
                                @endif
                                @if($incident->related_risk_assessment_id)
                                    <div class="p-3 bg-blue-50 rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1">Related Risk Assessment</p>
                                        <a href="{{ route('risk-assessment.risk-assessments.show', $incident->relatedRiskAssessment) }}" class="text-sm font-medium text-blue-700 hover:text-blue-800">
                                            {{ $incident->relatedRiskAssessment->reference_number }} - {{ $incident->relatedRiskAssessment->title }}
                                        </a>
                                    </div>
                                @endif
                                @if($incident->risk_assessment_gap_analysis)
                                    <div class="p-3 bg-yellow-50 rounded-lg">
                                        <p class="text-xs text-gray-600 mb-1">Gap Analysis</p>
                                        <p class="text-sm text-gray-700">{{ $incident->risk_assessment_gap_analysis }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Workflow Status -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Workflow Status</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Investigation</span>
                                    @if($incident->investigation)
                                        <span class="px-2 py-1 text-xs rounded-full {{ $incident->investigation->isCompleted() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($incident->investigation->status) }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Not Started</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Root Cause Analysis</span>
                                    @if($incident->rootCauseAnalysis)
                                        <span class="px-2 py-1 text-xs rounded-full {{ $incident->rootCauseAnalysis->isCompleted() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($incident->rootCauseAnalysis->status) }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Not Started</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">CAPAs</span>
                                    @php
                                        $totalCapas = $incident->capas->count();
                                        $closedCapas = $incident->capas->where('status', 'closed')->count();
                                    @endphp
                                    @if($totalCapas > 0)
                                        <span class="px-2 py-1 text-xs rounded-full {{ $closedCapas == $totalCapas ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $closedCapas }}/{{ $totalCapas }} Closed
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">None</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Investigation Tab -->
            <div id="content-investigation" class="tab-content hidden">
                @if($incident->investigation)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">Investigation Details</h2>
                            <div class="flex space-x-3">
                                <a href="{{ route('incidents.investigations.show', [$incident, $incident->investigation]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>View Full Investigation
                                </a>
                                @if($incident->investigation->status !== 'completed')
                                    <a href="{{ route('incidents.investigations.edit', [$incident, $incident->investigation]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Investigator</h3>
                                <p class="text-gray-900">{{ $incident->investigation->investigator->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                                {!! $incident->investigation->getStatusBadge() !!}
                            </div>
                            @if($incident->investigation->what_happened)
                                <div class="md:col-span-2">
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">What Happened?</h3>
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $incident->investigation->what_happened }}</p>
                                </div>
                            @endif
                            @if($incident->investigation->key_findings)
                                <div class="md:col-span-2">
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Key Findings</h3>
                                    <p class="text-gray-900 whitespace-pre-wrap">{{ $incident->investigation->key_findings }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Investigation Started</h3>
                        <p class="text-gray-500 mb-6">Start an investigation to gather facts and analyze this incident.</p>
                        <a href="{{ route('incidents.investigations.create', $incident) }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Start Investigation
                        </a>
                    </div>
                @endif
            </div>

            <!-- RCA Tab -->
            <div id="content-rca" class="tab-content hidden">
                @if($incident->rootCauseAnalysis)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-semibold text-gray-900">Root Cause Analysis</h2>
                            <div class="flex space-x-3">
                                <a href="{{ route('incidents.rca.show', [$incident, $incident->rootCauseAnalysis]) }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>View Full RCA
                                </a>
                                @if($incident->rootCauseAnalysis->status !== 'reviewed')
                                    <a href="{{ route('incidents.rca.edit', [$incident, $incident->rootCauseAnalysis]) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="mb-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Analysis Type</h3>
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                                {{ ucfirst(str_replace('_', ' ', $incident->rootCauseAnalysis->analysis_type)) }}
                            </span>
                        </div>
                        @if($incident->rootCauseAnalysis->isFiveWhys() && $incident->rootCauseAnalysis->root_cause)
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Root Cause Identified</h3>
                                <p class="text-gray-900 text-lg font-medium">{{ $incident->rootCauseAnalysis->root_cause }}</p>
                            </div>
                        @endif
                        @if($incident->rootCauseAnalysis->lessons_learned)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Lessons Learned</h3>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $incident->rootCauseAnalysis->lessons_learned }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <i class="fas fa-project-diagram text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Root Cause Analysis</h3>
                        <p class="text-gray-500 mb-6">Perform a root cause analysis to identify underlying causes of this incident.</p>
                        @if($incident->investigation && $incident->investigation->isCompleted())
                            <a href="{{ route('incidents.rca.create', $incident) }}" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Create Root Cause Analysis
                            </a>
                        @else
                            <p class="text-sm text-gray-400">Complete the investigation first to perform RCA.</p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- CAPAs Tab -->
            <div id="content-capas" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Corrective & Preventive Actions</h2>
                        @if($incident->rootCauseAnalysis && $incident->rootCauseAnalysis->isCompleted())
                            <a href="{{ route('incidents.capas.create', $incident) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>Create CAPA
                            </a>
                        @endif
                    </div>
                    @if($incident->capas->count() > 0)
                        <div class="space-y-4">
                            @foreach($incident->capas as $capa)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $capa->title }}</h3>
                                            <p class="text-sm text-gray-500">{{ $capa->reference_number }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            {!! $capa->getStatusBadge() !!}
                                            {!! $capa->getPriorityBadge() !!}
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-3">{{ Str::limit($capa->description, 150) }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span><i class="fas fa-user mr-1"></i>{{ $capa->assignedTo->name ?? 'Unassigned' }}</span>
                                            @if($capa->due_date)
                                                <span><i class="fas fa-calendar mr-1"></i>Due: {{ $capa->due_date->format('M j, Y') }}</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('incidents.capas.show', [$incident, $capa]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View Details <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-tasks text-gray-400 text-5xl mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No CAPAs Created</h3>
                            <p class="text-gray-500 mb-6">Create corrective or preventive actions based on the root cause analysis.</p>
                            @if($incident->rootCauseAnalysis && $incident->rootCauseAnalysis->isCompleted())
                                <a href="{{ route('incidents.capas.create', $incident) }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Create CAPA
                                </a>
                            @else
                                <p class="text-sm text-gray-400">Complete the root cause analysis first to create CAPAs.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attachments Tab -->
            <div id="content-attachments" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">Attachments & Evidence</h2>
                        <button onclick="showUploadModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-upload mr-2"></i>Upload File
                        </button>
                    </div>
                    @if($incident->attachments->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($incident->attachments as $attachment)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center space-x-3">
                                            @if($attachment->isImage())
                                                <i class="fas fa-image text-blue-600 text-2xl"></i>
                                            @elseif($attachment->isVideo())
                                                <i class="fas fa-video text-purple-600 text-2xl"></i>
                                            @else
                                                <i class="fas fa-file text-gray-600 text-2xl"></i>
                                            @endif
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">{{ Str::limit($attachment->original_name, 30) }}</h4>
                                                <p class="text-xs text-gray-500">{{ $attachment->getFileSizeHuman() }}</p>
                                            </div>
                                        </div>
                                        {!! $attachment->getCategoryBadge() !!}
                                    </div>
                                    @if($attachment->description)
                                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($attachment->description, 80) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            @if($attachment->is_evidence)
                                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Evidence</span>
                                            @endif
                                            @if($attachment->is_confidential)
                                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Confidential</span>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('incidents.attachments.show', [$incident, $attachment]) }}" class="text-blue-600 hover:text-blue-900" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('incidents.attachments.destroy', [$incident, $attachment]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this attachment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-paperclip text-gray-400 text-5xl mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Attachments</h3>
                            <p class="text-gray-500 mb-6">Upload files, photos, videos, or documents related to this incident.</p>
                            <button onclick="showUploadModal()" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-upload mr-2"></i>Upload File
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Upload Attachment</h3>
                <button onclick="hideUploadModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <form action="{{ route('incidents.attachments.store', $incident) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File *</label>
                    <input type="file" name="file" required accept="image/*,video/*,.pdf,.doc,.docx"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="photo">Photo</option>
                        <option value="video">Video</option>
                        <option value="document">Document</option>
                        <option value="witness_statement">Witness Statement</option>
                        <option value="interview_recording">Interview Recording</option>
                        <option value="policy_manual">Policy Manual</option>
                        <option value="training_record">Training Record</option>
                        <option value="equipment_manual">Equipment Manual</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_evidence" value="1" class="mr-2">
                        <span class="text-sm text-gray-700">Mark as Evidence</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_confidential" value="1" class="mr-2">
                        <span class="text-sm text-gray-700">Confidential</span>
                    </label>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="hideUploadModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.tab-button {
    transition: all 0.2s;
}
.tab-button.active {
    border-bottom-color: #3b82f6;
    color: #2563eb;
}
.tab-content {
    display: block;
}
.tab-content.hidden {
    display: none;
}
</style>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Activate selected button
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500');
}

function showUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function hideUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show overview tab by default
    switchTab('overview');
});
</script>
@endsection

