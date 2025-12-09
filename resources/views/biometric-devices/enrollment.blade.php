@extends('layouts.app')

@section('title', 'Employee Enrollment')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('biometric-devices.show', $biometricDevice) }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Device
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Employee Enrollment - {{ $biometricDevice->device_name }}</h1>
                </div>
                <div class="flex space-x-3">
                    <form action="{{ route('biometric-devices.sync-users', $biometricDevice) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-sync mr-2"></i>Sync All Employees
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Total Employees</div>
                <div class="text-2xl font-bold text-gray-900">{{ $employees->count() }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Enrolled</div>
                <div class="text-2xl font-bold text-green-600">
                    {{ collect($enrollmentStatus)->where('enrolled', true)->count() }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Not Enrolled</div>
                <div class="text-2xl font-bold text-yellow-600">
                    {{ collect($enrollmentStatus)->where('enrolled', false)->count() }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600">Device Status</div>
                <div class="text-2xl font-bold {{ $biometricDevice->status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                    {{ ucfirst($biometricDevice->status) }}
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-wrap gap-4">
                <input type="text" id="searchInput" placeholder="Search employees..." 
                       class="flex-1 min-w-[200px] px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Status</option>
                    <option value="enrolled">Enrolled</option>
                    <option value="not_enrolled">Not Enrolled</option>
                </select>
                <select id="departmentFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Departments</option>
                    @foreach($employees->pluck('department')->filter()->unique('id') as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Employee List -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">Employees</h2>
                <div class="flex space-x-2">
                    <button type="button" id="selectAllBtn" class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">
                        Select All
                    </button>
                    <form action="{{ route('biometric-devices.bulk-enroll', $biometricDevice) }}" method="POST" id="bulkEnrollForm" class="inline">
                        @csrf
                        <input type="hidden" name="employee_ids" id="bulkEmployeeIds">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-user-plus mr-2"></i>Bulk Enroll Selected
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="employeeTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollment Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($employees as $employee)
                        @php
                            $status = $enrollmentStatus[$employee->id] ?? ['enrolled' => false, 'status' => 'unknown'];
                        @endphp
                        <tr class="employee-row" data-name="{{ strtolower($employee->full_name) }}" 
                            data-department="{{ $employee->department_id }}" 
                            data-status="{{ $status['enrolled'] ? 'enrolled' : 'not_enrolled' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <input type="checkbox" name="employee_ids[]" value="{{ $employee->id }}" 
                                       class="employee-checkbox rounded border-gray-300">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">{{ substr($employee->first_name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $employee->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ $employee->employee_id_number }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($status['enrolled'])
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>Enrolled
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-times-circle mr-1"></i>Not Enrolled
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                @if($status['enrolled'])
                                    <form action="{{ route('biometric-devices.remove-employee', $biometricDevice) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('Remove {{ $employee->full_name }} from device?')">
                                            <i class="fas fa-user-minus mr-1"></i>Remove
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('biometric-devices.enroll-employee', $biometricDevice) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        <button type="submit" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-user-plus mr-1"></i>Enroll
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const departmentFilter = document.getElementById('departmentFilter');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');
    const bulkEnrollForm = document.getElementById('bulkEnrollForm');
    const bulkEmployeeIds = document.getElementById('bulkEmployeeIds');

    // Search functionality
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    departmentFilter.addEventListener('change', filterTable);

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const deptValue = departmentFilter.value;

        document.querySelectorAll('.employee-row').forEach(row => {
            const name = row.dataset.name;
            const department = row.dataset.department;
            const status = row.dataset.status;

            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = statusValue === 'all' || status === statusValue;
            const matchesDept = deptValue === 'all' || department === deptValue;

            row.style.display = (matchesSearch && matchesStatus && matchesDept) ? '' : 'none';
        });
    }

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        employeeCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (row.style.display !== 'none') {
                checkbox.checked = selectAllCheckbox.checked;
            }
        });
    });

    // Bulk enroll
    bulkEnrollForm.addEventListener('submit', function(e) {
        const selected = Array.from(employeeCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);
        
        if (selected.length === 0) {
            e.preventDefault();
            alert('Please select at least one employee');
            return false;
        }

        bulkEmployeeIds.value = JSON.stringify(selected);
    });
});
</script>
@endsection

