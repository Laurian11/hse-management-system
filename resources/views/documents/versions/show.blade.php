@extends('layouts.app')

@section('title', 'Document Version: ' . $version->version_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('documents.versions.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">Version {{ $version->version_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $version->document->title ?? 'N/A' }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('documents.versions.edit', $version) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Version Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Document</dt>
                        <dd class="mt-1 text-sm text-black">{{ $version->document->title ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Version Number</dt>
                        <dd class="mt-1 text-sm font-medium text-black">
                            {{ $version->version_number }}
                            @if($version->is_current)
                                <span class="ml-2 px-2 py-1 text-xs bg-[#F5F5F5] text-[#0066CC] border border-[#0066CC]">Current</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $version->status == 'approved' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $version->status == 'under_review' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $version->status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($version->change_summary)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Change Summary</dt>
                        <dd class="mt-1 text-sm text-black">{{ $version->change_summary }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            @if($version->file_path)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Version File</h2>
                <a href="{{ Storage::url($version->file_path) }}" target="_blank" class="text-[#0066CC] hover:underline">
                    <i class="fas fa-file mr-2"></i>{{ $version->file_name ?? 'View File' }}
                </a>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $version->creator->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $version->created_at->format('M d, Y') }}</dd>
                    </div>
                    @if($version->reviewer)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Reviewed By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $version->reviewer->name }}</dd>
                    </div>
                    @endif
                    @if($version->approver)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Approved By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $version->approver->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

