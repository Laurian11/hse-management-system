<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\Supplier;
use App\Models\EquipmentCertification;
use App\Models\StockConsumptionReport;
use App\Models\SafetyMaterialGapAnalysis;
use Carbon\Carbon;

class ProcurementDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_requests' => ProcurementRequest::forCompany($companyId)->count(),
            'pending_approval' => ProcurementRequest::forCompany($companyId)->pendingApproval()->count(),
            'approved_requests' => ProcurementRequest::forCompany($companyId)->where('status', 'approved')->count(),
            'total_suppliers' => Supplier::forCompany($companyId)->count(),
            'active_suppliers' => Supplier::forCompany($companyId)->active()->count(),
            'total_certifications' => EquipmentCertification::forCompany($companyId)->count(),
            'expired_certifications' => EquipmentCertification::forCompany($companyId)->expired()->count(),
            'due_certifications' => EquipmentCertification::forCompany($companyId)->due()->count(),
            'total_gap_analyses' => SafetyMaterialGapAnalysis::forCompany($companyId)->count(),
            'unresolved_gaps' => SafetyMaterialGapAnalysis::forCompany($companyId)->unresolved()->count(),
        ];

        $recentRequests = ProcurementRequest::forCompany($companyId)
            ->with(['requestedBy', 'department', 'supplier'])
            ->latest()
            ->limit(10)
            ->get();

        $recentGaps = SafetyMaterialGapAnalysis::forCompany($companyId)
            ->with(['analyzedBy', 'department'])
            ->latest('analysis_date')
            ->limit(10)
            ->get();

        $requestStatusDistribution = ProcurementRequest::forCompany($companyId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('procurement.dashboard', compact('stats', 'recentRequests', 'recentGaps', 'requestStatusDistribution'));
    }
}
