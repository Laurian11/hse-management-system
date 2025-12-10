@extends('layouts.app')

@section('title', 'Inspection Checklist: ' . $checklist->name)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.checklists.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $checklist->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $checklist->category ?? 'Inspection Checklist' }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('inspections.checklists.edit', $checklist) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Checklist Details</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">Name</dt>
                <dd class="mt-1 text-sm text-black">{{ $checklist->name }}</dd>
            </div>
            @if($checklist->category)
            <div>
                <dt class="text-sm font-medium text-gray-500">Category</dt>
                <dd class="mt-1 text-sm text-black">{{ $checklist->category }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $checklist->is_active ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500 border-gray-300' }}">
                        {{ $checklist->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Total Items</dt>
                <dd class="mt-1 text-sm text-black">{{ count($checklist->items ?? []) }} items</dd>
            </div>
            @if($checklist->description)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-black">{{ $checklist->description }}</dd>
            </div>
            @endif
        </dl>

        <!-- Checklist Items -->
        <div class="mt-6">
            <h3 class="text-md font-semibold text-black mb-4">Checklist Items</h3>
            <div class="space-y-2">
                @forelse($checklist->items ?? [] as $index => $item)
                    <div class="border border-gray-300 p-3">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-black">{{ $index + 1 }}. {{ $item['item'] ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500 mt-1">Type: {{ ucfirst(str_replace('_', ' ', $item['type'] ?? 'N/A')) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No items in this checklist.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- QR Code -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">
                <i class="fas fa-qrcode mr-2"></i>QR Code
            </h2>
            @php
                $qrData = \App\Services\QRCodeService::forInspectionChecklist($checklist->id, $checklist->name);
                $qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 200);
            @endphp
            <div class="text-center">
                <img src="{{ $qrUrl }}" alt="QR Code" class="mx-auto mb-4 border-2 border-gray-200 p-2 rounded">
                <p class="text-xs text-gray-500 mb-2">Scan to use this checklist</p>
                <a href="{{ route('qr.printable', ['type' => 'inspection-checklist', 'id' => $checklist->id]) }}" 
                   target="_blank" 
                   class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
                    <i class="fas fa-print mr-2"></i>Print QR Code
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Quick Actions</h2>
            <div class="space-y-2">
                <a href="{{ route('inspections.create', ['checklist_id' => $checklist->id]) }}" 
                   class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-clipboard-check mr-2"></i>Use Checklist
                </a>
                <a href="{{ route('qr.scanner') }}" 
                   class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-qrcode mr-2"></i>Scan QR Code
                </a>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

