# QR Code Scanning for Inspections and Audits - Implementation Guide

## Overview

QR code scanning has been integrated into the HSE Management System to simplify inspection and audit workflows. This allows inspectors to quickly access inspection forms, checklists, and audit records by scanning QR codes with their mobile devices.

## Features

### 1. **Mobile QR Code Scanner**
- Camera-based QR code scanning
- Manual QR code input option
- Real-time QR code processing
- Mobile-optimized interface

### 2. **QR Code Types Supported**

#### Inspections
- **Inspection Schedules**: Scan to create inspection from schedule
- **Inspection Checklists**: Scan to load checklist for new inspection
- **Inspection Records**: Scan to view existing inspection
- **Location-based**: Scan location QR codes to start inspection

#### Audits
- **Audit Records**: Scan to view audit details
- **Audit Findings**: Scan to view specific finding
- **Equipment Certifications**: Scan for equipment audit

#### PPE
- **PPE Items**: Scan to check stock or create inspection
- **PPE Issuances**: Scan to view issuance or create inspection

## How to Use

### For Inspectors

#### 1. Access QR Scanner
- Navigate to: `/qr/scanner`
- Or add a "Scan QR Code" button in your navigation menu

#### 2. Scan QR Code
- **Option A**: Use camera to scan QR code
  - Grant camera permission when prompted
  - Position QR code within the frame
  - System automatically processes the scan

- **Option B**: Manual input
  - Paste QR code URL or data into the input field
  - Click the arrow button to process

#### 3. Automatic Actions
When you scan a QR code, the system automatically:
- Identifies the type (inspection, audit, PPE, etc.)
- Loads the appropriate form or record
- Pre-fills relevant information
- Redirects to the correct page

### For Administrators

#### 1. Generate QR Codes for Inspection Schedules

```php
// In your view or controller
$qrData = \App\Services\QRCodeService::forInspectionSchedule(
    $schedule->id, 
    $schedule->reference_number
);
$qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 300);
```

**Display in Blade:**
```blade
<img src="{{ $qrUrl }}" alt="QR Code" class="w-48 h-48">
<a href="{{ route('qr.printable', ['type' => 'inspection-schedule', 'id' => $schedule->id]) }}" 
   target="_blank">
    Print QR Code
</a>
```

#### 2. Generate QR Codes for Inspection Checklists

```php
$qrData = \App\Services\QRCodeService::forInspectionChecklist(
    $checklist->id, 
    $checklist->name
);
$qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 300);
```

#### 3. Generate QR Codes for Locations

Place QR codes at physical locations (e.g., equipment, areas):

```php
$qrData = \App\Services\QRCodeService::forLocationInspection(
    'Warehouse A - Section 3', 
    $departmentId
);
$qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 300);
```

#### 4. Generate QR Codes for Audits

```php
$qrData = \App\Services\QRCodeService::forAuditRecord(
    $audit->id, 
    $audit->reference_number
);
$qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 300);
```

## Workflow Examples

### Example 1: Scheduled Inspection

1. **Setup** (Admin):
   - Create inspection schedule
   - Print QR code for the schedule
   - Place QR code at inspection location

2. **Execution** (Inspector):
   - Scan QR code with mobile device
   - System loads inspection form with:
     - Schedule details pre-filled
     - Checklist items loaded
     - Location auto-detected
   - Complete inspection checklist
   - Submit inspection

### Example 2: Location-Based Inspection

1. **Setup** (Admin):
   - Generate QR code for location: "Main Warehouse - Fire Exit"
   - Print and place QR code at location

2. **Execution** (Inspector):
   - Scan location QR code
   - System opens inspection form with:
     - Location pre-filled
     - Department auto-detected (if linked)
   - Select checklist
   - Complete inspection

### Example 3: Quick Audit Review

1. **Setup** (Admin):
   - Generate QR code for audit record
   - Include in audit report PDF

2. **Execution** (Auditor/Manager):
   - Scan QR code from report
   - System opens audit details page
   - View findings, corrective actions, etc.

## Adding QR Codes to Views

### Inspection Schedule View

Add to `resources/views/inspections/schedules/show.blade.php`:

```blade
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">QR Code</h3>
    @php
        $qrData = \App\Services\QRCodeService::forInspectionSchedule(
            $schedule->id, 
            $schedule->reference_number
        );
        $qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 200);
    @endphp
    <div class="text-center">
        <img src="{{ $qrUrl }}" alt="QR Code" class="mx-auto mb-4 border-2 border-gray-200 p-2">
        <p class="text-xs text-gray-500 mb-2">Scan to create inspection</p>
        <a href="{{ route('qr.printable', ['type' => 'inspection-schedule', 'id' => $schedule->id]) }}" 
           target="_blank" 
           class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
            <i class="fas fa-print mr-2"></i>Print QR Code
        </a>
    </div>
</div>
```

### Inspection Checklist View

Add to `resources/views/inspections/checklists/show.blade.php`:

```blade
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">QR Code</h3>
    @php
        $qrData = \App\Services\QRCodeService::forInspectionChecklist(
            $checklist->id, 
            $checklist->name
        );
        $qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 200);
    @endphp
    <div class="text-center">
        <img src="{{ $qrUrl }}" alt="QR Code" class="mx-auto mb-4 border-2 border-gray-200 p-2">
        <p class="text-xs text-gray-500 mb-2">Scan to use this checklist</p>
        <a href="{{ route('qr.printable', ['type' => 'inspection-checklist', 'id' => $checklist->id]) }}" 
           target="_blank" 
           class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm">
            <i class="fas fa-print mr-2"></i>Print QR Code
        </a>
    </div>
</div>
```

### Audit View

Add to `resources/views/inspections/audits/show.blade.php`:

```blade
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">QR Code</h3>
    @php
        $qrData = \App\Services\QRCodeService::forAuditRecord(
            $audit->id, 
            $audit->reference_number
        );
        $qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 200);
    @endphp
    <div class="text-center">
        <img src="{{ $qrUrl }}" alt="QR Code" class="mx-auto mb-4 border-2 border-gray-200 p-2">
        <p class="text-xs text-gray-500 mb-2">Scan to view audit</p>
        <a href="{{ route('qr.printable', ['type' => 'audit', 'id' => $audit->id]) }}" 
           target="_blank" 
           class="inline-block bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 text-sm">
            <i class="fas fa-print mr-2"></i>Print QR Code
        </a>
    </div>
</div>
```

## Mobile Optimization

The QR scanner is optimized for mobile devices:
- Uses back camera on mobile devices
- Touch-friendly interface
- Works offline (after initial page load)
- Responsive design

## Security

- All QR code routes require authentication
- Company-level data isolation enforced
- Activity logging for all scans
- Permission checks for actions

## Troubleshooting

### Camera Not Working
- Ensure HTTPS is enabled (required for camera access)
- Check browser permissions
- Use manual input as fallback

### QR Code Not Recognized
- Ensure QR code is not damaged
- Check lighting conditions
- Try manual input option
- Verify QR code format matches system expectations

### Redirect Not Working
- Check user permissions
- Verify company access
- Check route exists
- Review browser console for errors

## Best Practices

1. **Print Quality**: Use high-quality printers for QR codes
2. **Size**: Minimum 2cm x 2cm for reliable scanning
3. **Placement**: Place QR codes at eye level, well-lit areas
4. **Protection**: Laminate QR codes for durability
5. **Testing**: Test QR codes before deployment
6. **Backup**: Keep digital copies of QR codes

## API Endpoints

### Scan QR Code
```
GET /qr/{type}/{id}?action={action}
```

### Process Scan (API)
```
POST /qr/process
Body: { "qr_data": "https://..." }
```

### Generate Printable QR Code
```
GET /qr/{type}/{id}/printable
```

### Scanner Interface
```
GET /qr/scanner
```

## Integration with Mobile Apps

For native mobile apps, use the API endpoint:

```javascript
// Example: React Native
const processQRCode = async (qrData) => {
  const response = await fetch('https://your-domain.com/qr/process', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({ qr_data: qrData })
  });
  
  const result = await response.json();
  // Handle result
};
```

## Future Enhancements

- Offline QR code scanning
- Batch QR code generation
- QR code analytics
- Custom QR code designs
- Integration with barcode scanners

---

**Last Updated:** December 2025  
**Version:** 1.0.0

