<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkPermit;
use App\Models\WorkPermitType;
use App\Models\WorkPermitApproval;
use App\Models\GCALog;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class WorkPermitDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        // Statistics
        $stats = [
            'total_permits' => WorkPermit::whereIn('company_id', $companyGroupIds)->count(),
            'pending_approval' => WorkPermit::whereIn('company_id', $companyGroupIds)->pending()->count(),
            'active_permits' => WorkPermit::whereIn('company_id', $companyGroupIds)->active()->count(),
            'expired_permits' => WorkPermit::whereIn('company_id', $companyGroupIds)->expired()->count(),
            'closed_permits' => WorkPermit::whereIn('company_id', $companyGroupIds)->where('status', 'closed')->count(),
            'total_types' => WorkPermitType::whereIn('company_id', $companyGroupIds)->active()->count(),
            'gca_logs' => GCALog::whereIn('company_id', $companyGroupIds)->count(),
            'non_compliant_gca' => GCALog::whereIn('company_id', $companyGroupIds)->nonCompliant()->count(),
        ];
        
        // Recent permits
        $recentPermits = WorkPermit::whereIn('company_id', $companyGroupIds)
            ->with(['workPermitType', 'requestedBy', 'department'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Pending approvals
        $pendingApprovals = WorkPermitApproval::whereIn('company_id', $companyGroupIds)
            ->pending()
            ->with(['workPermit', 'approver'])
            ->get();
        
        // Expiring soon (within 24 hours)
        $expiringSoon = WorkPermit::whereIn('company_id', $companyGroupIds)
            ->where('status', 'active')
            ->where('expiry_date', '>', now())
            ->where('expiry_date', '<=', now()->addHours(24))
            ->with(['workPermitType', 'requestedBy'])
            ->get();
        
        // Monthly permits chart (last 6 months)
        $monthlyPermits = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthLabels[] = $month->format('M');
            $monthPermits = WorkPermit::whereIn('company_id', $companyGroupIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $monthlyPermits[] = $monthPermits;
        }
        
        // Permit type distribution
        $typeDistribution = WorkPermit::whereIn('work_permits.company_id', $companyGroupIds)
            ->join('work_permit_types', 'work_permits.work_permit_type_id', '=', 'work_permit_types.id')
            ->whereNull('work_permits.deleted_at')
            ->selectRaw('work_permit_types.name, COUNT(*) as count')
            ->groupBy('work_permit_types.name')
            ->pluck('count', 'name')
            ->toArray();
        
        // Status distribution
        $statusDistribution = WorkPermit::whereIn('company_id', $companyGroupIds)
            ->selectRaw('work_permits.status, COUNT(*) as count')
            ->groupBy('work_permits.status')
            ->pluck('count', 'status')
            ->toArray();
        
        return view('work-permits.dashboard', compact(
            'stats',
            'recentPermits',
            'pendingApprovals',
            'expiringSoon',
            'monthlyPermits',
            'monthLabels',
            'typeDistribution',
            'statusDistribution'
        ));
    }
}
