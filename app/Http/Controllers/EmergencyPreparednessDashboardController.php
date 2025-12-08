<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FireDrill;
use App\Models\EmergencyContact;
use App\Models\EmergencyEquipment;
use App\Models\EvacuationPlan;
use App\Models\EmergencyResponseTeam;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class EmergencyPreparednessDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();

        $stats = [
            'total_fire_drills' => FireDrill::whereIn('company_id', $companyGroupIds)->count(),
            'drills_this_year' => FireDrill::whereIn('company_id', $companyGroupIds)
                ->whereYear('drill_date', Carbon::now()->year)
                ->count(),
            'pending_follow_up' => FireDrill::whereIn('company_id', $companyGroupIds)->requiresFollowUp()->count(),
            'active_contacts' => EmergencyContact::whereIn('company_id', $companyGroupIds)->active()->count(),
            'total_equipment' => EmergencyEquipment::whereIn('company_id', $companyGroupIds)->count(),
            'equipment_due_inspection' => EmergencyEquipment::whereIn('company_id', $companyGroupIds)->dueForInspection()->count(),
            'expired_equipment' => EmergencyEquipment::whereIn('company_id', $companyGroupIds)->expired()->count(),
            'active_plans' => EvacuationPlan::whereIn('company_id', $companyGroupIds)->active()->count(),
            'plans_due_review' => EvacuationPlan::whereIn('company_id', $companyGroupIds)->dueForReview()->count(),
            'active_teams' => EmergencyResponseTeam::whereIn('company_id', $companyGroupIds)->active()->count(),
        ];

        $recentFireDrills = FireDrill::whereIn('company_id', $companyGroupIds)
            ->with(['conductedBy'])
            ->latest()
            ->limit(10)
            ->get();

        $recentEquipmentInspections = EmergencyEquipment::whereIn('company_id', $companyGroupIds)
            ->whereNotNull('last_inspection_date')
            ->with(['inspectedBy'])
            ->latest('last_inspection_date')
            ->limit(10)
            ->get();

        $drillResults = FireDrill::whereIn('company_id', $companyGroupIds)
            ->selectRaw('overall_result, COUNT(*) as count')
            ->whereNotNull('overall_result')
            ->groupBy('overall_result')
            ->pluck('count', 'overall_result')
            ->toArray();

        return view('emergency.dashboard', compact('stats', 'recentFireDrills', 'recentEquipmentInspections', 'drillResults'));
    }
}
