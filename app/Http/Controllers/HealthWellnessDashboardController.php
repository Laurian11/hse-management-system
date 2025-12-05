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
use Carbon\Carbon;

class HealthWellnessDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_surveillance_records' => HealthSurveillanceRecord::forCompany($companyId)->count(),
            'due_surveillance' => HealthSurveillanceRecord::forCompany($companyId)->due()->count(),
            'total_first_aid_entries' => FirstAidLogbookEntry::forCompany($companyId)->count(),
            'total_ergonomic_assessments' => ErgonomicAssessment::forCompany($companyId)->count(),
            'high_risk_ergonomic' => ErgonomicAssessment::forCompany($companyId)->whereIn('risk_level', ['high', 'very_high'])->count(),
            'total_hygiene_inspections' => WorkplaceHygieneInspection::forCompany($companyId)->count(),
            'unsatisfactory_hygiene' => WorkplaceHygieneInspection::forCompany($companyId)->where('overall_status', 'unsatisfactory')->count(),
            'total_campaigns' => HealthCampaign::forCompany($companyId)->count(),
            'active_campaigns' => HealthCampaign::forCompany($companyId)->where('status', 'ongoing')->count(),
            'work_related_sick_leave' => SickLeaveRecord::forCompany($companyId)->workRelated()->count(),
        ];

        $recentFirstAid = FirstAidLogbookEntry::forCompany($companyId)
            ->with(['injuredPerson', 'firstAider'])
            ->latest('incident_date')
            ->limit(10)
            ->get();

        $recentSurveillance = HealthSurveillanceRecord::forCompany($companyId)
            ->with(['user', 'conductedBy'])
            ->latest('examination_date')
            ->limit(10)
            ->get();

        $surveillanceTypeDistribution = HealthSurveillanceRecord::forCompany($companyId)
            ->selectRaw('surveillance_type, COUNT(*) as count')
            ->groupBy('surveillance_type')
            ->pluck('count', 'surveillance_type')
            ->toArray();

        return view('health.dashboard', compact('stats', 'recentFirstAid', 'recentSurveillance', 'surveillanceTypeDistribution'));
    }
}
