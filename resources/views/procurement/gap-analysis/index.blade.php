@extends('layouts.app')

@section('title', 'Safety Material Gap Analysis')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Safety Material Gap Analysis</h1>
                <p class="text-sm text-gray-500 mt-1">Gap analysis for missing safety materials</p>
            </div>
            <div>
                <a href="{{ route('procurement.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('procurement.gap-analysis.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Analysis
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
                <option value="identified" {{ request('status') == 'identified' ? 'selected' : '' }}>Identified</option>
                <option value="procurement_requested" {{ request('status') == 'procurement_requested' ? 'selected' : '' }}>Procurement Requested</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
            <select name="priority" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Priorities</option>
                <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('procurement.gap-analysis.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Material</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Gap Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($analyses as $analysis)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $analysis->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $analysis->required_material }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $analysis->analysis_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $analysis->gap_quantity ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $analysis->priority == 'critical' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $analysis->priority == 'high' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst($analysis->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]">
                                {{ ucfirst(str_replace('_', ' ', $analysis->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('procurement.gap-analysis.show', $analysis) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('procurement.gap-analysis.edit', $analysis) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No gap analyses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $analyses->links() }}</div>
</div>
@endsection

