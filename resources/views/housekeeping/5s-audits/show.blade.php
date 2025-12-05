@extends('layouts.app')

@section('title', '5S Audit: ' . $audit->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('housekeeping.5s-audits.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $audit->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $audit->area }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('housekeeping.5s-audits.edit', $audit) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">5S Audit Scores</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sort (Seiri)</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->sort_score ?? 'N/A' }}/100</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Set in Order (Seiton)</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->set_score ?? 'N/A' }}/100</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Shine (Seiso)</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->shine_score ?? 'N/A' }}/100</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Standardize (Seiketsu)</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->standardize_score ?? 'N/A' }}/100</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sustain (Shitsuke)</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->sustain_score ?? 'N/A' }}/100</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Score</dt>
                        <dd class="mt-1 text-sm font-bold text-black">{{ number_format($audit->total_score ?? 0, 1) }}/100</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Overall Rating</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $audit->overall_rating == 'excellent' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $audit->overall_rating == 'good' ? 'bg-[#F5F5F5] text-green-600 border-green-600' : '' }}
                                {{ $audit->overall_rating == 'needs_improvement' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $audit->overall_rating ?? 'N/A')) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $audit->status == 'completed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $audit->status == 'in_progress' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $audit->status)) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Audit Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Area</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->area }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Audit Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->audit_date->format('M d, Y') }}</dd>
                    </div>
                    @if($audit->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->department->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Audited By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->auditedBy->name ?? 'N/A' }}</dd>
                    </div>
                    @if($audit->notes)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->created_at->format('M d, Y') }}</dd>
                    </div>
                    @if($audit->next_audit_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Next Audit Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $audit->next_audit_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

