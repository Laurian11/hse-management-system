<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Period Communication Report</title>
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
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
        }
        .stat-box h3 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #666;
        }
        .stat-box p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
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
        <h1>Period Communication Report</h1>
        <p>Period: {{ ucfirst($period) }} - {{ $date }}</p>
        <p>Generated on: {{ date('F j, Y g:i A') }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>Total</h3>
            <p>{{ $stats['total'] }}</p>
        </div>
        <div class="stat-box">
            <h3>Sent</h3>
            <p>{{ $stats['sent'] }}</p>
        </div>
        <div class="stat-box">
            <h3>Scheduled</h3>
            <p>{{ $stats['scheduled'] }}</p>
        </div>
        <div class="stat-box">
            <h3>Avg. Acknowledgment</h3>
            <p>{{ number_format($stats['avg_ack_rate'], 1) }}%</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Title</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Recipients</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($communications as $communication)
            <tr>
                <td>{{ $communication->reference_number }}</td>
                <td>{{ Str::limit($communication->title, 40) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $communication->communication_type)) }}</td>
                <td>{{ ucfirst($communication->priority_level) }}</td>
                <td>{{ ucfirst($communication->status) }}</td>
                <td>{{ $communication->total_recipients ?? 0 }}</td>
                <td>{{ $communication->created_at->format('M j, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px;">
                    No communications found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total Records: {{ $communications->count() }}</p>
        <p>This is a system-generated report. Please do not reply to this document.</p>
    </div>
</body>
</html>

