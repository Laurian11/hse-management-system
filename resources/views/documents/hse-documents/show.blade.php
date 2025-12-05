@extends('layouts.app')

@section('title', 'HSE Document: ' . $document->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('documents.hse-documents.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $document->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $document->title }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('documents.hse-documents.edit', $document) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Document Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Document Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($document->document_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($document->category) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $document->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $document->status == 'under_review' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $document->status == 'archived' ? 'bg-[#F5F5F5] text-gray-500 border-gray-500' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($document->document_code)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Document Code</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->document_code }}</dd>
                    </div>
                    @endif
                    @if($document->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->department->name }}</dd>
                    </div>
                    @endif
                    @if($document->effective_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Effective Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->effective_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($document->expiry_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->expiry_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($document->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            @if($document->file_path)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Document File</h2>
                <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-[#0066CC] hover:underline">
                    <i class="fas fa-file mr-2"></i>{{ $document->file_name ?? 'View Document' }}
                </a>
            </div>
            @endif

            @if($document->versions->count() > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Versions ({{ $document->versions->count() }})</h2>
                <div class="space-y-2">
                    @foreach($document->versions as $version)
                        <div class="border border-gray-300 p-3 flex justify-between items-center">
                            <div>
                                <span class="font-medium text-black">Version {{ $version->version_number }}</span>
                                @if($version->is_current)
                                    <span class="ml-2 px-2 py-1 text-xs bg-[#F5F5F5] text-[#0066CC] border border-[#0066CC]">Current</span>
                                @endif
                            </div>
                            <a href="{{ route('documents.versions.show', $version) }}" class="text-[#0066CC] hover:underline">View</a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->creator->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->created_at->format('M d, Y') }}</dd>
                    </div>
                    @if($document->approver)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Approved By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->approver->name }}</dd>
                    </div>
                    @endif
                    @if($document->approval_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Approval Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $document->approval_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Access Level</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($document->access_level) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

