@extends('layouts.app')

@section('title', 'Waste Management Record: ' . $wasteManagementRecord->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('environmental.waste-management.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $wasteManagementRecord->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $wasteManagementRecord->waste_type }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('environmental.waste-management.edit', $wasteManagementRecord) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Waste Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Waste Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->waste_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->category ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->quantity ?? 'N/A' }} {{ $wasteManagementRecord->unit ?? '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Segregation Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $wasteManagementRecord->segregation_status == 'properly_segregated' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $wasteManagementRecord->segregation_status == 'improperly_segregated' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $wasteManagementRecord->segregation_status == 'mixed' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $wasteManagementRecord->segregation_status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($wasteManagementRecord->storage_location)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Storage Location</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->storage_location }}</dd>
                    </div>
                    @endif
                    @if($wasteManagementRecord->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->department->name }}</dd>
                    </div>
                    @endif
                    @if($wasteManagementRecord->collection_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Collection Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->collection_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($wasteManagementRecord->disposal_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Disposal Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->disposal_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($wasteManagementRecord->disposal_method)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Disposal Method</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($wasteManagementRecord->disposal_method) }}</dd>
                    </div>
                    @endif
                    @if($wasteManagementRecord->disposalContractor)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Disposal Contractor</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->disposalContractor->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Quick Info</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Reference Number</dt>
                        <dd class="mt-1 text-sm font-medium text-black">{{ $wasteManagementRecord->reference_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Recorded By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->recordedBy->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-black">{{ $wasteManagementRecord->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>

            @if($wasteManagementRecord->notes)
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Notes</h3>
                <p class="text-sm text-black">{{ $wasteManagementRecord->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

