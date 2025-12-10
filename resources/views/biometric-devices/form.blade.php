<!-- Basic Information -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="device_name" class="block text-sm font-medium text-gray-700 mb-1">Device Name *</label>
            <input type="text" id="device_name" name="device_name" value="{{ old('device_name', $biometricDevice->device_name ?? '') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="e.g., Site A - Main Entrance">
            @error('device_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="device_serial_number" class="block text-sm font-medium text-gray-700 mb-1">Serial Number *</label>
            <input type="text" id="device_serial_number" name="device_serial_number" value="{{ old('device_serial_number', $biometricDevice->device_serial_number ?? '') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            @error('device_serial_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="device_type" class="block text-sm font-medium text-gray-700 mb-1">Device Type *</label>
            <input type="text" id="device_type" name="device_type" value="{{ old('device_type', $biometricDevice->device_type ?? 'ZKTeco K40') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="device_category" class="block text-sm font-medium text-gray-700 mb-1">Device Category *</label>
            <select id="device_category" name="device_category" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    onchange="updateCategorySettings()">
                <option value="attendance" {{ old('device_category', $biometricDevice->device_category ?? 'attendance') == 'attendance' ? 'selected' : '' }}>
                    Employee Attendance
                </option>
                <option value="toolbox_training" {{ old('device_category', $biometricDevice->device_category ?? '') == 'toolbox_training' ? 'selected' : '' }}>
                    Toolbox Talk & Training
                </option>
                <option value="both" {{ old('device_category', $biometricDevice->device_category ?? '') == 'both' ? 'selected' : '' }}>
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
                      placeholder="e.g., Main entrance attendance device for Site A">{{ old('device_purpose', $biometricDevice->device_purpose ?? '') }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Optional description of the device's specific purpose</p>
        </div>

        <div>
            <label for="company_id" class="block text-sm font-medium text-gray-700 mb-1">Company *</label>
            <select id="company_id" name="company_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Select Company</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $biometricDevice->company_id ?? '') == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<!-- Location Information -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Location Information</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <label for="location_name" class="block text-sm font-medium text-gray-700 mb-1">Location Name *</label>
            <input type="text" id="location_name" name="location_name" value="{{ old('location_name', $biometricDevice->location_name ?? '') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="md:col-span-2">
            <label for="location_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea id="location_address" name="location_address" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('location_address', $biometricDevice->location_address ?? '') }}</textarea>
        </div>

        <div>
            <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
            <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude', $biometricDevice->latitude ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
            <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude', $biometricDevice->longitude ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
</div>

<!-- Network Configuration -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Network Configuration</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="device_ip" class="block text-sm font-medium text-gray-700 mb-1">IP Address *</label>
            <input type="text" id="device_ip" name="device_ip" value="{{ old('device_ip', $biometricDevice->device_ip ?? '') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="port" class="block text-sm font-medium text-gray-700 mb-1">Port *</label>
            <input type="number" id="port" name="port" value="{{ old('port', $biometricDevice->port ?? 4370) }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="api_key" class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
            <input type="text" id="api_key" name="api_key" value="{{ old('api_key', $biometricDevice->api_key ?? '') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="connection_type" class="block text-sm font-medium text-gray-700 mb-1">Connection Type *</label>
            <select id="connection_type" name="connection_type" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="both" {{ old('connection_type', $biometricDevice->connection_type ?? 'both') == 'both' ? 'selected' : '' }}>Both (HTTP & TCP)</option>
                <option value="http" {{ old('connection_type', $biometricDevice->connection_type ?? '') == 'http' ? 'selected' : '' }}>HTTP Only</option>
                <option value="tcp" {{ old('connection_type', $biometricDevice->connection_type ?? '') == 'tcp' ? 'selected' : '' }}>TCP Only</option>
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
            <input type="time" id="work_start_time" name="work_start_time" 
                   value="{{ old('work_start_time', $biometricDevice->work_start_time ? \Carbon\Carbon::parse($biometricDevice->work_start_time)->format('H:i') : '08:00') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="work_end_time" class="block text-sm font-medium text-gray-700 mb-1">Work End Time *</label>
            <input type="time" id="work_end_time" name="work_end_time" 
                   value="{{ old('work_end_time', $biometricDevice->work_end_time ? \Carbon\Carbon::parse($biometricDevice->work_end_time)->format('H:i') : '17:00') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="grace_period_minutes" class="block text-sm font-medium text-gray-700 mb-1">Grace Period (minutes) *</label>
            <input type="number" id="grace_period_minutes" name="grace_period_minutes" 
                   value="{{ old('grace_period_minutes', $biometricDevice->grace_period_minutes ?? 15) }}" min="0" max="60" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
    </div>
</div>

<!-- Settings -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Settings</h2>
    
    <div class="space-y-4">
        <div class="flex items-center">
            <input type="checkbox" id="auto_sync_enabled" name="auto_sync_enabled" value="1" 
                   {{ old('auto_sync_enabled', $biometricDevice->auto_sync_enabled ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="auto_sync_enabled" class="ml-2 text-sm text-gray-700">Enable Auto Sync</label>
        </div>

        <div class="flex items-center">
            <input type="checkbox" id="daily_attendance_enabled" name="daily_attendance_enabled" value="1"
                   {{ old('daily_attendance_enabled', $biometricDevice->daily_attendance_enabled ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="daily_attendance_enabled" class="ml-2 text-sm text-gray-700">
                Enable Daily Attendance Tracking
                <span class="text-xs text-gray-500">(for employee check-in/check-out)</span>
            </label>
        </div>

        <div class="flex items-center">
            <input type="checkbox" id="toolbox_attendance_enabled" name="toolbox_attendance_enabled" value="1"
                   {{ old('toolbox_attendance_enabled', $biometricDevice->toolbox_attendance_enabled ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            <label for="toolbox_attendance_enabled" class="ml-2 text-sm text-gray-700">
                Enable Toolbox Talk & Training Attendance
                <span class="text-xs text-gray-500">(for toolbox talks and training sessions)</span>
            </label>
        </div>

        <div>
            <label for="sync_interval_minutes" class="block text-sm font-medium text-gray-700 mb-1">Sync Interval (minutes)</label>
            <input type="number" id="sync_interval_minutes" name="sync_interval_minutes" 
                   value="{{ old('sync_interval_minutes', $biometricDevice->sync_interval_minutes ?? 5) }}" min="1" max="60"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="active" {{ old('status', $biometricDevice->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $biometricDevice->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="maintenance" {{ old('status', $biometricDevice->status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="offline" {{ old('status', $biometricDevice->status ?? '') == 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
        </div>
    </div>
</div>

<!-- Notes -->
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Notes</h2>
    <textarea id="notes" name="notes" rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('notes', $biometricDevice->notes ?? '') }}</textarea>
</div>

<script>
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCategorySettings();
});
</script>

