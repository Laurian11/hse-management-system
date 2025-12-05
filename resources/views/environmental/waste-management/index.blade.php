@extends('layouts.app')

@section('title', 'Waste Management Records')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Waste Management Records</h1>
                <p class="text-sm text-gray-500 mt-1">Manage waste segregation, storage, and disposal</p>
            </div>
            <div>
                <a href="{{ route('environmental.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('environmental.waste-management.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Record
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Filters -->
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search records..."
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="waste_type" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Types</option>
                <option value="hazardous" {{ request('waste_type') == 'hazardous' ? 'selected' : '' }}>Hazardous</option>
                <option value="non_hazardous" {{ request('waste_type') == 'non_hazardous' ? 'selected' : '' }}>Non-Hazardous</option>
                <option value="recyclable" {{ request('waste_type') == 'recyclable' ? 'selected' : '' }}>Recyclable</option>
            </select>
            <select name="segregation_status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="properly_segregated" {{ request('segregation_status') == 'properly_segregated' ? 'selected' : '' }}>Properly Segregated</option>
                <option value="improperly_segregated" {{ request('segregation_status') == 'improperly_segregated' ? 'selected' : '' }}>Improperly Segregated</option>
                <option value="mixed" {{ request('segregation_status') == 'mixed' ? 'selected' : '' }}>Mixed</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('environmental.waste-management.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <!-- Records Table -->
    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Waste Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Disposal Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Segregation</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($records as $record)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">{{ $record->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $record->waste_type)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                            {{ $record->quantity ?? 'N/A' }} {{ $record->unit ?? '' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                            {{ $record->disposal_date ? $record->disposal_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $record->segregation_status == 'properly_segregated' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $record->segregation_status == 'improperly_segregated' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $record->segregation_status == 'mixed' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $record->segregation_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('environmental.waste-management.show', $record) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('environmental.waste-management.edit', $record) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No waste management records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $records->links() }}
    </div>
</div>
@endsection

