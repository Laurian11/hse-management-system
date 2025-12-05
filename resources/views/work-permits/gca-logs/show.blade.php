@extends('layouts.app')

@section('title', 'GCLA Log Details')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('work-permits.gca-logs.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $gcaLog->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $gcaLog->gcla_type)) }} - {{ $gcaLog->location }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('work-permits.gca-logs.edit', $gcaLog) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                @if($gcaLog->action_assigned_to && !$gcaLog->action_completed)
                    <form action="{{ route('work-permits.gca-logs.complete-action', $gcaLog) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                            <i class="fas fa-check mr-2"></i>Complete Action
                        </button>
                    </form>
                @endif
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
                <h2 class="text-lg font-semibold text-black mb-4">GCLA Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">GCLA Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $gcaLog->gcla_type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Check Date & Time</dt>
                        <dd class="mt-1 text-sm text-black">{{ $gcaLog->check_date->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-black">{{ $gcaLog->location }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Compliance Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $gcaLog->compliance_status == 'compliant' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}
                                {{ $gcaLog->compliance_status == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $gcaLog->compliance_status == 'partial' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $gcaLog->compliance_status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($gcaLog->workPermit)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Work Permit</dt>
                        <dd class="mt-1">
                            <a href="{{ route('work-permits.show', $gcaLog->workPermit) }}" class="text-sm text-[#0066CC] hover:underline">
                                {{ $gcaLog->workPermit->reference_number }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $gcaLog->description }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Corrective Actions -->
            @if($gcaLog->corrective_actions || $gcaLog->action_assigned_to)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Corrective Actions</h2>
                <dl class="space-y-4">
                    @if($gcaLog->corrective_actions)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Actions</dt>
                        <dd class="mt-1 text-sm text-black">{{ $gcaLog->corrective_actions }}</dd>
                    </div>
                    @endif
                    @if($gcaLog->actionAssignedTo)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                        <dd class="mt-1 text-sm text-black">{{ $gcaLog->actionAssignedTo->name }}</dd>
                    </div>
                    @endif
                    @if($gcaLog->action_due_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                        <dd class="mt-1 text-sm text-black {{ $gcaLog->action_due_date < now() && !$gcaLog->action_completed ? 'text-[#CC0000]' : '' }}">
                            {{ $gcaLog->action_due_date->format('M d, Y') }}
                            @if($gcaLog->action_due_date < now() && !$gcaLog->action_completed)
                                <span class="ml-2 text-xs">(Overdue)</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $gcaLog->action_completed ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' }}">
                                {{ $gcaLog->action_completed ? 'Completed' : 'Pending' }}
                            </span>
                            @if($gcaLog->action_completed_at)
                                <span class="ml-2 text-xs text-gray-500">on {{ $gcaLog->action_completed_at->format('M d, Y') }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- Verification -->
            @if($gcaLog->verified_by)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Verification</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verified By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $gcaLog->verifiedBy->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verified At</dt>
                        <dd class="mt-1 text-sm text-black">{{ $gcaLog->verified_at->format('M d, Y H:i') }}</dd>
                    </div>
                    @if($gcaLog->verification_notes)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Verification Notes</dt>
                        <dd class="mt-1 text-sm text-black">{{ $gcaLog->verification_notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    @if(!$gcaLog->verified_by)
                        <button onclick="showVerifyModal()" class="block w-full text-center px-4 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                            <i class="fas fa-check-circle mr-2"></i>Verify
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verify Modal -->
<div id="verifyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white border border-gray-300 p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-black mb-4">Verify GCLA Log</h3>
        <form action="{{ route('work-permits.gca-logs.verify', $gcaLog) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="verification_notes" class="block text-sm font-medium text-black mb-1">Verification Notes *</label>
                <textarea id="verification_notes" name="verification_notes" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideVerifyModal()" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">Verify</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showVerifyModal() {
    document.getElementById('verifyModal').classList.remove('hidden');
}
function hideVerifyModal() {
    document.getElementById('verifyModal').classList.add('hidden');
}
</script>
@endpush
@endsection

