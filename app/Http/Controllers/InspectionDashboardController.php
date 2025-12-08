<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\NonConformanceReport;
use App\Models\Audit;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class InspectionDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();

        $stats = [
            'total_inspections' => Inspection::whereIn('company_id', $companyGroupIds)->count(),
            'compliant_inspections' => Inspection::whereIn('company_id', $companyGroupIds)->compliant()->count(),
            'non_compliant_inspections' => Inspection::whereIn('company_id', $companyGroupIds)->nonCompliant()->count(),
            'pending_follow_up' => Inspection::whereIn('company_id', $companyGroupIds)->requiresFollowUp()->count(),
            'active_schedules' => InspectionSchedule::whereIn('company_id', $companyGroupIds)->active()->count(),
            'due_schedules' => InspectionSchedule::whereIn('company_id', $companyGroupIds)->due()->count(),
            'open_ncrs' => NonConformanceReport::whereIn('company_id', $companyGroupIds)->open()->count(),
            'critical_ncrs' => NonConformanceReport::whereIn('company_id', $companyGroupIds)->critical()->count(),
            'total_audits' => Audit::whereIn('company_id', $companyGroupIds)->count(),
            'in_progress_audits' => Audit::whereIn('company_id', $companyGroupIds)->inProgress()->count(),
        ];

        $recentInspections = Inspection::whereIn('company_id', $companyGroupIds)
            ->with(['inspectedBy', 'department', 'checklist'])
            ->latest()
            ->limit(10)
            ->get();

        $recentNCRs = NonConformanceReport::whereIn('company_id', $companyGroupIds)
            ->with(['identifiedBy', 'department'])
            ->latest()
            ->limit(10)
            ->get();

        $statusDistribution = Inspection::whereIn('company_id', $companyGroupIds)
            ->selectRaw('overall_status, COUNT(*) as count')
            ->groupBy('overall_status')
            ->pluck('count', 'overall_status')
            ->toArray();

        return view('inspections.dashboard', compact('stats', 'recentInspections', 'recentNCRs', 'statusDistribution'));
    }
}
