@extends('layouts.app')

@section('title', 'Work Permit Details')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('work-permits.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $workPermit->work_title }}</h1>
                    <p class="text-sm text-gray-500">{{ $workPermit->reference_number }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <x-print-button />
                <a href="{{ route('work-permits.create', ['copy_from' => $workPermit->id]) }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" title="Copy this work permit">
                    <i class="fas fa-copy mr-2"></i>Copy
                </a>
                @if(in_array($workPermit->status, ['draft', 'rejected']))
                    <a href="{{ route('work-permits.edit', $workPermit) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif
                @if($workPermit->status == 'draft')
                    <form action="{{ route('work-permits.submit', $workPermit) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                            <i class="fas fa-paper-plane mr-2"></i>Submit
                        </button>
                    </form>
                @endif
                @if($workPermit->canBeApproved())
                    <button onclick="showApproveModal()" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                        <i class="fas fa-check mr-2"></i>Approve
                    </button>
                    <button onclick="showRejectModal()" class="bg-[#CC0000] text-white px-4 py-2 border border-[#CC0000] hover:bg-[#AA0000]">
                        <i class="fas fa-times mr-2"></i>Reject
                    </button>
                @endif
                @if($workPermit->status == 'approved')
                    <form action="{{ route('work-permits.activate', $workPermit) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                            <i class="fas fa-play mr-2"></i>Activate
                        </button>
                    </form>
                @endif
                @if($workPermit->canBeClosed())
                    <button onclick="showCloseModal()" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                        <i class="fas fa-stop mr-2"></i>Close
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Permit Information -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Permit Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Permit Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->workPermitType->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $workPermit->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $workPermit->status == 'submitted' || $workPermit->status == 'under_review' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $workPermit->status == 'expired' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $workPermit->status == 'closed' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $workPermit->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Requested By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->requestedBy->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->department->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Work Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->work_description }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Work Location</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->work_location }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Work Schedule -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Work Schedule</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Start Date & Time</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->work_start_date->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">End Date & Time</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->work_end_date->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Validity Hours</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->validity_hours }} hours</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                        <dd class="mt-1 text-sm text-black {{ $workPermit->isExpired() ? 'text-[#CC0000]' : '' }}">
                            {{ $workPermit->expiry_date->format('M d, Y H:i') }}
                            @if($workPermit->isExpired())
                                <span class="ml-2 text-xs">(Expired)</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Safety Requirements -->
            @if($workPermit->gas_test_required || $workPermit->fire_watch_required || $workPermit->emergency_procedures)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Safety Requirements</h2>
                <dl class="space-y-4">
                    @if($workPermit->gas_test_required)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gas Test</dt>
                        <dd class="mt-1 text-sm text-black">
                            @if($workPermit->gas_test_date)
                                Tested on {{ $workPermit->gas_test_date->format('M d, Y H:i') }} by {{ $workPermit->gasTester->name ?? 'N/A' }}
                                @if($workPermit->gas_test_results)
                                    <br><span class="text-gray-500">{{ $workPermit->gas_test_results }}</span>
                                @endif
                            @else
                                Required but not yet performed
                            @endif
                        </dd>
                    </div>
                    @endif
                    @if($workPermit->fire_watch_required)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fire Watch</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->fireWatchPerson->name ?? 'Not assigned' }}</dd>
                    </div>
                    @endif
                    @if($workPermit->emergency_procedures)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Emergency Procedures</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermit->emergency_procedures }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            <!-- GCLA Logs -->
            @if($workPermit->gcaLogs->count() > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">GCLA Logs</h2>
                <div class="space-y-3">
                    @foreach($workPermit->gcaLogs as $log)
                        <a href="{{ route('work-permits.gca-logs.show', $log) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-black">{{ $log->reference_number }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $log->check_date->format('M d, Y H:i') }} - {{ $log->location }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $log->compliance_status == 'compliant' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}
                                    {{ $log->compliance_status == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                    {{ $log->compliance_status == 'partial' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                    {{ ucfirst($log->compliance_status) }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Approval Status -->
            @if($workPermit->approvals->count() > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Approval Status</h3>
                <div class="space-y-3">
                    @foreach($workPermit->approvals->sortBy('approval_level') as $approval)
                        <div class="border border-gray-300 p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="text-sm font-medium text-black">Level {{ $approval->approval_level }}</p>
                                    <p class="text-xs text-gray-500">{{ $approval->approver->name }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $approval->status == 'approved' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                    {{ $approval->status == 'rejected' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                    {{ $approval->status == 'pending' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                    {{ ucfirst($approval->status) }}
                                </span>
                            </div>
                            @if($approval->comments)
                                <p class="text-xs text-gray-500 mt-2">{{ $approval->comments }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('work-permits.gca-logs.create', ['work_permit_id' => $workPermit->id]) }}" class="block w-full text-center px-4 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                        <i class="fas fa-clipboard-check mr-2"></i>Create GCLA Log
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white border border-gray-300 p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-black mb-4">Approve Work Permit</h3>
        <form action="{{ route('work-permits.approve', $workPermit) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="approval_notes" class="block text-sm font-medium text-black mb-1">Approval Notes</label>
                <textarea id="approval_notes" name="approval_notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideApproveModal()" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">Approve</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white border border-gray-300 p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-black mb-4">Reject Work Permit</h3>
        <form action="{{ route('work-permits.reject', $workPermit) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-black mb-1">Rejection Reason *</label>
                <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#CC0000] text-white border border-[#CC0000] hover:bg-[#AA0000]">Reject</button>
            </div>
        </form>
    </div>
</div>

<!-- Close Modal -->
<div id="closeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white border border-gray-300 p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold text-black mb-4">Close Work Permit</h3>
        <form action="{{ route('work-permits.close', $workPermit) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="closure_notes" class="block text-sm font-medium text-black mb-1">Closure Notes *</label>
                <textarea id="closure_notes" name="closure_notes" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="hideCloseModal()" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">Close</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function showApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
}
function hideApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}
function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
function showCloseModal() {
    document.getElementById('closeModal').classList.remove('hidden');
}
function hideCloseModal() {
    document.getElementById('closeModal').classList.add('hidden');
}
</script>
@endpush
@endsection

