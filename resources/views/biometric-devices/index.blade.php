@extends('layouts.app')

@section('title', 'Biometric Devices')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-fingerprint text-blue-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Biometric Devices</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('biometric-devices.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Device
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form method="GET" action="{{ route('biometric-devices.index') }}" class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                    <select name="company_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="device_category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <option value="attendance" {{ request('device_category') == 'attendance' ? 'selected' : '' }}>Employee Attendance</option>
                        <option value="toolbox_training" {{ request('device_category') == 'toolbox_training' ? 'selected' : '' }}>Toolbox & Training</option>
                        <option value="both" {{ request('device_category') == 'both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" value="{{ request('location') }}" placeholder="Search location..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Devices Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Sync</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($devices as $device)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="fas fa-fingerprint text-blue-600 mr-2"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $device->device_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $device->device_type }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $categoryLabels = [
                                        'attendance' => 'Attendance',
                                        'toolbox_training' => 'Toolbox & Training',
                                        'both' => 'Both'
                                    ];
                                    $category = $device->device_category ?? 'attendance';
                                    $label = $categoryLabels[$category] ?? 'Unknown';
                                    
                                    // Determine badge classes based on category
                                    $badgeClasses = match($category) {
                                        'attendance' => 'px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800',
                                        'toolbox_training' => 'px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800',
                                        'both' => 'px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800',
                                        default => 'px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800'
                                    };
                                    
                                    $icon = match($category) {
                                        'attendance' => 'clock',
                                        'toolbox_training' => 'chalkboard-teacher',
                                        'both' => 'tasks',
                                        default => 'question-circle'
                                    };
                                @endphp
                                <span class="{{ $badgeClasses }}">
                                    <i class="fas fa-{{ $icon }} mr-1"></i>
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $device->location_name }}</div>
                                @if($device->location_address)
                                    <div class="text-sm text-gray-500">{{ Str::limit($device->location_address, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $device->company->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $device->device_ip }}:{{ $device->port }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badgeClasses = match($device->status) {
                                        'active' => 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800',
                                        'inactive' => 'px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800',
                                        'maintenance' => 'px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800',
                                        'offline' => 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800',
                                        default => 'px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="{{ $badgeClasses }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($device->last_sync_at)
                                    {{ $device->last_sync_at->diffForHumans() }}
                                @else
                                    <span class="text-gray-400">Never</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('biometric-devices.show', $device) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('biometric-devices.edit', $device) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('biometric-devices.destroy', $device) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this device? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No devices found. <a href="{{ route('biometric-devices.create') }}" class="text-blue-600 hover:underline">Add your first device</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $devices->links() }}
        </div>
    </div>
</div>
@endsection

