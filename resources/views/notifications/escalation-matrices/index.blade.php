@extends('layouts.app')

@section('title', 'Escalation Matrices')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Escalation Matrices</h1>
                <p class="text-sm text-gray-500 mt-1">Escalation matrix configuration</p>
            </div>
            <div>
                <a href="{{ route('notifications.escalation-matrices.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Matrix
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="event_type" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Events</option>
                <option value="incident" {{ request('event_type') == 'incident' ? 'selected' : '' }}>Incident</option>
                <option value="capa_overdue" {{ request('event_type') == 'capa_overdue' ? 'selected' : '' }}>CAPA Overdue</option>
                <option value="permit_expiry" {{ request('event_type') == 'permit_expiry' ? 'selected' : '' }}>Permit Expiry</option>
            </select>
            <select name="severity_level" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Severities</option>
                <option value="low" {{ request('severity_level') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('severity_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('severity_level') == 'high' ? 'selected' : '' }}>High</option>
                <option value="critical" {{ request('severity_level') == 'critical' ? 'selected' : '' }}>Critical</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('notifications.escalation-matrices.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Event Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Severity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Days Overdue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Escalation Levels</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($matrices as $matrix)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $matrix->name }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $matrix->event_type)) }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst($matrix->severity_level) }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $matrix->days_overdue ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ is_array($matrix->escalation_levels) ? count($matrix->escalation_levels) : 0 }} levels</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $matrix->is_active ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500 border-gray-500' }}">
                                {{ $matrix->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('notifications.escalation-matrices.show', $matrix) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('notifications.escalation-matrices.edit', $matrix) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No escalation matrices found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $matrices->links() }}</div>
</div>
@endsection

