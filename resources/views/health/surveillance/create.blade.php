@extends('layouts.app')

@section('title', 'Create Health Surveillance Record')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('health.health-surveillance-records.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Health Surveillance Record</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('health.health-surveillance-records.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Employee Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-black mb-1">Employee *</label>
                    <select id="user_id" name="user_id" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->employee_id ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Surveillance Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="surveillance_type" class="block text-sm font-medium text-black mb-1">Surveillance Type *</label>
                    <select id="surveillance_type" name="surveillance_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Type</option>
                        <option value="medical_examination" {{ old('surveillance_type') == 'medical_examination' ? 'selected' : '' }}>Medical Examination</option>
                        <option value="health_test" {{ old('surveillance_type') == 'health_test' ? 'selected' : '' }}>Health Test</option>
                        <option value="vaccination" {{ old('surveillance_type') == 'vaccination' ? 'selected' : '' }}>Vaccination</option>
                        <option value="audiometry" {{ old('surveillance_type') == 'audiometry' ? 'selected' : '' }}>Audiometry</option>
                        <option value="lung_function" {{ old('surveillance_type') == 'lung_function' ? 'selected' : '' }}>Lung Function</option>
                        <option value="vision_test" {{ old('surveillance_type') == 'vision_test' ? 'selected' : '' }}>Vision Test</option>
                        <option value="blood_test" {{ old('surveillance_type') == 'blood_test' ? 'selected' : '' }}>Blood Test</option>
                        <option value="other" {{ old('surveillance_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('surveillance_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="examination_name" class="block text-sm font-medium text-black mb-1">Examination Name</label>
                    <input type="text" id="examination_name" name="examination_name" value="{{ old('examination_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="examination_date" class="block text-sm font-medium text-black mb-1">Examination Date *</label>
                    <input type="date" id="examination_date" name="examination_date" required value="{{ old('examination_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('examination_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="next_due_date" class="block text-sm font-medium text-black mb-1">Next Due Date</label>
                    <input type="date" id="next_due_date" name="next_due_date" value="{{ old('next_due_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="medical_provider_id" class="block text-sm font-medium text-black mb-1">Medical Provider</label>
                    <select id="medical_provider_id" name="medical_provider_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Provider</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('medical_provider_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="provider_name" class="block text-sm font-medium text-black mb-1">Provider Name</label>
                    <input type="text" id="provider_name" name="provider_name" value="{{ old('provider_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="result" class="block text-sm font-medium text-black mb-1">Result *</label>
                    <select id="result" name="result" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Result</option>
                        <option value="fit" {{ old('result') == 'fit' ? 'selected' : '' }}>Fit</option>
                        <option value="unfit" {{ old('result') == 'unfit' ? 'selected' : '' }}>Unfit</option>
                        <option value="fit_with_restrictions" {{ old('result') == 'fit_with_restrictions' ? 'selected' : '' }}>Fit with Restrictions</option>
                        <option value="requires_follow_up" {{ old('result') == 'requires_follow_up' ? 'selected' : '' }}>Requires Follow-up</option>
                        <option value="pending" {{ old('result') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                    @error('result')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="findings" class="block text-sm font-medium text-black mb-1">Findings</label>
                    <textarea id="findings" name="findings" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('findings') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="restrictions" class="block text-sm font-medium text-black mb-1">Restrictions</label>
                    <textarea id="restrictions" name="restrictions" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('restrictions') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="recommendations" class="block text-sm font-medium text-black mb-1">Recommendations</label>
                    <textarea id="recommendations" name="recommendations" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('recommendations') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('health.health-surveillance-records.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Record
            </button>
        </div>
    </form>
</div>
@endsection

