<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WasteSustainabilityRecord;
use App\Models\CarbonFootprintRecord;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class WasteSustainabilityDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        $currentMonth = Carbon::now()->startOfMonth();
        $currentYear = Carbon::now()->startOfYear();

        $stats = [
            'total_records' => WasteSustainabilityRecord::whereIn('company_id', $companyGroupIds)->count(),
            'recycling_records' => WasteSustainabilityRecord::whereIn('company_id', $companyGroupIds)->where('record_type', 'recycling')->count(),
            'waste_segregation' => WasteSustainabilityRecord::whereIn('company_id', $companyGroupIds)->where('record_type', 'waste_segregation')->count(),
            'total_carbon_records' => CarbonFootprintRecord::whereIn('company_id', $companyGroupIds)->count(),
            'monthly_carbon' => CarbonFootprintRecord::whereIn('company_id', $companyGroupIds)
                ->where('record_date', '>=', $currentMonth)
                ->sum('carbon_equivalent'),
            'yearly_carbon' => CarbonFootprintRecord::whereIn('company_id', $companyGroupIds)
                ->where('record_date', '>=', $currentYear)
                ->sum('carbon_equivalent'),
            'energy_saved' => WasteSustainabilityRecord::whereIn('company_id', $companyGroupIds)
                ->where('record_date', '>=', $currentYear)
                ->sum('energy_saved'),
        ];

        $recentRecords = WasteSustainabilityRecord::whereIn('company_id', $companyGroupIds)
            ->with(['recordedBy', 'department'])
            ->latest('record_date')
            ->limit(10)
            ->get();

        $recentCarbonRecords = CarbonFootprintRecord::whereIn('company_id', $companyGroupIds)
            ->with(['recordedBy', 'department'])
            ->latest('record_date')
            ->limit(10)
            ->get();

        $recordTypeDistribution = WasteSustainabilityRecord::whereIn('company_id', $companyGroupIds)
            ->selectRaw('record_type, COUNT(*) as count')
            ->groupBy('record_type')
            ->pluck('count', 'record_type')
            ->toArray();

        $sourceTypeDistribution = CarbonFootprintRecord::whereIn('company_id', $companyGroupIds)
            ->selectRaw('source_type, SUM(carbon_equivalent) as total')
            ->groupBy('source_type')
            ->pluck('total', 'source_type')
            ->toArray();

        return view('waste-sustainability.dashboard', compact('stats', 'recentRecords', 'recentCarbonRecords', 'recordTypeDistribution', 'sourceTypeDistribution'));
    }
}
