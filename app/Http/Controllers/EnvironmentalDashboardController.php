<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WasteManagementRecord;
use App\Models\WasteTrackingRecord;
use App\Models\EmissionMonitoringRecord;
use App\Models\SpillIncident;
use App\Models\ResourceUsageRecord;
use App\Models\ISO14001ComplianceRecord;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class EnvironmentalDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();

        $stats = [
            'total_waste_records' => WasteManagementRecord::whereIn('company_id', $companyGroupIds)->count(),
            'total_tracking_records' => WasteTrackingRecord::whereIn('company_id', $companyGroupIds)->count(),
            'total_emission_records' => EmissionMonitoringRecord::whereIn('company_id', $companyGroupIds)->count(),
            'total_spills' => SpillIncident::whereIn('company_id', $companyGroupIds)->count(),
            'open_spills' => SpillIncident::whereIn('company_id', $companyGroupIds)->whereIn('status', ['reported', 'contained', 'cleaned_up', 'investigating'])->count(),
            'compliant_emissions' => EmissionMonitoringRecord::whereIn('company_id', $companyGroupIds)->compliant()->count(),
            'non_compliant_emissions' => EmissionMonitoringRecord::whereIn('company_id', $companyGroupIds)->nonCompliant()->count(),
            'iso_compliant' => ISO14001ComplianceRecord::whereIn('company_id', $companyGroupIds)->compliant()->count(),
            'iso_non_compliant' => ISO14001ComplianceRecord::whereIn('company_id', $companyGroupIds)->nonCompliant()->count(),
        ];

        $recentSpills = SpillIncident::whereIn('company_id', $companyGroupIds)
            ->with(['reportedBy', 'department'])
            ->latest('incident_date')
            ->limit(10)
            ->get();

        $recentEmissions = EmissionMonitoringRecord::whereIn('company_id', $companyGroupIds)
            ->with(['monitoredBy'])
            ->latest('monitoring_date')
            ->limit(10)
            ->get();

        $wasteTypeDistribution = WasteManagementRecord::whereIn('company_id', $companyGroupIds)
            ->selectRaw('waste_type, COUNT(*) as count')
            ->groupBy('waste_type')
            ->pluck('count', 'waste_type')
            ->toArray();

        $emissionComplianceDistribution = EmissionMonitoringRecord::whereIn('company_id', $companyGroupIds)
            ->selectRaw('compliance_status, COUNT(*) as count')
            ->groupBy('compliance_status')
            ->pluck('count', 'compliance_status')
            ->toArray();

        return view('environmental.dashboard', compact('stats', 'recentSpills', 'recentEmissions', 'wasteTypeDistribution', 'emissionComplianceDistribution'));
    }
}
