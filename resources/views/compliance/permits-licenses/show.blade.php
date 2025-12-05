@extends('layouts.app')

@section('title', 'Permit/License: ' . $permitsLicense->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('compliance.permits-licenses.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $permitsLicense->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $permitsLicense->name }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('compliance.permits-licenses.edit', $permitsLicense) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Permit/License Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-black">{{ $permitsLicense->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Permit/License Number</dt>
                        <dd class="mt-1 text-sm text-black">{{ $permitsLicense->permit_license_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $permitsLicense->type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $permitsLicense->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $permitsLicense->status == 'expired' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $permitsLicense->status == 'pending_renewal' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $permitsLicense->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $permitsLicense->issue_date->format('M d, Y') }}</dd>
                    </div>
                    @if($permitsLicense->expiry_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $permitsLicense->expiry_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($permitsLicense->issuing_authority)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Issuing Authority</dt>
                        <dd class="mt-1 text-sm text-black">{{ $permitsLicense->issuing_authority }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            @if($permitsLicense->attachment_path)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Attachment</h2>
                <a href="{{ Storage::url($permitsLicense->attachment_path) }}" target="_blank" class="text-[#0066CC] hover:underline">
                    <i class="fas fa-file mr-2"></i>View Attachment
                </a>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <dl class="space-y-3">
                    @if($permitsLicense->responsiblePerson)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Responsible Person</dt>
                        <dd class="mt-1 text-sm text-black">{{ $permitsLicense->responsiblePerson->name }}</dd>
                    </div>
                    @endif
                    @if($permitsLicense->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $permitsLicense->department->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $permitsLicense->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

