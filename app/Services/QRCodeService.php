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
     * @param string $action Optional action (check, inspect, audit)
     * @return string QR code data
     */
    public static function generateItemData(string $type, int $itemId, string $referenceNumber, string $action = ''): string
    {
        $baseUrl = config('app.url');
        $actionParam = $action ? "&action={$action}" : '';
        return "{$baseUrl}/qr/{$type}/{$itemId}?ref={$referenceNumber}{$actionParam}";
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
     * Generate QR code for PPE issuance
     */
    public static function forIssuance(int $issuanceId, string $referenceNumber, string $action = 'check'): string
    {
        return self::generateItemData('issuance', $issuanceId, $referenceNumber, $action);
    }

    /**
     * Generate QR code for PPE item (stock)
     */
    public static function forPPEItem(int $itemId, string $referenceNumber, string $action = 'check'): string
    {
        return self::generateItemData('ppe', $itemId, $referenceNumber, $action);
    }

    /**
     * Generate QR code for procurement item
     */
    public static function forProcurement(int $itemId, string $referenceNumber): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/qr/procurement/{$itemId}?ref={$referenceNumber}";
    }

    /**
     * Generate QR code for inspection schedule
     */
    public static function forInspectionSchedule(int $scheduleId, string $referenceNumber): string
    {
        return self::generateItemData('inspection-schedule', $scheduleId, $referenceNumber, 'inspect');
    }

    /**
     * Generate QR code for inspection checklist
     */
    public static function forInspectionChecklist(int $checklistId, string $name): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/qr/inspection-checklist/{$checklistId}?name=" . urlencode($name);
    }

    /**
     * Generate QR code for location-based inspection
     */
    public static function forLocationInspection(string $location, int $departmentId = null): string
    {
        $baseUrl = config('app.url');
        $params = ['location' => urlencode($location)];
        if ($departmentId) {
            $params['department_id'] = $departmentId;
        }
        return "{$baseUrl}/inspections/create?" . http_build_query($params);
    }

    /**
     * Generate QR code for audit
     */
    public static function forAuditRecord(int $auditId, string $referenceNumber): string
    {
        return self::generateItemData('audit', $auditId, $referenceNumber, 'view');
    }

    /**
     * Generate QR code for audit finding
     */
    public static function forAuditFinding(int $findingId, string $referenceNumber): string
    {
        $baseUrl = config('app.url');
        return "{$baseUrl}/qr/audit-finding/{$findingId}?ref={$referenceNumber}";
    }

    /**
     * Generate QR code for quick inspection creation
     */
    public static function forQuickInspection(int $scheduleId = null, int $checklistId = null, string $location = null): string
    {
        $baseUrl = config('app.url');
        $params = [];
        if ($scheduleId) $params['schedule_id'] = $scheduleId;
        if ($checklistId) $params['checklist_id'] = $checklistId;
        if ($location) $params['location'] = urlencode($location);
        return "{$baseUrl}/inspections/create?" . http_build_query($params);
    }

    /**
     * Generate printable QR code HTML for embedding in PDFs
     */
    public static function generatePrintableHtml(string $data, int $size = 150): string
    {
        $qrUrl = self::generateUrl($data, $size);
        return '<img src="' . $qrUrl . '" alt="QR Code" style="width: ' . $size . 'px; height: ' . $size . 'px;" />';
    }

    /**
     * Generate QR code for incident
     */
    public static function forIncident(int $incidentId, string $referenceNumber): string
    {
        return self::generateItemData('incident', $incidentId, $referenceNumber, 'view');
    }

    /**
     * Generate QR code for risk assessment
     */
    public static function forRiskAssessment(int $assessmentId, string $referenceNumber): string
    {
        return self::generateItemData('risk-assessment', $assessmentId, $referenceNumber, 'view');
    }

    /**
     * Generate QR code for JSA (Job Safety Analysis)
     */
    public static function forJSA(int $jsaId, string $referenceNumber): string
    {
        return self::generateItemData('jsa', $jsaId, $referenceNumber, 'view');
    }

    /**
     * Generate QR code for toolbox talk
     */
    public static function forToolboxTalk(int $talkId, string $referenceNumber): string
    {
        return self::generateItemData('toolbox-talk', $talkId, $referenceNumber, 'view');
    }

    /**
     * Generate QR code for training certificate
     */
    public static function forTrainingCertificate(int $certificateId, string $certificateNumber): string
    {
        return self::generateItemData('training-certificate', $certificateId, $certificateNumber, 'verify');
    }

    /**
     * Generate QR code for training session
     */
    public static function forTrainingSession(int $sessionId, string $referenceNumber): string
    {
        return self::generateItemData('training-session', $sessionId, $referenceNumber, 'view');
    }

    /**
     * Generate QR code for work permit
     */
    public static function forWorkPermit(int $permitId, string $permitNumber): string
    {
        return self::generateItemData('work-permit', $permitId, $permitNumber, 'view');
    }

    /**
     * Generate QR code for non-conformance report (NCR)
     */
    public static function forNCR(int $ncrId, string $referenceNumber): string
    {
        return self::generateItemData('ncr', $ncrId, $referenceNumber, 'view');
    }

    /**
     * Generate QR code for permit/license
     */
    public static function forPermitLicense(int $permitId, string $permitNumber): string
    {
        return self::generateItemData('permit-license', $permitId, $permitNumber, 'verify');
    }

    /**
     * Generate QR code for document
     */
    public static function forDocument(int $documentId, string $documentNumber): string
    {
        return self::generateItemData('document', $documentId, $documentNumber, 'view');
    }

    /**
     * Generate QR code for report (generic)
     */
    public static function forReport(string $reportType, array $params = []): string
    {
        $baseUrl = config('app.url');
        $queryString = http_build_query(array_merge(['type' => $reportType], $params));
        return "{$baseUrl}/reports/view?{$queryString}";
    }

    /**
     * Generate QR code for PDF document (includes document URL)
     */
    public static function forPDFDocument(string $documentUrl, string $documentType = 'document', string $referenceNumber = ''): string
    {
        // For PDFs, we encode the document URL so scanning it opens the document
        $baseUrl = config('app.url');
        if (str_starts_with($documentUrl, 'http')) {
            return $documentUrl; // Full URL
        }
        return "{$baseUrl}{$documentUrl}";
    }
}

