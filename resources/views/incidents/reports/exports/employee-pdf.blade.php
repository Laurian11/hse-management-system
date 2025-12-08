<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Incident Report</title>
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
        <h1>Employee Incident Report</h1>
        <p>Period: {{ ucfirst($period) }}</p>
        <p>Date Range: {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
        <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Employee ID</th>
                <th>Department</th>
                <th>Total Reported</th>
                <th>Total Assigned</th>
                <th>Critical Reported</th>
                <th>Injury Reported</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employeeStats as $emp)
            <tr>
                <td>{{ $emp['name'] }}</td>
                <td>{{ $emp['employee_id'] }}</td>
                <td>{{ $emp['department'] }}</td>
                <td>{{ $emp['total_reported'] }}</td>
                <td>{{ $emp['total_assigned'] }}</td>
                <td>{{ $emp['critical_reported'] }}</td>
                <td>{{ $emp['injury_reported'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

