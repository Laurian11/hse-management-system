<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PPEItem;
use App\Models\PPEIssuance;
use App\Models\EquipmentCertification;
use App\Models\StockConsumptionReport;
use App\Models\ProcurementRequest;
use App\Models\InspectionSchedule;
use App\Models\InspectionChecklist;
use App\Models\Inspection;
use App\Models\Audit;
use App\Models\AuditFinding;
use App\Models\Incident;
use App\Models\RiskAssessment;
use App\Models\JSA;
use App\Models\ToolboxTalk;
use App\Models\TrainingCertificate;
use App\Models\TrainingSession;
use App\Models\WorkPermit;
use App\Models\NonConformanceReport;
use App\Models\PermitLicense;

class QRCodeController extends Controller
{
    /**
     * Display QR code scan result page and update system
     */
    public function scan(Request $request, string $type, int $id)
    {
        $companyId = Auth::user()->company_id;
        $action = $request->get('action', 'view'); // view, check, inspect, audit
        
        switch ($type) {
            case 'stock':
                $item = StockConsumptionReport::forCompany($companyId)->findOrFail($id);
                if ($action === 'check') {
                    // Update last checked timestamp
                    $item->update(['updated_at' => now()]);
                    return response()->json([
                        'success' => true,
                        'message' => 'Stock checked successfully',
                        'item' => $item
                    ]);
                }
                return view('qr.stock', compact('item'));
                
            case 'audit':
                // Check if it's an audit record or equipment certification
                if (is_numeric($id) && Audit::where('id', $id)->exists()) {
                    $item = Audit::forCompany($companyId)->findOrFail($id);
                    $item->load(['leadAuditor', 'department', 'findings']);
                    if ($action === 'view') {
                        return redirect()->route('inspections.audits.show', $item)
                            ->with('info', 'QR code scanned - Viewing audit');
                    }
                    return view('qr.audit-record', compact('item'));
                } else {
                    // Legacy equipment certification
                    $item = EquipmentCertification::forCompany($companyId)->findOrFail($id);
                    if ($action === 'audit') {
                        // Log audit scan
                        \App\Models\ActivityLog::log('scan', 'equipment', 'EquipmentCertification', $item->id, "QR code scanned for audit: {$item->reference_number}");
                        return response()->json([
                            'success' => true,
                            'message' => 'Audit scan recorded',
                            'item' => $item
                        ]);
                    }
                    return view('qr.audit', compact('item'));
                }
                
            case 'audit-finding':
                $item = AuditFinding::forCompany($companyId)->findOrFail($id);
                $item->load(['audit', 'correctiveActionAssignedTo', 'verifiedBy']);
                if ($action === 'view') {
                    return redirect()->route('inspections.audits.show', $item->audit)
                        ->with('info', 'QR code scanned - Viewing audit finding');
                }
                return view('qr.audit-finding', compact('item'));
                
            case 'inspection':
                $item = Inspection::forCompany($companyId)->findOrFail($id);
                $item->load(['inspectedBy', 'department', 'checklist', 'schedule']);
                if ($action === 'view') {
                    return redirect()->route('inspections.show', $item)
                        ->with('info', 'QR code scanned - Viewing inspection');
                }
                return view('qr.inspection', compact('item'));
                
            case 'inspection-schedule':
                $item = InspectionSchedule::forCompany($companyId)->findOrFail($id);
                $item->load(['checklist', 'assignedTo', 'department']);
                if ($action === 'inspect') {
                    // Redirect to inspection creation with schedule pre-filled
                    return redirect()->route('inspections.create', ['schedule_id' => $item->id])
                        ->with('success', 'QR code scanned - Ready to create inspection from schedule');
                }
                return view('qr.inspection-schedule', compact('item'));
                
            case 'inspection-checklist':
                $item = InspectionChecklist::forCompany($companyId)->findOrFail($id);
                if ($action === 'use') {
                    // Redirect to inspection creation with checklist pre-filled
                    return redirect()->route('inspections.create', ['checklist_id' => $item->id])
                        ->with('success', 'QR code scanned - Checklist loaded');
                }
                return view('qr.inspection-checklist', compact('item'));
                
            case 'ppe':
                $item = PPEItem::forCompany($companyId)->findOrFail($id);
                if ($action === 'inspect') {
                    // Redirect to inspection creation
                    return redirect()->route('ppe.inspections.create', ['ppe_item_id' => $item->id])
                        ->with('info', 'QR code scanned - Create inspection');
                }
                if ($action === 'check') {
                    // Update last checked timestamp
                    \App\Models\ActivityLog::log('scan', 'ppe', 'PPEItem', $item->id, "QR code scanned for stock check: {$item->reference_number}");
                    return response()->json([
                        'success' => true,
                        'message' => 'Stock checked successfully',
                        'item' => $item
                    ]);
                }
                return view('qr.ppe-item', compact('item'));
                
            case 'issuance':
                $item = PPEIssuance::forCompany($companyId)->findOrFail($id);
                $item->load(['ppeItem', 'issuedTo', 'department']);
                
                if ($action === 'inspect') {
                    // Redirect to inspection creation for this issuance
                    return redirect()->route('ppe.inspections.create', ['ppe_issuance_id' => $item->id])
                        ->with('info', 'QR code scanned - Create inspection');
                }
                if ($action === 'check') {
                    // Log scan and update last checked
                    \App\Models\ActivityLog::log('scan', 'ppe', 'PPEIssuance', $item->id, "QR code scanned for issuance check: {$item->reference_number}");
                    return response()->json([
                        'success' => true,
                        'message' => 'Issuance checked successfully',
                        'item' => $item
                    ]);
                }
                return view('qr.issuance', compact('item'));
                
            case 'procurement':
                $item = ProcurementRequest::forCompany($companyId)->findOrFail($id);
                return view('qr.procurement', compact('item'));
                
            case 'incident':
                $item = Incident::forCompany($companyId)->findOrFail($id);
                $item->load(['reporter', 'assignedTo', 'department', 'company']);
                if ($action === 'view') {
                    return redirect()->route('incidents.show', $item)
                        ->with('info', 'QR code scanned - Viewing incident');
                }
                return view('qr.incident', compact('item'));
                
            case 'risk-assessment':
                $item = RiskAssessment::forCompany($companyId)->findOrFail($id);
                $item->load(['creator', 'assignedTo', 'department', 'hazard']);
                if ($action === 'view') {
                    return redirect()->route('risk-assessment.risk-assessments.show', $item)
                        ->with('info', 'QR code scanned - Viewing risk assessment');
                }
                return view('qr.risk-assessment', compact('item'));
                
            case 'jsa':
                $item = JSA::forCompany($companyId)->findOrFail($id);
                $item->load(['creator', 'assignedTo', 'department']);
                if ($action === 'view') {
                    return redirect()->route('risk-assessment.jsas.show', $item)
                        ->with('info', 'QR code scanned - Viewing JSA');
                }
                return view('qr.jsa', compact('item'));
                
            case 'toolbox-talk':
                $item = ToolboxTalk::forCompany($companyId)->findOrFail($id);
                $item->load(['presenter', 'topic', 'department']);
                if ($action === 'view') {
                    return redirect()->route('toolbox-talks.show', $item)
                        ->with('info', 'QR code scanned - Viewing toolbox talk');
                }
                return view('qr.toolbox-talk', compact('item'));
                
            case 'training-certificate':
                $item = TrainingCertificate::forCompany($companyId)->findOrFail($id);
                $item->load(['user', 'trainingSession', 'issuer', 'company']);
                if ($action === 'verify') {
                    return redirect()->route('training.certificates.show', $item)
                        ->with('info', 'QR code scanned - Verifying certificate');
                }
                return view('qr.training-certificate', compact('item'));
                
            case 'training-session':
                $item = TrainingSession::forCompany($companyId)->findOrFail($id);
                $item->load(['trainingPlan', 'instructor', 'department']);
                if ($action === 'view') {
                    return redirect()->route('training.sessions.show', $item)
                        ->with('info', 'QR code scanned - Viewing training session');
                }
                return view('qr.training-session', compact('item'));
                
            case 'work-permit':
                $item = WorkPermit::forCompany($companyId)->findOrFail($id);
                $item->load(['applicant', 'approvedBy', 'workPermitType']);
                if ($action === 'view') {
                    return redirect()->route('work-permits.show', $item)
                        ->with('info', 'QR code scanned - Viewing work permit');
                }
                return view('qr.work-permit', compact('item'));
                
            case 'ncr':
                $item = NonConformanceReport::forCompany($companyId)->findOrFail($id);
                $item->load(['reportedBy', 'assignedTo', 'department']);
                if ($action === 'view') {
                    return redirect()->route('inspections.ncrs.show', $item)
                        ->with('info', 'QR code scanned - Viewing NCR');
                }
                return view('qr.ncr', compact('item'));
                
            case 'permit-license':
                $item = PermitLicense::forCompany($companyId)->findOrFail($id);
                if ($action === 'verify') {
                    return redirect()->route('permit-licenses.show', $item)
                        ->with('info', 'QR code scanned - Verifying permit/license');
                }
                return view('qr.permit-license', compact('item'));
                
            default:
                abort(404, 'Invalid QR code type');
        }
    }

    /**
     * Mobile scanner interface
     */
    public function scanner()
    {
        return view('qr.scanner');
    }

    /**
     * Process scanned QR code (API endpoint for mobile apps)
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $qrData = $request->qr_data;
        $companyId = Auth::user()->company_id;

        // Parse QR code URL
        $parsed = parse_url($qrData);
        if (!$parsed || !isset($parsed['path'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code format'
            ], 400);
        }

        // Extract type and ID from path: /qr/{type}/{id}
        $pathParts = explode('/', trim($parsed['path'], '/'));
        if (count($pathParts) < 3 || $pathParts[0] !== 'qr') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code format'
            ], 400);
        }

        $type = $pathParts[1];
        $id = (int) $pathParts[2];
        $action = $request->get('action', 'view');

        // Redirect to scan handler
        return redirect()->route('qr.scan', ['type' => $type, 'id' => $id, 'action' => $action]);
    }

    /**
     * Generate printable QR code page
     */
    public function printable(Request $request, string $type, int $id)
    {
        $companyId = Auth::user()->company_id;
        $item = null;
        $qrData = '';
        $qrUrl = '';

        switch ($type) {
            case 'ppe':
                $item = PPEItem::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forPPEItem($item->id, $item->reference_number ?? $item->name, 'check');
                break;
                
            case 'issuance':
                $item = PPEIssuance::forCompany($companyId)->findOrFail($id);
                $item->load(['ppeItem', 'issuedTo']);
                $qrData = \App\Services\QRCodeService::forIssuance($item->id, $item->reference_number, 'check');
                break;
                
            case 'equipment':
                $item = EquipmentCertification::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forAudit($item->id, $item->reference_number ?? $item->certificate_number);
                break;
                
            case 'stock':
                $item = StockConsumptionReport::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forStockCheck($item->id, $item->reference_number);
                break;
                
            case 'procurement':
                $item = ProcurementRequest::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forProcurement($item->id, $item->reference_number);
                break;
                
            case 'inspection-schedule':
                $item = InspectionSchedule::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forInspectionSchedule($item->id, $item->reference_number);
                break;
                
            case 'inspection-checklist':
                $item = InspectionChecklist::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forInspectionChecklist($item->id, $item->name);
                break;
                
            case 'inspection':
                $item = Inspection::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forInspection($item->id, $item->reference_number);
                break;
                
            case 'audit-record':
                $item = Audit::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forAuditRecord($item->id, $item->reference_number);
                break;
                
            case 'audit':
                // Check if it's an Audit model or EquipmentCertification
                try {
                    $item = Audit::forCompany($companyId)->findOrFail($id);
                    $qrData = \App\Services\QRCodeService::forAuditRecord($item->id, $item->reference_number);
                } catch (\Exception $e) {
                    // Fallback to EquipmentCertification
                    $item = EquipmentCertification::forCompany($companyId)->findOrFail($id);
                    $qrData = \App\Services\QRCodeService::forAudit($item->id, $item->reference_number ?? $item->certificate_number);
                }
                break;
                
            default:
                abort(404, 'Invalid type');
        }

        $qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 300);

        return view('qr.printable', compact('item', 'qrData', 'qrUrl', 'type'));
    }
}

