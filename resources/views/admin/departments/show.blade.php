@extends('layouts.app')

@section('title', 'Department Details')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.departments.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Departments
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $department->name }}</h1>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.departments.edit', $department->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Department Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Basic Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Department Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Department Code</label>
                        <p class="text-sm text-gray-900">{{ $department->code }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Company</label>
                        <p class="text-sm text-gray-900">{{ $department->company->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <p class="text-sm text-gray-900">{{ $department->location }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        @if($department->is_active)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="text-sm text-gray-900">{{ $department->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Personnel -->
        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Key Personnel</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Head of Department</label>
                        @if($department->headOfDepartment)
                            <p class="text-sm text-gray-900">{{ $department->headOfDepartment->name }}</p>
                            <p class="text-xs text-gray-500">{{ $department->headOfDepartment->email }}</p>
                        @else
                            <p class="text-sm text-gray-500">Not assigned</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">HSE Officer</label>
                        @if($department->hseOfficer)
                            <p class="text-sm text-gray-900">{{ $department->hseOfficer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $department->hseOfficer->email }}</p>
                        @else
                            <p class="text-sm text-gray-500">Not assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HSE Objectives -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">HSE Objectives</h2>
        @if($department->hse_objectives)
            <ul class="list-disc list-inside space-y-2">
                @foreach(json_decode($department->hse_objectives) as $objective)
                    <li class="text-sm text-gray-900">{{ $objective }}</li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-500">No HSE objectives defined</p>
        @endif
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Employees</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $department->employees->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i class="fas fa-exclamation-triangle text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Incidents</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $department->incidents->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i class="fas fa-comments text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Toolbox Talks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $department->toolboxTalks->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i class="fas fa-sitemap text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Sub-departments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $department->childDepartments->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sub-departments -->
    @if($department->childDepartments->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Sub-departments</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($department->childDepartments as $child)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ $child->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $child->code }}</p>
                            </div>
                            <a href="{{ route('admin.departments.show', $child->id) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Employees -->
    @if($department->employees->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Department Employees</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($department->employees->take(10) as $employee)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $employee->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $employee->role->display_name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($employee->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($department->employees->count() > 10)
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-500">
                        Showing 10 of {{ $department->employees->count() }} employees
                    </p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
