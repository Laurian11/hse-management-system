@extends('layouts.app')

@section('title', 'Create Escalation Matrix')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('notifications.escalation-matrices.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Escalation Matrix</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('notifications.escalation-matrices.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Escalation Matrix Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-black mb-1">Matrix Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('name')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="event_type" class="block text-sm font-medium text-black mb-1">Event Type *</label>
                    <select id="event_type" name="event_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Type</option>
                        <option value="incident" {{ old('event_type') == 'incident' ? 'selected' : '' }}>Incident</option>
                        <option value="capa_overdue" {{ old('event_type') == 'capa_overdue' ? 'selected' : '' }}>CAPA Overdue</option>
                        <option value="permit_expiry" {{ old('event_type') == 'permit_expiry' ? 'selected' : '' }}>Permit Expiry</option>
                        <option value="audit_finding" {{ old('event_type') == 'audit_finding' ? 'selected' : '' }}>Audit Finding</option>
                        <option value="non_conformance" {{ old('event_type') == 'non_conformance' ? 'selected' : '' }}>Non-Conformance</option>
                    </select>
                    @error('event_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="severity_level" class="block text-sm font-medium text-black mb-1">Severity Level</label>
                    <select id="severity_level" name="severity_level"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Severity</option>
                        <option value="low" {{ old('severity_level') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('severity_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('severity_level') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('severity_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>

                <div>
                    <label for="days_overdue" class="block text-sm font-medium text-black mb-1">Days Overdue</label>
                    <input type="number" id="days_overdue" name="days_overdue" min="0" value="{{ old('days_overdue') }}"
                           placeholder="e.g., 3 for 3 days overdue"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="default_assignee_id" class="block text-sm font-medium text-black mb-1">Default Assignee</label>
                    <select id="default_assignee_id" name="default_assignee_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Assignee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('default_assignee_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-sm text-black">Active</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('notifications.escalation-matrices.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Matrix
            </button>
        </div>
    </form>
</div>
@endsection

