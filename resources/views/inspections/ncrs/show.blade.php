@extends('layouts.app')

@section('title', 'NCR: ' . $ncr->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.ncrs.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $ncr->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $ncr->title }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('inspections.ncrs.edit', $ncr) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">NCR Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Severity</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $ncr->severity == 'critical' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $ncr->severity == 'major' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $ncr->severity == 'minor' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}">
                                {{ ucfirst($ncr->severity) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $ncr->status == 'open' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $ncr->status == 'closed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $ncr->status == 'investigating' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $ncr->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Identified Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->identified_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Identified By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->identifiedBy->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->location }}</dd>
                    </div>
                    @if($ncr->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->department->name }}</dd>
                    </div>
                    @endif
                    @if($ncr->inspection)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Related Inspection</dt>
                        <dd class="mt-1 text-sm text-black">
                            <a href="{{ route('inspections.show', $ncr->inspection) }}" class="text-[#0066CC] hover:underline">
                                {{ $ncr->inspection->reference_number }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->description }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Root Cause & Immediate Action -->
            @if($ncr->root_cause || $ncr->immediate_action)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Root Cause & Immediate Action</h2>
                <dl class="space-y-4">
                    @if($ncr->root_cause)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Root Cause</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->root_cause }}</dd>
                    </div>
                    @endif
                    @if($ncr->immediate_action)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Immediate Action</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->immediate_action }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            <!-- Corrective Action -->
            @if($ncr->corrective_action_plan || $ncr->corrective_action_assigned_to)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Corrective Action</h2>
                <dl class="space-y-4">
                    @if($ncr->corrective_action_plan)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Action Plan</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->corrective_action_plan }}</dd>
                    </div>
                    @endif
                    @if($ncr->correctiveActionAssignedTo)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->correctiveActionAssignedTo->name }}</dd>
                    </div>
                    @endif
                    @if($ncr->corrective_action_due_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                        <dd class="mt-1 text-sm text-black {{ $ncr->corrective_action_due_date < now() && !$ncr->corrective_action_completed ? 'text-[#CC0000]' : '' }}">
                            {{ $ncr->corrective_action_due_date->format('M d, Y') }}
                            @if($ncr->corrective_action_due_date < now() && !$ncr->corrective_action_completed)
                                <span class="ml-2 text-xs">(Overdue)</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $ncr->corrective_action_completed ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' }}">
                                {{ $ncr->corrective_action_completed ? 'Completed' : 'Pending' }}
                            </span>
                            @if($ncr->corrective_action_completed_date)
                                <span class="ml-2 text-xs text-gray-500">on {{ $ncr->corrective_action_completed_date->format('M d, Y') }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- Verification -->
            @if($ncr->verification_required && $ncr->verified_by)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Verification</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verified By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->verifiedBy->name }}</dd>
                    </div>
                    @if($ncr->verified_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verified At</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->verified_at->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($ncr->verification_notes)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verification Notes</dt>
                        <dd class="mt-1 text-sm text-black">{{ $ncr->verification_notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

