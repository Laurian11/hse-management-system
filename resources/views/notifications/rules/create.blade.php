@extends('layouts.app')

@section('title', 'Create Notification Rule')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('notifications.rules.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Notification Rule</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('notifications.rules.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Rule Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-black mb-1">Rule Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('name')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="trigger_type" class="block text-sm font-medium text-black mb-1">Trigger Type *</label>
                    <select id="trigger_type" name="trigger_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Type</option>
                        <option value="incident" {{ old('trigger_type') == 'incident' ? 'selected' : '' }}>Incident</option>
                        <option value="permit_expiry" {{ old('trigger_type') == 'permit_expiry' ? 'selected' : '' }}>Permit Expiry</option>
                        <option value="ppe_expiry" {{ old('trigger_type') == 'ppe_expiry' ? 'selected' : '' }}>PPE Expiry</option>
                        <option value="training_due" {{ old('trigger_type') == 'training_due' ? 'selected' : '' }}>Training Due</option>
                        <option value="audit_due" {{ old('trigger_type') == 'audit_due' ? 'selected' : '' }}>Audit Due</option>
                        <option value="certificate_expiry" {{ old('trigger_type') == 'certificate_expiry' ? 'selected' : '' }}>Certificate Expiry</option>
                    </select>
                    @error('trigger_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notification_channel" class="block text-sm font-medium text-black mb-1">Notification Channel *</label>
                    <select id="notification_channel" name="notification_channel" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Channel</option>
                        <option value="email" {{ old('notification_channel') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="sms" {{ old('notification_channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="push" {{ old('notification_channel') == 'push' ? 'selected' : '' }}>Push Notification</option>
                    </select>
                    @error('notification_channel')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="days_before" class="block text-sm font-medium text-black mb-1">Days Before Event</label>
                    <input type="number" id="days_before" name="days_before" min="0" value="{{ old('days_before') }}"
                           placeholder="e.g., 7 for 7 days before"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="frequency" class="block text-sm font-medium text-black mb-1">Frequency</label>
                    <select id="frequency" name="frequency"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Frequency</option>
                        <option value="once" {{ old('frequency') == 'once' ? 'selected' : '' }}>Once</option>
                        <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
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

                <div class="md:col-span-2">
                    <label for="message_template" class="block text-sm font-medium text-black mb-1">Message Template</label>
                    <textarea id="message_template" name="message_template" rows="4"
                              placeholder="Use {variable} for dynamic content"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('message_template') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('notifications.rules.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Rule
            </button>
        </div>
    </form>
</div>
@endsection

