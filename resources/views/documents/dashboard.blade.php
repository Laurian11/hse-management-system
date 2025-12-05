@extends('layouts.app')

@section('title', 'Document & Record Management Dashboard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Document & Record Management Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Centralized control of HSE documents and versioning</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Documents</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_documents'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-file-alt text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Active Documents</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['active_documents'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-check-circle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pending Approval</p>
                    <p class="text-xl md:text-3xl font-bold text-[#FF9900] mt-1 md:mt-2">{{ $stats['pending_approval'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-clock text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Expiring Soon</p>
                    <p class="text-xl md:text-3xl font-bold text-[#CC0000] mt-1 md:mt-2">{{ $stats['expiring_soon'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Documents -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Documents</h3>
            <div class="space-y-3">
                @forelse($recentDocuments as $document)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $document->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $document->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $document->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $document->status == 'under_review' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $document->status == 'archived' ? 'bg-[#F5F5F5] text-gray-500 border-gray-500' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-user mr-1"></i>{{ $document->creator->name ?? 'N/A' }}</span>
                            <span><i class="fas fa-tag mr-1"></i>{{ ucfirst($document->document_type) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No documents</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Versions -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Versions</h3>
            <div class="space-y-3">
                @forelse($recentVersions as $version)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $version->document->title ?? 'N/A' }}</h4>
                                <p class="text-xs text-gray-500 mt-1">Version {{ $version->version_number }}</p>
                            </div>
                            @if($version->is_current)
                                <span class="px-2 py-1 text-xs font-semibold bg-[#F5F5F5] text-[#0066CC] border border-[#0066CC]">Current</span>
                            @endif
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-user mr-1"></i>{{ $version->creator->name ?? 'N/A' }}</span>
                            <span><i class="fas fa-calendar mr-1"></i>{{ $version->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No versions</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('documents.hse-documents.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-file-alt text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Documents</h3>
                    <p class="text-sm text-gray-500">Manage HSE documents</p>
                </div>
            </div>
        </a>
        <a href="{{ route('documents.versions.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-code-branch text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Versions</h3>
                    <p class="text-sm text-gray-500">Document version control</p>
                </div>
            </div>
        </a>
        <a href="{{ route('documents.templates.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Templates</h3>
                    <p class="text-sm text-gray-500">Document templates library</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

