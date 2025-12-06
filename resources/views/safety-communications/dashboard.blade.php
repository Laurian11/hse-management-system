@extends('layouts.app')

@section('title', 'Safety Communications Dashboard')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-bold text-black">Safety Communications Dashboard</h1>
            <a href="{{ route('safety-communications.create') }}" class="px-4 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                <i class="fas fa-plus mr-2"></i>New Communication
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white border border-gray-300 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Sent</p>
                    <p class="text-2xl font-bold text-black mt-1">{{ number_format($stats['total_sent']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-paper-plane text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-black mt-1">{{ number_format($stats['this_month']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Emergency Sent</p>
                    <p class="text-2xl font-bold text-black mt-1">{{ number_format($stats['emergency_sent']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Avg. Acknowledgment</p>
                    <p class="text-2xl font-bold text-black mt-1">{{ number_format($stats['avg_acknowledgment_rate'], 1) }}%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Communication Types Chart -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Communication Types</h2>
            <div class="space-y-3">
                @forelse($typeBreakdown as $type => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-[#0066CC]"></div>
                            <span class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $type) }}</span>
                        </div>
                        <span class="text-sm font-semibold text-black">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- Scheduled Communications -->
        <div class="bg-white border border-gray-300 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-black">Scheduled Communications</h2>
                <a href="{{ route('safety-communications.index', ['status' => 'scheduled']) }}" class="text-sm text-[#0066CC] hover:underline">View All</a>
            </div>
            <div class="space-y-3">
                @forelse($scheduledCommunications as $communication)
                    <div class="border-l-4 border-yellow-500 pl-4 py-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-black">{{ Str::limit($communication->title, 40) }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $communication->scheduled_send_time->format('M d, Y H:i') }}
                                </p>
                            </div>
                            <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded">
                                {{ ucfirst($communication->priority_level) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No scheduled communications</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Communications -->
    <div class="bg-white border border-gray-300 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-black">Recent Communications</h2>
            <a href="{{ route('safety-communications.index') }}" class="text-sm text-[#0066CC] hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentCommunications as $communication)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-black">{{ Str::limit($communication->title, 40) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded capitalize">
                                    {{ str_replace('_', ' ', $communication->communication_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs px-2 py-1 rounded
                                    @if($communication->priority_level === 'emergency') bg-red-100 text-red-800
                                    @elseif($communication->priority_level === 'high') bg-orange-100 text-orange-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($communication->priority_level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs px-2 py-1 rounded
                                    @if($communication->status === 'sent') bg-green-100 text-green-800
                                    @elseif($communication->status === 'scheduled') bg-yellow-100 text-yellow-800
                                    @elseif($communication->status === 'draft') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($communication->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $communication->creator->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $communication->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('safety-communications.show', $communication) }}" class="text-[#0066CC] hover:underline">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No communications found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

