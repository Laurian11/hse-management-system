@extends('layouts.app')

@section('title', 'Toolbox Talks')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-hard-hat text-blue-600 text-2xl"></i>
                    <h1 class="text-2xl font-bold text-gray-900">Toolbox Talks</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('toolbox-talks.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>New Talk
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Stats -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="bg-gray-100 p-2 rounded-full">
                        <i class="fas fa-comments text-gray-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Scheduled</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['scheduled'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-2 rounded-full">
                        <i class="fas fa-calendar text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="bg-green-100 p-2 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Avg Attendance</p>
                        <p class="text-2xl font-bold text-purple-600">{{ round($stats['avg_attendance']) }}%</p>
                    </div>
                    <div class="bg-purple-100 p-2 rounded-full">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('toolbox-talks.index', ['filter' => 'today']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('filter') == 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-calendar-day mr-2"></i>Today
                </a>
                <a href="{{ route('toolbox-talks.index', ['filter' => 'this_week']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('filter') == 'this_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-calendar-week mr-2"></i>This Week
                </a>
                <a href="{{ route('toolbox-talks.index', ['filter' => 'this_month']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('filter') == 'this_month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-calendar mr-2"></i>This Month
                </a>
                <a href="{{ route('toolbox-talks.index', ['filter' => 'upcoming']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('filter') == 'upcoming' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-clock mr-2"></i>Upcoming
                </a>
                <a href="{{ route('toolbox-talks.index', ['filter' => 'my_talks']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('filter') == 'my_talks' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-user mr-2"></i>My Talks
                </a>
                <a href="{{ route('toolbox-talks.index') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">
                    <i class="fas fa-times mr-2"></i>Clear Filters
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" action="{{ route('toolbox-talks.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select name="department" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Departments</option>
                            <!-- Departments would be populated here -->
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('toolbox-talks.index') }}" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Clear
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Talks List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Talk Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($toolboxTalks as $talk)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            @switch($talk->talk_type)
                                                @case('safety')
                                                    <i class="fas fa-shield-alt text-blue-600"></i>
                                                    @break
                                                @case('health')
                                                    <i class="fas fa-heartbeat text-red-600"></i>
                                                    @break
                                                @case('environment')
                                                    <i class="fas fa-leaf text-green-600"></i>
                                                    @break
                                                @default
                                                    <i class="fas fa-comments text-gray-600"></i>
                                            @endswitch
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $talk->title }}
                                                @if($talk->biometric_required)
                                                    <i class="fas fa-fingerprint text-blue-600 text-xs ml-1" title="Biometric Required"></i>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $talk->reference_number }}</div>
                                            @if($talk->topic)
                                                <div class="text-sm text-gray-500">{{ $talk->topic->title }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $talk->scheduled_date->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $talk->start_time ? $talk->start_time->format('g:i A') : 'Not set' }}</div>
                                    <div class="text-sm text-gray-500">{{ $talk->duration_minutes }} min</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $talk->department?->name ?? 'All' }}</div>
                                    <div class="text-sm text-gray-500">{{ $talk->location }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $talk->supervisor?->name ?? 'Not assigned' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($talk->status === 'completed')
                                        <div class="text-sm text-gray-900">{{ $talk->present_attendees }}/{{ $talk->total_attendees }}</div>
                                        <div class="text-sm text-gray-500">{{ round($talk->attendance_rate) }}%</div>
                                    @else
                                        <div class="text-sm text-gray-500">N/A</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @switch($talk->status)
                                        @case('scheduled')
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Scheduled</span>
                                            @break
                                        @case('in_progress')
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                                            @break
                                        @case('completed')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>
                                            @break
                                        @case('cancelled')
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelled</span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ $talk->status }}</span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('toolbox-talks.show', $talk) }}" class="text-blue-600 hover:text-blue-700" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($talk->status !== 'completed')
                                            <a href="{{ route('toolbox-talks.edit', $talk) }}" class="text-yellow-600 hover:text-yellow-700" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($talk->status === 'scheduled')
                                            <form action="{{ route('toolbox-talks.start', $talk) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-700" title="Start Talk">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($talk->status === 'in_progress')
                                            <form action="{{ route('toolbox-talks.complete', $talk) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-purple-600 hover:text-purple-700" title="Complete Talk">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($talk->status !== 'completed')
                                            <form action="{{ route('toolbox-talks.destroy', $talk) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this talk?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-comments text-gray-400 text-5xl mb-4"></i>
                                    <p class="text-gray-500 text-lg">No toolbox talks found</p>
                                    <p class="text-gray-400 mb-4">Get started by creating your first toolbox talk</p>
                                    <a href="{{ route('toolbox-talks.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Create Talk
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($toolboxTalks->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            {{ $toolboxTalks->links() }}
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ $toolboxTalks->firstItem() }}</span>
                                    to
                                    <span class="font-medium">{{ $toolboxTalks->lastItem() }}</span>
                                    of
                                    <span class="font-medium">{{ $toolboxTalks->total() }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                {{ $toolboxTalks->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
