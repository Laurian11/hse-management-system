<?php

namespace App\Services;

class QRCodeService
{
    /**
     * Generate QR code URL using external service
     * 
     * @param string $data The data to encode in QR code
     * @param int $size QR code size in pixels (default: 200)
     * @return string QR code image URL
     */
    public static function generateUrl(string $data, int $size = 200): string
    {
        // Using QR Server API (free, no API key required)
        $encodedData = urlencode($data);
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedData}";
    }

    /**
     * Generate QR code data string for an item
     * 
     * @param string $type Item type (e.g., 'ppe', 'equipment', 'stock')
     * @param int $itemId Item ID
     * @param string $referenceNumber Reference number
     * @return string QR code data
     */
    public static function generateItemData(string $type, int $itemId, string $referenceNumber): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/qr/{$type}/{$itemId}?ref={$referenceNumber}";
    }

    /**
     * Generate QR code for stock checking
     */
    public static function forStockCheck(int $itemId, string $referenceNumber): string
    {
        return self::generateItemData('stock', $itemId, $referenceNumber);
    }

    /**
     * Generate QR code for auditing
     */
    public static function forAudit(int $itemId, string $referenceNumber): string
    {
        return self::generateItemData('audit', $itemId, $referenceNumber);
    }

    /**
     * Generate QR code for inspection
     */
    public static function forInspection(int $itemId, string $referenceNumber): string
    {
        return self::generateItemData('inspection', $itemId, $referenceNumber);
    }

    /**
     * Generate printable QR code HTML for embedding in PDFs
     */
    public static function generatePrintableHtml(string $data, int $size = 150): string
    {
        $qrUrl = self::generateUrl($data, $size);
        return '<img src="' . $qrUrl . '" alt="QR Code" style="width: ' . $size . 'px; height: ' . $size . 'px;" />';
    }
}

