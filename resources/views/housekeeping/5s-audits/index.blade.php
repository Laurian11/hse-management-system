@extends('layouts.app')

@section('title', '5S Audits')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">5S Audits</h1>
                <p class="text-sm text-gray-500 mt-1">5S audit checklist (Sort, Set, Shine, Standardize, Sustain)</p>
            </div>
            <div>
                <a href="{{ route('housekeeping.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('housekeeping.5s-audits.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New 5S Audit
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
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <select name="overall_rating" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Ratings</option>
                <option value="excellent" {{ request('overall_rating') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                <option value="good" {{ request('overall_rating') == 'good' ? 'selected' : '' }}>Good</option>
                <option value="needs_improvement" {{ request('overall_rating') == 'needs_improvement' ? 'selected' : '' }}>Needs Improvement</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('housekeeping.5s-audits.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Area</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Audit Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Total Score</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($audits as $audit)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $audit->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $audit->area }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $audit->audit_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ number_format($audit->total_score ?? 0, 1) }}/100</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $audit->overall_rating == 'excellent' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $audit->overall_rating == 'good' ? 'bg-[#F5F5F5] text-green-600 border-green-600' : '' }}
                                {{ $audit->overall_rating == 'needs_improvement' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $audit->overall_rating)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $audit->status == 'completed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $audit->status == 'in_progress' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $audit->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('housekeeping.5s-audits.show', $audit) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('housekeeping.5s-audits.edit', $audit) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No 5S audits found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $audits->links() }}</div>
</div>
@endsection

