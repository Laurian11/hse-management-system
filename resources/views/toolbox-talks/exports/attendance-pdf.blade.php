<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report - {{ $talk->reference_number }}</title>
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
        <h1>Toolbox Talk Attendance Report</h1>
        <p><strong>Reference:</strong> {{ $talk->reference_number }}</p>
        <p><strong>Title:</strong> {{ $talk->title }}</p>
        <p><strong>Date:</strong> {{ $talk->scheduled_date->format('F j, Y g:i A') }}</p>
        <p><strong>Location:</strong> {{ $talk->location }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Employee ID</th>
                <th>Department</th>
                <th>Status</th>
                <th>Check-in Time</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
            <tr>
                <td>{{ $attendance->employee_name }}</td>
                <td>{{ $attendance->employee_id_number }}</td>
                <td>{{ $attendance->department }}</td>
                <td>{{ ucfirst($attendance->attendance_status) }}</td>
                <td>{{ $attendance->check_in_time ? $attendance->check_in_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $attendance->check_in_method)) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No attendance records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary</h3>
        <div class="summary-item"><strong>Total Attendees:</strong> {{ $attendances->count() }}</div>
        <div class="summary-item"><strong>Present:</strong> {{ $attendances->where('attendance_status', 'present')->count() }}</div>
        <div class="summary-item"><strong>Absent:</strong> {{ $attendances->where('attendance_status', 'absent')->count() }}</div>
        <div class="summary-item"><strong>Late:</strong> {{ $attendances->where('attendance_status', 'late')->count() }}</div>
        <div class="summary-item"><strong>Attendance Rate:</strong> {{ number_format($talk->attendance_rate, 2) }}%</div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        Generated on {{ now()->format('F j, Y g:i A') }}
    </div>

    <!-- QR Code -->
    @php
        $qrData = \App\Services\QRCodeService::forToolboxTalk($talk->id, $talk->reference_number);
    @endphp
    <x-pdf-qr-code :data="$qrData" :size="100" position="bottom-right" label="Scan to view" />
</body>
</html>

