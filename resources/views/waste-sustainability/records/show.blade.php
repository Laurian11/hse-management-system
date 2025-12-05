@extends('layouts.app')

@section('title', 'Waste & Sustainability Record: ' . $record->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('waste-sustainability.records.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $record->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $record->title }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('waste-sustainability.records.edit', $record) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Record Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm text-black">{{ $record->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Record Type</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $record->record_type)) }}</dd>
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
                    @if($record->quantity)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                        <dd class="mt-1 text-sm text-black">{{ $record->quantity }} {{ $record->unit ?? '' }}</dd>
                    </div>
                    @endif
                    @if($record->waste_category)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Waste Category</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($record->waste_category) }}</dd>
                    </div>
                    @endif
                    @if($record->disposal_method)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Disposal Method</dt>
                        <dd class="mt-1 text-sm text-black">{{ ucfirst($record->disposal_method) }}</dd>
                    </div>
                    @endif
                    @if($record->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $record->description }}</dd>
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

