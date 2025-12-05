@extends('layouts.app')

@section('title', 'Ergonomic Assessments')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Ergonomic Assessments</h1>
                <p class="text-sm text-gray-500 mt-1">Workplace ergonomic assessments</p>
            </div>
            <div>
                <a href="{{ route('health.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('health.ergonomic.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Assessment
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="risk_level" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Risk Levels</option>
                <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('risk_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('health.ergonomic.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Risk Level</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($assessments as $assessment)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $assessment->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $assessment->assessment_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $assessment->assessedEmployee->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $assessment->workstation_location ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $assessment->risk_level == 'high' || $assessment->risk_level == 'very_high' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $assessment->risk_level == 'medium' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $assessment->risk_level == 'low' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $assessment->risk_level)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('health.ergonomic.show', $assessment) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('health.ergonomic.edit', $assessment) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No assessments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $assessments->links() }}</div>
</div>
@endsection

