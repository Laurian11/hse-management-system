<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkPermit;
use App\Models\WorkPermitType;
use App\Models\WorkPermitApproval;
use App\Models\GCALog;
use Carbon\Carbon;

class WorkPermitDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;
        
        // Statistics
        $stats = [
            'total_permits' => WorkPermit::forCompany($companyId)->count(),
            'pending_approval' => WorkPermit::forCompany($companyId)->pending()->count(),
            'active_permits' => WorkPermit::forCompany($companyId)->active()->count(),
            'expired_permits' => WorkPermit::forCompany($companyId)->expired()->count(),
            'closed_permits' => WorkPermit::forCompany($companyId)->where('status', 'closed')->count(),
            'total_types' => WorkPermitType::forCompany($companyId)->active()->count(),
            'gca_logs' => GCALog::forCompany($companyId)->count(),
            'non_compliant_gca' => GCALog::forCompany($companyId)->nonCompliant()->count(),
        ];
        
        // Recent permits
        $recentPermits = WorkPermit::forCompany($companyId)
            ->with(['workPermitType', 'requestedBy', 'department'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Pending approvals
        $pendingApprovals = WorkPermitApproval::forCompany($companyId)
            ->pending()
            ->with(['workPermit', 'approver'])
            ->get();
        
        // Expiring soon (within 24 hours)
        $expiringSoon = WorkPermit::forCompany($companyId)
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
            $monthPermits = WorkPermit::forCompany($companyId)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $monthlyPermits[] = $monthPermits;
        }
        
        // Permit type distribution
        $typeDistribution = WorkPermit::where('work_permits.company_id', $companyId)
            ->join('work_permit_types', 'work_permits.work_permit_type_id', '=', 'work_permit_types.id')
            ->whereNull('work_permits.deleted_at')
            ->selectRaw('work_permit_types.name, COUNT(*) as count')
            ->groupBy('work_permit_types.name')
            ->pluck('count', 'name')
            ->toArray();
        
        // Status distribution
        $statusDistribution = WorkPermit::forCompany($companyId)
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
