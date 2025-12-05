@extends('layouts.app')

@section('title', 'Edit Carbon Footprint Record')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('waste-sustainability.carbon-footprint.show', $record) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Carbon Footprint Record</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('waste-sustainability.carbon-footprint.update', $record) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Carbon Footprint Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="source_name" class="block text-sm font-medium text-black mb-1">Source Name *</label>
                    <input type="text" id="source_name" name="source_name" required value="{{ old('source_name', $record->source_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="record_date" class="block text-sm font-medium text-black mb-1">Record Date *</label>
                    <input type="date" id="record_date" name="record_date" required value="{{ old('record_date', $record->record_date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="consumption" class="block text-sm font-medium text-black mb-1">Consumption *</label>
                    <input type="number" id="consumption" name="consumption" step="0.01" min="0" required value="{{ old('consumption', $record->consumption) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="carbon_equivalent" class="block text-sm font-medium text-black mb-1">Carbon Equivalent (CO₂e)</label>
                    <input type="number" id="carbon_equivalent" name="carbon_equivalent" step="0.01" min="0" value="{{ old('carbon_equivalent', $record->carbon_equivalent) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes', $record->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('waste-sustainability.carbon-footprint.show', $record) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Record
            </button>
        </div>
    </form>
</div>
@endsection

