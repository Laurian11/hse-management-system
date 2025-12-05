@extends('layouts.app')

@section('title', 'Housekeeping Inspections')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Housekeeping Inspections</h1>
                <p class="text-sm text-gray-500 mt-1">Housekeeping inspection records</p>
            </div>
            <div>
                <a href="{{ route('housekeeping.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('housekeeping.inspections.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Inspection
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="follow_up_required" {{ request('status') == 'follow_up_required' ? 'selected' : '' }}>Follow-up Required</option>
            </select>
            <select name="department_id" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('housekeeping.inspections.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Inspection Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Inspected By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($inspections as $inspection)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $inspection->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $inspection->location }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $inspection->inspection_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $inspection->inspectedBy->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst($inspection->overall_rating ?? 'N/A') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $inspection->status == 'completed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $inspection->status == 'follow_up_required' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $inspection->status == 'pending' ? 'bg-[#F5F5F5] text-black border-gray-500' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('housekeeping.inspections.show', $inspection) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('housekeeping.inspections.edit', $inspection) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No inspections found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $inspections->links() }}</div>
</div>
@endsection

