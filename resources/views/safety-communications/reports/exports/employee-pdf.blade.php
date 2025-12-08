<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Communication Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
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
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Employee Communication Report</h1>
        <p>Generated on: {{ date('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Department</th>
                <th>Total</th>
                <th>Sent</th>
                <th>Acknowledged</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employeeStats as $stat)
            <tr>
                <td>{{ $stat['employee']->name }}</td>
                <td>{{ $stat['employee']->employee->department->name ?? 'N/A' }}</td>
                <td>{{ $stat['total'] }}</td>
                <td>{{ $stat['sent'] }}</td>
                <td>{{ $stat['acknowledged'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">
                    No data available
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total Employees: {{ count($employeeStats) }}</p>
        <p>This is a system-generated report. Please do not reply to this document.</p>
    </div>
</body>
</html>

