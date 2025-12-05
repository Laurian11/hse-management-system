@extends('layouts.app')

@section('title', 'Housekeeping Inspection: ' . $inspection->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('housekeeping.inspections.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $inspection->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $inspection->location }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('housekeeping.inspections.edit', $inspection) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Inspection Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->location }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Inspection Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->inspection_date->format('M d, Y') }}</dd>
                    </div>
                    @if($inspection->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->department->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Inspected By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->inspectedBy->name ?? 'N/A' }}</dd>
                    </div>
                    @if($inspection->overall_rating)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Overall Rating</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $inspection->overall_rating == 'excellent' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $inspection->overall_rating == 'good' ? 'bg-[#F5F5F5] text-green-600 border-green-600' : '' }}
                                {{ $inspection->overall_rating == 'needs_improvement' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $inspection->overall_rating)) }}
                            </span>
                        </dd>
                    </div>
                    @endif
                    @if($inspection->score)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Score</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->score }}/100</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $inspection->status == 'completed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $inspection->status == 'follow_up_required' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($inspection->findings)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Findings</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->findings }}</dd>
                    </div>
                    @endif
                    @if($inspection->recommendations)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Recommendations</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->recommendations }}</dd>
                    </div>
                    @endif
                    @if($inspection->corrective_actions)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Corrective Actions</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->corrective_actions }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <dl class="space-y-3">
                    @if($inspection->follow_up_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Follow-up Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->follow_up_date->format('M d, Y') }}</dd>
                    </div>
                    @endif
                    @if($inspection->followUpAssignee)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Follow-up Assigned To</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->followUpAssignee->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

