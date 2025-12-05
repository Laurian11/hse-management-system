<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HousekeepingInspection;
use App\Models\FiveSAudit;
use Carbon\Carbon;

class HousekeepingDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_inspections' => HousekeepingInspection::forCompany($companyId)->count(),
            'completed_inspections' => HousekeepingInspection::forCompany($companyId)->where('status', 'completed')->count(),
            'requiring_follow_up' => HousekeepingInspection::forCompany($companyId)->requiringFollowUp()->count(),
            'total_5s_audits' => FiveSAudit::forCompany($companyId)->count(),
            'completed_5s_audits' => FiveSAudit::forCompany($companyId)->where('status', 'completed')->count(),
            'excellent_ratings' => FiveSAudit::forCompany($companyId)->where('overall_rating', 'excellent')->count(),
            'needs_improvement' => FiveSAudit::forCompany($companyId)->where('overall_rating', 'needs_improvement')->count(),
        ];

        $recentInspections = HousekeepingInspection::forCompany($companyId)
            ->with(['inspectedBy', 'department', 'followUpAssignee'])
            ->latest('inspection_date')
            ->limit(10)
            ->get();

        $recentAudits = FiveSAudit::forCompany($companyId)
            ->with(['auditedBy', 'department', 'teamLeader'])
            ->latest('audit_date')
            ->limit(10)
            ->get();

        $inspectionStatusDistribution = HousekeepingInspection::forCompany($companyId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $ratingDistribution = FiveSAudit::forCompany($companyId)
            ->selectRaw('overall_rating, COUNT(*) as count')
            ->groupBy('overall_rating')
            ->pluck('count', 'overall_rating')
            ->toArray();

        return view('housekeeping.dashboard', compact('stats', 'recentInspections', 'recentAudits', 'inspectionStatusDistribution', 'ratingDistribution'));
    }
}
