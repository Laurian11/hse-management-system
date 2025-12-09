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
            <p class="text-xs text-blue-700 mb-3">Click to pre-fill form with your device information:</p>
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

        <form action="{{ route('biometric-devices.store') }}" method="POST" class="space-y-6" id="deviceForm">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="device_name" class="block text-sm font-medium text-gray-700 mb-1">Device Name *</label>
                        <input type="text" id="device_name" name="device_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., Site A - Main Entrance">
                        @error('device_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="device_serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number *</label>
                        <input type="text" id="device_serial_number" name="device_serial_number" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="Device serial number">
                        @error('device_serial_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="device_type" class="block text-sm font-medium text-gray-700 mb-1">Device Type *</label>
                        <input type="text" id="device_type" name="device_type" value="ZKTeco K40" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('device_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                        <input type="text" id="location_name" name="location_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., Site A, Building B, Main Gate">
                        @error('location_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="location_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea id="location_address" name="location_address" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                  placeholder="Full address"></textarea>
                    </div>

                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input type="number" step="any" id="latitude" name="latitude"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="e.g., -6.7924">
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input type="number" step="any" id="longitude" name="longitude"
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
                        <input type="text" id="device_ip" name="device_ip" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="192.168.1.201">
                        @error('device_ip')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="port" class="block text-sm font-medium text-gray-700 mb-1">Port *</label>
                        <input type="number" id="port" name="port" value="4370" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                        <input type="text" id="api_key" name="api_key"
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
                </div>
            </div>

            <!-- Work Hours Configuration -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Work Hours Configuration</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="work_start_time" class="block text-sm font-medium text-gray-700 mb-1">Work Start Time *</label>
                        <input type="time" id="work_start_time" name="work_start_time" value="08:00" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="work_end_time" class="block text-sm font-medium text-gray-700 mb-1">Work End Time *</label>
                        <input type="time" id="work_end_time" name="work_end_time" value="17:00" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="grace_period_minutes" class="block text-sm font-medium text-gray-700 mb-1">Grace Period (minutes) *</label>
                        <input type="number" id="grace_period_minutes" name="grace_period_minutes" value="15" min="0" max="60" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Settings</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="auto_sync_enabled" name="auto_sync_enabled" value="1" checked
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="auto_sync_enabled" class="ml-2 text-sm text-gray-700">Enable Auto Sync</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="daily_attendance_enabled" name="daily_attendance_enabled" value="1" checked
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="daily_attendance_enabled" class="ml-2 text-sm text-gray-700">Enable Daily Attendance Tracking</label>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="toolbox_attendance_enabled" name="toolbox_attendance_enabled" value="1" checked
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="toolbox_attendance_enabled" class="ml-2 text-sm text-gray-700">Enable Toolbox Talk Attendance</label>
                    </div>

                    <div>
                        <label for="sync_interval_minutes" class="block text-sm font-medium text-gray-700 mb-1">Sync Interval (minutes)</label>
                        <input type="number" id="sync_interval_minutes" name="sync_interval_minutes" value="5" min="1" max="60"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h2>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Any additional notes about this device..."></textarea>
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
// Device templates for quick add
const deviceTemplates = {
    'cfs-warehouse': {
        device_name: 'CFS Warehouse',
        device_serial_number: 'AEWD233960244',
        device_type: 'ZKTeco K40',
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

