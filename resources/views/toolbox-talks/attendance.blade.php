@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary-black">Attendance Tracking System</h1>
        <div class="flex gap-3">
            <button onclick="exportAttendance()" class="btn-secondary">
                <i class="fas fa-download mr-2"></i>Export Report
            </button>
            <a href="{{ route('toolbox-talks.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Talks
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Total Attendances</div>
            <div class="text-2xl font-bold text-primary-black">{{ number_format($stats['total_attendances']) }}</div>
            <div class="text-xs text-medium-gray mt-1">
                Present: {{ $stats['present_count'] }} | Absent: {{ $stats['absent_count'] }}
            </div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Average Attendance Rate</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['average_attendance_rate'], 1) }}%</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">This Month</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($stats['this_month']) }}</div>
            <div class="text-xs text-medium-gray mt-1">
                Last month: {{ $stats['last_month'] }}
            </div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Check-in Methods</div>
            <div class="text-sm">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-blue-600">Biometric:</span>
                    <span class="font-medium">{{ $stats['biometric_count'] }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-600">Manual:</span>
                    <span class="font-medium">{{ $stats['manual_count'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-border-gray p-4 mb-6 rounded">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Talk title or reference" 
                       class="w-full border border-border-gray rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">Department</label>
                <select name="department_id" class="w-full border border-border-gray rounded px-3 py-2">
                    <option value="">All Departments</option>
                    @foreach($departmentsList ?? [] as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">Status</label>
                <select name="status" class="w-full border border-border-gray rounded px-3 py-2">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full border border-border-gray rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-primary-black mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full border border-border-gray rounded px-3 py-2">
            </div>
            <div class="md:col-span-5 flex gap-2">
                <button type="submit" class="btn-primary">Filter</button>
                <a href="{{ route('toolbox-talks.attendance') }}" class="btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Department Performance -->
    @if(count($departmentStats ?? []) > 0)
    <div class="bg-white border border-border-gray p-6 rounded mb-6">
        <h2 class="text-lg font-semibold text-primary-black mb-4">
            <i class="fas fa-building mr-2"></i>Department Performance
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($departmentStats as $dept)
                <div class="border border-border-gray p-4 rounded">
                    <div class="font-medium text-primary-black mb-2">{{ $dept['name'] }}</div>
                    <div class="text-sm text-medium-gray mb-2">
                        {{ $dept['total_talks'] }} talks • {{ $dept['total_attendances'] }} attendances
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: {{ min(100, $dept['attendance_rate']) }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ number_format($dept['attendance_rate'], 1) }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Attendance Trends -->
    <div class="bg-white border border-border-gray p-6 rounded mb-6">
        <h2 class="text-lg font-semibold text-primary-black mb-4">
            <i class="fas fa-chart-line mr-2"></i>Attendance Trends (Last 6 Months)
        </h2>
        <div class="space-y-2">
            @forelse($attendanceTrends ?? [] as $trend)
                <div class="flex items-center gap-3">
                    <div class="w-20 text-xs text-medium-gray">{{ $trend['month'] }}</div>
                    <div class="flex-1 bg-gray-200 rounded-full h-4">
                        <div class="bg-green-600 h-4 rounded-full flex items-center justify-end pr-2" 
                             style="width: {{ min(100, $trend['attendance_rate']) }}%">
                            @if($trend['attendance_rate'] > 10)
                                <span class="text-xs text-white font-medium">{{ number_format($trend['attendance_rate'], 1) }}%</span>
                            @endif
                        </div>
                    </div>
                    <div class="w-24 text-xs text-medium-gray text-right">
                        {{ $trend['present'] }}/{{ $trend['total'] }}
                    </div>
                </div>
            @empty
                <div class="text-center text-medium-gray py-4">No attendance data available</div>
            @endforelse
        </div>
    </div>

    <!-- Talks List -->
    <div class="bg-white border border-border-gray rounded overflow-hidden">
        <div class="p-4 border-b border-border-gray">
            <h2 class="text-lg font-semibold text-primary-black">All Talks with Attendance</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Talk</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Date</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Department</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Attendances</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Present</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Rate</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-medium text-primary-black">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-gray">
                    @forelse($talks as $talk)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-primary-black">{{ $talk->title }}</div>
                                <div class="text-xs text-medium-gray">{{ $talk->reference_number }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-medium-gray">
                                {{ $talk->scheduled_date->format('M d, Y') }}<br>
                                <span class="text-xs">{{ $talk->scheduled_date->format('h:i A') }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-medium-gray">
                                {{ $talk->department->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="font-medium">{{ $talk->attendances_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="text-green-600 font-medium">{{ $talk->present_count }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($talk->attendances_count > 0)
                                    @php
                                        $rate = ($talk->present_count / $talk->attendances_count) * 100;
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 style="width: {{ min(100, $rate) }}%"></div>
                                        </div>
                                        <span class="text-xs">{{ number_format($rate, 1) }}%</span>
                                    </div>
                                @else
                                    <span class="text-medium-gray">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 text-xs rounded
                                    {{ $talk->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $talk->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $talk->status == 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $talk->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('toolbox-talks.attendance-management', $talk) }}" 
                                   class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-users"></i> Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-medium-gray">No talks found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $talks->links() }}
    </div>
</div>

<script>
function exportAttendance() {
    // Get current filter parameters
    const params = new URLSearchParams(window.location.search);
    params.set('export', '1');
    
    // Create export URL
    const exportUrl = '{{ route("toolbox-talks.attendance") }}?' + params.toString();
    
    // For now, show alert. In production, this would trigger a download
    alert('Export functionality will generate a CSV/PDF report with current filters applied.\n\nThis feature can be enhanced with Laravel Excel or PDF libraries.');
    
    // Uncomment below when export route is implemented:
    // window.location.href = exportUrl;
}
</script>
@endsection
