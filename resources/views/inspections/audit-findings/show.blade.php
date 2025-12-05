@extends('layouts.app')

@section('title', 'Audit Finding: ' . $finding->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.audit-findings.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $finding->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $finding->title }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('inspections.audit-findings.edit', $finding) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Finding Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm text-black">{{ $finding->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Audit</dt>
                        <dd class="mt-1 text-sm text-black">
                            <a href="{{ route('inspections.audits.show', $finding->audit) }}" class="text-[#0066CC] hover:underline">
                                {{ $finding->audit->reference_number }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Finding Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $finding->finding_type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Severity</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $finding->severity == 'critical' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $finding->severity == 'major' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $finding->severity == 'minor' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}">
                                {{ ucfirst($finding->severity) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $finding->status == 'closed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $finding->status == 'in_progress' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $finding->status == 'open' ? 'bg-[#F5F5F5] text-gray-500 border-gray-300' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $finding->status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($finding->clause_reference)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Clause Reference</dt>
                        <dd class="mt-1 text-sm text-black">{{ $finding->clause_reference }}</dd>
                    </div>
                    @endif
                    @if($finding->evidence)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Evidence</dt>
                        <dd class="mt-1 text-sm text-black">{{ $finding->evidence }}</dd>
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $finding->description }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Root Cause -->
            @if($finding->root_cause)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Root Cause</h2>
                <p class="text-sm text-black">{{ $finding->root_cause }}</p>
            </div>
            @endif

            <!-- Corrective Action -->
            @if($finding->corrective_action_required)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Corrective Action</h2>
                <dl class="space-y-4">
                    @if($finding->corrective_action_plan)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Action Plan</dt>
                        <dd class="mt-1 text-sm text-black">{{ $finding->corrective_action_plan }}</dd>
                    </div>
                    @endif
                    @if($finding->correctiveActionAssignedTo)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                        <dd class="mt-1 text-sm text-black">{{ $finding->correctiveActionAssignedTo->name }}</dd>
                    </div>
                    @endif
                    @if($finding->corrective_action_due_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                        <dd class="mt-1 text-sm text-black {{ $finding->corrective_action_due_date < now() && !$finding->corrective_action_completed ? 'text-[#CC0000]' : '' }}">
                            {{ $finding->corrective_action_due_date->format('M d, Y') }}
                            @if($finding->corrective_action_due_date < now() && !$finding->corrective_action_completed)
                                <span class="ml-2 text-xs">(Overdue)</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $finding->corrective_action_completed ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' }}">
                                {{ $finding->corrective_action_completed ? 'Completed' : 'Pending' }}
                            </span>
                            @if($finding->corrective_action_completed_date)
                                <span class="ml-2 text-xs text-gray-500">on {{ $finding->corrective_action_completed_date->format('M d, Y') }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- Verification -->
            @if($finding->verified_by)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Verification</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verified By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $finding->verifiedBy->name }}</dd>
                    </div>
                    @if($finding->verified_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verified At</dt>
                        <dd class="mt-1 text-sm text-black">{{ $finding->verified_at->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($finding->verification_notes)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verification Notes</dt>
                        <dd class="mt-1 text-sm text-black">{{ $finding->verification_notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

