@extends('layouts.app')

@section('title', 'Environmental Management Dashboard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Environmental Management Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Monitor environmental performance and compliance</p>
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
                    <p class="text-xs md:text-sm text-gray-500">Waste Records</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_waste_records'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-trash-alt text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Spill Incidents</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_spills'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Open Spills</p>
                    <p class="text-xl md:text-3xl font-bold text-[#CC0000] mt-1 md:mt-2">{{ $stats['open_spills'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-tint text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Compliant Emissions</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['compliant_emissions'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-wind text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">ISO Compliant</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['iso_compliant'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-certificate text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Spills -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Spill Incidents</h3>
            <div class="space-y-3">
                @forelse($recentSpills as $spill)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $spill->location }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $spill->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $spill->severity == 'major' || $spill->severity == 'catastrophic' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' }}">
                                {{ ucfirst($spill->severity) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $spill->incident_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-tag mr-1"></i>{{ ucfirst(str_replace('_', ' ', $spill->spill_type)) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No spill incidents recorded</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Emissions -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Emission Monitoring</h3>
            <div class="space-y-3">
                @forelse($recentEmissions as $emission)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $emission->parameter ?? 'N/A' }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $emission->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $emission->compliance_status == 'compliant' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' }}">
                                {{ ucfirst(str_replace('_', ' ', $emission->compliance_status)) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $emission->monitoring_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $emission->location ?? 'N/A' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No emission records</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('environmental.waste-management.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-trash-alt text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Waste Management</h3>
                    <p class="text-sm text-gray-500">Manage waste records</p>
                </div>
            </div>
        </a>
        <a href="{{ route('environmental.spills.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-tint text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Spill Incidents</h3>
                    <p class="text-sm text-gray-500">Report and track spills</p>
                </div>
            </div>
        </a>
        <a href="{{ route('environmental.emissions.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-wind text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Emission Monitoring</h3>
                    <p class="text-sm text-gray-500">Monitor emissions</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

