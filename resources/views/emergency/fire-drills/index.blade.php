@extends('layouts.app')

@section('title', 'Fire Drills')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Fire Drills</h1>
                <p class="text-sm text-gray-500 mt-1">Manage fire drill records</p>
            </div>
            <div>
                <a href="{{ route('emergency.fire-drills.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Fire Drill
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Filters -->
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search fire drills..."
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="drill_type" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Types</option>
                <option value="announced" {{ request('drill_type') == 'announced' ? 'selected' : '' }}>Announced</option>
                <option value="unannounced" {{ request('drill_type') == 'unannounced' ? 'selected' : '' }}>Unannounced</option>
                <option value="partial" {{ request('drill_type') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="full" {{ request('drill_type') == 'full' ? 'selected' : '' }}>Full</option>
            </select>
            <input type="date" name="drill_date_from" value="{{ request('drill_date_from') }}"
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('emergency.fire-drills.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <!-- Fire Drills Table -->
    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Participants</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Result</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($fireDrills as $drill)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">{{ $drill->reference_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                            {{ $drill->drill_date->format('M d, Y') }} {{ $drill->drill_time }}
                        </td>
                        <td class="px-6 py-4 text-sm text-black">{{ $drill->location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ ucfirst($drill->drill_type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $drill->total_participants }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($drill->overall_result)
                                <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                    {{ $drill->overall_result == 'excellent' || $drill->overall_result == 'good' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                    {{ $drill->overall_result == 'needs_improvement' || $drill->overall_result == 'poor' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $drill->overall_result)) }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('emergency.fire-drills.show', $drill) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('emergency.fire-drills.edit', $drill) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No fire drills found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $fireDrills->links() }}
    </div>
</div>
@endsection

