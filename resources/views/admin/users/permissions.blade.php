@extends('layouts.app')

@section('title', 'Manage Permissions: ' . $user->name)

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.users.show', $user) }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to User
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manage Permissions</h1>
                    <p class="text-sm text-gray-500">{{ $user->name }} ({{ $user->email }})</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- User Info & Statistics Card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-lg p-6 mb-6 border border-blue-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-shield text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        @if($user->role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <i class="fas fa-user-tag mr-1"></i>{{ $user->role->display_name ?? $user->role->name }}
                            </span>
                        @endif
                        @if($user->company)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-building mr-1"></i>{{ $user->company->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <div class="text-xs text-gray-500 uppercase tracking-wide">Total</div>
                    <div class="text-2xl font-bold text-gray-900" id="total-permissions">{{ count($permissions->flatten()) }}</div>
                </div>
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <div class="text-xs text-gray-500 uppercase tracking-wide">Selected</div>
                    <div class="text-2xl font-bold text-blue-600" id="selected-count">{{ count($userPermissions) }}</div>
                </div>
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <div class="text-xs text-gray-500 uppercase tracking-wide">Modules</div>
                    <div class="text-2xl font-bold text-indigo-600">{{ count($permissions) }}</div>
                </div>
                <div class="bg-white rounded-lg p-3 shadow-sm">
                    <div class="text-xs text-gray-500 uppercase tracking-wide">Coverage</div>
                    <div class="text-2xl font-bold text-green-600" id="coverage-percent">
                        {{ count($permissions->flatten()) > 0 ? round((count($userPermissions) / count($permissions->flatten())) * 100) : 0 }}%
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-blue-200">
            <div class="flex items-start space-x-2">
                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                <div class="text-sm text-gray-700">
                    <p class="mb-2">
                        <strong>Note:</strong> User-specific permissions override role permissions. 
                        Permissions assigned directly to this user will be granted regardless of role settings.
                    </p>
                    <div class="flex items-center space-x-4 text-xs">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 border-2 border-blue-500 bg-blue-50 rounded"></div>
                            <span>User-specific permission</span>
                        </div>
                        @if(isset($rolePermissions) && count($rolePermissions) > 0)
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 border-2 border-green-300 bg-green-50 rounded"></div>
                            <span>From role ({{ count($rolePermissions) }} permissions)</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="search-input"
                           placeholder="Search permissions by module or action..." 
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="flex gap-2">
                <select id="filter-module" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Modules</option>
                    @foreach($modules as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select id="filter-action" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Actions</option>
                    @foreach(\App\Models\Permission::getActions() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.users.update-permissions', $user) }}" method="POST" id="permissions-form">
        @csrf
        @method('PUT')

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i>Quick Actions
                </h3>
                <div class="text-sm text-gray-500">
                    <span id="visible-count">0</span> permissions visible
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" onclick="selectAll()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-check-square mr-2"></i>Select All
                </button>
                <button type="button" onclick="deselectAll()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-square mr-2"></i>Deselect All
                </button>
                <button type="button" onclick="selectByAction('view')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-eye mr-2"></i>All View
                </button>
                <button type="button" onclick="selectByAction('create')" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-plus mr-2"></i>All Create
                </button>
                <button type="button" onclick="selectByAction('write')" class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-pen mr-2"></i>All Write
                </button>
                <button type="button" onclick="selectByAction('edit')" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-edit mr-2"></i>All Edit
                </button>
                <button type="button" onclick="selectByAction('delete')" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-trash mr-2"></i>All Delete
                </button>
                <button type="button" onclick="selectByAction('print')" class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-print mr-2"></i>All Print
                </button>
                <button type="button" onclick="selectByAction('export')" class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-file-export mr-2"></i>All Export
                </button>
                <button type="button" onclick="selectByAction('manage')" class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors text-sm font-medium shadow-sm">
                    <i class="fas fa-cog mr-2"></i>All Manage
                </button>
            </div>
        </div>

        <!-- Permissions by Module -->
        <div class="space-y-6" id="permissions-container">
            @foreach($permissions as $module => $modulePermissions)
            <div class="bg-white rounded-lg shadow-lg border border-gray-200 module-card" data-module="{{ $module }}">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 rounded-t-lg border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                                <i class="fas fa-{{ $module === 'admin' ? 'cog' : ($module === 'incidents' ? 'exclamation-triangle' : ($module === 'risk_assessments' ? 'shield-alt' : ($module === 'toolbox_talks' ? 'hard-hat' : ($module === 'training' ? 'graduation-cap' : 'folder')))) }} text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $modules[$module] ?? ucfirst(str_replace('_', ' ', $module)) }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    <span class="module-count">{{ count($modulePermissions) }}</span> permissions
                                    <span class="mx-2">•</span>
                                    <span class="module-selected text-blue-600 font-medium">0</span> selected
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" 
                                    onclick="selectModule('{{ $module }}')" 
                                    class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors">
                                <i class="fas fa-check mr-1"></i>Select All
                            </button>
                            <button type="button" 
                                    onclick="toggleModule('{{ $module }}')" 
                                    class="px-3 py-1 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                <i class="fas fa-chevron-down" id="toggle-icon-{{ $module }}"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="module-{{ $module }}" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($modulePermissions as $permission)
                        @php
                            $isUserPermission = in_array($permission->name, $userPermissions);
                            $isRolePermission = isset($rolePermissions) && in_array($permission->name, $rolePermissions);
                            $hasPermission = $isUserPermission || $isRolePermission;
                        @endphp
                        <div class="permission-card border-2 rounded-lg p-4 transition-all hover:shadow-md {{ $isUserPermission ? 'border-blue-500 bg-blue-50' : ($isRolePermission ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-white') }}" 
                             data-permission-name="{{ $permission->name }}"
                             data-module="{{ $module }}"
                             data-action="{{ $permission->action }}">
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->name }}"
                                       class="mt-1 permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       data-module="{{ $module }}"
                                       data-action="{{ $permission->action }}"
                                       {{ $isUserPermission ? 'checked' : '' }}>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-medium text-gray-900 truncate">{{ $permission->getActionLabel() }}</span>
                                        <div class="flex items-center space-x-1">
                                            {!! $permission->getBadge() !!}
                                            @if($isRolePermission && !$isUserPermission)
                                                <span class="px-1.5 py-0.5 text-xs rounded-full bg-green-100 text-green-700" title="From Role">
                                                    <i class="fas fa-user-tag"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($permission->description)
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2" title="{{ $permission->description }}">
                                        {{ $permission->description }}
                                    </p>
                                    @else
                                    <p class="text-xs text-gray-400 italic mt-1">No description</p>
                                    @endif
                                    <div class="mt-2 flex items-center justify-between">
                                        <code class="text-xs text-gray-400 bg-gray-100 px-1 py-0.5 rounded">{{ $permission->name }}</code>
                                        @if($isRolePermission && !$isUserPermission)
                                            <span class="text-xs text-green-600 italic">via role</span>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Submit Button -->
        <div class="mt-8 bg-white rounded-lg shadow p-6 sticky bottom-0 z-10 border-t-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    <span id="final-count">{{ count($userPermissions) }}</span> permissions will be assigned to this user
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.users.show', $user) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-medium shadow-lg">
                        <i class="fas fa-save mr-2"></i>Save Permissions
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Permission management functions
let allPermissions = @json($permissions->flatten()->pluck('name')->toArray());
let userPermissions = @json($userPermissions);

function updateCounts() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const checked = document.querySelectorAll('.permission-checkbox:checked');
    const selectedCount = checked.length;
    const totalCount = checkboxes.length;
    const coverage = totalCount > 0 ? Math.round((selectedCount / totalCount) * 100) : 0;
    
    document.getElementById('selected-count').textContent = selectedCount;
    document.getElementById('coverage-percent').textContent = coverage + '%';
    document.getElementById('final-count').textContent = selectedCount;
    
    // Update module counts
    document.querySelectorAll('.module-card').forEach(card => {
        const module = card.dataset.module;
        const moduleCheckboxes = card.querySelectorAll('.permission-checkbox');
        const moduleChecked = card.querySelectorAll('.permission-checkbox:checked');
        const moduleSelected = card.querySelector(`.module-selected`);
        if (moduleSelected) {
            moduleSelected.textContent = moduleChecked.length;
        }
    });
    
    // Update visible count
    const visibleCards = document.querySelectorAll('.permission-card:not([style*="display: none"])');
    document.getElementById('visible-count').textContent = visibleCards.length;
}

function selectAll() {
    document.querySelectorAll('.permission-checkbox:not([style*="display: none"])').forEach(checkbox => {
        if (checkbox.closest('.permission-card').style.display !== 'none') {
            checkbox.checked = true;
            updateCardStyle(checkbox);
        }
    });
    updateCounts();
}

function deselectAll() {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        updateCardStyle(checkbox);
    });
    updateCounts();
}

function selectByAction(action) {
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        if (checkbox.dataset.action === action && checkbox.closest('.permission-card').style.display !== 'none') {
            checkbox.checked = true;
            updateCardStyle(checkbox);
        }
    });
    updateCounts();
}

function selectModule(module) {
    const moduleCard = document.querySelector(`.module-card[data-module="${module}"]`);
    if (moduleCard) {
        moduleCard.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.checked = true;
            updateCardStyle(checkbox);
        });
        updateCounts();
    }
}

function toggleModule(module) {
    const moduleDiv = document.getElementById('module-' + module);
    const toggleIcon = document.getElementById('toggle-icon-' + module);
    
    if (moduleDiv.style.display === 'none') {
        moduleDiv.style.display = 'block';
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
    } else {
        moduleDiv.style.display = 'none';
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
    }
}

function updateCardStyle(checkbox) {
    const card = checkbox.closest('.permission-card');
    if (checkbox.checked) {
        card.classList.remove('border-gray-200', 'bg-white');
        card.classList.add('border-blue-500', 'bg-blue-50');
    } else {
        card.classList.remove('border-blue-500', 'bg-blue-50');
        card.classList.add('border-gray-200', 'bg-white');
    }
}

// Search functionality
document.getElementById('search-input').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    filterPermissions();
});

document.getElementById('filter-module').addEventListener('change', filterPermissions);
document.getElementById('filter-action').addEventListener('change', filterPermissions);

function filterPermissions() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const moduleFilter = document.getElementById('filter-module').value;
    const actionFilter = document.getElementById('filter-action').value;
    
    document.querySelectorAll('.permission-card').forEach(card => {
        const permissionName = card.dataset.permission-name.toLowerCase();
        const module = card.dataset.module;
        const action = card.dataset.action;
        
        const matchesSearch = !searchTerm || permissionName.includes(searchTerm);
        const matchesModule = !moduleFilter || module === moduleFilter;
        const matchesAction = !actionFilter || action === actionFilter;
        
        if (matchesSearch && matchesModule && matchesAction) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Hide/show module cards
    document.querySelectorAll('.module-card').forEach(moduleCard => {
        const visiblePermissions = moduleCard.querySelectorAll('.permission-card[style*="display: block"], .permission-card:not([style*="display: none"])');
        if (visiblePermissions.length === 0) {
            moduleCard.style.display = 'none';
        } else {
            moduleCard.style.display = 'block';
        }
    });
    
    updateCounts();
}

// Initialize
document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateCardStyle(this);
        updateCounts();
    });
    updateCardStyle(checkbox);
});

updateCounts();

// Form submission confirmation
document.getElementById('permissions-form').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.permission-checkbox:checked').length;
    if (checked === 0) {
        if (!confirm('No permissions selected. This will remove all user-specific permissions. Continue?')) {
            e.preventDefault();
        }
    }
});
</script>

<style>
.permission-card {
    transition: all 0.2s ease;
}

.permission-card:hover {
    transform: translateY(-2px);
}

.module-card {
    transition: all 0.3s ease;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection

