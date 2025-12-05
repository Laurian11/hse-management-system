@extends('layouts.app')

@section('title', 'Waste & Sustainability Records')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Waste & Sustainability Records</h1>
                <p class="text-sm text-gray-500 mt-1">Recycling and waste segregation logs</p>
            </div>
            <div>
                <a href="{{ route('waste-sustainability.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('waste-sustainability.records.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Record
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="record_type" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Types</option>
                <option value="recycling" {{ request('record_type') == 'recycling' ? 'selected' : '' }}>Recycling</option>
                <option value="waste_segregation" {{ request('record_type') == 'waste_segregation' ? 'selected' : '' }}>Waste Segregation</option>
                <option value="composting" {{ request('record_type') == 'composting' ? 'selected' : '' }}>Composting</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From Date" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To Date" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('waste-sustainability.records.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Record Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Location</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($records as $record)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $record->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $record->title }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $record->record_type)) }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $record->record_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $record->quantity }} {{ $record->unit }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $record->location }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('waste-sustainability.records.show', $record) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('waste-sustainability.records.edit', $record) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $records->links() }}</div>
</div>
@endsection

