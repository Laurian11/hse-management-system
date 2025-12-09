@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('profile.show') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Profile
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            <button onclick="showTab('personal')" id="tab-personal" class="tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600">
                Personal Information
            </button>
            <button onclick="showTab('password')" id="tab-password" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                Change Password
            </button>
        </nav>
    </div>

    <!-- Personal Information Tab -->
    <div id="tab-content-personal" class="tab-content">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Profile Photo -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Photo</h2>
                <div class="flex items-center space-x-6">
                    <div>
                        @if($user->profile_photo)
                            <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}" 
                                 class="h-24 w-24 rounded-full object-cover">
                        @else
                            <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-user text-gray-600 text-3xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-2 text-xs text-gray-500">JPG, PNG or GIF. Max size: 2MB</p>
                        @if($user->profile_photo)
                        <form action="{{ route('profile.delete-photo') }}" method="POST" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete your profile photo?')">
                                <i class="fas fa-trash mr-1"></i>Delete Photo
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Personal Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                        <input type="text" id="nationality" name="nationality" value="{{ old('nationality', $user->nationality) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('nationality')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="blood_group" class="block text-sm font-medium text-gray-700 mb-1">Blood Group</label>
                        <select id="blood_group" name="blood_group"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Blood Group</option>
                            <option value="A+" {{ old('blood_group', $user->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                            <option value="A-" {{ old('blood_group', $user->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                            <option value="B+" {{ old('blood_group', $user->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                            <option value="B-" {{ old('blood_group', $user->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                            <option value="AB+" {{ old('blood_group', $user->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                            <option value="AB-" {{ old('blood_group', $user->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                            <option value="O+" {{ old('blood_group', $user->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                            <option value="O-" {{ old('blood_group', $user->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                        </select>
                        @error('blood_group')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Emergency Contacts -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Emergency Contacts</h2>
                    <button type="button" onclick="addEmergencyContact()" class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-plus mr-1"></i>Add Contact
                    </button>
                </div>
                <div id="emergency-contacts-container" class="space-y-4">
                    @if(old('emergency_contacts'))
                        @foreach(old('emergency_contacts') as $index => $contact)
                            <div class="emergency-contact border border-gray-200 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="text" name="emergency_contacts[{{ $index }}][name]" value="{{ $contact['name'] ?? '' }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                                        <input type="text" name="emergency_contacts[{{ $index }}][relationship]" value="{{ $contact['relationship'] ?? '' }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <div class="flex space-x-2">
                                            <input type="text" name="emergency_contacts[{{ $index }}][phone]" value="{{ $contact['phone'] ?? '' }}"
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <button type="button" onclick="removeEmergencyContact(this)" class="px-3 py-2 text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @elseif($user->emergency_contacts && count($user->emergency_contacts) > 0)
                        @foreach($user->emergency_contacts as $index => $contact)
                            <div class="emergency-contact border border-gray-200 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input type="text" name="emergency_contacts[{{ $index }}][name]" value="{{ $contact['name'] ?? '' }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                                        <input type="text" name="emergency_contacts[{{ $index }}][relationship]" value="{{ $contact['relationship'] ?? '' }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <div class="flex space-x-2">
                                            <input type="text" name="emergency_contacts[{{ $index }}][phone]" value="{{ $contact['phone'] ?? '' }}"
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <button type="button" onclick="removeEmergencyContact(this)" class="px-3 py-2 text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Known Allergies -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Known Allergies</h2>
                    <button type="button" onclick="addAllergy()" class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-plus mr-1"></i>Add Allergy
                    </button>
                </div>
                <div id="allergies-container" class="flex flex-wrap gap-2">
                    @if(old('known_allergies'))
                        @foreach(old('known_allergies') as $index => $allergy)
                            <div class="allergy-item flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full">
                                <input type="text" name="known_allergies[]" value="{{ $allergy }}" 
                                       class="bg-transparent border-0 focus:ring-0 text-sm">
                                <button type="button" onclick="removeAllergy(this)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        @endforeach
                    @elseif($user->known_allergies && count($user->known_allergies) > 0)
                        @foreach($user->known_allergies as $allergy)
                            <div class="allergy-item flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full">
                                <input type="text" name="known_allergies[]" value="{{ $allergy }}" 
                                       class="bg-transparent border-0 focus:ring-0 text-sm">
                                <button type="button" onclick="removeAllergy(this)" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('profile.show') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Tab -->
    <div id="tab-content-password" class="tab-content hidden">
        <form action="{{ route('profile.update-password') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h2>
                <div class="space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Must be at least 8 characters long</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('profile.show') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-key mr-2"></i>Update Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tab) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active styles from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById('tab-content-' + tab).classList.remove('hidden');
    
    // Add active styles to selected tab
    const selectedTab = document.getElementById('tab-' + tab);
    selectedTab.classList.remove('border-transparent', 'text-gray-500');
    selectedTab.classList.add('border-blue-500', 'text-blue-600');
}

let emergencyContactIndex = {{ old('emergency_contacts') ? count(old('emergency_contacts')) : ($user->emergency_contacts ? count($user->emergency_contacts) : 0) }};

function addEmergencyContact() {
    const container = document.getElementById('emergency-contacts-container');
    const contactDiv = document.createElement('div');
    contactDiv.className = 'emergency-contact border border-gray-200 rounded-lg p-4';
    contactDiv.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="emergency_contacts[${emergencyContactIndex}][name]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                <input type="text" name="emergency_contacts[${emergencyContactIndex}][relationship]"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <div class="flex space-x-2">
                    <input type="text" name="emergency_contacts[${emergencyContactIndex}][phone]"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="button" onclick="removeEmergencyContact(this)" class="px-3 py-2 text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.appendChild(contactDiv);
    emergencyContactIndex++;
}

function removeEmergencyContact(button) {
    button.closest('.emergency-contact').remove();
}

function addAllergy() {
    const container = document.getElementById('allergies-container');
    const allergyDiv = document.createElement('div');
    allergyDiv.className = 'allergy-item flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-full';
    allergyDiv.innerHTML = `
        <input type="text" name="known_allergies[]" 
               class="bg-transparent border-0 focus:ring-0 text-sm">
        <button type="button" onclick="removeAllergy(this)" class="text-red-600 hover:text-red-800">
            <i class="fas fa-times text-xs"></i>
        </button>
    `;
    container.appendChild(allergyDiv);
}

function removeAllergy(button) {
    button.closest('.allergy-item').remove();
}
</script>
@endsection

