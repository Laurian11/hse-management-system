@extends('layouts.app')

@section('title', 'Procurement Request: ' . $procurementRequest->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('procurement.requests.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $procurementRequest->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $procurementRequest->item_name }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('procurement.requests.edit', $procurementRequest) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
            <!-- Item Information -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Item Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Item Name</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->item_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $procurementRequest->item_category)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->quantity }} {{ $procurementRequest->unit ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Priority</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $procurementRequest->priority == 'urgent' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $procurementRequest->priority == 'high' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $procurementRequest->priority == 'medium' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $procurementRequest->priority == 'low' ? 'bg-[#F5F5F5] text-gray-500 border-gray-300' : '' }}">
                                {{ ucfirst($procurementRequest->priority) }}
                            </span>
                        </dd>
                    </div>
                    @if($procurementRequest->estimated_cost)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Estimated Cost</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->currency ?? '' }} {{ number_format($procurementRequest->estimated_cost, 2) }}</dd>
                    </div>
                    @endif
                    @if($procurementRequest->required_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Required By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->required_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($procurementRequest->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->department->name }}</dd>
                    </div>
                    @endif
                    @if($procurementRequest->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->description }}</dd>
                    </div>
                    @endif
                    @if($procurementRequest->justification)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Justification</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->justification }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Status & Approval -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Status & Approval</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $procurementRequest->status == 'approved' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $procurementRequest->status == 'rejected' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ in_array($procurementRequest->status, ['submitted', 'under_review']) ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $procurementRequest->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Requested By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->requestedBy->name ?? 'N/A' }}</dd>
                    </div>
                    @if($procurementRequest->approvedBy)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Approved By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->approvedBy->name }}</dd>
                    </div>
                    @endif
                    @if($procurementRequest->approval_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Approval Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->approval_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Info -->
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Quick Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Reference Number</dt>
                        <dd class="mt-1 text-sm font-medium text-black">{{ $procurementRequest->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-black">{{ $procurementRequest->updated_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>

            @if($procurementRequest->notes)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Notes</h3>
                <p class="text-sm text-black">{{ $procurementRequest->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

