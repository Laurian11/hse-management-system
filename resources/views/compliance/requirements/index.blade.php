@extends('layouts.app')

@section('title', 'Compliance Requirements')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Compliance Requirements</h1>
                <p class="text-sm text-gray-500 mt-1">Regulatory requirements register</p>
            </div>
            <div>
                <a href="{{ route('compliance.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('compliance.requirements.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Requirement
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="regulatory_body" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Bodies</option>
                <option value="GCLA" {{ request('regulatory_body') == 'GCLA' ? 'selected' : '' }}>GCLA</option>
                <option value="OSHA" {{ request('regulatory_body') == 'OSHA' ? 'selected' : '' }}>OSHA</option>
                <option value="NEMC" {{ request('regulatory_body') == 'NEMC' ? 'selected' : '' }}>NEMC</option>
            </select>
            <select name="compliance_status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="compliant" {{ request('compliance_status') == 'compliant' ? 'selected' : '' }}>Compliant</option>
                <option value="non_compliant" {{ request('compliance_status') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
                <option value="partially_compliant" {{ request('compliance_status') == 'partially_compliant' ? 'selected' : '' }}>Partially Compliant</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('compliance.requirements.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Requirement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Regulatory Body</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Due Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($requirements as $requirement)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $requirement->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $requirement->requirement_title }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $requirement->regulatory_body }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $requirement->compliance_status == 'compliant' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $requirement->compliance_status == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $requirement->compliance_status == 'partially_compliant' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $requirement->compliance_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-black">{{ $requirement->compliance_due_date ? $requirement->compliance_due_date->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('compliance.requirements.show', $requirement) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('compliance.requirements.edit', $requirement) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No requirements found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $requirements->links() }}</div>
</div>
@endsection

