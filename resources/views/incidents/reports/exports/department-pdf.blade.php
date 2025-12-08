<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Department Incident Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
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
        <h1>Department Incident Report</h1>
        <p>Period: {{ ucfirst($period) }}</p>
        <p>Date Range: {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
        <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Department</th>
                <th>Total Incidents</th>
                <th>Open</th>
                <th>Investigating</th>
                <th>Closed</th>
                <th>Critical</th>
                <th>Injury/Illness</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departmentStats as $dept)
            <tr>
                <td>{{ $dept['name'] }}</td>
                <td>{{ $dept['total_incidents'] }}</td>
                <td>{{ $dept['open'] }}</td>
                <td>{{ $dept['investigating'] }}</td>
                <td>{{ $dept['closed'] }}</td>
                <td>{{ $dept['critical'] }}</td>
                <td>{{ $dept['injury'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

