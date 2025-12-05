@extends('layouts.app')

@section('title', $trainingSession->title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('training.training-sessions.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Sessions
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $trainingSession->title }}</h1>
                        <p class="text-sm text-gray-500">{{ $trainingSession->reference_number }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    @if($trainingSession->status === 'scheduled')
                        <form action="{{ route('training.training-sessions.start', $trainingSession) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-play mr-2"></i>Start Session
                            </button>
                        </form>
                        <a href="{{ route('training.training-sessions.edit', $trainingSession) }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    @endif
                    @if($trainingSession->status === 'in_progress')
                        <form action="{{ route('training.training-sessions.complete', $trainingSession) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-check mr-2"></i>Complete Session
                            </button>
                        </form>
                    @endif
                    @if($trainingSession->status === 'scheduled' && $trainingSession->attendances->count() === 0)
                        <form action="{{ route('training.training-sessions.destroy', $trainingSession) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this session?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Status Badge -->
        <div class="mb-6">
            <span class="px-4 py-2 rounded-lg text-sm font-semibold
                {{ $trainingSession->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $trainingSession->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $trainingSession->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                {{ $trainingSession->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                {{ ucfirst(str_replace('_', ' ', $trainingSession->status)) }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Session Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Session Details</h2>
                    <div class="space-y-4">
                        @if($trainingSession->description)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Description</h3>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $trainingSession->description }}</p>
                            </div>
                        @endif
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Scheduled Start</h3>
                                <p class="text-gray-900">{{ $trainingSession->scheduled_start->format('M j, Y g:i A') }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Scheduled End</h3>
                                <p class="text-gray-900">{{ $trainingSession->scheduled_end->format('M j, Y g:i A') }}</p>
                            </div>
                            @if($trainingSession->location_name)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Location</h3>
                                    <p class="text-gray-900">{{ $trainingSession->location_name }}</p>
                                </div>
                            @endif
                            @if($trainingSession->instructor)
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">Instructor</h3>
                                    <p class="text-gray-900">{{ $trainingSession->instructor->name }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Attendance -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Attendance</h2>
                        @if($trainingSession->status === 'in_progress' || $trainingSession->status === 'scheduled')
                            <button onclick="showAttendanceModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                <i class="fas fa-user-check mr-2"></i>Mark Attendance
                            </button>
                        @endif
                    </div>
                    @if($trainingSession->attendances->count() > 0)
                        <div class="space-y-2">
                            @foreach($trainingSession->attendances as $attendance)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $attendance->attendance_status === 'attended' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $attendance->attendance_status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $attendance->attendance_status === 'partially_attended' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $attendance->attendance_status)) }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900">{{ $attendance->user->name }}</span>
                                    </div>
                                    @if($attendance->checked_in_at)
                                        <span class="text-xs text-gray-500">
                                            Checked in: {{ $attendance->checked_in_at->format('g:i A') }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                <strong>Attendance Rate:</strong> {{ $trainingSession->attendance_percentage }}% 
                                ({{ $trainingSession->attendance_count }}/{{ $trainingSession->attendances->count() }})
                            </p>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">No attendance recorded yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Info -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Info</h3>
                    <div class="space-y-4">
                        @if($trainingSession->trainingPlan)
                            <div>
                                <p class="text-sm text-gray-500">Training Plan</p>
                                <a href="{{ route('training.training-plans.show', $trainingSession->trainingPlan) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                    {{ Str::limit($trainingSession->trainingPlan->title, 40) }}
                                </a>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Registered Participants</p>
                            <p class="text-sm font-medium text-gray-900">{{ $trainingSession->registered_participants }}</p>
                        </div>
                        @if($trainingSession->max_participants)
                            <div>
                                <p class="text-sm text-gray-500">Max Participants</p>
                                <p class="text-sm font-medium text-gray-900">{{ $trainingSession->max_participants }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Modal -->
<div id="attendanceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mark Attendance</h3>
            <form action="{{ route('training.training-sessions.attendance', $trainingSession) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Participant</label>
                    <select name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Participant</option>
                        @foreach(\App\Models\User::where('company_id', auth()->user()->company_id)->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="attendance_status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="attended">Attended</option>
                        <option value="absent">Absent</option>
                        <option value="partially_attended">Partially Attended</option>
                        <option value="excused">Excused</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAttendanceModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showAttendanceModal() {
        document.getElementById('attendanceModal').classList.remove('hidden');
    }
    function closeAttendanceModal() {
        document.getElementById('attendanceModal').classList.add('hidden');
    }
</script>
@endsection
