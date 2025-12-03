@extends('layouts.app')

@section('title', $toolboxTalk->title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('toolbox-talks.index') }}" class="text-gray-600 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Talks
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $toolboxTalk->title }}</h1>
                </div>
                <div class="flex space-x-3">
                    @if($toolboxTalk->status === 'scheduled')
                        <form action="{{ route('toolbox-talks.start', $toolboxTalk) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-play mr-2"></i>Start Talk
                            </button>
                        </form>
                    @endif
                    @if($toolboxTalk->status === 'in_progress')
                        <button onclick="showCompleteModal()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-check mr-2"></i>Complete Talk
                        </button>
                    @endif
                    @if($toolboxTalk->status !== 'completed')
                        <a href="{{ route('toolbox-talks.edit', $toolboxTalk) }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Status Alert -->
        <div class="mb-6">
            @switch($toolboxTalk->status)
                @case('scheduled')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>
                            <div>
                                <h3 class="text-sm font-medium text-blue-800">Scheduled Talk</h3>
                                <p class="text-sm text-blue-700">This talk is scheduled for {{ $toolboxTalk->scheduled_date->format('F j, Y') }} at {{ $toolboxTalk->start_time?->format('g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                    @break
                @case('in_progress')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-play-circle text-yellow-600 mr-3"></i>
                            <div>
                                <h3 class="text-sm font-medium text-yellow-800">Talk In Progress</h3>
                                <p class="text-sm text-yellow-700">This talk is currently in progress. Attendance can be recorded.</p>
                            </div>
                        </div>
                    </div>
                    @break
                @case('completed')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <div>
                                <h3 class="text-sm font-medium text-green-800">Completed Talk</h3>
                                <p class="text-sm text-green-700">This talk was completed on {{ $toolboxTalk->end_time?->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                    @break
            @endswitch
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Talk Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Talk Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Reference Number</h3>
                            <p class="text-lg font-medium text-gray-900">{{ $toolboxTalk->reference_number }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Talk Type</h3>
                            <div class="flex items-center">
                                @switch($toolboxTalk->talk_type)
                                    @case('safety')
                                        <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                                        <span class="text-gray-900">Safety</span>
                                        @break
                                    @case('health')
                                        <i class="fas fa-heartbeat text-red-600 mr-2"></i>
                                        <span class="text-gray-900">Health</span>
                                        @break
                                    @case('environment')
                                        <i class="fas fa-leaf text-green-600 mr-2"></i>
                                        <span class="text-gray-900">Environment</span>
                                        @break
                                    @default
                                        <i class="fas fa-comments text-gray-600 mr-2"></i>
                                        <span class="text-gray-900">{{ ucfirst($toolboxTalk->talk_type) }}</span>
                                @endswitch
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Date & Time</h3>
                            <p class="text-gray-900">{{ $toolboxTalk->scheduled_date->format('F j, Y') }}</p>
                            <p class="text-gray-900">{{ $toolboxTalk->start_time?->format('g:i A') }} - {{ $toolboxTalk->duration_minutes }} minutes</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Location</h3>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                <span class="text-gray-900">{{ $toolboxTalk->location }}</span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Department</h3>
                            <p class="text-gray-900">{{ $toolboxTalk->department?->name ?? 'All Departments' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 mb-1">Supervisor</h3>
                            <div class="flex items-center">
                                <i class="fas fa-user text-gray-400 mr-2"></i>
                                <span class="text-gray-900">{{ $toolboxTalk->supervisor?->name ?? 'Not assigned' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($toolboxTalk->description)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                            <p class="text-gray-900">{{ $toolboxTalk->description }}</p>
                        </div>
                    @endif
                    
                    @if($toolboxTalk->topic)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Topic</h3>
                            <p class="text-lg font-medium text-gray-900">{{ $toolboxTalk->topic->title }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $toolboxTalk->topic->category }} - {{ $toolboxTalk->topic->difficulty_level }}</p>
                        </div>
                    @endif
                </div>

                <!-- Attendance -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Attendance</h2>
                        @if($toolboxTalk->status === 'in_progress')
                            <button onclick="showAttendanceModal()" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-user-plus mr-1"></i>Record Attendance
                            </button>
                        @endif
                    </div>
                    
                    @if($toolboxTalk->status === 'completed')
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">{{ $attendanceStats['present'] }}</p>
                                <p class="text-sm text-gray-600">Present</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-yellow-600">{{ $attendanceStats['late'] }}</p>
                                <p class="text-sm text-gray-600">Late</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-red-600">{{ $attendanceStats['absent'] }}</p>
                                <p class="text-sm text-gray-600">Absent</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">{{ round($toolboxTalk->attendance_rate) }}%</p>
                                <p class="text-sm text-gray-600">Rate</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Signature</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($toolboxTalk->attendances as $attendance)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $attendance->employee_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $attendance->department }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            {!! $attendance->getAttendanceStatusBadge() !!}
                                        </td>
                                        <td class="px-4 py-3">
                                            {!! $attendance->getCheckInMethodBadge() !!}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $attendance->check_in_time?->format('g:i A') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($attendance->hasSignature())
                                                <i class="fas fa-signature text-green-600" title="Digital Signature"></i>
                                            @else
                                                <i class="fas fa-times text-gray-400" title="No Signature"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            No attendance recorded yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Feedback -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Feedback</h2>
                        <div class="flex gap-2">
                            @if($toolboxTalk->status === 'completed')
                                <a href="{{ route('toolbox-talks.view-feedback', $toolboxTalk) }}" class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>View All
                                </a>
                                <a href="{{ route('toolbox-talks.submit-feedback', $toolboxTalk) }}" class="px-3 py-1 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Submit
                                </a>
                            @endif
                        </div>
                    </div>
                    @if($toolboxTalk->status === 'completed')
                        <div class="mb-4">
                            <span class="text-sm text-gray-600">
                                {{ $feedbackStats['total'] }} responses • {{ number_format($feedbackStats['average_rating'], 1) }} avg rating
                            </span>
                        </div>
                    @endif
                    
                    @if($toolboxTalk->status === 'completed')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-green-600">{{ $feedbackStats['positive'] }}</p>
                                <p class="text-sm text-gray-600">Positive</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-600">{{ $feedbackStats['neutral'] }}</p>
                                <p class="text-sm text-gray-600">Neutral</p>
                            </div>
                            <div class="text-center">
                                <p class="text-3xl font-bold text-red-600">{{ $feedbackStats['negative'] }}</p>
                                <p class="text-sm text-gray-600">Negative</p>
                            </div>
                        </div>
                    @endif
                    
                    <div class="space-y-4">
                        @forelse($toolboxTalk->feedback as $feedback)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $feedback->employee_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $feedback->created_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        {!! $feedback->getSentimentBadge() !!}
                                        @if($feedback->overall_rating)
                                            <span class="text-sm text-gray-600">{{ $feedback->getOverallRatingStars() }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if($feedback->specific_comments)
                                    <p class="text-sm text-gray-700">{{ $feedback->specific_comments }}</p>
                                @endif
                                @if($feedback->improvement_suggestion)
                                    <div class="mt-2 p-2 bg-yellow-50 rounded text-sm text-yellow-800">
                                        <strong>Suggestion:</strong> {{ $feedback->improvement_suggestion }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                No feedback received yet
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($toolboxTalk->status === 'in_progress')
                            <button onclick="showAttendanceModal()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-user-plus mr-2"></i>Record Attendance
                            </button>
                            <button onclick="showFeedbackModal()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-comment mr-2"></i>Collect Feedback
                            </button>
                        @endif
                        @if($toolboxTalk->status === 'completed')
                            <button class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                <i class="fas fa-file-pdf mr-2"></i>Generate Report
                            </button>
                            <button class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-copy mr-2"></i>Duplicate Talk
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Settings -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Settings</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Biometric Required</span>
                            @if($toolboxTalk->biometric_required)
                                <i class="fas fa-fingerprint text-blue-600"></i>
                            @else
                                <i class="fas fa-times text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Recurring</span>
                            @if($toolboxTalk->is_recurring)
                                <span class="text-sm text-gray-900">{{ ucfirst($toolboxTalk->recurrence_pattern) }}</span>
                            @else
                                <i class="fas fa-times text-gray-400"></i>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Duration</span>
                            <span class="text-sm text-gray-900">{{ $toolboxTalk->duration_minutes }} min</span>
                        </div>
                    </div>
                </div>

                <!-- Materials -->
                @if($toolboxTalk->materials)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Materials</h3>
                        <div class="space-y-2">
                            @foreach($toolboxTalk->materials as $material)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="text-sm text-gray-700">{{ $material['name'] ?? 'Material' }}</span>
                                    <a href="{{ $material['url'] ?? '#' }}" class="text-blue-600 hover:text-blue-700 text-sm">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Photos -->
                @if($toolboxTalk->photos)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Photos</h3>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($toolboxTalk->photos as $photo)
                                <img src="{{ $photo['url'] }}" alt="Talk Photo" class="w-full h-32 object-cover rounded">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Complete Talk Modal -->
<div id="completeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900">Complete Toolbox Talk</h3>
            <form action="{{ route('toolbox-talks.complete', $toolboxTalk) }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supervisor Notes</label>
                    <textarea name="supervisor_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Key Points</label>
                    <textarea name="key_points" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideCompleteModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Complete Talk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Attendance Modal -->
<div id="attendanceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900">Record Attendance</h3>
            <p class="text-sm text-gray-600 mt-1">Select attendance method for employees</p>
            <div class="mt-4 space-y-3">
                <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-fingerprint mr-2"></i>Biometric Check-in
                </button>
                <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-mobile-alt mr-2"></i>Mobile App
                </button>
                <button class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-edit mr-2"></i>Manual Entry
                </button>
            </div>
            <div class="mt-4 flex justify-end">
                <button onclick="hideAttendanceModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900">Collect Feedback</h3>
            <p class="text-sm text-gray-600 mt-1">Choose feedback collection method</p>
            <div class="mt-4 space-y-3">
                <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-star mr-2"></i>Quick Rating (1-5)
                </button>
                <button class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-comment-alt mr-2"></i>Detailed Survey
                </button>
                <button class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <i class="fas fa-mobile-alt mr-2"></i>Mobile App
                </button>
            </div>
            <div class="mt-4 flex justify-end">
                <button onclick="hideFeedbackModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showCompleteModal() {
        document.getElementById('completeModal').classList.remove('hidden');
    }
    
    function hideCompleteModal() {
        document.getElementById('completeModal').classList.add('hidden');
    }
    
    function showAttendanceModal() {
        document.getElementById('attendanceModal').classList.remove('hidden');
    }
    
    function hideAttendanceModal() {
        document.getElementById('attendanceModal').classList.add('hidden');
    }
    
    function showFeedbackModal() {
        document.getElementById('feedbackModal').classList.remove('hidden');
    }
    
    function hideFeedbackModal() {
        document.getElementById('feedbackModal').classList.add('hidden');
    }
    
    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('fixed')) {
            event.target.classList.add('hidden');
        }
    }
</script>
@endpush
