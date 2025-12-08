<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Department Communication Report</title>
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
        <h1>Department Communication Report</h1>
        <p>Generated on: {{ date('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Department</th>
                <th>Total</th>
                <th>Sent</th>
                <th>Scheduled</th>
                <th>Draft</th>
                <th>Avg. Acknowledgment Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departmentStats as $stat)
            <tr>
                <td>{{ $stat['department']->name }}</td>
                <td>{{ $stat['total'] }}</td>
                <td>{{ $stat['sent'] }}</td>
                <td>{{ $stat['scheduled'] }}</td>
                <td>{{ $stat['draft'] }}</td>
                <td>{{ number_format($stat['avg_ack_rate'], 1) }}%</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    No data available
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total Departments: {{ count($departmentStats) }}</p>
        <p>This is a system-generated report. Please do not reply to this document.</p>
    </div>
</body>
</html>

