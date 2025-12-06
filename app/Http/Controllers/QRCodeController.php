<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PPEItem;
use App\Models\PPEIssuance;
use App\Models\EquipmentCertification;
use App\Models\StockConsumptionReport;
use App\Models\ProcurementRequest;

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
                
            case 'inspection':
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
                
            default:
                abort(404, 'Invalid QR code type');
        }
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
                
            default:
                abort(404, 'Invalid type');
        }

        $qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 300);

        return view('qr.printable', compact('item', 'qrData', 'qrUrl', 'type'));
    }
}

