<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Training Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .certificate-container {
            background: white;
            padding: 60px;
            border: 8px solid #d4af37;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            min-height: 500px;
        }
        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .certificate-header h1 {
            font-size: 36px;
            color: #1a1a1a;
            margin: 0;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .certificate-header p {
            font-size: 18px;
            color: #666;
            margin: 10px 0;
        }
        .certificate-body {
            text-align: center;
            margin: 50px 0;
        }
        .certificate-body p {
            font-size: 20px;
            line-height: 1.8;
            color: #333;
            margin: 20px 0;
        }
        .recipient-name {
            font-size: 32px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 30px 0;
            text-decoration: underline;
            text-decoration-color: #d4af37;
            text-decoration-thickness: 3px;
        }
        .certificate-details {
            margin: 40px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #667eea;
        }
        .certificate-details h3 {
            font-size: 16px;
            color: #333;
            margin: 10px 0 5px 0;
        }
        .certificate-details p {
            font-size: 14px;
            color: #666;
            margin: 5px 0;
        }
        .certificate-footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-section {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 2px solid #333;
            margin: 60px auto 10px;
            width: 150px;
        }
        .signature-name {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .signature-title {
            font-size: 12px;
            color: #666;
        }
        .certificate-number {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 10px;
            color: #999;
        }
        .verification-code {
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-size: 10px;
            color: #999;
        }
        .company-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-logo h2 {
            font-size: 24px;
            color: #667eea;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Company Header -->
        <div class="company-logo">
            <h2>{{ $certificate->company->name ?? 'HSE Management System' }}</h2>
        </div>

        <!-- Certificate Header -->
        <div class="certificate-header">
            <h1>CERTIFICATE OF COMPLETION</h1>
            <p>This is to certify that</p>
        </div>

        <!-- Certificate Body -->
        <div class="certificate-body">
            <div class="recipient-name">
                {{ $certificate->user->name ?? 'N/A' }}
            </div>
            <p>has successfully completed the training program</p>
            <p style="font-size: 24px; font-weight: bold; color: #667eea; margin: 30px 0;">
                {{ $certificate->certificate_title ?? 'Training Program' }}
            </p>
            @if($certificate->certificate_description)
                <p style="font-size: 16px; color: #666; margin: 20px 0;">
                    {{ $certificate->certificate_description }}
                </p>
            @endif
        </div>

        <!-- Certificate Details -->
        <div class="certificate-details">
            <h3>Training Details</h3>
            @if($certificate->trainingSession)
                <p><strong>Session:</strong> {{ $certificate->trainingSession->title }}</p>
                <p><strong>Date:</strong> {{ $certificate->trainingSession->scheduled_start->format('F j, Y') }}</p>
            @endif
            <p><strong>Issue Date:</strong> {{ $certificate->issue_date->format('F j, Y') }}</p>
            @if($certificate->has_expiry && $certificate->expiry_date)
                <p><strong>Valid Until:</strong> {{ $certificate->expiry_date->format('F j, Y') }}</p>
            @endif
            @if($certificate->issuing_organization)
                <p><strong>Issued By:</strong> {{ $certificate->issuing_organization }}</p>
            @endif
        </div>

        <!-- Certificate Footer -->
        <div class="certificate-footer">
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $certificate->issuer->name ?? 'Authorized Signatory' }}</div>
                <div class="signature-title">{{ $certificate->issuing_authority ?? 'Training Manager' }}</div>
            </div>
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $certificate->company->name ?? 'Company' }}</div>
                <div class="signature-title">Date: {{ $certificate->issue_date->format('F j, Y') }}</div>
            </div>
        </div>

        <!-- Certificate Number -->
        <div class="certificate-number">
            Certificate No: {{ $certificate->certificate_number }}
        </div>

        <!-- Verification Code -->
        @if($certificate->verification_code)
            <div class="verification-code">
                Verification Code: {{ $certificate->verification_code }}
            </div>
        @endif

        <!-- QR Code for Verification -->
        @php
            $qrData = \App\Services\QRCodeService::forTrainingCertificate($certificate->id, $certificate->certificate_number);
        @endphp
        <x-pdf-qr-code :data="$qrData" :size="100" position="bottom-right" label="Scan to verify" />
    </div>
</body>
</html>
