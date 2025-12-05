@extends('layouts.app')

@section('title', 'Create GCLA Log')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('work-permits.gca-logs.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create GCLA Log</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('work-permits.gca-logs.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="work_permit_id" class="block text-sm font-medium text-black mb-1">Work Permit</label>
                    <select id="work_permit_id" name="work_permit_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">None</option>
                        @foreach($workPermits as $permit)
                            <option value="{{ $permit->id }}" {{ old('work_permit_id', $workPermitId) == $permit->id ? 'selected' : '' }}>
                                {{ $permit->reference_number }} - {{ $permit->work_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="gcla_type" class="block text-sm font-medium text-black mb-1">GCLA Type *</label>
                    <select id="gcla_type" name="gcla_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="pre_work" {{ old('gcla_type') == 'pre_work' ? 'selected' : '' }}>Pre-Work</option>
                        <option value="during_work" {{ old('gcla_type') == 'during_work' ? 'selected' : '' }}>During Work</option>
                        <option value="post_work" {{ old('gcla_type') == 'post_work' ? 'selected' : '' }}>Post-Work</option>
                        <option value="continuous" {{ old('gcla_type') == 'continuous' ? 'selected' : '' }}>Continuous</option>
                    </select>
                    @error('gcla_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="check_date" class="block text-sm font-medium text-black mb-1">Check Date & Time *</label>
                    <input type="datetime-local" id="check_date" name="check_date" required value="{{ old('check_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('check_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">Location *</label>
                    <input type="text" id="location" name="location" required value="{{ old('location') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('location')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description *</label>
                    <textarea id="description" name="description" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Compliance Status -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Compliance Status</h2>
            
            <div>
                <label for="compliance_status" class="block text-sm font-medium text-black mb-1">Compliance Status *</label>
                <select id="compliance_status" name="compliance_status" required
                        class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    <option value="compliant" {{ old('compliance_status') == 'compliant' ? 'selected' : '' }}>Compliant</option>
                    <option value="non_compliant" {{ old('compliance_status') == 'non_compliant' ? 'selected' : '' }}>Non-Compliant</option>
                    <option value="partial" {{ old('compliance_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                </select>
                @error('compliance_status')
                    <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Corrective Actions -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Corrective Actions</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="corrective_actions" class="block text-sm font-medium text-black mb-1">Corrective Actions</label>
                    <textarea id="corrective_actions" name="corrective_actions" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('corrective_actions') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="action_assigned_to" class="block text-sm font-medium text-black mb-1">Assign To</label>
                        <select id="action_assigned_to" name="action_assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('action_assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="action_due_date" class="block text-sm font-medium text-black mb-1">Due Date</label>
                        <input type="date" id="action_due_date" name="action_due_date" value="{{ old('action_due_date') }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('work-permits.gca-logs.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create GCLA Log
            </button>
        </div>
    </form>
</div>
@endsection

