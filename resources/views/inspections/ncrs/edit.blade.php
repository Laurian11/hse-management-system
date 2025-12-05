@extends('layouts.app')

@section('title', 'Edit Non-Conformance Report')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('inspections.ncrs.show', $ncr) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Non-Conformance Report</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('inspections.ncrs.update', $ncr) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-black mb-1">Title *</label>
                    <input type="text" id="title" name="title" required value="{{ old('title', $ncr->title) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('title')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="severity" class="block text-sm font-medium text-black mb-1">Severity *</label>
                    <select id="severity" name="severity" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="critical" {{ old('severity', $ncr->severity) == 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="major" {{ old('severity', $ncr->severity) == 'major' ? 'selected' : '' }}>Major</option>
                        <option value="minor" {{ old('severity', $ncr->severity) == 'minor' ? 'selected' : '' }}>Minor</option>
                        <option value="observation" {{ old('severity', $ncr->severity) == 'observation' ? 'selected' : '' }}>Observation</option>
                    </select>
                    @error('severity')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-black mb-1">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="open" {{ old('status', $ncr->status) == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="investigating" {{ old('status', $ncr->status) == 'investigating' ? 'selected' : '' }}>Investigating</option>
                        <option value="corrective_action" {{ old('status', $ncr->status) == 'corrective_action' ? 'selected' : '' }}>Corrective Action</option>
                        <option value="closed" {{ old('status', $ncr->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="cancelled" {{ old('status', $ncr->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="identified_date" class="block text-sm font-medium text-black mb-1">Identified Date *</label>
                    <input type="date" id="identified_date" name="identified_date" required value="{{ old('identified_date', $ncr->identified_date->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('identified_date')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">Location *</label>
                    <input type="text" id="location" name="location" required value="{{ old('location', $ncr->location) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('location')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description', $ncr->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Root Cause & Immediate Action -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Root Cause & Immediate Action</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="root_cause" class="block text-sm font-medium text-black mb-1">Root Cause</label>
                    <textarea id="root_cause" name="root_cause" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('root_cause', $ncr->root_cause) }}</textarea>
                </div>

                <div>
                    <label for="immediate_action" class="block text-sm font-medium text-black mb-1">Immediate Action</label>
                    <textarea id="immediate_action" name="immediate_action" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('immediate_action', $ncr->immediate_action) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Corrective Action -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Corrective Action</h2>
            
            <div class="space-y-4">
                <div>
                    <label for="corrective_action_plan" class="block text-sm font-medium text-black mb-1">Corrective Action Plan</label>
                    <textarea id="corrective_action_plan" name="corrective_action_plan" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('corrective_action_plan', $ncr->corrective_action_plan) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="corrective_action_due_date" class="block text-sm font-medium text-black mb-1">Due Date</label>
                        <input type="date" id="corrective_action_due_date" name="corrective_action_due_date" value="{{ old('corrective_action_due_date', $ncr->corrective_action_due_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    </div>

                    <div>
                        <label for="corrective_action_assigned_to" class="block text-sm font-medium text-black mb-1">Assign To</label>
                        <select id="corrective_action_assigned_to" name="corrective_action_assigned_to"
                                class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('corrective_action_assigned_to', $ncr->corrective_action_assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="verification_required" value="1" {{ old('verification_required', $ncr->verification_required) ? 'checked' : '' }}
                               class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                        <span class="ml-2 text-sm text-black">Verification Required</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('inspections.ncrs.show', $ncr) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update NCR
            </button>
        </div>
    </form>
</div>
@endsection

