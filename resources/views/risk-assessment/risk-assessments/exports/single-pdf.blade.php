<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Risk Assessment Report - {{ $assessment->reference_number }}</title>
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
        <h1>Risk Assessment Report</h1>
        <h2>{{ $assessment->reference_number }}</h2>
        <p>Generated on: {{ now()->format('F j, Y g:i A') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Basic Information</div>
        <div class="field">
            <span class="field-label">Reference Number:</span>
            <span class="field-value">{{ $assessment->reference_number }}</span>
        </div>
        <div class="field">
            <span class="field-label">Title:</span>
            <span class="field-value">{{ $assessment->title }}</span>
        </div>
        <div class="field">
            <span class="field-label">Status:</span>
            <span class="field-value">{{ ucfirst(str_replace('_', ' ', $assessment->status)) }}</span>
        </div>
        <div class="field">
            <span class="field-label">Assessment Type:</span>
            <span class="field-value">{{ ucfirst($assessment->assessment_type ?? 'N/A') }}</span>
        </div>
        <div class="field">
            <span class="field-label">Assessment Date:</span>
            <span class="field-value">{{ $assessment->assessment_date ? $assessment->assessment_date->format('F j, Y') : 'N/A' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Risk Assessment</div>
        <div class="field">
            <span class="field-label">Severity:</span>
            <span class="field-value">{{ ucfirst($assessment->severity ?? 'N/A') }} (Score: {{ $assessment->severity_score ?? 'N/A' }})</span>
        </div>
        <div class="field">
            <span class="field-label">Likelihood:</span>
            <span class="field-value">{{ ucfirst($assessment->likelihood ?? 'N/A') }} (Score: {{ $assessment->likelihood_score ?? 'N/A' }})</span>
        </div>
        <div class="field">
            <span class="field-label">Risk Score:</span>
            <span class="field-value">{{ $assessment->risk_score ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Risk Level:</span>
            <span class="field-value">{{ ucfirst($assessment->risk_level ?? 'N/A') }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Description</div>
        <p>{{ $assessment->description ?? 'No description provided.' }}</p>
    </div>

    @if($assessment->existing_controls)
    <div class="section">
        <div class="section-title">Existing Controls</div>
        <p>{{ $assessment->existing_controls }}</p>
        @if($assessment->existing_controls_effectiveness)
        <div class="field">
            <span class="field-label">Effectiveness:</span>
            <span class="field-value">{{ ucfirst($assessment->existing_controls_effectiveness) }}</span>
        </div>
        @endif
    </div>
    @endif

    @if($assessment->residual_risk_score)
    <div class="section">
        <div class="section-title">Residual Risk</div>
        <div class="field">
            <span class="field-label">Residual Risk Score:</span>
            <span class="field-value">{{ $assessment->residual_risk_score }}</span>
        </div>
        <div class="field">
            <span class="field-label">Residual Risk Level:</span>
            <span class="field-value">{{ ucfirst($assessment->residual_risk_level ?? 'N/A') }}</span>
        </div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">People Involved</div>
        <div class="field">
            <span class="field-label">Created By:</span>
            <span class="field-value">{{ $assessment->creator?->name ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Assigned To:</span>
            <span class="field-value">{{ $assessment->assignedTo?->name ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Department:</span>
            <span class="field-value">{{ $assessment->department?->name ?? 'N/A' }}</span>
        </div>
        <div class="field">
            <span class="field-label">Company:</span>
            <span class="field-value">{{ $assessment->company?->name ?? 'N/A' }}</span>
        </div>
    </div>

    @if($assessment->next_review_date)
    <div class="section">
        <div class="section-title">Review Information</div>
        <div class="field">
            <span class="field-label">Next Review Date:</span>
            <span class="field-value">{{ $assessment->next_review_date->format('F j, Y') }}</span>
        </div>
        <div class="field">
            <span class="field-label">Review Frequency:</span>
            <span class="field-value">{{ ucfirst(str_replace('_', ' ', $assessment->review_frequency ?? 'N/A')) }}</span>
        </div>
    </div>
    @endif

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666;">
        <p>This report was generated by the HSE Management System</p>
    </div>

    <!-- QR Code -->
    @php
        $qrData = \App\Services\QRCodeService::forRiskAssessment($assessment->id, $assessment->reference_number);
    @endphp
    <x-pdf-qr-code :data="$qrData" :size="100" position="bottom-right" label="Scan to view online" />
</body>
</html>

