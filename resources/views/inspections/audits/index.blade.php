@extends('layouts.app')

@section('title', 'Audits')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Audits</h1>
                <p class="text-sm text-gray-500 mt-1">Manage audit records</p>
            </div>
            <div>
                <a href="{{ route('inspections.audits.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Audit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Filters -->
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search audits..."
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="audit_type" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Types</option>
                <option value="internal" {{ request('audit_type') == 'internal' ? 'selected' : '' }}>Internal</option>
                <option value="external" {{ request('audit_type') == 'external' ? 'selected' : '' }}>External</option>
                <option value="certification" {{ request('audit_type') == 'certification' ? 'selected' : '' }}>Certification</option>
                <option value="regulatory" {{ request('audit_type') == 'regulatory' ? 'selected' : '' }}>Regulatory</option>
            </select>
            <select name="status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('inspections.audits.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <!-- Audits Table -->
    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Planned Dates</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Lead Auditor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($audits as $audit)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">{{ $audit->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $audit->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ ucfirst($audit->audit_type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                            {{ $audit->planned_start_date->format('M d') }} - {{ $audit->planned_end_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-black">{{ $audit->leadAuditor->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $audit->status == 'completed' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $audit->status == 'in_progress' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $audit->status == 'planned' ? 'bg-[#F5F5F5] text-gray-500 border-gray-300' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $audit->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('inspections.audits.show', $audit) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('inspections.audits.edit', $audit) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No audits found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $audits->links() }}
    </div>
</div>
@endsection

