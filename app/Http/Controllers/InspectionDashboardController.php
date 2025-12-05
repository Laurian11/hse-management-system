<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\NonConformanceReport;
use App\Models\Audit;
use Carbon\Carbon;

class InspectionDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_inspections' => Inspection::forCompany($companyId)->count(),
            'compliant_inspections' => Inspection::forCompany($companyId)->compliant()->count(),
            'non_compliant_inspections' => Inspection::forCompany($companyId)->nonCompliant()->count(),
            'pending_follow_up' => Inspection::forCompany($companyId)->requiresFollowUp()->count(),
            'active_schedules' => InspectionSchedule::forCompany($companyId)->active()->count(),
            'due_schedules' => InspectionSchedule::forCompany($companyId)->due()->count(),
            'open_ncrs' => NonConformanceReport::forCompany($companyId)->open()->count(),
            'critical_ncrs' => NonConformanceReport::forCompany($companyId)->critical()->count(),
            'total_audits' => Audit::forCompany($companyId)->count(),
            'in_progress_audits' => Audit::forCompany($companyId)->inProgress()->count(),
        ];

        $recentInspections = Inspection::forCompany($companyId)
            ->with(['inspectedBy', 'department', 'checklist'])
            ->latest()
            ->limit(10)
            ->get();

        $recentNCRs = NonConformanceReport::forCompany($companyId)
            ->with(['identifiedBy', 'department'])
            ->latest()
            ->limit(10)
            ->get();

        $statusDistribution = Inspection::forCompany($companyId)
            ->selectRaw('overall_status, COUNT(*) as count')
            ->groupBy('overall_status')
            ->pluck('count', 'overall_status')
            ->toArray();

        return view('inspections.dashboard', compact('stats', 'recentInspections', 'recentNCRs', 'statusDistribution'));
    }
}
