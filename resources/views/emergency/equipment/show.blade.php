@extends('layouts.app')

@section('title', 'Emergency Equipment: ' . $equipment->equipment_name)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.equipment.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $equipment->equipment_name }}</h1>
                    <p class="text-sm text-gray-500">{{ $equipment->location }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('emergency.equipment.edit', $equipment) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-6">
        <h2 class="text-lg font-semibold text-black mb-4">Equipment Details</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Equipment Name</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->equipment_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Type</dt>
                <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $equipment->equipment_type)) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Location</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->location }}</dd>
            </div>
            @if($equipment->serial_number)
            <div>
                <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->serial_number }}</dd>
            </div>
            @endif
            @if($equipment->manufacturer)
            <div>
                <dt class="text-sm font-medium text-gray-500">Manufacturer</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->manufacturer }}</dd>
            </div>
            @endif
            @if($equipment->model)
            <div>
                <dt class="text-sm font-medium text-gray-500">Model</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->model }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                        {{ $equipment->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                        {{ $equipment->status == 'maintenance' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                        {{ $equipment->status == 'inactive' ? 'bg-[#F5F5F5] text-gray-500 border-gray-300' : '' }}">
                        {{ ucfirst($equipment->status) }}
                    </span>
                </dd>
            </div>
            @if($equipment->condition)
            <div>
                <dt class="text-sm font-medium text-gray-500">Condition</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->condition }}</dd>
            </div>
            @endif
            @if($equipment->purchase_date)
            <div>
                <dt class="text-sm font-medium text-gray-500">Purchase Date</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->purchase_date->format('M d, Y') }}</dd>
            </div>
            @endif
            @if($equipment->expiry_date)
            <div>
                <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                <dd class="mt-1 text-sm text-black {{ $equipment->expiry_date < now() ? 'text-[#CC0000]' : '' }}">
                    {{ $equipment->expiry_date->format('M d, Y') }}
                    @if($equipment->expiry_date < now())
                        <span class="ml-2 text-xs">(Expired)</span>
                    @endif
                </dd>
            </div>
            @endif
            @if($equipment->last_inspection_date)
            <div>
                <dt class="text-sm font-medium text-gray-500">Last Inspection</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->last_inspection_date->format('M d, Y') }}</dd>
            </div>
            @endif
            @if($equipment->next_inspection_date)
            <div>
                <dt class="text-sm font-medium text-gray-500">Next Inspection</dt>
                <dd class="mt-1 text-sm text-black {{ $equipment->next_inspection_date < now() ? 'text-[#FF9900]' : '' }}">
                    {{ $equipment->next_inspection_date->format('M d, Y') }}
                    @if($equipment->next_inspection_date < now())
                        <span class="ml-2 text-xs">(Due)</span>
                    @endif
                </dd>
            </div>
            @endif
            @if($equipment->inspection_frequency)
            <div>
                <dt class="text-sm font-medium text-gray-500">Inspection Frequency</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->inspection_frequency }}</dd>
            </div>
            @endif
            @if($equipment->inspectedBy)
            <div>
                <dt class="text-sm font-medium text-gray-500">Last Inspected By</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->inspectedBy->name }}</dd>
            </div>
            @endif
            @if($equipment->inspection_notes)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Inspection Notes</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->inspection_notes }}</dd>
            </div>
            @endif
            @if($equipment->notes)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-black">{{ $equipment->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>
@endsection

