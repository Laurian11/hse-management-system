@extends('layouts.app')

@section('title', 'ISO 14001 Compliance Records')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">ISO 14001 Compliance Records</h1>
                <p class="text-sm text-gray-500 mt-1">ISO 14001 compliance checklist</p>
            </div>
            <div>
                <a href="{{ route('environmental.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('environmental.iso14001.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
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
            <select name="compliance_status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="compliant" {{ request('compliance_status') == 'compliant' ? 'selected' : '' }}>Compliant</option>
                <option value="non_compliant" {{ request('compliance_status') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('environmental.iso14001.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Clause</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Requirement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Assessment Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($records as $record)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $record->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $record->clause_reference ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $record->requirement ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $record->assessment_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $record->compliance_status == 'compliant' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' }}">
                                {{ ucfirst(str_replace('_', ' ', $record->compliance_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('environmental.iso14001.show', $record) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('environmental.iso14001.edit', $record) }}" class="text-[#0066CC] hover:underline">Edit</a>
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

