# QR Code Implementation - Modules and Documents

## Overview
This document outlines all modules that can benefit from QR code scanning and the status of QR code integration in downloaded documents.

## Modules with QR Code Support

### ✅ Fully Implemented

1. **Inspections**
   - Inspection Schedules
   - Inspection Checklists
   - Inspection Records
   - Location-based Inspections

2. **Audits**
   - Audit Records
   - Audit Findings
   - Equipment Certifications

3. **PPE Management**
   - PPE Items
   - PPE Issuances

4. **Stock & Procurement**
   - Stock Consumption Reports
   - Procurement Requests

### 🆕 Newly Added Modules

5. **Incidents**
   - Incident Records
   - Incident Reports (PDF)
   - Incident Exports (PDF)

6. **Risk Assessment**
   - Risk Assessment Records
   - Risk Assessment Reports (PDF)
   - Risk Assessment Exports (PDF)

7. **Job Safety Analysis (JSA)**
   - JSA Records

8. **Toolbox Talks**
   - Toolbox Talk Records
   - Attendance Reports (PDF)

9. **Training**
   - Training Certificates (PDF)
   - Training Sessions

10. **Work Permits**
    - Work Permit Records

11. **Non-Conformance Reports (NCR)**
    - NCR Records

12. **Permits & Licenses**
    - Permit/License Records

## QR Code Usage by Module

### For Quick Access
- **Scan to View**: Opens the record detail page
- **Scan to Verify**: Verifies authenticity (certificates, permits)
- **Scan to Check**: Quick status check

### For Workflow
- **Scan to Inspect**: Opens inspection form with pre-filled data
- **Scan to Audit**: Opens audit form
- **Scan to Use**: Loads checklist/template

## PDF Documents with QR Codes

### ✅ Implemented

1. **Training Certificates** (`training/certificates/pdf.blade.php`)
   - QR code for certificate verification
   - Position: Bottom-right
   - Action: Verify certificate

2. **Incident Reports** (`incidents/exports/single-pdf.blade.php`)
   - QR code to view incident online
   - Position: Bottom-right
   - Action: View incident

3. **Incident Exports** (`incidents/exports/pdf.blade.php`)
   - QR code to access report online
   - Position: Bottom-right
   - Action: View report

4. **Risk Assessment Reports** (`risk-assessment/risk-assessments/exports/single-pdf.blade.php`)
   - QR code to view assessment online
   - Position: Bottom-right
   - Action: View assessment

5. **Risk Assessment Exports** (`risk-assessment/risk-assessments/exports/pdf.blade.php`)
   - QR code to access report online
   - Position: Bottom-right
   - Action: View report

6. **Toolbox Talk Attendance** (`toolbox-talks/exports/attendance-pdf.blade.php`)
   - QR code to view toolbox talk
   - Position: Bottom-right
   - Action: View talk

### 📋 Remaining PDFs to Update

The following PDF exports should have QR codes added:

1. **Daily Attendance Reports** (`daily-attendance/exports/pdf.blade.php`)
2. **Safety Communication Reports** (4 files)
   - `safety-communications/exports/single-pdf.blade.php`
   - `safety-communications/exports/pdf.blade.php`
   - `safety-communications/reports/exports/period-pdf.blade.php`
   - `safety-communications/reports/exports/employee-pdf.blade.php`
   - `safety-communications/reports/exports/department-pdf.blade.php`
   - `safety-communications/reports/exports/companies-pdf.blade.php`
3. **Risk Assessment Reports** (4 files)
   - `risk-assessment/reports/exports/period-pdf.blade.php`
   - `risk-assessment/reports/exports/employee-pdf.blade.php`
   - `risk-assessment/reports/exports/department-pdf.blade.php`
   - `risk-assessment/reports/exports/companies-pdf.blade.php`
4. **Incident Reports** (4 files)
   - `incidents/reports/exports/period-pdf.blade.php`
   - `incidents/reports/exports/employee-pdf.blade.php`
   - `incidents/reports/exports/department-pdf.blade.php`
   - `incidents/reports/exports/companies-pdf.blade.php`
5. **Toolbox Talk Reports** (4 files)
   - `toolbox-talks/reports/exports/period-report-pdf.blade.php`
   - `toolbox-talks/reports/exports/employee-attendance-pdf.blade.php`
   - `toolbox-talks/reports/exports/department-attendance-pdf.blade.php`
   - `toolbox-talks/reports/exports/companies-pdf.blade.php`
6. **Procurement Requests** (`procurement/requests/pdf.blade.php`)

## QR Code Component

A reusable component has been created for PDF documents:

**File**: `resources/views/components/pdf-qr-code.blade.php`

**Usage**:
```blade
@php
    $qrData = \App\Services\QRCodeService::forIncident($incident->id, $incident->reference_number);
@endphp
<x-pdf-qr-code :data="$qrData" :size="100" position="bottom-right" label="Scan to view online" />
```

**Parameters**:
- `data`: QR code data (URL or encoded string)
- `size`: QR code size in pixels (default: 120)
- `position`: Position on page (top-left, top-right, bottom-left, bottom-right, header, footer)
- `label`: Text label below QR code

## QR Code Service Methods

All QR code generation methods are in `app/Services/QRCodeService.php`:

### For Records
- `forIncident($id, $referenceNumber)`
- `forRiskAssessment($id, $referenceNumber)`
- `forJSA($id, $referenceNumber)`
- `forToolboxTalk($id, $referenceNumber)`
- `forTrainingCertificate($id, $certificateNumber)`
- `forTrainingSession($id, $referenceNumber)`
- `forWorkPermit($id, $permitNumber)`
- `forNCR($id, $referenceNumber)`
- `forPermitLicense($id, $permitNumber)`
- `forDocument($id, $documentNumber)`

### For Reports
- `forReport($reportType, $params = [])` - Generic report QR code

### For PDFs
- `forPDFDocument($documentUrl, $documentType, $referenceNumber)` - For PDF documents

## Implementation Status

- ✅ QR Code Service methods created
- ✅ QR Code Controller updated
- ✅ QR Code component for PDFs created
- ✅ Training Certificate PDF updated
- ✅ Incident PDFs updated
- ✅ Risk Assessment PDFs updated
- ✅ Toolbox Talk Attendance PDF updated
- ⏳ Remaining PDF exports (15 files) - To be updated

## Next Steps

1. Add QR codes to remaining PDF exports
2. Add QR code displays to show pages for new modules
3. Test QR code scanning for all new module types
4. Update documentation with usage examples


