@extends('layouts.app')

@section('title', 'PPE Inspection Details')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('ppe.inspections.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Inspection: {{ $inspection->reference_number }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inspection->reference_number }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">PPE Item</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inspection->ppeItem->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">User</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inspection->user->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Inspection Date</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inspection->inspection_date->format('M d, Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Inspection Type</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $inspection->inspection_type)) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Condition</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($inspection->condition) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Action Taken</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($inspection->action_taken) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Compliance</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs rounded-full {{ $inspection->is_compliant ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $inspection->is_compliant ? 'Compliant' : 'Non-Compliant' }}
                    </span>
                </dd>
            </div>
            @if($inspection->findings)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Findings</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inspection->findings }}</dd>
            </div>
            @endif
            @if($inspection->defects)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Defects</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inspection->defects }}</dd>
            </div>
            @endif
            @if($inspection->defect_photos && count($inspection->defect_photos) > 0)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500 mb-2">Defect Photos</dt>
                <dd class="mt-1">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($inspection->defect_photos as $photo)
                            <a href="{{ Storage::url($photo) }}" target="_blank" class="block">
                                <img src="{{ Storage::url($photo) }}" alt="Defect photo" class="w-full h-32 object-cover rounded-lg border border-gray-200 hover:border-teal-500 transition-colors">
                            </a>
                        @endforeach
                    </div>
                </dd>
            </div>
            @endif
        </dl>
    </div>
</div>
@endsection

