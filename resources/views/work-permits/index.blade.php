@extends('layouts.app')

@section('title', 'Work Permits')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Work Permits</h1>
                <p class="text-sm text-gray-500 mt-1">Manage and track work permits</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('work-permits.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3] transition-colors">
                    <i class="fas fa-plus mr-2"></i>New Permit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-300 p-4">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-black">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-gray-300 p-4">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-[#FF9900]">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white border border-gray-300 p-4">
            <p class="text-sm text-gray-500">Active</p>
            <p class="text-2xl font-bold text-[#0066CC]">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white border border-gray-300 p-4">
            <p class="text-sm text-gray-500">Expired</p>
            <p class="text-2xl font-bold text-[#CC0000]">{{ $stats['expired'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search permits..." 
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
            <select name="work_permit_type_id" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Types</option>
                @foreach($permitTypes as $type)
                    <option value="{{ $type->id }}" {{ request('work_permit_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                @endforeach
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('work-permits.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <!-- Permits Table -->
    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Work Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Start Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($permits as $permit)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-black">{{ $permit->reference_number }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-black">{{ $permit->work_title }}</div>
                            <div class="text-xs text-gray-500">{{ $permit->requestedBy->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-black">{{ $permit->workPermitType->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-black">{{ $permit->work_location }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-black">{{ $permit->work_start_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $permit->work_start_date->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $permit->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $permit->status == 'submitted' || $permit->status == 'under_review' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $permit->status == 'expired' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $permit->status == 'closed' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}
                                {{ $permit->status == 'draft' ? 'bg-[#F5F5F5] text-gray-500 border-gray-300' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $permit->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('work-permits.show', $permit) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            @if(in_array($permit->status, ['draft', 'rejected']))
                                <a href="{{ route('work-permits.edit', $permit) }}" class="text-[#0066CC] hover:underline">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No permits found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $permits->links() }}
    </div>
</div>
@endsection

