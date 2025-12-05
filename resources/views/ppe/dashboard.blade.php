@extends('layouts.app')

@section('title', 'PPE Management Dashboard')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">PPE Management Dashboard</h1>
                <p class="text-sm text-gray-600 mt-1">Track and manage Personal Protective Equipment</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('ppe.items.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>New Item
                </a>
                <a href="{{ route('ppe.issuances.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-hand-holding mr-2"></i>Issue PPE
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-600">Total Items</p>
                    <p class="text-xl md:text-3xl font-bold text-gray-900 mt-1 md:mt-2">{{ $stats['total_items'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-boxes text-blue-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-600">Low Stock Items</p>
                    <p class="text-xl md:text-3xl font-bold text-red-600 mt-1 md:mt-2">{{ $stats['low_stock_items'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-600">Active Issuances</p>
                    <p class="text-xl md:text-3xl font-bold text-green-600 mt-1 md:mt-2">{{ $stats['active_issuances'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-holding text-green-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-600">Expiring Soon</p>
                    <p class="text-xl md:text-3xl font-bold text-orange-600 mt-1 md:mt-2">{{ $stats['expiring_soon'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-600">Expired Issuances</p>
                    <p class="text-xl md:text-3xl font-bold text-red-600 mt-1 md:mt-2">{{ $stats['expired_issuances'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-600">Overdue Inspections</p>
                    <p class="text-xl md:text-3xl font-bold text-yellow-600 mt-1 md:mt-2">{{ $stats['overdue_inspections'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-search text-yellow-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-600">Non-Compliant</p>
                    <p class="text-xl md:text-3xl font-bold text-red-600 mt-1 md:mt-2">{{ $stats['non_compliant'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-red-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-3 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs md:text-sm text-gray-600">Total Suppliers</p>
                    <p class="text-xl md:text-3xl font-bold text-teal-600 mt-1 md:mt-2">{{ $stats['total_suppliers'] }}</p>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-truck text-teal-600 text-lg md:text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Low Stock Items -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Low Stock Items</h2>
            </div>
            <div class="p-6">
                @if($lowStockItems->count() > 0)
                    <div class="space-y-4">
                        @foreach($lowStockItems as $item)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $item->category }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-red-600">{{ $item->available_quantity }} / {{ $item->minimum_stock_level }}</p>
                                    <p class="text-xs text-gray-500">Available / Min</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No low stock items</p>
                @endif
            </div>
        </div>

        <!-- Expiring Soon -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Expiring Soon</h2>
            </div>
            <div class="p-6">
                @if($expiringSoon->count() > 0)
                    <div class="space-y-4">
                        @foreach($expiringSoon as $issuance)
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $issuance->ppeItem->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $issuance->issuedTo->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-orange-600">{{ $issuance->replacement_due_date->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">Due Date</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No items expiring soon</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Issuances Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Monthly Issuances</h2>
            <div class="relative" style="height: 250px;">
                <canvas id="monthlyIssuancesChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Items by Category</h2>
            <div class="relative" style="height: 250px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Issuances -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">Recent Issuances</h2>
                <a href="{{ route('ppe.issuances.index') }}" class="text-sm text-teal-600 hover:text-teal-700">View All</a>
            </div>
            <div class="p-6">
                @if($recentIssuances->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentIssuances as $issuance)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $issuance->ppeItem->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $issuance->issuedTo->name }} • {{ $issuance->issue_date->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $issuance->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($issuance->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent issuances</p>
                @endif
            </div>
        </div>

        <!-- Recent Inspections -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">Recent Inspections</h2>
                <a href="{{ route('ppe.inspections.index') }}" class="text-sm text-teal-600 hover:text-teal-700">View All</a>
            </div>
            <div class="p-6">
                @if($recentInspections->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentInspections as $inspection)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $inspection->ppeItem->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $inspection->inspection_date->format('M d, Y') }} • {{ ucfirst($inspection->condition) }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $inspection->is_compliant ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $inspection->is_compliant ? 'Compliant' : 'Non-Compliant' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent inspections</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Issuances Chart
    const monthlyCtx = document.getElementById('monthlyIssuancesChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($monthLabels),
                datasets: [{
                    label: 'Issuances',
                    data: @json($monthlyIssuances),
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
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
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        const categoryData = @json($categoryDistribution);
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(categoryData),
                datasets: [{
                    data: Object.values(categoryData),
                    backgroundColor: [
                        '#14b8a6',
                        '#3b82f6',
                        '#8b5cf6',
                        '#ec4899',
                        '#f59e0b',
                        '#ef4444'
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
});
</script>
@endsection


