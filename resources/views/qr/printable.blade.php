<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code - {{ $item->reference_number ?? $item->item_code ?? 'Item' }}</title>
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .qr-container {
            text-align: center;
            page-break-inside: avoid;
        }
        .qr-code {
            margin: 20px auto;
            border: 2px solid #000;
            padding: 10px;
            display: inline-block;
        }
        .qr-code img {
            display: block;
        }
        .item-info {
            margin-top: 10px;
            font-size: 14px;
        }
        .item-info strong {
            display: block;
            margin-bottom: 5px;
        }
        @media print {
            body {
                padding: 0;
            }
            .qr-container {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <div class="qr-code">
            <img src="{{ $qrUrl }}" alt="QR Code" />
        </div>
        <div class="item-info">
            <strong>{{ $item->reference_number ?? $item->item_code ?? 'Item' }}</strong>
            @if(isset($item->item_name))
                <div>{{ $item->item_name }}</div>
            @elseif(isset($item->equipment_name))
                <div>{{ $item->equipment_name }}</div>
            @endif
            @if(isset($item->item_code))
                <div>Code: {{ $item->item_code }}</div>
            @endif
        </div>
    </div>
</body>
</html>

