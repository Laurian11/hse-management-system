@extends('layouts.app')

@section('title', 'Carbon Footprint Record: ' . $record->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('waste-sustainability.carbon-footprint.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $record->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $record->source_name }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('waste-sustainability.carbon-footprint.edit', $record) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Carbon Footprint Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Source Name</dt>
                        <dd class="mt-1 text-sm text-black">{{ $record->source_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Source Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($record->source_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Record Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $record->record_date->format('M d, Y') }}</dd>
                    </div>
                    @if($record->location)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-black">{{ $record->location }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Consumption</dt>
                        <dd class="mt-1 text-sm text-black">{{ number_format($record->consumption, 2) }} {{ $record->consumption_unit }}</dd>
                    </div>
                    @if($record->carbon_equivalent)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Carbon Equivalent (CO₂e)</dt>
                        <dd class="mt-1 text-sm font-bold text-black">{{ number_format($record->carbon_equivalent, 2) }} CO₂e</dd>
                    </div>
                    @endif
                    @if($record->emission_factor)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Emission Factor</dt>
                        <dd class="mt-1 text-sm text-black">{{ number_format($record->emission_factor, 4) }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-300 p-6">
                <h3 class="text-lg font-semibold text-black mb-4">Details</h3>
                <dl class="space-y-3">
                    @if($record->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $record->department->name }}</dd>
                    </div>
                    @endif
                    @if($record->recordedBy)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Recorded By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $record->recordedBy->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $record->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

