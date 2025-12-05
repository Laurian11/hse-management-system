@extends('layouts.app')

@section('title', 'Create Emergency Contact')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.contacts.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Emergency Contact</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('emergency.contacts.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Contact Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Contact Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-black mb-1">Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('name')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="organization" class="block text-sm font-medium text-black mb-1">Organization</label>
                    <input type="text" id="organization" name="organization" value="{{ old('organization') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="contact_type" class="block text-sm font-medium text-black mb-1">Contact Type *</label>
                    <input type="text" id="contact_type" name="contact_type" required value="{{ old('contact_type') }}"
                           placeholder="e.g., Fire Department, Police, Hospital"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('contact_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_primary" class="block text-sm font-medium text-black mb-1">Primary Phone *</label>
                    <input type="text" id="phone_primary" name="phone_primary" required value="{{ old('phone_primary') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('phone_primary')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_secondary" class="block text-sm font-medium text-black mb-1">Secondary Phone</label>
                    <input type="text" id="phone_secondary" name="phone_secondary" value="{{ old('phone_secondary') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-black mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('email')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-black mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="specialization" class="block text-sm font-medium text-black mb-1">Specialization</label>
                    <input type="text" id="specialization" name="specialization" value="{{ old('specialization') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-black mb-1">Priority (1-10)</label>
                    <input type="number" id="priority" name="priority" min="1" max="10" value="{{ old('priority') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-black mb-1">Address</label>
                    <textarea id="address" name="address" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('address') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="availability" class="block text-sm font-medium text-black mb-1">Availability</label>
                    <input type="text" id="availability" name="availability" value="{{ old('availability') }}"
                           placeholder="e.g., 24/7, Mon-Fri 9am-5pm"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes') }}</textarea>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                        <span class="ml-2 text-sm text-black">Active</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('emergency.contacts.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Contact
            </button>
        </div>
    </form>
</div>
@endsection

