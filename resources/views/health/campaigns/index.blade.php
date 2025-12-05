@extends('layouts.app')

@section('title', 'Health Campaigns')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Health Campaigns</h1>
                <p class="text-sm text-gray-500 mt-1">Health campaigns & wellness programs</p>
            </div>
            <div>
                <a href="{{ route('health.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('health.campaigns.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Campaign
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('health.campaigns.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Start Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($campaigns as $campaign)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $campaign->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $campaign->campaign_title }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $campaign->campaign_type)) }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $campaign->start_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $campaign->status == 'completed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $campaign->status == 'ongoing' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('health.campaigns.show', $campaign) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('health.campaigns.edit', $campaign) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No campaigns found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $campaigns->links() }}</div>
</div>
@endsection

