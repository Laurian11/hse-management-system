@extends('layouts.app')

@section('title', 'Work Permit Type Details')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('work-permits.types.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $workPermitType->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $workPermitType->code }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('work-permits.types.edit', $workPermitType) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermitType->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Code</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermitType->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $workPermitType->is_active ? 'bg-[#F5F5F5] text-black' : 'bg-[#F5F5F5] text-gray-500' }}">
                                {{ $workPermitType->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                    @if($workPermitType->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermitType->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Validity Settings -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Validity Settings</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Default Validity</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermitType->default_validity_hours }} hours</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Maximum Validity</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermitType->max_validity_hours }} hours</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Approval Levels</dt>
                        <dd class="mt-1 text-sm text-black">{{ $workPermitType->approval_levels }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Safety Requirements -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Safety Requirements</h2>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <i class="fas {{ $workPermitType->requires_risk_assessment ? 'fa-check text-[#0066CC]' : 'fa-times text-gray-400' }} mr-2"></i>
                        <span class="text-sm text-black">Requires Risk Assessment</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas {{ $workPermitType->requires_jsa ? 'fa-check text-[#0066CC]' : 'fa-times text-gray-400' }} mr-2"></i>
                        <span class="text-sm text-black">Requires JSA</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas {{ $workPermitType->requires_gas_test ? 'fa-check text-[#0066CC]' : 'fa-times text-gray-400' }} mr-2"></i>
                        <span class="text-sm text-black">Requires Gas Test</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas {{ $workPermitType->requires_fire_watch ? 'fa-check text-[#0066CC]' : 'fa-times text-gray-400' }} mr-2"></i>
                        <span class="text-sm text-black">Requires Fire Watch</span>
                    </div>
                </div>
            </div>

            <!-- Associated Permits -->
            @if($workPermitType->workPermits->count() > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Associated Permits ({{ $workPermitType->workPermits->count() }})</h2>
                <div class="space-y-2">
                    @foreach($workPermitType->workPermits->take(10) as $permit)
                        <a href="{{ route('work-permits.show', $permit) }}" class="block border border-gray-300 p-3 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-black">{{ $permit->reference_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $permit->work_title }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $permit->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                    {{ $permit->status == 'closed' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}">
                                    {{ ucfirst($permit->status) }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

