@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.roles.show', $role) }}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Role
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Edit Role: {{ $role->display_name }}</h1>
            </div>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="{{ route('admin.roles.update', $role) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role Name (Slug)</label>
                    <input type="text" value="{{ $role->name }}" readonly
                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                    <p class="mt-1 text-xs text-gray-500">Role name cannot be changed</p>
                </div>
                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                    <input type="text" id="display_name" name="display_name" required value="{{ old('display_name', $role->display_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('display_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <input type="text" value="{{ ucfirst(str_replace('_', ' ', $role->level)) }}" readonly
                           class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                    <p class="mt-1 text-xs text-gray-500">Level cannot be changed</p>
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Brief description of the role">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Permissions</h2>
            <div class="space-y-6">
                @php
                    $rolePermissionIds = $role->permissions->pluck('id')->toArray();
                @endphp
                @foreach($permissions as $module => $modulePermissions)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $module)) }}</h3>
                            <label class="flex items-center">
                                <input type="checkbox" class="module-checkbox" data-module="{{ $module }}"
                                       onchange="toggleModulePermissions('{{ $module }}', this.checked)">
                                <span class="ml-2 text-xs text-gray-600">Select All</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($modulePermissions as $permission)
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                           class="permission-checkbox permission-{{ $module }} rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">{{ $permission->display_name ?? $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            @error('permissions.*')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.roles.show', $role) }}" 
                   class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Role
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleModulePermissions(module, checked) {
    const checkboxes = document.querySelectorAll(`.permission-${module}`);
    checkboxes.forEach(checkbox => {
        checkbox.checked = checked;
    });
}

// Update "Select All" checkboxes based on current state
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-module]').forEach(moduleCheckbox => {
        const module = moduleCheckbox.getAttribute('data-module');
        const checkboxes = document.querySelectorAll(`.permission-${module}`);
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        moduleCheckbox.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
    });
});
</script>
@endsection

