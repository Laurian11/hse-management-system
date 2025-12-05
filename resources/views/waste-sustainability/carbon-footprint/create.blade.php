@extends('layouts.app')

@section('title', 'Create Carbon Footprint Record')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('waste-sustainability.carbon-footprint.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Carbon Footprint Record</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('waste-sustainability.carbon-footprint.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Carbon Footprint Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="record_date" class="block text-sm font-medium text-black mb-1">Record Date *</label>
                    <input type="date" id="record_date" name="record_date" required value="{{ old('record_date', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('record_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="source_type" class="block text-sm font-medium text-black mb-1">Source Type *</label>
                    <select id="source_type" name="source_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Type</option>
                        <option value="electricity" {{ old('source_type') == 'electricity' ? 'selected' : '' }}>Electricity</option>
                        <option value="fuel" {{ old('source_type') == 'fuel' ? 'selected' : '' }}>Fuel</option>
                        <option value="water" {{ old('source_type') == 'water' ? 'selected' : '' }}>Water</option>
                        <option value="transportation" {{ old('source_type') == 'transportation' ? 'selected' : '' }}>Transportation</option>
                        <option value="waste" {{ old('source_type') == 'waste' ? 'selected' : '' }}>Waste</option>
                        <option value="other" {{ old('source_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('source_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="source_name" class="block text-sm font-medium text-black mb-1">Source Name *</label>
                    <input type="text" id="source_name" name="source_name" required value="{{ old('source_name') }}"
                           placeholder="e.g., Main Building, Vehicle Fleet"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('source_name')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="consumption" class="block text-sm font-medium text-black mb-1">Consumption *</label>
                    <input type="number" id="consumption" name="consumption" step="0.01" min="0" required value="{{ old('consumption') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('consumption')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="consumption_unit" class="block text-sm font-medium text-black mb-1">Consumption Unit *</label>
                    <input type="text" id="consumption_unit" name="consumption_unit" required value="{{ old('consumption_unit') }}"
                           placeholder="e.g., kWh, liters, kg"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('consumption_unit')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="emission_factor" class="block text-sm font-medium text-black mb-1">Emission Factor</label>
                    <input type="number" id="emission_factor" name="emission_factor" step="0.0001" min="0" value="{{ old('emission_factor') }}"
                           placeholder="CO₂e per unit"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="carbon_equivalent" class="block text-sm font-medium text-black mb-1">Carbon Equivalent (CO₂e)</label>
                    <input type="number" id="carbon_equivalent" name="carbon_equivalent" step="0.01" min="0" value="{{ old('carbon_equivalent') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="department_id" class="block text-sm font-medium text-black mb-1">Department</label>
                    <select id="department_id" name="department_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="recorded_by" class="block text-sm font-medium text-black mb-1">Recorded By</label>
                    <select id="recorded_by" name="recorded_by"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Person</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('recorded_by', auth()->id()) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('waste-sustainability.carbon-footprint.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Record
            </button>
        </div>
    </form>
</div>
@endsection

