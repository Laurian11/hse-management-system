<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PPEItem;
use App\Models\EquipmentCertification;
use App\Models\StockConsumptionReport;

class QRCodeController extends Controller
{
    /**
     * Display QR code scan result page
     */
    public function scan(Request $request, string $type, int $id)
    {
        $companyId = Auth::user()->company_id;
        
        switch ($type) {
            case 'stock':
                $item = StockConsumptionReport::forCompany($companyId)->findOrFail($id);
                return view('qr.stock', compact('item'));
                
            case 'audit':
                $item = EquipmentCertification::forCompany($companyId)->findOrFail($id);
                return view('qr.audit', compact('item'));
                
            case 'inspection':
                $item = PPEItem::forCompany($companyId)->findOrFail($id);
                return view('qr.inspection', compact('item'));
                
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
                $qrData = \App\Services\QRCodeService::forInspection($item->id, $item->reference_number ?? $item->item_code);
                break;
                
            case 'equipment':
                $item = EquipmentCertification::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forAudit($item->id, $item->reference_number ?? $item->certificate_number);
                break;
                
            case 'stock':
                $item = StockConsumptionReport::forCompany($companyId)->findOrFail($id);
                $qrData = \App\Services\QRCodeService::forStockCheck($item->id, $item->reference_number);
                break;
                
            default:
                abort(404, 'Invalid type');
        }

        $qrUrl = \App\Services\QRCodeService::generateUrl($qrData, 300);

        return view('qr.printable', compact('item', 'qrData', 'qrUrl', 'type'));
    }
}

