@props(['data', 'size' => 120, 'position' => 'bottom-right', 'label' => 'Scan to view online'])

@php
    $qrUrl = \App\Services\QRCodeService::generateUrl($data, $size);
    $positionClass = match($position) {
        'top-left' => 'position: absolute; top: 10px; left: 10px;',
        'top-right' => 'position: absolute; top: 10px; right: 10px;',
        'bottom-left' => 'position: absolute; bottom: 10px; left: 10px;',
        'bottom-right' => 'position: absolute; bottom: 10px; right: 10px;',
        'header' => 'text-align: center; margin: 10px 0;',
        'footer' => 'text-align: center; margin: 10px 0;',
        default => 'text-align: center; margin: 10px 0;'
    };
@endphp

<div style="{{ $positionClass }} z-index: 1000;">
    <div style="text-align: center; background: white; padding: 8px; border: 1px solid #ddd; border-radius: 4px; display: inline-block;">
        <img src="{{ $qrUrl }}" alt="QR Code" style="width: {{ $size }}px; height: {{ $size }}px; display: block; margin: 0 auto;">
        @if($label)
            <p style="font-size: 8px; color: #666; margin: 4px 0 0 0; text-align: center;">{{ $label }}</p>
        @endif
    </div>
</div>




