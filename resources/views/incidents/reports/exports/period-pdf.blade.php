<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Period Incident Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .stats {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Period Incident Report</h1>
        <p>Period: {{ ucfirst($period) }}</p>
        <p>Date Range: {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
        <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <div class="stats">
        <h2>Summary Statistics</h2>
        <p>Total Incidents: {{ $stats['total_incidents'] }}</p>
        <p>Open: {{ $stats['open'] }}</p>
        <p>Investigating: {{ $stats['investigating'] }}</p>
        <p>Closed: {{ $stats['closed'] }}</p>
        <p>Critical: {{ $stats['critical'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Title</th>
                <th>Date</th>
                <th>Status</th>
                <th>Severity</th>
                <th>Event Type</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidents as $incident)
            <tr>
                <td>{{ $incident->reference_number }}</td>
                <td>{{ $incident->title ?? $incident->incident_type }}</td>
                <td>{{ $incident->incident_date->format('Y-m-d') }}</td>
                <td>{{ ucfirst($incident->status) }}</td>
                <td>{{ ucfirst($incident->severity) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $incident->event_type ?? 'N/A')) }}</td>
                <td>{{ $incident->department?->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

