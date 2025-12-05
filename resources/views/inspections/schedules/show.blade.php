@extends('layouts.app')

@section('title', 'Inspection Schedule: ' . $schedule->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.schedules.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $schedule->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $schedule->title }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('inspections.schedules.edit', $schedule) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Schedule Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm text-black">{{ $schedule->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Frequency</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($schedule->frequency) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $schedule->start_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">End Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $schedule->end_date ? $schedule->end_date->format('M d, Y') : 'Ongoing' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Next Inspection Date</dt>
                        <dd class="mt-1 text-sm text-black {{ $schedule->next_inspection_date < now() ? 'text-[#FF9900]' : '' }}">
                            {{ $schedule->next_inspection_date->format('M d, Y') }}
                            @if($schedule->next_inspection_date < now())
                                <span class="ml-2 text-xs">(Due)</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $schedule->is_active ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500 border-gray-300' }}">
                                {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                    @if($schedule->assignedTo)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                        <dd class="mt-1 text-sm text-black">{{ $schedule->assignedTo->name }}</dd>
                    </div>
                    @endif
                    @if($schedule->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $schedule->department->name }}</dd>
                    </div>
                    @endif
                    @if($schedule->checklist)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Checklist</dt>
                        <dd class="mt-1 text-sm text-black">{{ $schedule->checklist->name }}</dd>
                    </div>
                    @endif
                    @if($schedule->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $schedule->description }}</dd>
                    </div>
                    @endif
                    @if($schedule->notes)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-black">{{ $schedule->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Related Inspections -->
            @if($schedule->inspections->count() > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Related Inspections ({{ $schedule->inspections->count() }})</h2>
                <div class="space-y-3">
                    @foreach($schedule->inspections->take(10) as $inspection)
                        <a href="{{ route('inspections.show', $inspection) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-black">{{ $inspection->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $inspection->reference_number }} - {{ $inspection->inspection_date->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $inspection->overall_status == 'compliant' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                    {{ $inspection->overall_status == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $inspection->overall_status)) }}
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

