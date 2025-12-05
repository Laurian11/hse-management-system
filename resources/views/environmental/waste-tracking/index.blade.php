@extends('layouts.app')

@section('title', 'Waste Tracking Records')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Waste Tracking Records</h1>
                <p class="text-sm text-gray-500 mt-1">Track waste movement and disposal</p>
            </div>
            <div>
                <a href="{{ route('environmental.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('environmental.waste-tracking.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Record
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
                <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('environmental.waste-tracking.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Waste Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Volume</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($records as $record)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $record->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $record->tracking_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $record->waste_type)) }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $record->volume }} {{ $record->unit }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]">
                                {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('environmental.waste-tracking.show', $record) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('environmental.waste-tracking.edit', $record) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $records->links() }}</div>
</div>
@endsection

