<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Period Risk Assessment Report</title>
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
        <h1>Period Risk Assessment Report</h1>
        <p>Period: {{ ucfirst($period) }}</p>
        <p>Date Range: {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
        <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <div class="stats">
        <h2>Summary Statistics</h2>
        <p>Total Assessments: {{ $stats['total_assessments'] }}</p>
        <p>High Risk: {{ $stats['high_risk'] }}</p>
        <p>Approved: {{ $stats['approved'] }}</p>
        <p>Due for Review: {{ $stats['due_for_review'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Title</th>
                <th>Date</th>
                <th>Status</th>
                <th>Risk Level</th>
                <th>Risk Score</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assessments as $assessment)
            <tr>
                <td>{{ $assessment->reference_number }}</td>
                <td>{{ $assessment->title }}</td>
                <td>{{ $assessment->assessment_date->format('Y-m-d') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $assessment->status)) }}</td>
                <td>{{ ucfirst($assessment->risk_level) }}</td>
                <td>{{ $assessment->risk_score }}</td>
                <td>{{ $assessment->department?->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

