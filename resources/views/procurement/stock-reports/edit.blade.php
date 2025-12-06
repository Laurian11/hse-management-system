@extends('layouts.app')

@section('title', 'Edit Stock Consumption Report')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('procurement.stock-reports.show', $stockConsumptionReport) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Stock Consumption Report</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('procurement.stock-reports.update', $stockConsumptionReport) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Item Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Item Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="item_name" class="block text-sm font-medium text-black mb-1">Item Name *</label>
                    <input type="text" id="item_name" name="item_name" required value="{{ old('item_name', $stockConsumptionReport->item_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('item_name')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="item_category" class="block text-sm font-medium text-black mb-1">Category</label>
                    <input type="text" id="item_category" name="item_category" value="{{ old('item_category', $stockConsumptionReport->item_category) }}"
                           placeholder="e.g., Safety Equipment, PPE, Tools"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('item_category')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="item_code" class="block text-sm font-medium text-black mb-1">Item Code</label>
                    <input type="text" id="item_code" name="item_code" value="{{ old('item_code', $stockConsumptionReport->item_code) }}"
                           placeholder="e.g., STK-001"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('item_code')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="unit" class="block text-sm font-medium text-black mb-1">Unit</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $stockConsumptionReport->unit) }}"
                           placeholder="e.g., pieces, kg, liters, boxes"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('unit')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Stock Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Stock Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="opening_stock" class="block text-sm font-medium text-black mb-1">Opening Stock</label>
                    <input type="number" id="opening_stock" name="opening_stock" step="0.01" min="0" value="{{ old('opening_stock', $stockConsumptionReport->opening_stock) }}"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('opening_stock')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="received_quantity" class="block text-sm font-medium text-black mb-1">Received Quantity</label>
                    <input type="number" id="received_quantity" name="received_quantity" step="0.01" min="0" value="{{ old('received_quantity', $stockConsumptionReport->received_quantity) }}"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('received_quantity')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="consumed_quantity" class="block text-sm font-medium text-black mb-1">Consumed Quantity</label>
                    <input type="number" id="consumed_quantity" name="consumed_quantity" step="0.01" min="0" value="{{ old('consumed_quantity', $stockConsumptionReport->consumed_quantity) }}"
                           placeholder="0.00"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('consumed_quantity')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-black mb-1">Closing Stock (Auto-calculated)</label>
                    <input type="text" id="closing_stock_display" readonly
                           value="{{ number_format($stockConsumptionReport->closing_stock ?? 0, 2) }}"
                           class="w-full px-3 py-2 border border-gray-300 bg-gray-100 text-gray-600 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-500">Formula: Opening + Received - Consumed</p>
                </div>
            </div>
        </div>

        <!-- Report Period -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Report Period</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="report_period_start" class="block text-sm font-medium text-black mb-1">Period Start *</label>
                    <input type="date" id="report_period_start" name="report_period_start" required value="{{ old('report_period_start', $stockConsumptionReport->report_period_start->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('report_period_start')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="report_period_end" class="block text-sm font-medium text-black mb-1">Period End *</label>
                    <input type="date" id="report_period_end" name="report_period_end" required value="{{ old('report_period_end', $stockConsumptionReport->report_period_end->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('report_period_end')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Additional Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="department_id" class="block text-sm font-medium text-black mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $stockConsumptionReport->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="consumption_details" class="block text-sm font-medium text-black mb-1">Consumption Details</label>
                <textarea id="consumption_details" name="consumption_details" rows="4"
                          placeholder="Provide details about consumption patterns, usage, etc."
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('consumption_details', $stockConsumptionReport->consumption_details) }}</textarea>
                @error('consumption_details')
                    <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3"
                          placeholder="Any additional notes or comments"
                          class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes', $stockConsumptionReport->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('procurement.stock-reports.show', $stockConsumptionReport) }}" class="px-6 py-2 border border-gray-300 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white hover:bg-[#0052A3]">
                <i class="fas fa-save mr-2"></i>Update Report
            </button>
        </div>
    </form>
</div>

<script>
    // Auto-calculate closing stock
    function calculateClosingStock() {
        const opening = parseFloat(document.getElementById('opening_stock').value) || 0;
        const received = parseFloat(document.getElementById('received_quantity').value) || 0;
        const consumed = parseFloat(document.getElementById('consumed_quantity').value) || 0;
        const closing = opening + received - consumed;
        
        const display = document.getElementById('closing_stock_display');
        if (closing >= 0) {
            display.value = closing.toFixed(2);
            display.classList.remove('bg-red-100', 'text-red-600');
            display.classList.add('bg-gray-100', 'text-gray-600');
        } else {
            display.value = 'Negative stock detected: ' + closing.toFixed(2);
            display.classList.remove('bg-gray-100', 'text-gray-600');
            display.classList.add('bg-red-100', 'text-red-600');
        }
    }

    document.getElementById('opening_stock').addEventListener('input', calculateClosingStock);
    document.getElementById('received_quantity').addEventListener('input', calculateClosingStock);
    document.getElementById('consumed_quantity').addEventListener('input', calculateClosingStock);
</script>
@endsection

