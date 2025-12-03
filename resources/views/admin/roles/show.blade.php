@extends('layouts.app')

@section('title', $role->display_name)

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.roles.index') }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Roles
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $role->display_name }}</h1>
            </div>
            <div class="flex space-x-3">
                @if(!$role->is_system)
                    <a href="{{ route('admin.roles.edit', $role) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Role Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Role Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Role Name</h3>
                        <p class="text-lg font-medium text-gray-900">{{ $role->name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Display Name</h3>
                        <p class="text-lg font-medium text-gray-900">{{ $role->display_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Level</h3>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ ucfirst(str_replace('_', ' ', $role->level)) }}
                        </span>
                        @if($role->is_system)
                            <span class="ml-2 px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                System Role
                            </span>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                        @if($role->is_active)
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </div>
                    @if($role->description)
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Description</h3>
                            <p class="text-gray-900">{{ $role->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Permissions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Permissions ({{ $role->permissions->count() }})</h2>
                @if($role->permissions->count() > 0)
                    <div class="space-y-4">
                        @foreach($role->permissions->groupBy('module') as $module => $modulePermissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ ucfirst(str_replace('_', ' ', $module)) }}</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($modulePermissions as $permission)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                            {{ $permission->display_name ?? $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No permissions assigned to this role.</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Total Permissions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $role->permissions->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Users with this Role</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $role->users->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if(!$role->is_system)
                        <a href="{{ route('admin.roles.edit', $role) }}" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit Role
                        </a>
                        <form action="{{ route('admin.roles.duplicate', $role) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-copy mr-2"></i>Duplicate Role
                            </button>
                        </form>
                        @if($role->is_active)
                            <form action="{{ route('admin.roles.deactivate', $role) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                    <i class="fas fa-ban mr-2"></i>Deactivate
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.roles.activate', $role) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-check mr-2"></i>Activate
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Users with this Role -->
            @if($role->users->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Users with this Role</h3>
                    <div class="space-y-2">
                        @foreach($role->users->take(10) as $user)
                            <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                                <div class="flex items-center space-x-2">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                            </div>
                        @endforeach
                        @if($role->users->count() > 10)
                            <p class="text-xs text-gray-500 text-center mt-2">
                                And {{ $role->users->count() - 10 }} more...
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

