@extends('layouts.app')

@section('title', 'Inspections')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Inspections</h1>
                <p class="text-sm text-gray-500 mt-1">Manage inspection records</p>
            </div>
            <div>
                <a href="{{ route('inspections.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Inspection
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Filters -->
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search inspections..."
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="overall_status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="compliant" {{ request('overall_status') == 'compliant' ? 'selected' : '' }}>Compliant</option>
                <option value="non_compliant" {{ request('overall_status') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
                <option value="partial" {{ request('overall_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="pending" {{ request('overall_status') == 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
            <input type="date" name="inspection_date_from" value="{{ request('inspection_date_from') }}"
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('inspections.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <!-- Inspections Table -->
    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Inspected By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($inspections as $inspection)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">{{ $inspection->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $inspection->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $inspection->inspection_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $inspection->location }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $inspection->inspectedBy->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $inspection->overall_status == 'compliant' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $inspection->overall_status == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $inspection->overall_status == 'partial' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $inspection->overall_status == 'pending' ? 'bg-[#F5F5F5] text-gray-500 border-gray-300' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $inspection->overall_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('inspections.show', $inspection) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('inspections.edit', $inspection) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No inspections found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $inspections->links() }}
    </div>
</div>
@endsection

