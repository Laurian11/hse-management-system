@extends('layouts.app')

@section('title', 'Stock Consumption Reports')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Stock Consumption Reports</h1>
                <p class="text-sm text-gray-500 mt-1">Stock and consumption reports</p>
            </div>
            <div>
                <a href="{{ route('procurement.dashboard') }}" class="text-[#0066CC] hover:underline mr-4">Dashboard</a>
                <a href="{{ route('procurement.stock-reports.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Report
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <input type="date" name="report_period_start" value="{{ request('report_period_start') }}" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('procurement.stock-reports.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Opening</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Consumed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Closing</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($reports as $report)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $report->reference_number }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $report->item_name }}</td>
                        <td class="px-6 py-4 text-sm text-black">
                            {{ $report->report_period_start->format('M d') }} - {{ $report->report_period_end->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-black">{{ $report->opening_stock ?? 'N/A' }} {{ $report->unit ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $report->consumed_quantity ?? 'N/A' }} {{ $report->unit ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $report->closing_stock ?? 'N/A' }} {{ $report->unit ?? '' }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('procurement.stock-reports.show', $report) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('procurement.stock-reports.edit', $report) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No reports found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $reports->links() }}</div>
</div>
@endsection

