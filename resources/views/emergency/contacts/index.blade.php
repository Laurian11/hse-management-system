@extends('layouts.app')

@section('title', 'Emergency Contacts')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div>
                <h1 class="text-2xl font-bold text-black">Emergency Contacts</h1>
                <p class="text-sm text-gray-500 mt-1">Manage emergency contact information</p>
            </div>
            <div>
                <a href="{{ route('emergency.contacts.create') }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-plus mr-2"></i>New Contact
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Filters -->
    <div class="bg-white border border-gray-300 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search contacts..."
                   class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
            <select name="contact_type" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Types</option>
                <option value="fire_department" {{ request('contact_type') == 'fire_department' ? 'selected' : '' }}>Fire Department</option>
                <option value="police" {{ request('contact_type') == 'police' ? 'selected' : '' }}>Police</option>
                <option value="ambulance" {{ request('contact_type') == 'ambulance' ? 'selected' : '' }}>Ambulance</option>
                <option value="hospital" {{ request('contact_type') == 'hospital' ? 'selected' : '' }}>Hospital</option>
            </select>
            <select name="is_active" class="px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                <option value="">All Status</option>
                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">Filter</button>
                <a href="{{ route('emergency.contacts.index') }}" class="px-4 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Reset</a>
            </div>
        </form>
    </div>

    <!-- Contacts Table -->
    <div class="bg-white border border-gray-300 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-[#F5F5F5]">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Organization</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Contact Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Primary Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-300">
                @forelse($contacts as $contact)
                    <tr class="hover:bg-[#F5F5F5]">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-black">{{ $contact->name }}</td>
                        <td class="px-6 py-4 text-sm text-black">{{ $contact->organization ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ ucfirst(str_replace('_', ' ', $contact->contact_type)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $contact->phone_primary }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $contact->email ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $contact->is_active ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500 border-gray-300' }}">
                                {{ $contact->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('emergency.contacts.show', $contact) }}" class="text-[#0066CC] hover:underline mr-3">View</a>
                            <a href="{{ route('emergency.contacts.edit', $contact) }}" class="text-[#0066CC] hover:underline">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No emergency contacts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $contacts->links() }}
    </div>
</div>
@endsection

