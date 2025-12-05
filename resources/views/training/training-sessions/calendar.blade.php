@extends('layouts.app')

@section('title', 'Training Calendar')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center py-6 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Training Calendar</h1>
                    <p class="text-sm text-gray-500 mt-1">View and manage scheduled training sessions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('training.training-sessions.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Schedule Session
                    </a>
                    <a href="{{ route('training.training-sessions.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-list mr-2"></i>List View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Main Calendar -->
            <div class="lg:col-span-3">
                <!-- Calendar Navigation & Filters -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <!-- Month Navigation -->
                        <div class="flex gap-3 items-center">
                            @php
                                $prevMonth = $month - 1;
                                $prevYear = $year;
                                if ($prevMonth < 1) {
                                    $prevMonth = 12;
                                    $prevYear = $year - 1;
                                }
                                $nextMonth = $month + 1;
                                $nextYear = $year;
                                if ($nextMonth > 12) {
                                    $nextMonth = 1;
                                    $nextYear = $year + 1;
                                }
                                $filterParams = request()->except(['year', 'month']);
                                $prevParams = array_merge(['year' => $prevYear, 'month' => $prevMonth], $filterParams);
                                $nextParams = array_merge(['year' => $nextYear, 'month' => $nextMonth], $filterParams);
                            @endphp
                            <a href="{{ route('training.training-sessions.calendar', $prevParams) }}" 
                               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            <h2 class="text-xl font-semibold text-gray-900 min-w-[150px] text-center">
                                {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                            </h2>
                            <a href="{{ route('training.training-sessions.calendar', $nextParams) }}" 
                               class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            <a href="{{ route('training.training-sessions.calendar', $filterParams) }}" 
                               class="px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 text-sm font-medium transition-colors">
                                <i class="fas fa-calendar-day mr-1"></i>Today
                            </a>
                        </div>

                        <!-- Filters -->
                        <form method="GET" class="flex gap-2 flex-wrap">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="month" value="{{ $month }}">
                            <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            <select name="session_type" class="border border-gray-300 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="classroom" {{ request('session_type') == 'classroom' ? 'selected' : '' }}>Classroom</option>
                                <option value="e_learning" {{ request('session_type') == 'e_learning' ? 'selected' : '' }}>E-Learning</option>
                                <option value="on_job_training" {{ request('session_type') == 'on_job_training' ? 'selected' : '' }}>On-Job Training</option>
                                <option value="workshop" {{ request('session_type') == 'workshop' ? 'selected' : '' }}>Workshop</option>
                            </select>
                            @if(request()->hasAny(['status', 'session_type']))
                                <a href="{{ route('training.training-sessions.calendar', ['year' => $year, 'month' => $month]) }}" 
                                   class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm transition-colors">
                                    <i class="fas fa-times mr-1"></i>Clear
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="grid grid-cols-7 gap-px bg-gray-200">
                        <!-- Day Headers -->
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="bg-gray-50 p-3 text-center text-sm font-semibold text-gray-900 border-b-2 border-gray-200">
                                {{ $day }}
                            </div>
                        @endforeach

                        <!-- Calendar Days -->
                        @php
                            $firstDay = \Carbon\Carbon::create($year, $month, 1);
                            $lastDay = $firstDay->copy()->endOfMonth();
                            $startDate = $firstDay->copy()->startOfWeek();
                            $endDate = $lastDay->copy()->endOfWeek();
                            $currentDate = $startDate->copy();
                        @endphp

                        @while($currentDate <= $endDate)
                            @php
                                $daySessions = $sessions->filter(function($session) use ($currentDate) {
                                    return $session->scheduled_start->format('Y-m-d') == $currentDate->format('Y-m-d');
                                });
                                $isToday = $currentDate->isToday();
                                $isCurrentMonth = $currentDate->month == $month;
                            @endphp
                            <div class="bg-white p-2 min-h-[120px] border-r border-b border-gray-200 relative
                                {{ !$isCurrentMonth ? 'bg-gray-50 opacity-60' : '' }}
                                {{ $isToday ? 'bg-blue-50 border-blue-300 border-2' : '' }}
                                hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="text-sm font-semibold
                                        {{ $isToday ? 'text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full' : 'text-gray-900' }}
                                        {{ !$isCurrentMonth ? 'text-gray-400' : '' }}">
                                        {{ $currentDate->day }}
                                    </div>
                                    @if($daySessions->count() > 0)
                                        <span class="text-xs bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded-full font-medium">
                                            {{ $daySessions->count() }}
                                        </span>
                                    @endif
                                </div>
                                <div class="space-y-1 max-h-[80px] overflow-y-auto">
                                    @foreach($daySessions->take(3) as $session)
                                        <a href="{{ route('training.training-sessions.show', $session) }}" 
                                           class="block text-xs p-1.5 rounded transition-all hover:shadow-md cursor-pointer
                                           {{ $session->status == 'completed' ? 'bg-green-100 text-green-800 border border-green-300' : '' }}
                                           {{ $session->status == 'in_progress' ? 'bg-blue-100 text-blue-800 border border-blue-300' : '' }}
                                           {{ $session->status == 'scheduled' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : '' }}
                                           {{ $session->status == 'cancelled' ? 'bg-red-100 text-red-800 border border-red-300' : '' }}">
                                            <div class="font-medium truncate">{{ $session->title }}</div>
                                            <div class="text-xs opacity-75">
                                                <i class="fas fa-clock text-xs"></i> {{ $session->scheduled_start->format('g:i A') }}
                                                @if($session->location_name)
                                                    <span class="ml-1"><i class="fas fa-map-marker-alt text-xs"></i> {{ Str::limit($session->location_name, 15) }}</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                    @if($daySessions->count() > 3)
                                        <div class="text-xs text-gray-500 text-center py-1">
                                            +{{ $daySessions->count() - 3 }} more
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @php $currentDate->addDay(); @endphp
                        @endwhile
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-4 bg-white rounded-lg shadow p-4">
                    <div class="flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-yellow-100 border-2 border-yellow-300 rounded"></div>
                            <span class="font-medium">Scheduled</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-blue-100 border-2 border-blue-300 rounded"></div>
                            <span class="font-medium">In Progress</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-green-100 border-2 border-green-300 rounded"></div>
                            <span class="font-medium">Completed</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-red-100 border-2 border-red-300 rounded"></div>
                            <span class="font-medium">Cancelled</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Statistics -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">This Month</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Total Sessions</span>
                            <span class="text-lg font-bold text-gray-900">{{ $stats['total_sessions'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Scheduled</span>
                            <span class="text-lg font-bold text-yellow-600">{{ $stats['scheduled'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Completed</span>
                            <span class="text-lg font-bold text-green-600">{{ $stats['completed'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Sessions -->
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming (Next 7 Days)</h3>
                    <div class="space-y-2">
                        @php
                            $upcomingSessions = $sessions->filter(function($session) {
                                return $session->scheduled_start->isFuture() && 
                                       $session->scheduled_start->diffInDays(now()) <= 7;
                            })->take(5);
                        @endphp
                        @forelse($upcomingSessions as $session)
                            <a href="{{ route('training.training-sessions.show', $session) }}" 
                               class="block p-3 border border-gray-200 rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all">
                                <div class="font-medium text-sm text-gray-900 mb-1">{{ $session->title }}</div>
                                <div class="text-xs text-gray-500">
                                    <div><i class="fas fa-calendar mr-1"></i>{{ $session->scheduled_start->format('M d, Y') }}</div>
                                    <div><i class="fas fa-clock mr-1"></i>{{ $session->scheduled_start->format('g:i A') }}</div>
                                    @if($session->location_name)
                                        <div><i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($session->location_name, 20) }}</div>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No upcoming sessions</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
