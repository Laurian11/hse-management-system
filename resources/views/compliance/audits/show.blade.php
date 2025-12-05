@extends('layouts.app')

@section('title', 'Compliance Audit: ' . $audit->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('compliance.audits.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $audit->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $audit->audit_title }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('compliance.audits.edit', $audit) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Audit Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Audit Title</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->audit_title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Audit Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $audit->audit_type)) }}</dd>
                    </div>
                    @if($audit->standard)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Standard</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->standard }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $audit->audit_status == 'completed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $audit->audit_status == 'in_progress' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $audit->audit_status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Audit Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->audit_date->format('M d, Y') }}</dd>
                    </div>
                    @if($audit->overall_result)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Overall Result</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($audit->overall_result) }}</dd>
                    </div>
                    @endif
                    @if($audit->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            @if($audit->audit_report_path)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Audit Report</h2>
                <a href="{{ Storage::url($audit->audit_report_path) }}" target="_blank" class="text-[#0066CC] hover:underline">
                    <i class="fas fa-file-pdf mr-2"></i>View Report
                </a>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <dl class="space-y-3">
                    @if($audit->auditor)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Auditor</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->auditor->name }}</dd>
                    </div>
                    @endif
                    @if($audit->external_auditor_name)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">External Auditor</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->external_auditor_name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

