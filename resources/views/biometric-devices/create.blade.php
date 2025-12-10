@extends('layouts.app')

@section('title', 'Add Biometric Device')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('biometric-devices.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Devices
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Add Biometric Device</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Add Templates -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">Quick Add - HESU Devices</h3>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="fillDeviceTemplate('cfs-warehouse')" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-warehouse mr-1"></i>CFS Warehouse
                </button>
                <button type="button" onclick="fillDeviceTemplate('hesu-icd')" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-building mr-1"></i>HESU ICD
                </button>
                <button type="button" onclick="fillDeviceTemplate('cfs-office')" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-briefcase mr-1"></i>CFS Office
                </button>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('biometric-devices.store') }}" method="POST" class="space-y-6" id="deviceForm">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="device_name" class="block text-sm font-medium text-gray-700 mb-1">Device Name *</label>
                        <input type="text" id="device_name" name="device_name" 
                               value="{{ old('device_name') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('device_name') border-red-500 @enderror"
                               placeholder="e.g., Site A - Main Entrance">
                        @error('device_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="device_serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number *</label>
                        <input type="text" id="device_serial_number" name="device_serial_number" 
                               value="{{ old('device_serial_number') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('device_serial_number') border-red-500 @enderror"
                               placeholder="Device serial number">
                        @error('device_serial_number')
                            <div class="mt-1 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="device_type" class="block text-sm font-medium text-gray-700 mb-1">Device Type *</label>
                        <input type="text" id="device_type" name="device_type" 
                               value="{{ old('device_type', 'ZKTeco K40') }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('device_type') border-red-500 @enderror">
                        @error('device_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="device_category" class="block text-sm font-medium text-gray-700 mb-1">Device Category *</label>
                        <select id="device_category" name="device_category" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                onchange="updateCategorySettings()">
                            <option value="attendance" {{ old('device_category', 'attendance') == 'attendance' ? 'selected' : '' }}>
                                Employee Attendance
                            </option>
                            <option value="toolbox_training" {{ old('device_category') == 'toolbox_training' ? 'selected' : '' }}>
                                Toolbox Talk & Training
                            </option>
                            <option value="both" {{ old('device_category') == 'both' ? 'selected' : '' }}>
                                Both (Attendance & Training)
                            </option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <span id="category-description">Select the primary purpose of this device</span>
                        </p>
                        @error('device_category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="device_purpose" class="block text-sm font-medium text-gray-700 mb-1">Device Purpose (Optional)</label>
                        <textarea id="device_purpose" name="device_purpose" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                  placeholder="e.g., Main entrance attendance device for Site A">{{ old('device_purpose') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Optional description of the device's specific purpose</p>
                    </div>

                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company *</label>
                        <select id="company_id" name="company_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Location Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Location Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="location_name" class="block text-sm font-medium text-gray-700 mb-1">Location Name *</label>
                        <input type="text" id="location_name" name="location_name" 
                               value="{{ old('location_name') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('location_name') border-red-500 @enderror"
                               placeholder="e.g., Site A, Building B, Main Gate">
                        @error('location_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="location_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea id="location_address" name="location_address" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                  placeholder="Full address">{{ old('location_address') }}</textarea>
                    </div>

                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" step="any" id="latitude" name="latitude"
                               value="{{ old('latitude') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., -6.7924">
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" step="any" id="longitude" name="longitude"
                               value="{{ old('longitude') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., 39.2083">
                    </div>
                </div>
            </div>

            <!-- Network Configuration -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Network Configuration</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="device_ip" class="block text-sm font-medium text-gray-700 mb-1">IP Address *</label>
                        <input type="text" id="device_ip" name="device_ip" 
                               value="{{ old('device_ip') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('device_ip') border-red-500 @enderror"
                               placeholder="192.168.1.201">
                        @error('device_ip')
                            <div class="mt-1 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="port" class="block text-sm font-medium text-gray-700 mb-1">Port *</label>
                        <input type="number" id="port" name="port" 
                               value="{{ old('port', '4370') }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('port') border-red-500 @enderror">
                        @error('port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                        <input type="text" id="api_key" name="api_key"
                               value="{{ old('api_key') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Optional API key">
                    </div>

                    <div>
                        <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-1">Connection Type *</label>
                        <select id="connection_type" name="connection_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="both" {{ old('connection_type') == 'both' ? 'selected' : '' }}>Both (HTTP & TCP)</option>
                            <option value="http" {{ old('connection_type') == 'http' ? 'selected' : '' }}>HTTP Only</option>
                            <option value="tcp" {{ old('connection_type') == 'tcp' ? 'selected' : '' }}>TCP Only</option>
                        </select>
                    </div>

                    <!-- Advanced Network Settings (Collapsible) -->
                    <div class="md:col-span-2">
                        <button type="button" onclick="toggleAdvancedNetwork()" class="text-sm text-blue-600 hover:text-blue-700 flex items-center">
                            <i class="fas fa-chevron-down mr-1" id="advanced-network-icon"></i>
                            <span>Advanced Network Settings</span>
                        </button>
                    </div>

                    <div id="advanced-network-settings" class="md:col-span-2 hidden space-y-4 pt-4 border-t">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="public_ip" class="block text-sm font-medium text-gray-700 mb-1">Public IP (Optional)</label>
                                <input type="text" id="public_ip" name="public_ip" value="{{ old('public_ip') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                       placeholder="For remote/internet connections">
                                <p class="mt-1 text-xs text-gray-500">Use for devices accessible via internet</p>
                            </div>

                            <div>
                                <label for="network_type" class="block text-sm font-medium text-gray-700 mb-1">Network Type</label>
                                <select id="network_type" name="network_type"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Auto-detect</option>
                                    <option value="local" {{ old('network_type') == 'local' ? 'selected' : '' }}>Local Network</option>
                                    <option value="remote" {{ old('network_type') == 'remote' ? 'selected' : '' }}>Remote Network</option>
                                    <option value="internet" {{ old('network_type') == 'internet' ? 'selected' : '' }}>Internet</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Auto-detected if not specified</p>
                            </div>

                            <div>
                                <label for="connection_timeout" class="block text-sm font-medium text-gray-700 mb-1">Connection Timeout (seconds)</label>
                                <input type="number" id="connection_timeout" name="connection_timeout" 
                                       value="{{ old('connection_timeout', '10') }}" min="5" max="120"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Default: 10s (local), 20s (remote), 30s (internet)</p>
                            </div>

                            <div class="flex items-center pt-6">
                                <input type="checkbox" id="auto_detect_network" name="auto_detect_network" value="1" checked
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="auto_detect_network" class="ml-2 text-sm text-gray-700">
                                    Auto-detect network type
                                </label>
                            </div>

                            <div class="flex items-center pt-6">
                                <input type="checkbox" id="requires_vpn" name="requires_vpn" value="1" {{ old('requires_vpn') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="requires_vpn" class="ml-2 text-sm text-gray-700">
                                    Requires VPN connection
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Hours Configuration -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Work Hours Configuration</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="work_start_time" class="block text-sm font-medium text-gray-700 mb-1">Work Start Time *</label>
                        <input type="time" id="work_start_time" name="work_start_time" 
                               value="{{ old('work_start_time', '08:00') }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('work_start_time') border-red-500 @enderror">
                        @error('work_start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="work_end_time" class="block text-sm font-medium text-gray-700 mb-1">Work End Time *</label>
                        <input type="time" id="work_end_time" name="work_end_time" 
                               value="{{ old('work_end_time', '17:00') }}" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('work_end_time') border-red-500 @enderror">
                        @error('work_end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="grace_period_minutes" class="block text-sm font-medium text-gray-700 mb-1">Grace Period (minutes) *</label>
                        <input type="number" id="grace_period_minutes" name="grace_period_minutes" 
                               value="{{ old('grace_period_minutes', '15') }}" 
                               min="0" max="60" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('grace_period_minutes') border-red-500 @enderror">
                        @error('grace_period_minutes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Settings</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="auto_sync_enabled" name="auto_sync_enabled" value="1" 
                               {{ old('auto_sync_enabled', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="auto_sync_enabled" class="ml-2 text-sm text-gray-700">Enable Auto Sync</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="daily_attendance_enabled" name="daily_attendance_enabled" value="1" 
                               {{ old('daily_attendance_enabled', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="daily_attendance_enabled" class="ml-2 text-sm text-gray-700">
                            Enable Daily Attendance Tracking
                            <span class="text-xs text-gray-500">(for employee check-in/check-out)</span>
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="toolbox_attendance_enabled" name="toolbox_attendance_enabled" value="1" 
                               {{ old('toolbox_attendance_enabled', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="toolbox_attendance_enabled" class="ml-2 text-sm text-gray-700">
                            Enable Toolbox Talk & Training Attendance
                            <span class="text-xs text-gray-500">(for toolbox talks and training sessions)</span>
                        </label>
                    </div>

                    <div>
                        <label for="sync_interval_minutes" class="block text-sm font-medium text-gray-700 mb-1">Sync Interval (minutes)</label>
                        <input type="number" id="sync_interval_minutes" name="sync_interval_minutes" 
                               value="{{ old('sync_interval_minutes', '5') }}" 
                               min="1" max="60"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h2>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Any additional notes about this device...">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('biometric-devices.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Create Device
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Update category settings based on selection
function updateCategorySettings() {
    const category = document.getElementById('device_category').value;
    const dailyAttendance = document.getElementById('daily_attendance_enabled');
    const toolboxAttendance = document.getElementById('toolbox_attendance_enabled');
    const description = document.getElementById('category-description');
    
    // Update description
    const descriptions = {
        'attendance': 'This device will be used for employee daily attendance tracking (check-in/check-out)',
        'toolbox_training': 'This device will be used for toolbox talks and training session attendance',
        'both': 'This device can be used for both daily attendance and toolbox/training attendance'
    };
    description.textContent = descriptions[category] || 'Select the primary purpose of this device';
    
    // Auto-update checkboxes based on category
    if (category === 'attendance') {
        dailyAttendance.checked = true;
        toolboxAttendance.checked = false;
        dailyAttendance.disabled = false;
        toolboxAttendance.disabled = false;
    } else if (category === 'toolbox_training') {
        dailyAttendance.checked = false;
        toolboxAttendance.checked = true;
        dailyAttendance.disabled = false;
        toolboxAttendance.disabled = false;
    } else if (category === 'both') {
        dailyAttendance.checked = true;
        toolboxAttendance.checked = true;
        dailyAttendance.disabled = false;
        toolboxAttendance.disabled = false;
    }
}

// Toggle advanced network settings
function toggleAdvancedNetwork() {
    const settings = document.getElementById('advanced-network-settings');
    const icon = document.getElementById('advanced-network-icon');
    if (settings.classList.contains('hidden')) {
        settings.classList.remove('hidden');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        settings.classList.add('hidden');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCategorySettings();
});

// Device templates for quick add
const deviceTemplates = {
    'cfs-warehouse': {
        device_name: 'CFS Warehouse',
        device_serial_number: 'AEWD233960244',
        device_type: 'ZKTeco K40',
        device_category: 'attendance',
        device_ip: '192.168.60.251',
        port: '4370',
        connection_type: 'both',
        location_name: 'CFS Warehouse',
        work_start_time: '08:00',
        work_end_time: '17:00',
        grace_period_minutes: '15'
    },
    'hesu-icd': {
        device_name: 'HESU ICD',
        device_serial_number: 'AEWD233960257',
        device_type: 'ZKTeco K40',
        device_category: 'attendance',
        device_ip: '192.168.40.68',
        port: '4370',
        connection_type: 'both',
        location_name: 'HESU ICD',
        work_start_time: '08:00',
        work_end_time: '17:00',
        grace_period_minutes: '15'
    },
    'cfs-office': {
        device_name: 'CFS Office',
        device_serial_number: 'ZTC8235000106',
        device_type: 'ZKTeco K40',
        device_category: 'attendance',
        device_ip: '192.168.40.201',
        port: '4370',
        connection_type: 'both',
        location_name: 'CFS Office',
        work_start_time: '08:00',
        work_end_time: '17:00',
        grace_period_minutes: '15'
    }
};

function fillDeviceTemplate(templateKey) {
    const template = deviceTemplates[templateKey];
    if (!template) return;

    // Fill all form fields
    Object.keys(template).forEach(key => {
        const field = document.getElementById(key) || document.querySelector(`[name="${key}"]`);
        if (field) {
            if (field.type === 'checkbox') {
                field.checked = template[key] === '1' || template[key] === true;
            } else {
                field.value = template[key];
            }
        }
    });

    // Update category settings if category was set
    if (template.device_category) {
        updateCategorySettings();
    }

    // Scroll to top of form
    document.getElementById('deviceForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Show success message
    const message = document.createElement('div');
    message.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    message.innerHTML = `<i class="fas fa-check-circle mr-2"></i>Form pre-filled with ${template.device_name} details`;
    document.body.appendChild(message);
    
    setTimeout(() => {
        message.remove();
    }, 3000);
}
</script>
@endsection

