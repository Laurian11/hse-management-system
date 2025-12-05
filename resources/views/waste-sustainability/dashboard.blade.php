@extends('layouts.app')

@section('title', 'Waste & Sustainability Dashboard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Waste & Sustainability Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Expands environmental management to cover sustainability</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Records</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_records'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-list text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Recycling Records</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['recycling_records'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-recycle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Monthly Carbon (CO₂e)</p>
                    <p class="text-xl md:text-3xl font-bold text-[#FF9900] mt-1 md:mt-2">{{ number_format($stats['monthly_carbon'] ?? 0, 2) }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-cloud text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Yearly Carbon (CO₂e)</p>
                    <p class="text-xl md:text-3xl font-bold text-[#CC0000] mt-1 md:mt-2">{{ number_format($stats['yearly_carbon'] ?? 0, 2) }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-globe text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Records -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Waste Records</h3>
            <div class="space-y-3">
                @forelse($recentRecords as $record)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $record->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $record->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 bg-[#F5F5F5] text-black">
                                {{ ucfirst(str_replace('_', ' ', $record->record_type)) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $record->record_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-weight mr-1"></i>{{ $record->quantity }} {{ $record->unit }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No waste records</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Carbon Records -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Carbon Footprint</h3>
            <div class="space-y-3">
                @forelse($recentCarbonRecords as $record)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $record->source_name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $record->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 bg-[#F5F5F5] text-black">
                                {{ ucfirst($record->source_type) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $record->record_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-cloud mr-1"></i>{{ number_format($record->carbon_equivalent ?? 0, 2) }} CO₂e</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No carbon records</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('waste-sustainability.records.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-list text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Waste Records</h3>
                    <p class="text-sm text-gray-500">Waste & sustainability records</p>
                </div>
            </div>
        </a>
        <a href="{{ route('waste-sustainability.carbon-footprint.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-cloud text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Carbon Footprint</h3>
                    <p class="text-sm text-gray-500">Carbon footprint calculator</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

