@extends('layouts.app')

@section('title', 'Edit Notification Rule')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('notifications.rules.show', $rule) }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Edit Notification Rule</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('notifications.rules.update', $rule) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Rule Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-black mb-1">Rule Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $rule->name) }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="trigger_type" class="block text-sm font-medium text-black mb-1">Trigger Type *</label>
                    <select id="trigger_type" name="trigger_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="incident" {{ old('trigger_type', $rule->trigger_type) == 'incident' ? 'selected' : '' }}>Incident</option>
                        <option value="permit_expiry" {{ old('trigger_type', $rule->trigger_type) == 'permit_expiry' ? 'selected' : '' }}>Permit Expiry</option>
                        <option value="ppe_expiry" {{ old('trigger_type', $rule->trigger_type) == 'ppe_expiry' ? 'selected' : '' }}>PPE Expiry</option>
                        <option value="training_due" {{ old('trigger_type', $rule->trigger_type) == 'training_due' ? 'selected' : '' }}>Training Due</option>
                        <option value="audit_due" {{ old('trigger_type', $rule->trigger_type) == 'audit_due' ? 'selected' : '' }}>Audit Due</option>
                        <option value="certificate_expiry" {{ old('trigger_type', $rule->trigger_type) == 'certificate_expiry' ? 'selected' : '' }}>Certificate Expiry</option>
                    </select>
                </div>

                <div>
                    <label for="notification_channel" class="block text-sm font-medium text-black mb-1">Notification Channel *</label>
                    <select id="notification_channel" name="notification_channel" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="email" {{ old('notification_channel', $rule->notification_channel) == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="sms" {{ old('notification_channel', $rule->notification_channel) == 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="push" {{ old('notification_channel', $rule->notification_channel) == 'push' ? 'selected' : '' }}>Push Notification</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $rule->is_active) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-sm text-black">Active</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label for="message_template" class="block text-sm font-medium text-black mb-1">Message Template</label>
                    <textarea id="message_template" name="message_template" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('message_template', $rule->message_template) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('notifications.rules.show', $rule) }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Update Rule
            </button>
        </div>
    </form>
</div>
@endsection

