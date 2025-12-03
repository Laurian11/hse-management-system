@extends('layouts.app')

@section('title', 'Toolbox Talks Calendar')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-primary-black mb-2">Toolbox Talks Calendar</h1>
            <p class="text-medium-gray">View and manage your scheduled safety talks</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('toolbox-talks.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Schedule Talk
            </a>
            <a href="{{ route('toolbox-talks.index') }}" class="btn-secondary">
                <i class="fas fa-list mr-2"></i>List View
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Calendar -->
        <div class="lg:col-span-3">
            <!-- Calendar Navigation & Filters -->
            <div class="bg-white border border-border-gray p-4 mb-6 rounded-lg">
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
                        <a href="{{ route('toolbox-talks.calendar', $prevParams) }}" 
                           class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <h2 class="text-xl font-semibold text-primary-black min-w-[150px] text-center">
                            {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                        </h2>
                        <a href="{{ route('toolbox-talks.calendar', $nextParams) }}" 
                           class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <a href="{{ route('toolbox-talks.calendar', $filterParams) }}" 
                           class="px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 text-sm font-medium transition-colors">
                            <i class="fas fa-calendar-day mr-1"></i>Today
                        </a>
                    </div>

                    <!-- Filters -->
                    <form method="GET" class="flex gap-2 flex-wrap">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <select name="status" class="border border-border-gray rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <select name="department_id" class="border border-border-gray rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                            <option value="">All Departments</option>
                            @foreach($departments ?? [] as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="talk_type" class="border border-border-gray rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="safety" {{ request('talk_type') == 'safety' ? 'selected' : '' }}>Safety</option>
                            <option value="health" {{ request('talk_type') == 'health' ? 'selected' : '' }}>Health</option>
                            <option value="environment" {{ request('talk_type') == 'environment' ? 'selected' : '' }}>Environment</option>
                            <option value="incident_review" {{ request('talk_type') == 'incident_review' ? 'selected' : '' }}>Incident Review</option>
                        </select>
                        @if(request()->hasAny(['status', 'department_id', 'talk_type']))
                            <a href="{{ route('toolbox-talks.calendar', ['year' => $year, 'month' => $month]) }}" 
                               class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm transition-colors">
                                <i class="fas fa-times mr-1"></i>Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="bg-white border border-border-gray rounded-lg overflow-hidden shadow-sm">
                <div class="grid grid-cols-7 gap-px bg-border-gray">
                    <!-- Day Headers -->
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="bg-gradient-to-b from-gray-50 to-white p-3 text-center text-sm font-semibold text-primary-black border-b-2 border-gray-200">
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
                            $dayTalks = $talks->filter(function($talk) use ($currentDate) {
                                return $talk->scheduled_date->format('Y-m-d') == $currentDate->format('Y-m-d');
                            });
                            $isToday = $currentDate->isToday();
                            $isCurrentMonth = $currentDate->month == $month;
                        @endphp
                        <div class="bg-white p-2 min-h-[120px] border-r border-b border-border-gray relative
                            {{ !$isCurrentMonth ? 'bg-gray-50 opacity-60' : '' }}
                            {{ $isToday ? 'bg-blue-50 border-blue-300 border-2' : '' }}
                            hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <div class="text-sm font-semibold
                                    {{ $isToday ? 'text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full' : 'text-primary-black' }}
                                    {{ !$isCurrentMonth ? 'text-gray-400' : '' }}">
                                    {{ $currentDate->day }}
                                </div>
                                @if($dayTalks->count() > 0)
                                    <span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full font-medium">
                                        {{ $dayTalks->count() }}
                                    </span>
                                @endif
                            </div>
                            <div class="space-y-1 max-h-[80px] overflow-y-auto">
                                @foreach($dayTalks->take(3) as $talk)
                                    <a href="{{ route('toolbox-talks.show', $talk) }}" 
                                       class="block text-xs p-1.5 rounded transition-all hover:shadow-md cursor-pointer
                                       {{ $talk->status == 'completed' ? 'bg-green-100 text-green-800 border border-green-300' : '' }}
                                       {{ $talk->status == 'in_progress' ? 'bg-blue-100 text-blue-800 border border-blue-300' : '' }}
                                       {{ $talk->status == 'scheduled' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : '' }}
                                       {{ $talk->talk_type == 'safety' ? 'border-l-4 border-l-red-500' : '' }}
                                       {{ $talk->talk_type == 'health' ? 'border-l-4 border-l-green-500' : '' }}
                                       {{ $talk->talk_type == 'environment' ? 'border-l-4 border-l-blue-500' : '' }}">
                                        <div class="font-medium truncate">{{ $talk->title }}</div>
                                        <div class="text-xs opacity-75">
                                            @if($talk->start_time)
                                                <i class="fas fa-clock text-xs"></i> {{ $talk->start_time->format('h:i A') }}
                                            @endif
                                            @if($talk->department)
                                                <span class="ml-1"><i class="fas fa-building text-xs"></i> {{ $talk->department->name }}</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                                @if($dayTalks->count() > 3)
                                    <div class="text-xs text-medium-gray text-center py-1">
                                        +{{ $dayTalks->count() - 3 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                        @php $currentDate->addDay(); @endphp
                    @endwhile
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-4 bg-white border border-border-gray p-4 rounded-lg">
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
                    <div class="flex items-center gap-2 ml-4">
                        <div class="w-4 h-4 border-l-4 border-l-red-500 bg-gray-100"></div>
                        <span class="font-medium">Safety</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 border-l-4 border-l-green-500 bg-gray-100"></div>
                        <span class="font-medium">Health</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 border-l-4 border-l-blue-500 bg-gray-100"></div>
                        <span class="font-medium">Environment</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Statistics -->
            <div class="bg-white border border-border-gray p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-primary-black mb-4">This Month</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-medium-gray">Total Talks</span>
                        <span class="text-lg font-bold text-primary-black">{{ $stats['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-medium-gray">Scheduled</span>
                        <span class="text-lg font-bold text-yellow-600">{{ $stats['scheduled'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-medium-gray">In Progress</span>
                        <span class="text-lg font-bold text-blue-600">{{ $stats['in_progress'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-medium-gray">Completed</span>
                        <span class="text-lg font-bold text-green-600">{{ $stats['completed'] }}</span>
                    </div>
                    <div class="pt-3 border-t border-border-gray">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-medium-gray">Total Attendances</span>
                            <span class="text-sm font-bold text-primary-black">{{ $stats['total_attendances'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-medium-gray">Avg Attendance</span>
                            <span class="text-sm font-bold text-blue-600">{{ number_format($stats['avg_attendance_rate'], 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Talks -->
            <div class="bg-white border border-border-gray p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-primary-black mb-4">Upcoming (Next 7 Days)</h3>
                <div class="space-y-2">
                    @forelse($upcomingTalks ?? [] as $talk)
                        <a href="{{ route('toolbox-talks.show', $talk) }}" 
                           class="block p-3 border border-border-gray rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all">
                            <div class="font-medium text-sm text-primary-black mb-1">{{ $talk->title }}</div>
                            <div class="text-xs text-medium-gray">
                                <div><i class="fas fa-calendar mr-1"></i>{{ $talk->scheduled_date->format('M d, Y') }}</div>
                                @if($talk->start_time)
                                    <div><i class="fas fa-clock mr-1"></i>{{ $talk->start_time->format('h:i A') }}</div>
                                @endif
                                @if($talk->department)
                                    <div><i class="fas fa-building mr-1"></i>{{ $talk->department->name }}</div>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-medium-gray text-center py-4">No upcoming talks</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
