<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Incident Report - {{ $incident->reference_number }}</title>
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
            padding-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .field {
            margin-bottom: 8px;
        }
        .field-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .field-value {
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
        <h1>Incident Report</h1>
        <h2>{{ $incident->reference_number }}</h2>
        <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Basic Information</div>
        <div class="field">
            <span class="field-label">Reference Number:</span>
            <span class="field-value">{{ $incident->reference_number }}</span>
        </div>
        <div class="field">
            <span class="field-label">Title:</span>
            <span class="field-value">{{ $incident->title ?? $incident->incident_type }}</span>
        </div>
        <div class="field">
            <span class="field-label">Status:</span>
            <span class="field-value">{{ ucfirst($incident->status) }}</span>
        </div>
        <div class="field">
            <span class="field-label">Severity:</span>
            <span class="field-value">{{ ucfirst($incident->severity) }}</span>
        </div>
        <div class="field">
            <span class="field-label">Event Type:</span>
            <span class="field-value">{{ ucfirst(str_replace('_', ' ', $incident->event_type ?? 'N/A')) }}</span>
        </div>
        <div class="field">
            <span class="field-label">Incident Date:</span>
            <span class="field-value">{{ $incident->incident_date ? $incident->incident_date->format('F j, Y g:i A') : 'N/A' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Location</div>
        <div class="field">
            <span class="field-label">Location:</span>
            <span class="field-value">{{ $incident->location ?? 'N/A' }}</span>
        </div>
        @if($incident->location_specific)
        <div class="field">
            <span class="field-label">Specific Location:</span>
            <span class="field-value">{{ $incident->location_specific }}</span>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Description</div>
        <p>{{ $incident->description ?? 'No description provided.' }}</p>
    </div>

    <div class="section">
        <div class="section-title">People Involved</div>
        <div class="field">
            <span class="field-label">Reported By:</span>
            <span class="field-value">{{ $incident->reporter?->name ?? $incident->reporter_name ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Assigned To:</span>
            <span class="field-value">{{ $incident->assignedTo?->name ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Department:</span>
            <span class="field-value">{{ $incident->department?->name ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Company:</span>
            <span class="field-value">{{ $incident->company?->name ?? 'N/A' }}</span>
        </div>
    </div>

    @if($incident->actions_taken)
    <div class="section">
        <div class="section-title">Actions Taken</div>
        <p>{{ $incident->actions_taken }}</p>
    </div>
    @endif

    @if($incident->resolution_notes)
    <div class="section">
        <div class="section-title">Resolution Notes</div>
        <p>{{ $incident->resolution_notes }}</p>
    </div>
    @endif

    @if($incident->investigation)
    <div class="section">
        <div class="section-title">Investigation</div>
        <div class="field">
            <span class="field-label">Investigation Status:</span>
            <span class="field-value">{{ ucfirst($incident->investigation->status ?? 'N/A') }}</span>
        </div>
    </div>
    @endif

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666;">
        <p>This report was generated by the HSE Management System</p>
    </div>

    <!-- QR Code -->
    @php
        $qrData = \App\Services\QRCodeService::forIncident($incident->id, $incident->reference_number);
    @endphp
    <x-pdf-qr-code :data="$qrData" :size="100" position="bottom-right" label="Scan to view online" />
</body>
</html>

