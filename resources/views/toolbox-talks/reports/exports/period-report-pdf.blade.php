<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Period Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .summary { margin-top: 20px; padding: 10px; background-color: #f9f9f9; }
        .summary-item { margin: 5px 0; }
        .stats-grid { display: table; width: 100%; margin-top: 20px; }
        .stats-item { display: table-cell; padding: 10px; text-align: center; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Period Report</h1>
        <p><strong>Period:</strong> {{ ucfirst($period) }}</p>
        <p><strong>Date Range:</strong> {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
    </div>

    <div class="summary">
        <h3>Summary Statistics</h3>
        <div class="stats-grid">
            <div class="stats-item">
                <strong>Total Talks</strong><br>
                {{ $stats['total_talks'] }}
            </div>
            <div class="stats-item">
                <strong>Completed</strong><br>
                {{ $stats['completed'] }}
            </div>
            <div class="stats-item">
                <strong>Avg Attendance Rate</strong><br>
                {{ number_format($stats['avg_attendance_rate'], 2) }}%
            </div>
            <div class="stats-item">
                <strong>Avg Feedback Score</strong><br>
                {{ number_format($stats['avg_feedback_score'], 2) }}/5
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Title</th>
                <th>Date</th>
                <th>Status</th>
                <th>Department</th>
                <th>Attendance Rate (%)</th>
                <th>Feedback Score</th>
            </tr>
        </thead>
        <tbody>
            @forelse($talks as $talk)
            <tr>
                <td>{{ $talk->reference_number }}</td>
                <td>{{ $talk->title }}</td>
                <td>{{ $talk->scheduled_date->format('M j, Y') }}</td>
                <td>{{ ucfirst($talk->status) }}</td>
                <td>{{ $talk->department?->name ?? 'N/A' }}</td>
                <td>{{ number_format($talk->attendance_rate, 2) }}%</td>
                <td>{{ $talk->average_feedback_score ? number_format($talk->average_feedback_score, 2) : 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">No talks found for the selected period</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        Generated on {{ now()->format('F j, Y g:i A') }}
    </div>
</body>
</html>

