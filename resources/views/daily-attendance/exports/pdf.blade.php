<!DOCTYPE html>
<html>
<head>
    <title>Daily Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Attendance Report</h1>
        <p>Date: {{ $date }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Device/Location</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Status</th>
                <th>Total Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
            <tr>
                <td>{{ $attendance->attendance_date->format('Y-m-d') }}</td>
                <td>{{ $attendance->employee_id_number }}</td>
                <td>{{ $attendance->employee_name }}</td>
                <td>{{ $attendance->department->name ?? 'N/A' }}</td>
                <td>{{ $attendance->biometricDevice->location_name ?? 'N/A' }}</td>
                <td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') : 'N/A' }}</td>
                <td>{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i:s') : 'N/A' }}</td>
                <td>{{ ucfirst($attendance->attendance_status) }}</td>
                <td>{{ $attendance->total_work_hours ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

