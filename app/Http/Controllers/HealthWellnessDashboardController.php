<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HealthSurveillanceRecord;
use App\Models\FirstAidLogbookEntry;
use App\Models\ErgonomicAssessment;
use App\Models\WorkplaceHygieneInspection;
use App\Models\HealthCampaign;
use App\Models\SickLeaveRecord;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class HealthWellnessDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();

        $stats = [
            'total_surveillance_records' => HealthSurveillanceRecord::whereIn('company_id', $companyGroupIds)->count(),
            'due_surveillance' => HealthSurveillanceRecord::whereIn('company_id', $companyGroupIds)->due()->count(),
            'total_first_aid_entries' => FirstAidLogbookEntry::whereIn('company_id', $companyGroupIds)->count(),
            'total_ergonomic_assessments' => ErgonomicAssessment::whereIn('company_id', $companyGroupIds)->count(),
            'high_risk_ergonomic' => ErgonomicAssessment::whereIn('company_id', $companyGroupIds)->whereIn('risk_level', ['high', 'very_high'])->count(),
            'total_hygiene_inspections' => WorkplaceHygieneInspection::whereIn('company_id', $companyGroupIds)->count(),
            'unsatisfactory_hygiene' => WorkplaceHygieneInspection::whereIn('company_id', $companyGroupIds)->where('overall_status', 'unsatisfactory')->count(),
            'total_campaigns' => HealthCampaign::whereIn('company_id', $companyGroupIds)->count(),
            'active_campaigns' => HealthCampaign::whereIn('company_id', $companyGroupIds)->where('status', 'ongoing')->count(),
            'work_related_sick_leave' => SickLeaveRecord::whereIn('company_id', $companyGroupIds)->workRelated()->count(),
        ];

        $recentFirstAid = FirstAidLogbookEntry::whereIn('company_id', $companyGroupIds)
            ->with(['injuredPerson', 'firstAider'])
            ->latest('incident_date')
            ->limit(10)
            ->get();

        $recentSurveillance = HealthSurveillanceRecord::whereIn('company_id', $companyGroupIds)
            ->with(['user', 'conductedBy'])
            ->latest('examination_date')
            ->limit(10)
            ->get();

        $surveillanceTypeDistribution = HealthSurveillanceRecord::whereIn('company_id', $companyGroupIds)
            ->selectRaw('surveillance_type, COUNT(*) as count')
            ->groupBy('surveillance_type')
            ->pluck('count', 'surveillance_type')
            ->toArray();

        return view('health.dashboard', compact('stats', 'recentFirstAid', 'recentSurveillance', 'surveillanceTypeDistribution'));
    }
}
