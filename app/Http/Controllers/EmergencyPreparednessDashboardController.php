<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FireDrill;
use App\Models\EmergencyContact;
use App\Models\EmergencyEquipment;
use App\Models\EvacuationPlan;
use App\Models\EmergencyResponseTeam;
use Carbon\Carbon;

class EmergencyPreparednessDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_fire_drills' => FireDrill::forCompany($companyId)->count(),
            'drills_this_year' => FireDrill::forCompany($companyId)
                ->whereYear('drill_date', Carbon::now()->year)
                ->count(),
            'pending_follow_up' => FireDrill::forCompany($companyId)->requiresFollowUp()->count(),
            'active_contacts' => EmergencyContact::forCompany($companyId)->active()->count(),
            'total_equipment' => EmergencyEquipment::forCompany($companyId)->count(),
            'equipment_due_inspection' => EmergencyEquipment::forCompany($companyId)->dueForInspection()->count(),
            'expired_equipment' => EmergencyEquipment::forCompany($companyId)->expired()->count(),
            'active_plans' => EvacuationPlan::forCompany($companyId)->active()->count(),
            'plans_due_review' => EvacuationPlan::forCompany($companyId)->dueForReview()->count(),
            'active_teams' => EmergencyResponseTeam::forCompany($companyId)->active()->count(),
        ];

        $recentFireDrills = FireDrill::forCompany($companyId)
            ->with(['conductedBy'])
            ->latest()
            ->limit(10)
            ->get();

        $recentEquipmentInspections = EmergencyEquipment::forCompany($companyId)
            ->whereNotNull('last_inspection_date')
            ->with(['inspectedBy'])
            ->latest('last_inspection_date')
            ->limit(10)
            ->get();

        $drillResults = FireDrill::forCompany($companyId)
            ->selectRaw('overall_result, COUNT(*) as count')
            ->whereNotNull('overall_result')
            ->groupBy('overall_result')
            ->pluck('count', 'overall_result')
            ->toArray();

        return view('emergency.dashboard', compact('stats', 'recentFireDrills', 'recentEquipmentInspections', 'drillResults'));
    }
}
