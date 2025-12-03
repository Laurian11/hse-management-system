@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary-black">Toolbox Talks Reports</h1>
        <div class="flex gap-3">
            <a href="{{ route('toolbox-talks.export-reporting-excel') }}" class="btn-secondary">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </a>
            <a href="{{ route('toolbox-talks.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Talks
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Total Talks</div>
            <div class="text-2xl font-bold text-primary-black">{{ number_format($stats['total_talks']) }}</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Completion Rate</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($stats['completion_rate'], 1) }}%</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Participation Rate</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['participation_rate'], 1) }}%</div>
        </div>
        <div class="bg-white border border-border-gray p-4 rounded">
            <div class="text-sm text-medium-gray mb-1">Satisfaction Score</div>
            <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['satisfaction_score'], 1) }}/5</div>
        </div>
    </div>

    <!-- Report Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Attendance Trends -->
        <div class="bg-white border border-border-gray p-6 rounded">
            <h2 class="text-lg font-semibold text-primary-black mb-4">
                <i class="fas fa-chart-line mr-2"></i>Attendance Trends (Last 6 Months)
            </h2>
            <div class="space-y-2">
                @forelse($attendanceTrends ?? [] as $trend)
                    <div class="flex items-center gap-3">
                        <div class="w-20 text-xs text-medium-gray">{{ $trend['month'] }}</div>
                        <div class="flex-1 bg-gray-200 rounded-full h-4">
                            <div class="bg-blue-600 h-4 rounded-full flex items-center justify-end pr-2" 
                                 style="width: {{ min(100, $trend['avg_attendance']) }}%">
                                @if($trend['avg_attendance'] > 10)
                                    <span class="text-xs text-white font-medium">{{ number_format($trend['avg_attendance'], 1) }}%</span>
                                @endif
                            </div>
                        </div>
                        <div class="w-16 text-xs text-medium-gray text-right">{{ $trend['total_talks'] }} talks</div>
                    </div>
                @empty
                    <div class="text-center text-medium-gray py-8">No attendance data available</div>
                @endforelse
            </div>
        </div>

        <!-- Topic Performance -->
        <div class="bg-white border border-border-gray p-6 rounded">
            <h2 class="text-lg font-semibold text-primary-black mb-4">
                <i class="fas fa-book mr-2"></i>Top Topics Performance
            </h2>
            <div class="space-y-3">
                @forelse($topics ?? [] as $topic)
                    <div class="border border-border-gray p-3 rounded">
                        <div class="flex justify-between items-start mb-2">
                            <div class="font-medium text-primary-black">{{ $topic['name'] }}</div>
                            <div class="text-sm text-medium-gray">{{ $topic['count'] }} talks</div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-medium-gray">Avg Attendance:</span>
                                <span class="font-medium">{{ number_format($topic['avg_attendance'], 1) }}%</span>
                            </div>
                            <div>
                                <span class="text-medium-gray">Avg Rating:</span>
                                <span class="font-medium">{{ number_format($topic['avg_rating'], 1) }}/5</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-medium-gray py-8">No topic data available</div>
                @endforelse
            </div>
        </div>

        <!-- Department Comparison -->
        <div class="bg-white border border-border-gray p-6 rounded">
            <h2 class="text-lg font-semibold text-primary-black mb-4">
                <i class="fas fa-building mr-2"></i>Department Comparison
            </h2>
            <div class="space-y-3">
                @forelse($departmentPerformance ?? [] as $dept)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <div>
                            <span class="text-sm font-medium text-primary-black">{{ $dept['name'] }}</span>
                            <div class="text-xs text-medium-gray">{{ $dept['total_talks'] }} talks</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $dept['avg_attendance']) }}%"></div>
                            </div>
                            <span class="text-sm font-medium">{{ number_format($dept['avg_attendance'], 1) }}%</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-medium-gray py-4">No department data available</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white border border-border-gray p-6 rounded">
            <h2 class="text-lg font-semibold text-primary-black mb-4">
                <i class="fas fa-history mr-2"></i>Recent Activity
            </h2>
            <div class="space-y-3">
                @forelse($recentTalks ?? [] as $talk)
                    <div class="text-sm">
                        <div class="text-medium-gray">{{ $talk->updated_at->diffForHumans() }}</div>
                        <div class="text-primary-black">
                            {{ $talk->title }} - 
                            <span class="text-xs px-2 py-1 rounded
                                {{ $talk->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $talk->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $talk->status == 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $talk->status)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-medium-gray py-4">No recent activity</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function exportReport() {
    // Implement export functionality
    alert('Export functionality will be implemented');
}
</script>
@endsection
