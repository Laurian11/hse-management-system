@extends('layouts.app')

@section('title', 'Hazard Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $hazard->title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $hazard->reference_number }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('risk-assessment.hazards.edit', $hazard) }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="{{ route('risk-assessment.risk-assessments.create', ['hazard_id' => $hazard->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-clipboard-list mr-2"></i>Create Risk Assessment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Hazard Details</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Description</label>
                            <p class="mt-1 text-gray-900">{{ $hazard->description }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Category</label>
                                <p class="mt-1 text-gray-900">{{ $hazard->getCategoryLabel() }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Status</label>
                                <p class="mt-1">{!! $hazard->getStatusBadge() !!}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Location</label>
                                <p class="mt-1 text-gray-900">{{ $hazard->location ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Department</label>
                                <p class="mt-1 text-gray-900">{{ $hazard->department->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Risk Assessments -->
                @if($hazard->riskAssessments->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Related Risk Assessments</h2>
                    <div class="space-y-3">
                        @foreach($hazard->riskAssessments as $assessment)
                            <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded">
                                <a href="{{ route('risk-assessment.risk-assessments.show', $assessment) }}" class="font-semibold text-gray-900 hover:text-blue-700">
                                    {{ $assessment->title }}
                                </a>
                                <p class="text-sm text-gray-600 mt-1">{{ $assessment->reference_number }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Control Measures -->
                @if($hazard->controlMeasures->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Control Measures</h2>
                    <div class="space-y-3">
                        @foreach($hazard->controlMeasures as $control)
                            <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded">
                                <a href="{{ route('risk-assessment.control-measures.show', $control) }}" class="font-semibold text-gray-900 hover:text-green-700">
                                    {{ $control->title }}
                                </a>
                                <p class="text-sm text-gray-600 mt-1">{{ $control->getControlTypeLabel() }} • {{ ucfirst($control->status) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('risk-assessment.risk-assessments.create', ['hazard_id' => $hazard->id]) }}" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100">
                            <i class="fas fa-clipboard-list mr-2"></i>Create Risk Assessment
                        </a>
                        <a href="{{ route('risk-assessment.control-measures.create', ['hazard_id' => $hazard->id]) }}" class="block w-full text-left px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100">
                            <i class="fas fa-shield-alt mr-2"></i>Add Control Measure
                        </a>
                    </div>
                </div>

                <!-- Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">Information</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500">Created by:</span>
                            <p class="text-gray-900">{{ $hazard->creator->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <p class="text-gray-900">{{ $hazard->created_at->format('M d, Y') }}</p>
                        </div>
                        @if($hazard->relatedIncident)
                        <div>
                            <span class="text-gray-500">Related Incident:</span>
                            <a href="{{ route('incidents.show', $hazard->relatedIncident) }}" class="text-blue-600 hover:text-blue-700">
                                {{ $hazard->relatedIncident->reference_number }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

