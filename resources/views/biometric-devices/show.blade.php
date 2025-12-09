@extends('layouts.app')

@section('title', 'Device Details')

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
                    <h1 class="text-2xl font-bold text-gray-900">{{ $biometricDevice->device_name }}</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('biometric-devices.edit', $biometricDevice) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Device Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Device Information</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Device Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $biometricDevice->device_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Serial Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $biometricDevice->device_serial_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Device Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $biometricDevice->device_type }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @php
                                    $statusColors = [
                                        'active' => 'green',
                                        'inactive' => 'gray',
                                        'maintenance' => 'orange',
                                        'offline' => 'red'
                                    ];
                                    $color = $statusColors[$biometricDevice->status] ?? 'gray';
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ ucfirst($biometricDevice->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Company</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $biometricDevice->company->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $biometricDevice->location_name }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Network Configuration -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Network Configuration</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">IP Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $biometricDevice->device_ip }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Port</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $biometricDevice->port }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Connection Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ strtoupper($biometricDevice->connection_type) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Connected</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($biometricDevice->last_connected_at)
                                    {{ $biometricDevice->last_connected_at->diffForHumans() }}
                                @else
                                    <span class="text-gray-400">Never</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Work Hours -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Work Hours Configuration</h2>
                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Start Time</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($biometricDevice->work_start_time)->format('H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">End Time</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($biometricDevice->work_end_time)->format('H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Grace Period</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $biometricDevice->grace_period_minutes }} minutes</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Today's Stats -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Attendance</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total</span>
                                <span class="font-medium text-gray-900">{{ $stats['total_today'] }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Present</span>
                                <span class="font-medium text-green-600">{{ $stats['present_today'] }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Late</span>
                                <span class="font-medium text-yellow-600">{{ $stats['late_today'] }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Absent</span>
                                <span class="font-medium text-red-600">{{ $stats['absent_today'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <button onclick="testConnection()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plug mr-2"></i>Test Connection
                        </button>
                        <form action="{{ route('biometric-devices.sync-users', $biometricDevice) }}" method="POST" class="inline w-full">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-sync mr-2"></i>Sync Users
                            </button>
                        </form>
                        <form action="{{ route('biometric-devices.sync-attendance', $biometricDevice) }}" method="POST" class="inline w-full">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-download mr-2"></i>Sync Attendance
                            </button>
                        </form>
                        <a href="{{ route('biometric-devices.enrollment', $biometricDevice) }}" class="block w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-center">
                            <i class="fas fa-user-plus mr-2"></i>Manage Enrollment
                        </a>
                        <a href="{{ route('daily-attendance.index', ['device_id' => $biometricDevice->id]) }}" class="block w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-center">
                            <i class="fas fa-list mr-2"></i>View Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testConnection() {
    fetch('{{ route('biometric-devices.test-connection', $biometricDevice) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'online' || data.connected) {
            alert('Device is online and connected!');
        } else {
            alert('Device connection failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error testing connection: ' + error);
    });
}
</script>
@endsection

