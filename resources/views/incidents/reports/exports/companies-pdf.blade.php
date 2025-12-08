<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Companies Incident Report</title>
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
        <h1>Companies Incident Report</h1>
        <p>Period: {{ ucfirst($period) }}</p>
        <p>Date Range: {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
        <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Company</th>
                <th>Total Incidents</th>
                <th>Open</th>
                <th>Closed</th>
                <th>Critical</th>
                <th>Injury/Illness</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companyStats as $comp)
            <tr>
                <td>{{ $comp['name'] }}</td>
                <td>{{ $comp['total_incidents'] }}</td>
                <td>{{ $comp['open'] }}</td>
                <td>{{ $comp['closed'] }}</td>
                <td>{{ $comp['critical'] }}</td>
                <td>{{ $comp['injury'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

