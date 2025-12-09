@extends('layouts.app')

@section('title', 'Manage Permissions: ' . $user->name)

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i>Back to User
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Manage Permissions: {{ $user->name }}</h1>
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

    @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- User Info Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                <i class="fas fa-user text-gray-600 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <p class="text-xs text-gray-400">{{ $user->role->display_name ?? $user->role->name ?? 'No Role' }}</p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                <strong>Note:</strong> User-specific permissions will override role permissions. 
                If a user has a permission assigned directly, it will be granted regardless of role settings.
            </p>
        </div>
    </div>

    <form action="{{ route('admin.users.update-permissions', $user) }}" method="POST" id="permissions-form">
        @csrf
        @method('PUT')

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="selectAll()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <i class="fas fa-check-square mr-2"></i>Select All
                </button>
                <button type="button" onclick="deselectAll()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                    <i class="fas fa-square mr-2"></i>Deselect All
                </button>
                <button type="button" onclick="selectByAction('view')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                    <i class="fas fa-eye mr-2"></i>All View
                </button>
                <button type="button" onclick="selectByAction('create')" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                    <i class="fas fa-plus mr-2"></i>All Create
                </button>
                <button type="button" onclick="selectByAction('edit')" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm">
                    <i class="fas fa-edit mr-2"></i>All Edit
                </button>
                <button type="button" onclick="selectByAction('delete')" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                    <i class="fas fa-trash mr-2"></i>All Delete
                </button>
                <button type="button" onclick="selectByAction('print')" class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 text-sm">
                    <i class="fas fa-print mr-2"></i>All Print
                </button>
            </div>
        </div>

        <!-- Permissions by Module -->
        <div class="space-y-6">
            @foreach($permissions as $module => $modulePermissions)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $modules[$module] ?? ucfirst($module) }}
                    </h3>
                    <button type="button" onclick="toggleModule('{{ $module }}')" class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-chevron-down" id="toggle-icon-{{ $module }}"></i>
                        <span id="toggle-text-{{ $module }}">Collapse</span>
                    </button>
                </div>
                
                <div id="module-{{ $module }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($modulePermissions as $permission)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="permissions[]" 
                                   value="{{ $permission->name }}"
                                   class="mt-1 permission-checkbox"
                                   data-module="{{ $module }}"
                                   data-action="{{ $permission->action }}"
                                   {{ in_array($permission->name, $userPermissions) ? 'checked' : '' }}>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-900">{{ $permission->getActionLabel() }}</span>
                                    {!! $permission->getBadge() !!}
                                </div>
                                @if($permission->description)
                                <p class="text-xs text-gray-500 mt-1">{{ $permission->description }}</p>
                                @endif
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.users.show', $user) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>Save Permissions
            </button>
        </div>
    </form>
</div>

<script>
function selectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function selectByAction(action) {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        if (checkbox.dataset.action === action) {
            checkbox.checked = true;
        }
    });
}

function toggleModule(module) {
    const moduleDiv = document.getElementById('module-' + module);
    const toggleIcon = document.getElementById('toggle-icon-' + module);
    const toggleText = document.getElementById('toggle-text-' + module);
    
    if (moduleDiv.style.display === 'none') {
        moduleDiv.style.display = 'grid';
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
        toggleText.textContent = 'Collapse';
    } else {
        moduleDiv.style.display = 'none';
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
        toggleText.textContent = 'Expand';
    }
}

// Count selected permissions
function updatePermissionCount() {
    const checked = document.querySelectorAll('.permission-checkbox:checked').length;
    const total = document.querySelectorAll('.permission-checkbox').length;
    console.log(`Selected: ${checked} / ${total}`);
}

document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updatePermissionCount);
});

// Initialize count
updatePermissionCount();
</script>
@endsection

