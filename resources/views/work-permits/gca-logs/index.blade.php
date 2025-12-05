@extends('layouts.app')

@section('title', 'GCLA Logs')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">GCLA Logs</h1>
                <p class="text-sm text-gray-500 mt-1">Ground Control and Access Logs</p>
            </div>
            <div>
                <a href="{{ route('work-permits.gca-logs.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New GCLA Log
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-gray-300 p-4">
            <p class="text-sm text-gray-500">Total Logs</p>
            <p class="text-2xl font-bold text-black">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white border border-gray-300 p-4">
            <p class="text-sm text-gray-500">Compliant</p>
            <p class="text-2xl font-bold text-[#0066CC]">{{ $stats['compliant'] }}</p>
        </div>
        <div class="bg-white border border-gray-300 p-4">
            <p class="text-sm text-gray-500">Non-Compliant</p>
            <p class="text-2xl font-bold text-[#CC0000]">{{ $stats['non_compliant'] }}</p>
        </div>
        <div class="bg-white border border-gray-300 p-4">
            <p class="text-sm text-gray-500">Pending Actions</p>
            <p class="text-2xl font-bold text-[#FF9900]">{{ $stats['pending_actions'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..." 
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="compliance_status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="compliant" {{ request('compliance_status') == 'compliant' ? 'selected' : '' }}>Compliant</option>
                <option value="non_compliant" {{ request('compliance_status') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
                <option value="partial" {{ request('compliance_status') == 'partial' ? 'selected' : '' }}>Partial</option>
            </select>
            <select name="work_permit_id" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Permits</option>
                @foreach($workPermits as $permit)
                    <option value="{{ $permit->id }}" {{ request('work_permit_id') == $permit->id ? 'selected' : '' }}>{{ $permit->reference_number }}</option>
                @endforeach
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('work-permits.gca-logs.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Work Permit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Date & Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Compliance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($logs as $log)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-black">{{ $log->reference_number }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->workPermit)
                                <a href="{{ route('work-permits.show', $log->workPermit) }}" class="text-sm text-[#0066CC] hover:underline">
                                    {{ $log->workPermit->reference_number }}
                                </a>
                            @else
                                <span class="text-sm text-gray-500">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-black">{{ ucfirst(str_replace('_', ' ', $log->gcla_type)) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-black">{{ $log->check_date->format('M d, Y H:i') }}</div>
                            <div class="text-xs text-gray-500">{{ $log->location }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $log->compliance_status == 'compliant' ? 'bg-[#F5F5F5] text-black border-gray-300' : '' }}
                                {{ $log->compliance_status == 'non_compliant' ? 'bg-[#F5F5F5] text-[#CC0000] border-[#CC0000]' : '' }}
                                {{ $log->compliance_status == 'partial' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $log->compliance_status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->action_assigned_to && !$log->action_completed)
                                <div class="text-sm text-[#FF9900]">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>Action Pending
                                </div>
                                @if($log->action_due_date)
                                    <div class="text-xs text-gray-500">Due: {{ $log->action_due_date->format('M d, Y') }}</div>
                                @endif
                            @elseif($log->action_completed)
                                <div class="text-sm text-[#0066CC]">
                                    <i class="fas fa-check-circle mr-1"></i>Completed
                                </div>
                            @else
                                <span class="text-sm text-gray-500">None</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('work-permits.gca-logs.show', $log) }}" class="text-[#0066CC] hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No GCLA logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection

