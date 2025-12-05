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
use Carbon\Carbon;

class EnvironmentalDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_waste_records' => WasteManagementRecord::forCompany($companyId)->count(),
            'total_tracking_records' => WasteTrackingRecord::forCompany($companyId)->count(),
            'total_emission_records' => EmissionMonitoringRecord::forCompany($companyId)->count(),
            'total_spills' => SpillIncident::forCompany($companyId)->count(),
            'open_spills' => SpillIncident::forCompany($companyId)->whereIn('status', ['reported', 'contained', 'cleaned_up', 'investigating'])->count(),
            'compliant_emissions' => EmissionMonitoringRecord::forCompany($companyId)->compliant()->count(),
            'non_compliant_emissions' => EmissionMonitoringRecord::forCompany($companyId)->nonCompliant()->count(),
            'iso_compliant' => ISO14001ComplianceRecord::forCompany($companyId)->compliant()->count(),
            'iso_non_compliant' => ISO14001ComplianceRecord::forCompany($companyId)->nonCompliant()->count(),
        ];

        $recentSpills = SpillIncident::forCompany($companyId)
            ->with(['reportedBy', 'department'])
            ->latest('incident_date')
            ->limit(10)
            ->get();

        $recentEmissions = EmissionMonitoringRecord::forCompany($companyId)
            ->with(['monitoredBy'])
            ->latest('monitoring_date')
            ->limit(10)
            ->get();

        $wasteTypeDistribution = WasteManagementRecord::forCompany($companyId)
            ->selectRaw('waste_type, COUNT(*) as count')
            ->groupBy('waste_type')
            ->pluck('count', 'waste_type')
            ->toArray();

        $emissionComplianceDistribution = EmissionMonitoringRecord::forCompany($companyId)
            ->selectRaw('compliance_status, COUNT(*) as count')
            ->groupBy('compliance_status')
            ->pluck('count', 'compliance_status')
            ->toArray();

        return view('environmental.dashboard', compact('stats', 'recentSpills', 'recentEmissions', 'wasteTypeDistribution', 'emissionComplianceDistribution'));
    }
}
