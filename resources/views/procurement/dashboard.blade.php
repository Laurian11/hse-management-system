@extends('layouts.app')

@section('title', 'Procurement & Resource Management Dashboard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Procurement & Resource Management Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Acquisition and tracking of safety-related assets</p>
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
                    <p class="text-xs md:text-sm text-gray-500">Total Requests</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_requests'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Pending Approval</p>
                    <p class="text-xl md:text-3xl font-bold text-[#FF9900] mt-1 md:mt-2">{{ $stats['pending_approval'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-clock text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Active Suppliers</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['active_suppliers'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-truck text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Expired Certifications</p>
                    <p class="text-xl md:text-3xl font-bold text-[#CC0000] mt-1 md:mt-2">{{ $stats['expired_certifications'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-certificate text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Unresolved Gaps</p>
                    <p class="text-xl md:text-3xl font-bold text-[#FF9900] mt-1 md:mt-2">{{ $stats['unresolved_gaps'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Requests -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Procurement Requests</h3>
            <div class="space-y-3">
                @forelse($recentRequests as $request)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $request->item_name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $request->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $request->status == 'approved' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $request->status == 'pending_approval' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $request->status == 'rejected' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-user mr-1"></i>{{ $request->requestedBy->name ?? 'N/A' }}</span>
                            <span><i class="fas fa-box mr-1"></i>Qty: {{ $request->quantity }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No procurement requests</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Gaps -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Recent Gap Analyses</h3>
            <div class="space-y-3">
                @forelse($recentGaps as $gap)
                    <div class="border border-gray-300 p-4 hover:bg-[#F5F5F5] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $gap->required_material }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $gap->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $gap->priority == 'critical' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $gap->priority == 'high' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst($gap->priority) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $gap->analysis_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-exclamation-triangle mr-1"></i>Gap: {{ $gap->gap_quantity ?? 'N/A' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No gap analyses</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('procurement.requests.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Procurement Requests</h3>
                    <p class="text-sm text-gray-500">Request & approve purchases</p>
                </div>
            </div>
        </a>
        <a href="{{ route('procurement.suppliers.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-truck text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Suppliers</h3>
                    <p class="text-sm text-gray-500">Manage supplier database</p>
                </div>
            </div>
        </a>
        <a href="{{ route('procurement.gap-analysis.index') }}" class="bg-white border border-gray-300 p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-black text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-black">Gap Analysis</h3>
                    <p class="text-sm text-gray-500">Identify material gaps</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection

