<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProcurementRequest;
use App\Models\Supplier;
use App\Models\EquipmentCertification;
use App\Models\StockConsumptionReport;
use App\Models\SafetyMaterialGapAnalysis;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class ProcurementDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();

        $stats = [
            'total_requests' => ProcurementRequest::whereIn('company_id', $companyGroupIds)->count(),
            'pending_approval' => ProcurementRequest::whereIn('company_id', $companyGroupIds)->pendingApproval()->count(),
            'approved_requests' => ProcurementRequest::whereIn('company_id', $companyGroupIds)->where('status', 'approved')->count(),
            'total_suppliers' => Supplier::whereIn('company_id', $companyGroupIds)->count(),
            'active_suppliers' => Supplier::whereIn('company_id', $companyGroupIds)->active()->count(),
            'total_certifications' => EquipmentCertification::whereIn('company_id', $companyGroupIds)->count(),
            'expired_certifications' => EquipmentCertification::whereIn('company_id', $companyGroupIds)->expired()->count(),
            'due_certifications' => EquipmentCertification::whereIn('company_id', $companyGroupIds)->due()->count(),
            'total_gap_analyses' => SafetyMaterialGapAnalysis::whereIn('company_id', $companyGroupIds)->count(),
            'unresolved_gaps' => SafetyMaterialGapAnalysis::whereIn('company_id', $companyGroupIds)->unresolved()->count(),
        ];

        $recentRequests = ProcurementRequest::whereIn('company_id', $companyGroupIds)
            ->with(['requestedBy', 'department', 'supplier'])
            ->latest()
            ->limit(10)
            ->get();

        $recentGaps = SafetyMaterialGapAnalysis::whereIn('company_id', $companyGroupIds)
            ->with(['analyzedBy', 'department'])
            ->latest('analysis_date')
            ->limit(10)
            ->get();

        $requestStatusDistribution = ProcurementRequest::whereIn('company_id', $companyGroupIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('procurement.dashboard', compact('stats', 'recentRequests', 'recentGaps', 'requestStatusDistribution'));
    }
}
