<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Procurement Requisition - {{ $procurementRequest->reference_number }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
        }
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #000;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding: 5px 10px 5px 0;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #000;
            font-size: 10px;
            color: #666;
        }
        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 33%;
            padding: 10px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PROCUREMENT REQUISITION</h1>
        <p><strong>Reference Number:</strong> {{ $procurementRequest->reference_number }}</p>
        <p><strong>Date:</strong> {{ $procurementRequest->created_at->format('F d, Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Request Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Item Name:</div>
                <div class="info-value">{{ $procurementRequest->item_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Category:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $procurementRequest->item_category)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Quantity:</div>
                <div class="info-value">{{ $procurementRequest->quantity }} {{ $procurementRequest->unit ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Priority:</div>
                <div class="info-value">{{ strtoupper($procurementRequest->priority) }}</div>
            </div>
            @if($procurementRequest->estimated_cost)
            <div class="info-row">
                <div class="info-label">Estimated Cost:</div>
                <div class="info-value">{{ format_currency($procurementRequest->estimated_cost, $procurementRequest->currency) }}</div>
            </div>
            @endif
            @if($procurementRequest->required_date)
            <div class="info-row">
                <div class="info-label">Required By:</div>
                <div class="info-value">{{ $procurementRequest->required_date->format('F d, Y') }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">{{ strtoupper(str_replace('_', ' ', $procurementRequest->status)) }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Requestor Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Requested By:</div>
                <div class="info-value">{{ $procurementRequest->requestedBy->name ?? 'N/A' }}</div>
            </div>
            @if($procurementRequest->requestedBy)
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $procurementRequest->requestedBy->email ?? 'N/A' }}</div>
            </div>
            @endif
            @if($procurementRequest->department)
            <div class="info-row">
                <div class="info-label">Department:</div>
                <div class="info-value">{{ $procurementRequest->department->name }}</div>
            </div>
            @endif
        </div>
    </div>

    @if($procurementRequest->description)
    <div class="section">
        <div class="section-title">Description</div>
        <p>{{ $procurementRequest->description }}</p>
    </div>
    @endif

    @if($procurementRequest->justification)
    <div class="section">
        <div class="section-title">Justification</div>
        <p>{{ $procurementRequest->justification }}</p>
    </div>
    @endif

    @if($procurementRequest->notes)
    <div class="section">
        <div class="section-title">Additional Notes</div>
        <p>{{ $procurementRequest->notes }}</p>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <strong>Requestor Signature</strong>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Department Head</strong>
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Procurement Approval</strong>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This is an automatically generated requisition document from the HSE Management System.</p>
        <p>For inquiries, please contact: {{ config('mail.from.address', 'noreply@hesu.co.tz') }}</p>
    </div>
</body>
</html>

