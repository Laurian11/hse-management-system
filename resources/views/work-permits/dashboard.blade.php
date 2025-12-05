@extends('layouts.app')

@section('title', 'Permit to Work Dashboard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Permit to Work Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Manage work permits and GCLA compliance</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('work-permits.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3] transition-colors">
                    <i class="fas fa-plus mr-2"></i>New Permit
                </a>
                <a href="{{ route('work-permits.gca-logs.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3] transition-colors">
                    <i class="fas fa-clipboard-check mr-2"></i>New GCLA Log
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Total Permits</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_permits'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-file-alt text-black text-lg md:text-xl"></i>
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
                    <p class="text-xs md:text-sm text-gray-500">Active Permits</p>
                    <p class="text-xl md:text-3xl font-bold text-[#0066CC] mt-1 md:mt-2">{{ $stats['active_permits'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-check-circle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Expired Permits</p>
                    <p class="text-xl md:text-3xl font-bold text-[#CC0000] mt-1 md:mt-2">{{ $stats['expired_permits'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-times-circle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Closed Permits</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['closed_permits'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-archive text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Permit Types</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['total_types'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-list text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">GCLA Logs</p>
                    <p class="text-xl md:text-3xl font-bold text-black mt-1 md:mt-2">{{ $stats['gca_logs'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-3 md:p-6 hover:border-[#0066CC] transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-500">Non-Compliant GCLA</p>
                    <p class="text-xl md:text-3xl font-bold text-[#CC0000] mt-1 md:mt-2">{{ $stats['non_compliant_gca'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-[#F5F5F5] border border-gray-300 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-black text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Permits Chart -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Monthly Permits</h3>
            <div style="height: 300px;">
                <canvas id="monthlyPermitsChart"></canvas>
            </div>
        </div>

        <!-- Permit Type Distribution -->
        <div class="bg-white border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-black mb-4">Permit Type Distribution</h3>
            <div style="height: 300px;">
                <canvas id="typeDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Permits -->
        <div class="bg-white border border-gray-300 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-black">Recent Permits</h3>
                <a href="{{ route('work-permits.index') }}" class="text-sm text-[#0066CC] hover:underline">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentPermits ?? [] as $permit)
                    <a href="{{ route('work-permits.show', $permit) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $permit->work_title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $permit->reference_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $permit->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $permit->status == 'pending' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $permit->status == 'expired' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $permit->status == 'closed' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}">
                                {{ ucfirst($permit->status) }}
                            </span>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $permit->work_start_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $permit->work_location }}</span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-file-alt text-gray-300 text-4xl mb-2"></i>
                        <p class="text-gray-500">No recent permits</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white border border-gray-300 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-black">Pending Approvals</h3>
                <a href="{{ route('work-permits.index', ['status' => 'submitted']) }}" class="text-sm text-[#0066CC] hover:underline">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-3">
                @forelse($pendingApprovals ?? [] as $approval)
                    <a href="{{ route('work-permits.show', $approval->workPermit) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#0066CC] transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-medium text-black">{{ $approval->workPermit->work_title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">Level {{ $approval->approval_level }} - {{ $approval->approver->name }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]">
                                Pending
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-check-circle text-gray-300 text-4xl mb-2"></i>
                        <p class="text-gray-500">No pending approvals</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Expiring Soon -->
    @if(isset($expiringSoon) && $expiringSoon->count() > 0)
    <div class="bg-white border border-gray-300 p-6">
        <h3 class="text-lg font-semibold text-black mb-4">Permits Expiring Soon (24 hours)</h3>
        <div class="space-y-3">
            @foreach($expiringSoon as $permit)
                <a href="{{ route('work-permits.show', $permit) }}" class="block border border-gray-300 p-4 hover:bg-[#F5F5F5] hover:border-[#FF9900] transition-all border-l-4 border-l-[#FF9900]">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-medium text-black">{{ $permit->work_title }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $permit->reference_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-[#FF9900]">Expires: {{ $permit->expiry_date->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Permits Chart
    const monthlyCtx = document.getElementById('monthlyPermitsChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($monthLabels ?? []),
                datasets: [{
                    label: 'Permits',
                    data: @json($monthlyPermits ?? []),
                    borderColor: '#0066CC',
                    backgroundColor: 'rgba(0, 102, 204, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Permit Type Distribution Chart
    const typeCtx = document.getElementById('typeDistributionChart');
    if (typeCtx) {
        const typeData = @json($typeDistribution ?? []);
        const typeLabels = Object.keys(typeData);
        const typeValues = Object.values(typeData);

        if (typeLabels.length > 0) {
            new Chart(typeCtx, {
                type: 'doughnut',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        data: typeValues,
                        backgroundColor: [
                            '#0066CC',
                            '#FF9900',
                            '#CC0000',
                            '#000000',
                            '#666666',
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
@endsection

