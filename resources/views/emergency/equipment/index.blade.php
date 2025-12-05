@extends('layouts.app')

@section('title', 'Emergency Equipment')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Emergency Equipment</h1>
                <p class="text-sm text-gray-500 mt-1">Manage emergency equipment inventory</p>
            </div>
            <div>
                <a href="{{ route('emergency.equipment.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Equipment
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Filters -->
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search equipment..."
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="equipment_type" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Types</option>
                <option value="fire_extinguisher" {{ request('equipment_type') == 'fire_extinguisher' ? 'selected' : '' }}>Fire Extinguisher</option>
                <option value="first_aid_kit" {{ request('equipment_type') == 'first_aid_kit' ? 'selected' : '' }}>First Aid Kit</option>
                <option value="emergency_exit" {{ request('equipment_type') == 'emergency_exit' ? 'selected' : '' }}>Emergency Exit</option>
            </select>
            <select name="status" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('emergency.equipment.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <!-- Equipment Table -->
    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Equipment Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Serial Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Next Inspection</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($equipment as $item)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">{{ $item->equipment_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ ucfirst(str_replace('_', ' ', $item->equipment_type)) }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $item->location }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $item->serial_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                            {{ $item->next_inspection_date ? $item->next_inspection_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $item->status == 'active' ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : '' }}
                                {{ $item->status == 'maintenance' ? 'bg-[#F5F5F5] text-[#FF9900] border-[#FF9900]' : '' }}
                                {{ $item->status == 'inactive' ? 'bg-[#F5F5F5] text-gray-500 border-gray-300' : '' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('emergency.equipment.show', $item) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('emergency.equipment.edit', $item) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No emergency equipment found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $equipment->links() }}
    </div>
</div>
@endsection

