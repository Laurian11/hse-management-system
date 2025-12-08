<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Safety Communications Export</title>
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
        .header p {
            margin: 5px 0;
            color: #666;
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
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-draft { background-color: #e5e7eb; color: #374151; }
        .badge-scheduled { background-color: #dbeafe; color: #1e40af; }
        .badge-sent { background-color: #d1fae5; color: #065f46; }
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
        <h1>Safety Communications Export</h1>
        <p>Generated on: {{ date('F j, Y g:i A') }}</p>
        @if(!empty($filters))
        <p>Filters Applied: 
            @if(isset($filters['type'])) Type: {{ ucfirst($filters['type']) }} @endif
            @if(isset($filters['status'])) Status: {{ ucfirst($filters['status']) }} @endif
            @if(isset($filters['priority'])) Priority: {{ ucfirst($filters['priority']) }} @endif
        </p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Reference</th>
                <th>Title</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Target Audience</th>
                <th>Delivery Method</th>
                <th>Recipients</th>
                <th>Acknowledged</th>
                <th>Created</th>
                <th>Sent At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($communications as $communication)
            <tr>
                <td>{{ $communication->reference_number }}</td>
                <td>{{ Str::limit($communication->title, 40) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $communication->communication_type)) }}</td>
                <td>{{ ucfirst($communication->priority_level) }}</td>
                <td>
                    <span class="badge badge-{{ $communication->status }}">
                        {{ ucfirst($communication->status) }}
                    </span>
                </td>
                <td>{{ $communication->getTargetAudienceLabel() }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $communication->delivery_method)) }}</td>
                <td>{{ $communication->total_recipients ?? 0 }}</td>
                <td>
                    {{ $communication->acknowledged_count ?? 0 }}
                    @if($communication->acknowledgment_rate)
                        ({{ number_format($communication->acknowledgment_rate, 1) }}%)
                    @endif
                </td>
                <td>{{ $communication->created_at->format('M j, Y') }}</td>
                <td>{{ $communication->sent_at ? $communication->sent_at->format('M j, Y') : 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align: center; padding: 20px;">
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

