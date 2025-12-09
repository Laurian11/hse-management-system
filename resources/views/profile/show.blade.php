@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('profile.edit') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    @if($user->profile_photo)
                        <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->name }}" 
                             class="mx-auto h-24 w-24 rounded-full object-cover mb-4">
                    @else
                        <div class="mx-auto h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center mb-4">
                            <i class="fas fa-user text-gray-600 text-3xl"></i>
                        </div>
                    @endif
                    <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <div class="mt-4">
                        @if($user->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="mt-6 space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Role</h4>
                        <p class="text-sm text-gray-500">{{ $user->role->display_name ?? $user->role->name ?? 'No Role' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Company</h4>
                        <p class="text-sm text-gray-500">{{ $user->company->name ?? 'No Company' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Department</h4>
                        <p class="text-sm text-gray-500">{{ $user->department->name ?? 'No Department' }}</p>
                    </div>
                    @if($user->employee_id_number)
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Employee ID</h4>
                        <p class="text-sm text-gray-500">{{ $user->employee_id_number }}</p>
                    </div>
                    @endif
                    @if($user->phone)
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Phone</h4>
                        <p class="text-sm text-gray-500">{{ $user->phone }}</p>
                    </div>
                    @endif
                    @if($user->job_title)
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Job Title</h4>
                        <p class="text-sm text-gray-500">{{ $user->job_title }}</p>
                    </div>
                    @endif
                    @if($user->last_login_at)
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Last Login</h4>
                        <p class="text-sm text-gray-500">{{ $user->last_login_at->format('M d, Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Details and Activity -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($user->date_of_birth)
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Date of Birth</h4>
                        <p class="text-sm text-gray-500">{{ $user->date_of_birth->format('M d, Y') }}</p>
                    </div>
                    @endif
                    @if($user->nationality)
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Nationality</h4>
                        <p class="text-sm text-gray-500">{{ $user->nationality }}</p>
                    </div>
                    @endif
                    @if($user->blood_group)
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Blood Group</h4>
                        <p class="text-sm text-gray-500">{{ $user->blood_group }}</p>
                    </div>
                    @endif
                    @if($user->date_of_hire)
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Date of Hire</h4>
                        <p class="text-sm text-gray-500">{{ $user->date_of_hire->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>

                @if($user->emergency_contacts && count($user->emergency_contacts) > 0)
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Emergency Contacts</h4>
                    <div class="space-y-3">
                        @foreach($user->emergency_contacts as $contact)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <p class="text-sm font-medium text-gray-900">{{ $contact['name'] ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $contact['relationship'] ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $contact['phone'] ?? 'N/A' }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($user->known_allergies && count($user->known_allergies) > 0)
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Known Allergies</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->known_allergies as $allergy)
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">{{ $allergy }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Recent Activity -->
            @if($recentActivity->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    @foreach($recentActivity as $activity)
                    <div class="flex items-start space-x-3 pb-3 border-b border-gray-200 last:border-0">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-history text-blue-600 text-xs"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Active Sessions -->
            @if($activeSessions->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Active Sessions</h3>
                <div class="space-y-3">
                    @foreach($activeSessions as $session)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $session->ip_address }}</p>
                            <p class="text-xs text-gray-500">{{ $session->user_agent }}</p>
                            <p class="text-xs text-gray-500 mt-1">Last activity: {{ $session->last_activity_at ? \Carbon\Carbon::parse($session->last_activity_at)->diffForHumans() : 'N/A' }}</p>
                        </div>
                        @if($session->id === session()->getId())
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Current Session</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

