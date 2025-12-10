@extends('layouts.app')

@section('title', 'Inspection: ' . $inspection->reference_number)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $inspection->reference_number }}</h1>
                    <p class="text-sm text-gray-500">{{ $inspection->title }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('inspections.edit', $inspection) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
                <h2 class="text-lg font-semibold text-black mb-4">Inspection Information</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Title</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Inspection Date</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->inspection_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->location }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Inspected By</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->inspectedBy->name }}</dd>
                    </div>
                    @if($inspection->department)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->department->name }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Overall Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $inspection->overall_status == 'compliant' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $inspection->overall_status == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $inspection->overall_status == 'partial' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $inspection->overall_status)) }}
                            </span>
                        </dd>
                    </div>
                    @if($inspection->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Compliance Metrics -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Compliance Metrics</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-black">{{ $inspection->total_items }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total Items</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#0066CC]">{{ $inspection->compliant_items }}</p>
                        <p class="text-xs text-gray-500 mt-1">Compliant</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-[#CC0000]">{{ $inspection->non_compliant_items }}</p>
                        <p class="text-xs text-gray-500 mt-1">Non-Compliant</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-500">{{ $inspection->na_items }}</p>
                        <p class="text-xs text-gray-500 mt-1">N/A</p>
                    </div>
                </div>
            </div>

            <!-- Observations & Recommendations -->
            @if($inspection->observations || $inspection->recommendations)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Observations & Recommendations</h2>
                <dl class="space-y-4">
                    @if($inspection->observations)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Observations</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->observations }}</dd>
                    </div>
                    @endif
                    @if($inspection->recommendations)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Recommendations</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->recommendations }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endif

            <!-- Follow-up -->
            @if($inspection->requires_follow_up)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Follow-up</h2>
                <dl class="space-y-4">
                    @if($inspection->followUpAssignedTo)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                        <dd class="mt-1 text-sm text-black">{{ $inspection->followUpAssignedTo->name }}</dd>
                    </div>
                    @endif
                    @if($inspection->follow_up_date)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Follow-up Date</dt>
                        <dd class="mt-1 text-sm text-black {{ $inspection->follow_up_date < now() && !$inspection->follow_up_completed ? 'text-[#CC0000]' : '' }}">
                            {{ $inspection->follow_up_date->format('M d, Y') }}
                            @if($inspection->follow_up_date < now() && !$inspection->follow_up_completed)
                                <span class="ml-2 text-xs">(Overdue)</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $inspection->follow_up_completed ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' }}">
                                {{ $inspection->follow_up_completed ? 'Completed' : 'Pending' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
            @endif

            <!-- Related NCRs -->
            @if($inspection->nonConformanceReports->count() > 0)
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Related Non-Conformance Reports ({{ $inspection->nonConformanceReports->count() }})</h2>
                <div class="space-y-3">
                    @foreach($inspection->nonConformanceReports as $ncr)
                        <a href="{{ route('inspections.ncrs.show', $ncr) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-black">{{ $ncr->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $ncr->reference_number }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $ncr->severity == 'critical' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                    {{ $ncr->severity == 'major' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                    {{ ucfirst($ncr->severity) }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- QR Code -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">
                    <i class="fas fa-qrcode mr-2"></i>QR Code
                </h2>
                @php
                    $qrData = \App\Services\QRCodeService::forInspection($inspection->id, $inspection->reference_number);
                    $qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 200);
                @endphp
                <div class="text-center">
                    <img src="{{ $qrUrl }}" alt="QR Code" class="mx-auto mb-4 border-2 border-gray-200 p-2 rounded">
                    <p class="text-xs text-gray-500 mb-2">Scan to view this inspection</p>
                    <a href="{{ route('qr.printable', ['type' => 'inspection', 'id' => $inspection->id]) }}" 
                       target="_blank" 
                       class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                        <i class="fas fa-print mr-2"></i>Print QR Code
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white border border-gray-300 p-6">
                <h2 class="text-lg font-semibold text-black mb-4">Quick Actions</h2>
                <div class="space-y-2">
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

