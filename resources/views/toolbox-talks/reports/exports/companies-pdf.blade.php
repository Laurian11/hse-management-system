<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Companies Report</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Companies Report</h1>
        <p><strong>Period:</strong> {{ ucfirst($period) }}</p>
        <p><strong>Date Range:</strong> {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Company</th>
                <th>Total Talks</th>
                <th>Completed Talks</th>
                <th>Completion Rate (%)</th>
                <th>Total Attendees</th>
                <th>Present Attendees</th>
                <th>Attendance Rate (%)</th>
                <th>Avg Feedback Score</th>
            </tr>
        </thead>
        <tbody>
            @forelse($companyStats as $comp)
            <tr>
                <td>{{ $comp['name'] }}</td>
                <td>{{ $comp['total_talks'] }}</td>
                <td>{{ $comp['completed_talks'] }}</td>
                <td>{{ number_format($comp['completion_rate'], 2) }}%</td>
                <td>{{ $comp['total_attendees'] }}</td>
                <td>{{ $comp['present_attendees'] }}</td>
                <td>{{ number_format($comp['attendance_rate'], 2) }}%</td>
                <td>{{ number_format($comp['avg_feedback'], 2) }}/5</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">No company data available</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary</h3>
        <div class="summary-item"><strong>Total Companies:</strong> {{ $companyStats->count() }}</div>
        <div class="summary-item"><strong>Total Talks:</strong> {{ $companyStats->sum('total_talks') }}</div>
        <div class="summary-item"><strong>Total Completed:</strong> {{ $companyStats->sum('completed_talks') }}</div>
        <div class="summary-item"><strong>Average Completion Rate:</strong> {{ number_format($companyStats->avg('completion_rate'), 2) }}%</div>
        <div class="summary-item"><strong>Average Attendance Rate:</strong> {{ number_format($companyStats->avg('attendance_rate'), 2) }}%</div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        Generated on {{ now()->format('F j, Y g:i A') }}
    </div>
</body>
</html>

