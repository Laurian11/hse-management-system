@extends('layouts.app')

@section('title', 'Edit Safety Communication')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('safety-communications.show', $communication) }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Communication
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Safety Communication</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('safety-communications.update', $communication) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                        <input type="text" id="title" name="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter communication title" value="{{ old('title', $communication->title) }}">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="communication_type" class="block text-sm font-medium text-gray-700 mb-1">Communication Type *</label>
                        <select id="communication_type" name="communication_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Type</option>
                            <option value="announcement" {{ old('communication_type', $communication->communication_type) == 'announcement' ? 'selected' : '' }}>Announcement</option>
                            <option value="alert" {{ old('communication_type', $communication->communication_type) == 'alert' ? 'selected' : '' }}>Safety Alert</option>
                            <option value="bulletin" {{ old('communication_type', $communication->communication_type) == 'bulletin' ? 'selected' : '' }}>Bulletin</option>
                            <option value="emergency" {{ old('communication_type', $communication->communication_type) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="reminder" {{ old('communication_type', $communication->communication_type) == 'reminder' ? 'selected' : '' }}>Reminder</option>
                            <option value="policy_update" {{ old('communication_type', $communication->communication_type) == 'policy_update' ? 'selected' : '' }}>Policy Update</option>
                            <option value="training_notice" {{ old('communication_type', $communication->communication_type) == 'training_notice' ? 'selected' : '' }}>Training Notice</option>
                        </select>
                        @error('communication_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority_level" class="block text-sm font-medium text-gray-700 mb-1">Priority Level *</label>
                        <select id="priority_level" name="priority_level" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Priority</option>
                            <option value="low" {{ old('priority_level', $communication->priority_level) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority_level', $communication->priority_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority_level', $communication->priority_level) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ old('priority_level', $communication->priority_level) == 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="emergency" {{ old('priority_level', $communication->priority_level) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                        </select>
                        @error('priority_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                        <textarea id="message" name="message" rows="6" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter your safety communication message">{{ old('message', $communication->message) }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Target Audience -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Target Audience</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-1">Target Audience *</label>
                        <select id="target_audience" name="target_audience" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                onchange="toggleTargetOptions()">
                            <option value="">Select Audience</option>
                            <option value="all_employees" {{ old('target_audience', $communication->target_audience) == 'all_employees' ? 'selected' : '' }}>All Employees</option>
                            <option value="specific_departments" {{ old('target_audience', $communication->target_audience) == 'specific_departments' ? 'selected' : '' }}>Specific Departments</option>
                            <option value="specific_roles" {{ old('target_audience', $communication->target_audience) == 'specific_roles' ? 'selected' : '' }}>Specific Roles</option>
                            <option value="specific_locations" {{ old('target_audience', $communication->target_audience) == 'specific_locations' ? 'selected' : '' }}>Specific Locations</option>
                            <option value="management_only" {{ old('target_audience', $communication->target_audience) == 'management_only' ? 'selected' : '' }}>Management Only</option>
                            <option value="supervisors_only" {{ old('target_audience', $communication->target_audience) == 'supervisors_only' ? 'selected' : '' }}>Supervisors Only</option>
                        </select>
                        @error('target_audience')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="target_departments_div" style="display: {{ old('target_audience', $communication->target_audience) == 'specific_departments' ? 'block' : 'none' }};">
                        <label for="target_departments" class="block text-sm font-medium text-gray-700 mb-1">Departments</label>
                        <select id="target_departments" name="target_departments[]" multiple
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ in_array($dept->id, old('target_departments', $communication->target_departments ?? [])) ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="target_roles_div" style="display: {{ old('target_audience', $communication->target_audience) == 'specific_roles' ? 'block' : 'none' }};">
                        <label for="target_roles" class="block text-sm font-medium text-gray-700 mb-1">Roles</label>
                        <select id="target_roles" name="target_roles[]" multiple
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ in_array($role->name, old('target_roles', $communication->target_roles ?? [])) ? 'selected' : '' }}>
                                    {{ $role->display_name ?? $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Delivery & Scheduling -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Delivery & Scheduling</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="delivery_method" class="block text-sm font-medium text-gray-700 mb-1">Delivery Method *</label>
                        <select id="delivery_method" name="delivery_method" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Method</option>
                            <option value="digital_signage" {{ old('delivery_method', $communication->delivery_method) == 'digital_signage' ? 'selected' : '' }}>Digital Signage</option>
                            <option value="mobile_push" {{ old('delivery_method', $communication->delivery_method) == 'mobile_push' ? 'selected' : '' }}>Mobile Push</option>
                            <option value="email" {{ old('delivery_method', $communication->delivery_method) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="sms" {{ old('delivery_method', $communication->delivery_method) == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="printed_notice" {{ old('delivery_method', $communication->delivery_method) == 'printed_notice' ? 'selected' : '' }}>Printed Notice</option>
                            <option value="video_conference" {{ old('delivery_method', $communication->delivery_method) == 'video_conference' ? 'selected' : '' }}>Video Conference</option>
                            <option value="in_person" {{ old('delivery_method', $communication->delivery_method) == 'in_person' ? 'selected' : '' }}>In Person</option>
                        </select>
                        @error('delivery_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="scheduled_send_time" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Send Time</label>
                        <input type="datetime-local" id="scheduled_send_time" name="scheduled_send_time"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('scheduled_send_time', $communication->scheduled_send_time ? $communication->scheduled_send_time->format('Y-m-d\TH:i') : '') }}">
                        @error('scheduled_send_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                        <input type="datetime-local" id="expires_at" name="expires_at"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('expires_at', $communication->expires_at ? $communication->expires_at->format('Y-m-d\TH:i') : '') }}">
                        @error('expires_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Acknowledgment -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Acknowledgment</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="requires_acknowledgment" name="requires_acknowledgment" value="1"
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                               {{ old('requires_acknowledgment', $communication->requires_acknowledgment) ? 'checked' : '' }}
                               onchange="toggleAcknowledgmentDeadline()">
                        <label for="requires_acknowledgment" class="ml-2 text-sm font-medium text-gray-700">
                            Requires Acknowledgment
                        </label>
                    </div>

                    <div id="acknowledgment_deadline_div" style="display: {{ old('requires_acknowledgment', $communication->requires_acknowledgment) ? 'block' : 'none' }};">
                        <label for="acknowledgment_deadline" class="block text-sm font-medium text-gray-700 mb-1">Acknowledgment Deadline</label>
                        <input type="datetime-local" id="acknowledgment_deadline" name="acknowledgment_deadline"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="{{ old('acknowledgment_deadline', $communication->acknowledgment_deadline ? $communication->acknowledgment_deadline->format('Y-m-d\TH:i') : '') }}">
                        @error('acknowledgment_deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3">
                <a href="{{ route('safety-communications.show', $communication) }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Update Communication
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleTargetOptions() {
    const targetAudience = document.getElementById('target_audience').value;
    document.getElementById('target_departments_div').style.display = targetAudience === 'specific_departments' ? 'block' : 'none';
    document.getElementById('target_roles_div').style.display = targetAudience === 'specific_roles' ? 'block' : 'none';
}

function toggleAcknowledgmentDeadline() {
    const requiresAck = document.getElementById('requires_acknowledgment').checked;
    document.getElementById('acknowledgment_deadline_div').style.display = requiresAck ? 'block' : 'none';
}
</script>
@endpush
@endsection

