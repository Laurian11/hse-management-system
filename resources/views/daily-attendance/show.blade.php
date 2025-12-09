@extends('layouts.app')

@section('title', 'Attendance Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('daily-attendance.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Attendance
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Attendance Details</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Employee</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $dailyAttendance->employee_name }}</p>
                    <p class="text-sm text-gray-500">{{ $dailyAttendance->employee_id_number }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Date</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $dailyAttendance->attendance_date->format('F j, Y') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Department</h3>
                    <p class="text-lg text-gray-900">{{ $dailyAttendance->department->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Location</h3>
                    <p class="text-lg text-gray-900">{{ $dailyAttendance->biometricDevice->location_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Check-In Time</h3>
                    <p class="text-lg text-gray-900">
                        @if($dailyAttendance->check_in_time)
                            {{ \Carbon\Carbon::parse($dailyAttendance->check_in_time)->format('H:i:s') }}
                        @else
                            <span class="text-gray-400">Not recorded</span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-500">{{ ucfirst($dailyAttendance->check_in_method) }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Check-Out Time</h3>
                    <p class="text-lg text-gray-900">
                        @if($dailyAttendance->check_out_time)
                            {{ \Carbon\Carbon::parse($dailyAttendance->check_out_time)->format('H:i:s') }}
                        @else
                            <span class="text-gray-400">Not recorded</span>
                        @endif
                    </p>
                    @if($dailyAttendance->check_out_method)
                        <p class="text-sm text-gray-500">{{ ucfirst($dailyAttendance->check_out_method) }}</p>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Total Work Hours</h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $dailyAttendance->total_work_hours }} hours</p>
                    @if($dailyAttendance->overtime_minutes > 0)
                        <p class="text-sm text-orange-600">Overtime: {{ round($dailyAttendance->overtime_minutes / 60, 1) }}h</p>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Status</h3>
                    <div class="mt-1">
                        {!! $dailyAttendance->getStatusBadge() !!}
                    </div>
                </div>
            </div>

            @if($dailyAttendance->is_late || $dailyAttendance->is_early_departure || $dailyAttendance->remarks)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Additional Information</h3>
                    <div class="space-y-2">
                        @if($dailyAttendance->is_late)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                <span class="text-gray-700">Late by {{ $dailyAttendance->late_minutes }} minutes</span>
                            </div>
                        @endif
                        @if($dailyAttendance->is_early_departure)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
                                <span class="text-gray-700">Early departure by {{ $dailyAttendance->early_departure_minutes }} minutes</span>
                            </div>
                        @endif
                        @if($dailyAttendance->remarks)
                            <div class="text-sm text-gray-700">
                                <strong>Remarks:</strong> {{ $dailyAttendance->remarks }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Approval Section -->
            @if(auth()->user()->role && (auth()->user()->role->name === 'super_admin' || auth()->user()->role->name === 'admin' || auth()->user()->role->name === 'hse_officer'))
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-500 mb-3">Approval</h3>
                @if($dailyAttendance->approved_by)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-sm text-green-800">
                                Approved by {{ $dailyAttendance->approver->name ?? 'N/A' }} 
                                on {{ $dailyAttendance->approved_at->format('M j, Y H:i') }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="flex space-x-3">
                        <form action="{{ route('daily-attendance.approve', $dailyAttendance) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                        </form>
                        <button type="button" onclick="showRejectModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-times mr-2"></i>Reject
                        </button>
                    </div>
                    
                    <!-- Reject Modal -->
                    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                            <div class="mt-3">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Attendance</h3>
                                <form action="{{ route('daily-attendance.reject', $dailyAttendance) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                            Reason for Rejection *
                                        </label>
                                        <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>
                                    </div>
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" onclick="hideRejectModal()" 
                                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                            Reject
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection

