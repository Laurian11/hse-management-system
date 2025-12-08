<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Attendance Report</title>
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
        <h1>Employee Attendance Report</h1>
        <p><strong>Period:</strong> {{ ucfirst($period) }}</p>
        <p><strong>Date Range:</strong> {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Employee ID</th>
                <th>Department</th>
                <th>Total Talks</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>Attendance Rate (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employeeStats as $emp)
            <tr>
                <td>{{ $emp['name'] }}</td>
                <td>{{ $emp['employee_id'] }}</td>
                <td>{{ $emp['department'] }}</td>
                <td>{{ $emp['total_talks'] }}</td>
                <td>{{ $emp['present'] }}</td>
                <td>{{ $emp['absent'] }}</td>
                <td>{{ $emp['late'] }}</td>
                <td>{{ number_format($emp['attendance_rate'], 2) }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">No employee data available</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary</h3>
        <div class="summary-item"><strong>Total Employees:</strong> {{ $employeeStats->count() }}</div>
        <div class="summary-item"><strong>Total Talks:</strong> {{ $employeeStats->sum('total_talks') }}</div>
        <div class="summary-item"><strong>Total Present:</strong> {{ $employeeStats->sum('present') }}</div>
        <div class="summary-item"><strong>Total Absent:</strong> {{ $employeeStats->sum('absent') }}</div>
        <div class="summary-item"><strong>Average Attendance Rate:</strong> {{ number_format($employeeStats->avg('attendance_rate'), 2) }}%</div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        Generated on {{ now()->format('F j, Y g:i A') }}
    </div>
</body>
</html>

