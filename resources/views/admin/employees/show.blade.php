@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.employees.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Employees
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Employee Details</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                @if($employee->is_active)
                    <form action="{{ route('admin.employees.deactivate', $employee) }}" method="POST" class="inline">
                        @csrf
                        <button type="button" onclick="showDeactivateModal()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            <i class="fas fa-ban mr-2"></i>Deactivate
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.employees.activate', $employee) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            <i class="fas fa-check mr-2"></i>Activate
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Employee Profile -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    <div class="mx-auto h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center mb-4">
                        <i class="fas fa-user text-gray-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $employee->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $employee->email }}</p>
                    <div class="mt-4">
                        @if($employee->is_active)
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
                        <h4 class="text-sm font-medium text-gray-900">Employee ID</h4>
                        <p class="text-sm text-gray-500">{{ $employee->employee_id_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Role</h4>
                        <p class="text-sm text-gray-500">{{ $employee->role->display_name ?? $employee->role->name ?? 'No Role' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Department</h4>
                        <p class="text-sm text-gray-500">{{ $employee->department->name ?? 'No Department' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Job Title</h4>
                        <p class="text-sm text-gray-500">{{ $employee->job_title ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Employment Type</h4>
                        <p class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $employee->employment_type ?? 'N/A')) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Date of Hire</h4>
                        <p class="text-sm text-gray-500">{{ $employee->date_of_hire ? $employee->date_of_hire->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    @if($employee->directSupervisor)
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Direct Supervisor</h4>
                        <p class="text-sm text-gray-500">{{ $employee->directSupervisor->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-600">Incidents Reported</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $stats['incidents_reported'] }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-600">Toolbox Talks Attended</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $stats['toolbox_talks_attended'] }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-600">Talks Conducted</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $stats['toolbox_talks_conducted'] }}</span>
                        </div>
                    </div>
                    @if($stats['last_login'])
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm text-gray-600">Last Login</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $stats['last_login']->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Employee Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Phone</h4>
                        <p class="text-sm text-gray-500">{{ $employee->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Date of Birth</h4>
                        <p class="text-sm text-gray-500">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Nationality</h4>
                        <p class="text-sm text-gray-500">{{ $employee->nationality ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">Blood Group</h4>
                        <p class="text-sm text-gray-500">{{ $employee->blood_group ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    @forelse($recentActivity as $activity)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $activity->description }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $activity->module }} • {{ $activity->action }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $activity->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent activity</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate Modal -->
<div id="deactivateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Deactivate Employee</h3>
            <form action="{{ route('admin.employees.deactivate', $employee) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                    <textarea id="reason" name="reason" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideDeactivateModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Deactivate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showDeactivateModal() {
    document.getElementById('deactivateModal').classList.remove('hidden');
}

function hideDeactivateModal() {
    document.getElementById('deactivateModal').classList.add('hidden');
}
</script>
@endsection

