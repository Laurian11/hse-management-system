<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HousekeepingInspection;
use App\Models\FiveSAudit;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class HousekeepingDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();

        $stats = [
            'total_inspections' => HousekeepingInspection::whereIn('company_id', $companyGroupIds)->count(),
            'completed_inspections' => HousekeepingInspection::whereIn('company_id', $companyGroupIds)->where('status', 'completed')->count(),
            'requiring_follow_up' => HousekeepingInspection::whereIn('company_id', $companyGroupIds)->requiringFollowUp()->count(),
            'total_5s_audits' => FiveSAudit::whereIn('company_id', $companyGroupIds)->count(),
            'completed_5s_audits' => FiveSAudit::whereIn('company_id', $companyGroupIds)->where('status', 'completed')->count(),
            'excellent_ratings' => FiveSAudit::whereIn('company_id', $companyGroupIds)->where('overall_rating', 'excellent')->count(),
            'needs_improvement' => FiveSAudit::whereIn('company_id', $companyGroupIds)->where('overall_rating', 'needs_improvement')->count(),
        ];

        $recentInspections = HousekeepingInspection::whereIn('company_id', $companyGroupIds)
            ->with(['inspectedBy', 'department', 'followUpAssignee'])
            ->latest('inspection_date')
            ->limit(10)
            ->get();

        $recentAudits = FiveSAudit::whereIn('company_id', $companyGroupIds)
            ->with(['auditedBy', 'department', 'teamLeader'])
            ->latest('audit_date')
            ->limit(10)
            ->get();

        $inspectionStatusDistribution = HousekeepingInspection::whereIn('company_id', $companyGroupIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $ratingDistribution = FiveSAudit::whereIn('company_id', $companyGroupIds)
            ->selectRaw('overall_rating, COUNT(*) as count')
            ->groupBy('overall_rating')
            ->pluck('count', 'overall_rating')
            ->toArray();

        return view('housekeeping.dashboard', compact('stats', 'recentInspections', 'recentAudits', 'inspectionStatusDistribution', 'ratingDistribution'));
    }
}
