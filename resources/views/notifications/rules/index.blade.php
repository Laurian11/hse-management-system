@extends('layouts.app')

@section('title', 'Notification Rules')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Notification Rules</h1>
                <p class="text-sm text-gray-500 mt-1">Email/SMS/push notifications configuration</p>
            </div>
            <div>
                <a href="{{ route('notifications.rules.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Rule
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="trigger_type" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Triggers</option>
                <option value="incident" {{ request('trigger_type') == 'incident' ? 'selected' : '' }}>Incident</option>
                <option value="permit_expiry" {{ request('trigger_type') == 'permit_expiry' ? 'selected' : '' }}>Permit Expiry</option>
                <option value="ppe_expiry" {{ request('trigger_type') == 'ppe_expiry' ? 'selected' : '' }}>PPE Expiry</option>
                <option value="training_due" {{ request('trigger_type') == 'training_due' ? 'selected' : '' }}>Training Due</option>
            </select>
            <select name="notification_channel" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Channels</option>
                <option value="email" {{ request('notification_channel') == 'email' ? 'selected' : '' }}>Email</option>
                <option value="sms" {{ request('notification_channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                <option value="push" {{ request('notification_channel') == 'push' ? 'selected' : '' }}>Push</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('notifications.rules.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Trigger Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Channel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Days Before</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($rules as $rule)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 text-sm font-medium text-black">{{ $rule->name }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $rule->trigger_type)) }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ ucfirst($rule->notification_channel) }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $rule->days_before ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300
                                {{ $rule->is_active ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500 border-gray-500' }}">
                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('notifications.rules.show', $rule) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('notifications.rules.edit', $rule) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No rules found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $rules->links() }}</div>
</div>
@endsection

