@extends('layouts.app')

@section('title', 'Stock Consumption Report Details')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('procurement.stock-reports.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Stock Consumption Report</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('procurement.stock-reports.edit', $stockConsumptionReport) }}" class="px-4 py-2 border border-gray-300 hover:bg-gray-50">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Report Information -->
    <div class="bg-white border border-gray-300 p-6 mb-6">
        <h2 class="text-lg font-semibold text-black mb-4">Report Information</h2>
        
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                <dd class="mt-1 text-sm text-black font-semibold">{{ $stockConsumptionReport->reference_number }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Department</dt>
                <dd class="mt-1 text-sm text-black">{{ $stockConsumptionReport->department->name ?? 'N/A' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Prepared By</dt>
                <dd class="mt-1 text-sm text-black">{{ $stockConsumptionReport->preparedBy->name ?? 'N/A' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Created At</dt>
                <dd class="mt-1 text-sm text-black">{{ $stockConsumptionReport->created_at->format('M d, Y H:i') }}</dd>
            </div>
        </dl>
    </div>

    <!-- Item Information -->
    <div class="bg-white border border-gray-300 p-6 mb-6">
        <h2 class="text-lg font-semibold text-black mb-4">Item Information</h2>
        
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">Item Name</dt>
                <dd class="mt-1 text-sm text-black font-semibold">{{ $stockConsumptionReport->item_name }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Item Code</dt>
                <dd class="mt-1 text-sm text-black">{{ $stockConsumptionReport->item_code ?? 'N/A' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Category</dt>
                <dd class="mt-1 text-sm text-black">{{ $stockConsumptionReport->item_category ?? 'N/A' }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Unit</dt>
                <dd class="mt-1 text-sm text-black">{{ $stockConsumptionReport->unit ?? 'N/A' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Stock Details -->
    <div class="bg-white border border-gray-300 p-6 mb-6">
        <h2 class="text-lg font-semibold text-black mb-4">Stock Details</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="border border-gray-300 p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Opening Stock</p>
                <p class="text-xl font-bold text-black">{{ number_format($stockConsumptionReport->opening_stock ?? 0, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stockConsumptionReport->unit ?? '' }}</p>
            </div>

            <div class="border border-gray-300 p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Received</p>
                <p class="text-xl font-bold text-green-600">{{ number_format($stockConsumptionReport->received_quantity ?? 0, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stockConsumptionReport->unit ?? '' }}</p>
            </div>

            <div class="border border-gray-300 p-4 text-center">
                <p class="text-xs text-gray-500 mb-1">Consumed</p>
                <p class="text-xl font-bold text-red-600">{{ number_format($stockConsumptionReport->consumed_quantity ?? 0, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stockConsumptionReport->unit ?? '' }}</p>
            </div>

            <div class="border-2 border-[#0066CC] p-4 text-center bg-blue-50">
                <p class="text-xs text-gray-500 mb-1">Closing Stock</p>
                <p class="text-xl font-bold text-[#0066CC]">{{ number_format($stockConsumptionReport->closing_stock ?? 0, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stockConsumptionReport->unit ?? '' }}</p>
            </div>
        </div>
    </div>

    <!-- Report Period -->
    <div class="bg-white border border-gray-300 p-6 mb-6">
        <h2 class="text-lg font-semibold text-black mb-4">Report Period</h2>
        
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500">Period Start</dt>
                <dd class="mt-1 text-sm text-black">{{ $stockConsumptionReport->report_period_start->format('M d, Y') }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Period End</dt>
                <dd class="mt-1 text-sm text-black">{{ $stockConsumptionReport->report_period_end->format('M d, Y') }}</dd>
            </div>
        </dl>
    </div>

    <!-- Additional Information -->
    @if($stockConsumptionReport->consumption_details || $stockConsumptionReport->notes)
    <div class="bg-white border border-gray-300 p-6 mb-6">
        <h2 class="text-lg font-semibold text-black mb-4">Additional Information</h2>
        
        @if($stockConsumptionReport->consumption_details)
        <div class="mb-4">
            <dt class="text-sm font-medium text-gray-500 mb-2">Consumption Details</dt>
            <dd class="text-sm text-black whitespace-pre-wrap">{{ $stockConsumptionReport->consumption_details }}</dd>
        </div>
        @endif

        @if($stockConsumptionReport->notes)
        <div>
            <dt class="text-sm font-medium text-gray-500 mb-2">Notes</dt>
            <dd class="text-sm text-black whitespace-pre-wrap">{{ $stockConsumptionReport->notes }}</dd>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection

