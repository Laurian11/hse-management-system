@extends('layouts.app')

@section('title', 'Emergency Preparedness Dashboard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Emergency Preparedness Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Overview of emergency response readiness</p>
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
                    <p class="text-xs md:text-sm text-gray-500">Fire Drills (YTD)</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['drills_this_year'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-fire text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Active Contacts</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['active_contacts'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-phone text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Equipment Due</p>
                    <p class="text-xl md:text-3xl font-bold text-[#FF9900] mt-1 md:mt-2">{{ $stats['equipment_due_inspection'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-tools text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Evacuation Plans</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['active_plans'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-map-marked-alt text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Response Teams</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['active_teams'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-users text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Fire Drills -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Fire Drills</h3>
            <div class="space-y-3">
                @forelse($recentFireDrills as $drill)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $drill->location }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $drill->reference_number }}</p>
                            </div>
                            @if($drill->overall_result)
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $drill->overall_result == 'excellent' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                    {{ $drill->overall_result == 'good' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                    {{ $drill->overall_result == 'needs_improvement' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $drill->overall_result)) }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $drill->drill_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-users mr-1"></i>{{ $drill->total_participants }} participants</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent fire drills</p>
                @endforelse
            </div>
        </div>

        <!-- Equipment Inspections -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Equipment Inspections</h3>
            <div class="space-y-3">
                @forelse($recentEquipmentInspections as $equipment)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $equipment->equipment_name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $equipment->location }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $equipment->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}">
                                {{ ucfirst($equipment->status) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>Last: {{ $equipment->last_inspection_date->format('M d, Y') }}</span>
                            @if($equipment->next_inspection_date)
                                <span><i class="fas fa-clock mr-1"></i>Next: {{ $equipment->next_inspection_date->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent equipment inspections</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

