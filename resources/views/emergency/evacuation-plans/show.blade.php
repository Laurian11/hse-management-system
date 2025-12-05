@extends('layouts.app')

@section('title', 'Evacuation Plan: ' . $plan->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.evacuation-plans.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $plan->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $plan->title }} - {{ $plan->location }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('emergency.evacuation-plans.edit', $plan) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-6">
        <h2 class="text-lg font-semibold text-black mb-4">Plan Details</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Title</dt>
                <dd class="mt-1 text-sm text-black">{{ $plan->title }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Location</dt>
                <dd class="mt-1 text-sm text-black">{{ $plan->location }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Plan Type</dt>
                <dd class="mt-1 text-sm text-black">{{ ucfirst($plan->plan_type) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $plan->is_active ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500 border-gray-300' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
            @if($plan->next_review_date)
            <div>
                <dt class="text-sm font-medium text-gray-500">Next Review Date</dt>
                <dd class="mt-1 text-sm text-black {{ $plan->next_review_date < now() ? 'text-[#FF9900]' : '' }}">
                    {{ $plan->next_review_date->format('M d, Y') }}
                    @if($plan->next_review_date < now())
                        <span class="ml-2 text-xs">(Due)</span>
                    @endif
                </dd>
            </div>
            @endif
            @if($plan->description)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-black">{{ $plan->description }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <!-- Procedures -->
    @if($plan->evacuation_procedures || $plan->accountability_procedures || $plan->special_needs_procedures)
    <div class="bg-white border border-gray-300 p-6 mt-6">
        <h2 class="text-lg font-semibold text-black mb-4">Procedures</h2>
        <dl class="space-y-4">
            @if($plan->evacuation_procedures)
            <div>
                <dt class="text-sm font-medium text-gray-500">Evacuation Procedures</dt>
                <dd class="mt-1 text-sm text-black">{{ $plan->evacuation_procedures }}</dd>
            </div>
            @endif
            @if($plan->accountability_procedures)
            <div>
                <dt class="text-sm font-medium text-gray-500">Accountability Procedures</dt>
                <dd class="mt-1 text-sm text-black">{{ $plan->accountability_procedures }}</dd>
            </div>
            @endif
            @if($plan->special_needs_procedures)
            <div>
                <dt class="text-sm font-medium text-gray-500">Special Needs Procedures</dt>
                <dd class="mt-1 text-sm text-black">{{ $plan->special_needs_procedures }}</dd>
            </div>
            @endif
        </dl>
    </div>
    @endif
</div>
@endsection

