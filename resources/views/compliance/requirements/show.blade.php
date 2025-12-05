@extends('layouts.app')

@section('title', 'Compliance Requirement: ' . $requirement->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('compliance.requirements.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $requirement->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $requirement->requirement_title }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('compliance.requirements.edit', $requirement) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Requirement Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Requirement Title</dt>
                        <dd class="mt-1 text-sm text-black">{{ $requirement->requirement_title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Regulatory Body</dt>
                        <dd class="mt-1 text-sm text-black">{{ $requirement->regulatory_body }}</dd>
                    </div>
                    @if($requirement->regulation_code)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Regulation Code</dt>
                        <dd class="mt-1 text-sm text-black">{{ $requirement->regulation_code }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Requirement Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $requirement->requirement_type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Compliance Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $requirement->compliance_status == 'compliant' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $requirement->compliance_status == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $requirement->compliance_status == 'partially_compliant' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $requirement->compliance_status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($requirement->compliance_due_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Compliance Due Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $requirement->compliance_due_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($requirement->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $requirement->description }}</dd>
                    </div>
                    @endif
                    @if($requirement->compliance_evidence)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Compliance Evidence</dt>
                        <dd class="mt-1 text-sm text-black">{{ $requirement->compliance_evidence }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <dl class="space-y-3">
                    @if($requirement->responsiblePerson)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Responsible Person</dt>
                        <dd class="mt-1 text-sm text-black">{{ $requirement->responsiblePerson->name }}</dd>
                    </div>
                    @endif
                    @if($requirement->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $requirement->department->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $requirement->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

