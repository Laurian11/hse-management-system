<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Department Risk Assessment Report</title>
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
        <h1>Department Risk Assessment Report</h1>
        <p>Period: {{ ucfirst($period) }}</p>
        <p>Date Range: {{ $startDate->format('F j, Y') }} to {{ $endDate->format('F j, Y') }}</p>
        <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Department</th>
                <th>Total Assessments</th>
                <th>High Risk</th>
                <th>Approved</th>
                <th>Due for Review</th>
            </tr>
        </thead>
        <tbody>
            @foreach($departmentStats as $dept)
            <tr>
                <td>{{ $dept['name'] }}</td>
                <td>{{ $dept['total_assessments'] }}</td>
                <td>{{ $dept['high_risk'] }}</td>
                <td>{{ $dept['approved'] }}</td>
                <td>{{ $dept['due_for_review'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

