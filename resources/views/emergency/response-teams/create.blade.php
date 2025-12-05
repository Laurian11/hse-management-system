@extends('layouts.app')

@section('title', 'Create Emergency Response Team')

@section('content')
<div class="bg-white border-b border-gray-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('emergency.response-teams.index') }}" class="text-gray-500 hover:text-black">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <h1 class="text-2xl font-bold text-black">Create Emergency Response Team</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('emergency.response-teams.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white border border-gray-300 p-6">
            <h2 class="text-lg font-semibold text-black mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-black mb-1">Team Name *</label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                    @error('name')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="team_type" class="block text-sm font-medium text-black mb-1">Team Type *</label>
                    <select id="team_type" name="team_type" required
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="fire_warden" {{ old('team_type') == 'fire_warden' ? 'selected' : '' }}>Fire Warden</option>
                        <option value="first_aid" {{ old('team_type') == 'first_aid' ? 'selected' : '' }}>First Aid</option>
                        <option value="evacuation" {{ old('team_type') == 'evacuation' ? 'selected' : '' }}>Evacuation</option>
                        <option value="search_rescue" {{ old('team_type') == 'search_rescue' ? 'selected' : '' }}>Search & Rescue</option>
                        <option value="hazmat" {{ old('team_type') == 'hazmat' ? 'selected' : '' }}>HAZMAT</option>
                        <option value="security" {{ old('team_type') == 'security' ? 'selected' : '' }}>Security</option>
                        <option value="medical" {{ old('team_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                        <option value="general" {{ old('team_type') == 'general' ? 'selected' : '' }}>General</option>
                    </select>
                    @error('team_type')
                        <p class="mt-1 text-sm text-[#CC0000]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-black mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="responsibilities" class="block text-sm font-medium text-black mb-1">Responsibilities</label>
                    <textarea id="responsibilities" name="responsibilities" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('responsibilities') }}</textarea>
                </div>

                <div>
                    <label for="team_leader_id" class="block text-sm font-medium text-black mb-1">Team Leader</label>
                    <select id="team_leader_id" name="team_leader_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Team Leader</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('team_leader_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="deputy_leader_id" class="block text-sm font-medium text-black mb-1">Deputy Leader</label>
                    <select id="deputy_leader_id" name="deputy_leader_id"
                            class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                        <option value="">Select Deputy Leader</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('deputy_leader_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="next_training_date" class="block text-sm font-medium text-black mb-1">Next Training Date</label>
                    <input type="date" id="next_training_date" name="next_training_date" value="{{ old('next_training_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_24_7" value="1" {{ old('is_24_7') ? 'checked' : '' }}
                               class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                        <span class="ml-2 text-sm text-black">24/7 Availability</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-[#0066CC] focus:ring-[#0066CC] border-gray-300">
                        <span class="ml-2 text-sm text-black">Active</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-black mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 focus:border-[#0066CC] focus:ring-1 focus:ring-[#0066CC]">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('emergency.response-teams.index') }}" class="px-6 py-2 border border-gray-300 hover:bg-[#F5F5F5]">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-[#0066CC] text-white border border-[#0066CC] hover:bg-[#0052A3]">
                Create Team
            </button>
        </div>
    </form>
</div>
@endsection

