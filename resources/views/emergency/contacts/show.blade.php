@extends('layouts.app')

@section('title', 'Emergency Contact: ' . $contact->name)

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.contacts.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $contact->name }}</h1>
                    <p class="text-sm text-gray-500">{{ $contact->organization ?? 'Emergency Contact' }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('emergency.contacts.edit', $contact) }}" class="bg-[#0066CC] text-white px-4 py-2 border border-[#0066CC] hover:bg-[#0052A3]">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white border border-gray-300 p-6">
        <h2 class="text-lg font-semibold text-black mb-4">Contact Details</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Name</dt>
                <dd class="mt-1 text-sm text-black">{{ $contact->name }}</dd>
            </div>
            @if($contact->organization)
            <div>
                <dt class="text-sm font-medium text-gray-500">Organization</dt>
                <dd class="mt-1 text-sm text-black">{{ $contact->organization }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">Contact Type</dt>
                <dd class="mt-1 text-sm text-black">{{ ucfirst(str_replace('_', ' ', $contact->contact_type)) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Primary Phone</dt>
                <dd class="mt-1 text-sm text-black">
                    <a href="tel:{{ $contact->phone_primary }}" class="text-[#0066CC] hover:underline">{{ $contact->phone_primary }}</a>
                </dd>
            </div>
            @if($contact->phone_secondary)
            <div>
                <dt class="text-sm font-medium text-gray-500">Secondary Phone</dt>
                <dd class="mt-1 text-sm text-black">
                    <a href="tel:{{ $contact->phone_secondary }}" class="text-[#0066CC] hover:underline">{{ $contact->phone_secondary }}</a>
                </dd>
            </div>
            @endif
            @if($contact->email)
            <div>
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-black">
                    <a href="mailto:{{ $contact->email }}" class="text-[#0066CC] hover:underline">{{ $contact->email }}</a>
                </dd>
            </div>
            @endif
            @if($contact->location)
            <div>
                <dt class="text-sm font-medium text-gray-500">Location</dt>
                <dd class="mt-1 text-sm text-black">{{ $contact->location }}</dd>
            </div>
            @endif
            @if($contact->specialization)
            <div>
                <dt class="text-sm font-medium text-gray-500">Specialization</dt>
                <dd class="mt-1 text-sm text-black">{{ $contact->specialization }}</dd>
            </div>
            @endif
            @if($contact->availability)
            <div>
                <dt class="text-sm font-medium text-gray-500">Availability</dt>
                <dd class="mt-1 text-sm text-black">{{ $contact->availability }}</dd>
            </div>
            @endif
            @if($contact->priority)
            <div>
                <dt class="text-sm font-medium text-gray-500">Priority</dt>
                <dd class="mt-1 text-sm text-black">{{ $contact->priority }}/10</dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs font-semibold border border-gray-300 {{ $contact->is_active ? 'bg-[#F5F5F5] text-[#0066CC] border-[#0066CC]' : 'bg-[#F5F5F5] text-gray-500 border-gray-300' }}">
                        {{ $contact->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
            @if($contact->address)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Address</dt>
                <dd class="mt-1 text-sm text-black">{{ $contact->address }}</dd>
            </div>
            @endif
            @if($contact->notes)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-black">{{ $contact->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>
</div>
@endsection

