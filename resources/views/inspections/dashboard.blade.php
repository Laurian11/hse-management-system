@extends('layouts.app')

@section('title', 'Inspection & Audit Dashboard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Inspection & Audit Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Overview of inspections, audits, and compliance</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-6 mb-8">
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Inspections</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_inspections'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Compliant</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['compliant_inspections'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-check-circle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Non-Compliant</p>
                    <p class="text-xl md:text-3xl font-bold text-[#CC0000] mt-1 md:mt-2">{{ $stats['non_compliant_inspections'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Open NCRs</p>
                    <p class="text-xl md:text-3xl font-bold text-[#FF9900] mt-1 md:mt-2">{{ $stats['open_ncrs'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-file-alt text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Active Audits</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['in_progress_audits'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-search text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Inspections -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Inspections</h3>
            <div class="space-y-3">
                @forelse($recentInspections as $inspection)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $inspection->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $inspection->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $inspection->overall_status == 'compliant' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $inspection->overall_status == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $inspection->overall_status)) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $inspection->inspection_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-user mr-1"></i>{{ $inspection->inspectedBy->name }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent inspections</p>
                @endforelse
            </div>
        </div>

        <!-- Recent NCRs -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Non-Conformance Reports</h3>
            <div class="space-y-3">
                @forelse($recentNCRs as $ncr)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $ncr->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $ncr->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $ncr->severity == 'critical' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $ncr->severity == 'major' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst($ncr->severity) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $ncr->identified_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-user mr-1"></i>{{ $ncr->identifiedBy->name }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent NCRs</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

