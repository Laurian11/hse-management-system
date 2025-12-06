<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>QR Code Label - {{ $item->reference_number ?? $item->name ?? 'Item' }}</title>
    <style>
        @page {
            margin: 5mm;
            size: A4;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 10px;
        }
        .label-sheet {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5mm;
            width: 100%;
        }
        .label {
            width: 63mm;
            height: 38mm;
            border: 1px solid #000;
            padding: 3mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            page-break-inside: avoid;
            background: white;
        }
        .qr-code {
            width: 25mm;
            height: 25mm;
            margin-bottom: 2mm;
        }
        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .item-info {
            text-align: center;
            font-size: 8pt;
            line-height: 1.2;
        }
        .item-info .ref {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 1mm;
        }
        .item-info .name {
            font-size: 7pt;
            color: #333;
            word-wrap: break-word;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .label {
                border: 1px solid #000;
            }
            @page {
                margin: 5mm;
            }
        }
        .no-print {
            display: none;
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; padding: 20px; background: #f0f0f0; border-radius: 8px;">
        <h2 style="margin-bottom: 10px;">QR Code Labels - Print Instructions</h2>
        <p style="margin-bottom: 10px;"><strong>Item:</strong> {{ $item->name ?? $item->reference_number ?? 'Item' }}</p>
        <p style="margin-bottom: 10px;"><strong>Type:</strong> {{ ucfirst($type) }}</p>
        <p style="margin-bottom: 20px;">These labels are formatted for 63mm x 38mm sticker labels (3 per A4 page).</p>
        <button onclick="window.print()" style="background: #0066CC; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
            <i class="fas fa-print"></i> Print Labels
        </button>
    </div>

    <div class="label-sheet">
        @for($i = 0; $i < 30; $i++)
        <div class="label">
            <div class="qr-code">
                <img src="{{ $qrUrl }}" alt="QR Code" />
            </div>
            <div class="item-info">
                <div class="ref">
                    @if($type === 'ppe')
                        {{ $item->reference_number ?? $item->name ?? 'N/A' }}
                    @elseif($type === 'issuance')
                        {{ $item->reference_number ?? 'N/A' }}
                    @elseif($type === 'equipment')
                        {{ $item->reference_number ?? $item->certificate_number ?? 'N/A' }}
                    @elseif($type === 'stock')
                        {{ $item->reference_number ?? 'N/A' }}
                    @else
                        {{ $item->reference_number ?? $item->item_code ?? 'N/A' }}
                    @endif
                </div>
                @if($type === 'ppe' && isset($item->name) && $item->name !== ($item->reference_number ?? ''))
                    <div class="name">{{ mb_substr($item->name, 0, 20) }}</div>
                @elseif($type === 'issuance')
                    @if(isset($item->ppeItem))
                        <div class="name">{{ mb_substr($item->ppeItem->name, 0, 18) }}</div>
                    @endif
                    @if(isset($item->issuedTo))
                        <div class="name" style="font-size: 6pt; margin-top: 1mm;">{{ mb_substr($item->issuedTo->name, 0, 15) }}</div>
                    @endif
                @elseif($type === 'equipment' && isset($item->equipment_name))
                    <div class="name">{{ mb_substr($item->equipment_name, 0, 20) }}</div>
                @elseif($type === 'stock' && isset($item->item_name))
                    <div class="name">{{ mb_substr($item->item_name, 0, 20) }}</div>
                @endif
            </div>
        </div>
        @endfor
    </div>
</body>
</html>


