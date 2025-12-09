@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
                <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.permissions', $user->id) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                    <i class="fas fa-key mr-2"></i>Permissions
                </a>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    <div class="mx-auto h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center mb-4">
                        <i class="fas fa-user text-gray-600 text-3xl"></i>
                    </div>
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
                        <p class="text-sm text-gray-500">{{ $user->role->name ?? 'No Role' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Company</h4>
                        <p class="text-sm text-gray-500">{{ $user->company->name ?? 'No Company' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Department</h4>
                        <p class="text-sm text-gray-500">{{ $user->department->name ?? 'No Department' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Employee ID</h4>
                        <p class="text-sm text-gray-500">{{ $user->employee_id_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Phone</h4>
                        <p class="text-sm text-gray-500">{{ $user->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Job Title</h4>
                        <p class="text-sm text-gray-500">{{ $user->job_title ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Employment Type</h4>
                        <p class="text-sm text-gray-500">{{ ucfirst($user->employment_type) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Date of Hire</h4>
                        <p class="text-sm text-gray-500">{{ $user->date_of_hire ? $user->date_of_hire->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Details and Activity -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Date of Birth</h4>
                        <p class="text-sm text-gray-500">{{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Nationality</h4>
                        <p class="text-sm text-gray-500">{{ $user->nationality ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Blood Group</h4>
                        <p class="text-sm text-gray-500">{{ $user->blood_group ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Known Allergies</h4>
                        <p class="text-sm text-gray-500">{{ $user->known_allergies ?? 'None' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- HSE Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">HSE Information</h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Direct Supervisor</h4>
                        <p class="text-sm text-gray-500">{{ $user->directSupervisor->name ?? 'None' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">HSE Training History</h4>
                        <p class="text-sm text-gray-500">{{ $user->hse_training_history ? 'Available' : 'None' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Competency Certificates</h4>
                        <p class="text-sm text-gray-500">{{ $user->competency_certificates ? 'Available' : 'None' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Last Login</h4>
                        <p class="text-sm text-gray-500">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Last Login IP</h4>
                        <p class="text-sm text-gray-500">{{ $user->last_login_ip ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Failed Login Attempts</h4>
                        <p class="text-sm text-gray-500">{{ $user->failed_login_attempts }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Password Changed</h4>
                        <p class="text-sm text-gray-500">{{ $user->password_changed_at ? $user->password_changed_at->format('M d, Y') : 'Never' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Must Change Password</h4>
                        <p class="text-sm text-gray-500">{{ $user->must_change_password ? 'Yes' : 'No' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Account Status</h4>
                        <p class="text-sm text-gray-500">
                            @if($user->deactivated_at)
                                Deactivated on {{ $user->deactivated_at->format('M d, Y') }}
                                @if($user->deactivation_reason)
                                    ({{ $user->deactivation_reason }})
                                @endif
                            @else
                                Active
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                @if($recentActivity->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentActivity as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-{{ $activity->action === 'create' ? 'plus' : ($activity->action === 'update' ? 'edit' : ($activity->action === 'delete' ? 'trash' : 'info')) }} text-blue-600 text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No recent activity found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
