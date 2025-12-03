@extends('layouts.app')

@section('title', 'Create Safety Communication')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('safety-communications.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Communications
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Create Safety Communication</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('safety-communications.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter communication title" value="{{ old('title') }}">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="communication_type" class="block text-sm font-medium text-gray-700 mb-1">Communication Type *</label>
                    <select id="communication_type" name="communication_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Type</option>
                        <option value="alert" {{ old('communication_type') == 'alert' ? 'selected' : '' }}>Safety Alert</option>
                        <option value="reminder" {{ old('communication_type') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                        <option value="announcement" {{ old('communication_type') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                        <option value="update" {{ old('communication_type') == 'update' ? 'selected' : '' }}>Update</option>
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
                        <option value="low" {{ old('priority_level') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority_level') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ old('priority_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('priority_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                    <textarea id="message" name="message" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Enter your safety communication message">{{ old('message') }}</textarea>
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
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Audience</option>
                        <option value="all_employees" {{ old('target_audience') == 'all_employees' ? 'selected' : '' }}>All Employees</option>
                        <option value="specific_departments" {{ old('target_audience') == 'specific_departments' ? 'selected' : '' }}>Specific Departments</option>
                        <option value="specific_roles" {{ old('target_audience') == 'specific_roles' ? 'selected' : '' }}>Specific Roles</option>
                        <option value="management_only" {{ old('target_audience') == 'management_only' ? 'selected' : '' }}>Management Only</option>
                    </select>
                    @error('target_audience')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="delivery_method" class="block text-sm font-medium text-gray-700 mb-1">Delivery Method *</label>
                    <select id="delivery_method" name="delivery_method" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Method</option>
                        <option value="email" {{ old('delivery_method') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="sms" {{ old('delivery_method') == 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="mobile_push" {{ old('delivery_method') == 'mobile_push' ? 'selected' : '' }}>Mobile Push</option>
                        <option value="digital_signage" {{ old('delivery_method') == 'digital_signage' ? 'selected' : '' }}>Digital Signage</option>
                    </select>
                    @error('delivery_method')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('safety-communications.index') }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Communication
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
