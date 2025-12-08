<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Safety Communication - {{ $communication->reference_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            line-height: 1.6;
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
        .section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #0066CC;
        }
        .section h2 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 11px;
            margin-bottom: 3px;
        }
        .info-value {
            color: #333;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-draft { background-color: #e5e7eb; color: #374151; }
        .badge-scheduled { background-color: #dbeafe; color: #1e40af; }
        .badge-sent { background-color: #d1fae5; color: #065f46; }
        .badge-low { color: #6b7280; }
        .badge-medium { color: #2563eb; }
        .badge-high { color: #ea580c; }
        .badge-critical { color: #dc2626; }
        .badge-emergency { background-color: #fee2e2; color: #991b1b; }
        .message-content {
            background-color: white;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            white-space: pre-wrap;
            line-height: 1.8;
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
        <h1>Safety Communication Details</h1>
        <p>Reference Number: {{ $communication->reference_number }}</p>
        <p>Generated on: {{ date('F j, Y g:i A') }}</p>
    </div>

    <!-- Basic Information -->
    <div class="section">
        <h2>Basic Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Title</div>
                <div class="info-value">{{ $communication->title }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Reference Number</div>
                <div class="info-value">{{ $communication->reference_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Communication Type</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $communication->communication_type)) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Priority Level</div>
                <div class="info-value">
                    <span class="badge badge-{{ $communication->priority_level }}">
                        {{ ucfirst($communication->priority_level) }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge badge-{{ $communication->status }}">
                        {{ ucfirst($communication->status) }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Company</div>
                <div class="info-value">{{ $communication->company->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Message Content -->
    <div class="section">
        <h2>Message Content</h2>
        <div class="message-content">{{ $communication->message }}</div>
    </div>

    <!-- Target Audience & Delivery -->
    <div class="section">
        <h2>Target Audience & Delivery</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Target Audience</div>
                <div class="info-value">{{ $communication->getTargetAudienceLabel() }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Delivery Method</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $communication->delivery_method)) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Total Recipients</div>
                <div class="info-value">{{ $recipientCount }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Requires Acknowledgment</div>
                <div class="info-value">{{ $communication->requires_acknowledgment ? 'Yes' : 'No' }}</div>
            </div>
            @if($communication->acknowledgment_deadline)
            <div class="info-item">
                <div class="info-label">Acknowledgment Deadline</div>
                <div class="info-value">{{ $communication->acknowledgment_deadline->format('F j, Y g:i A') }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Acknowledgment Status -->
    @if($communication->requires_acknowledgment)
    <div class="section">
        <h2>Acknowledgment Status</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Acknowledged</div>
                <div class="info-value">{{ $communication->acknowledged_count ?? 0 }} / {{ $communication->total_recipients ?? 0 }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Acknowledgment Rate</div>
                <div class="info-value">{{ $communication->acknowledgment_rate ? number_format($communication->acknowledgment_rate, 1) . '%' : '0%' }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Timeline -->
    <div class="section">
        <h2>Timeline</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Created At</div>
                <div class="info-value">{{ $communication->created_at->format('F j, Y g:i A') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Created By</div>
                <div class="info-value">{{ $communication->creator->name ?? 'N/A' }}</div>
            </div>
            @if($communication->scheduled_send_time)
            <div class="info-item">
                <div class="info-label">Scheduled Send Time</div>
                <div class="info-value">{{ $communication->scheduled_send_time->format('F j, Y g:i A') }}</div>
            </div>
            @endif
            @if($communication->sent_at)
            <div class="info-item">
                <div class="info-label">Sent At</div>
                <div class="info-value">{{ $communication->sent_at->format('F j, Y g:i A') }}</div>
            </div>
            @endif
            @if($communication->expires_at)
            <div class="info-item">
                <div class="info-label">Expires At</div>
                <div class="info-value">{{ $communication->expires_at->format('F j, Y g:i A') }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>This is a system-generated report. Please do not reply to this document.</p>
    </div>
</body>
</html>

