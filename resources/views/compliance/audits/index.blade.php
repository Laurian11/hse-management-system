@extends('layouts.app')

@section('title', 'Compliance Audits')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Compliance Audits</h1>
                <p class="text-sm text-gray-500 mt-1">ISO audit preparation and documentation</p>
            </div>
            <div>
                <a href="{{ route('compliance.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('compliance.audits.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Audit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="audit_type" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Types</option>
                <option value="internal" {{ request('audit_type') == 'internal' ? 'selected' : '' }}>Internal</option>
                <option value="external" {{ request('audit_type') == 'external' ? 'selected' : '' }}>External</option>
                <option value="iso_14001" {{ request('audit_type') == 'iso_14001' ? 'selected' : '' }}>ISO 14001</option>
                <option value="iso_45001" {{ request('audit_type') == 'iso_45001' ? 'selected' : '' }}>ISO 45001</option>
            </select>
            <select name="audit_status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="scheduled" {{ request('audit_status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="in_progress" {{ request('audit_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('audit_status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('compliance.audits.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Audit Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($audits as $audit)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $audit->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $audit->audit_title }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $audit->audit_type)) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $audit->audit_status == 'completed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $audit->audit_status == 'in_progress' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $audit->audit_status == 'scheduled' ? 'bg-[#F5F5F5] text-black border-gray-500' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $audit->audit_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-black">{{ $audit->audit_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('compliance.audits.show', $audit) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('compliance.audits.edit', $audit) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No audits found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $audits->links() }}</div>
</div>
@endsection

