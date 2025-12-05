<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WasteSustainabilityRecord;
use App\Models\CarbonFootprintRecord;
use Carbon\Carbon;

class WasteSustainabilityDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;
        $currentMonth = Carbon::now()->startOfMonth();
        $currentYear = Carbon::now()->startOfYear();

        $stats = [
            'total_records' => WasteSustainabilityRecord::forCompany($companyId)->count(),
            'recycling_records' => WasteSustainabilityRecord::forCompany($companyId)->where('record_type', 'recycling')->count(),
            'waste_segregation' => WasteSustainabilityRecord::forCompany($companyId)->where('record_type', 'waste_segregation')->count(),
            'total_carbon_records' => CarbonFootprintRecord::forCompany($companyId)->count(),
            'monthly_carbon' => CarbonFootprintRecord::forCompany($companyId)
                ->where('record_date', '>=', $currentMonth)
                ->sum('carbon_equivalent'),
            'yearly_carbon' => CarbonFootprintRecord::forCompany($companyId)
                ->where('record_date', '>=', $currentYear)
                ->sum('carbon_equivalent'),
            'energy_saved' => WasteSustainabilityRecord::forCompany($companyId)
                ->where('record_date', '>=', $currentYear)
                ->sum('energy_saved'),
        ];

        $recentRecords = WasteSustainabilityRecord::forCompany($companyId)
            ->with(['recordedBy', 'department'])
            ->latest('record_date')
            ->limit(10)
            ->get();

        $recentCarbonRecords = CarbonFootprintRecord::forCompany($companyId)
            ->with(['recordedBy', 'department'])
            ->latest('record_date')
            ->limit(10)
            ->get();

        $recordTypeDistribution = WasteSustainabilityRecord::forCompany($companyId)
            ->selectRaw('record_type, COUNT(*) as count')
            ->groupBy('record_type')
            ->pluck('count', 'record_type')
            ->toArray();

        $sourceTypeDistribution = CarbonFootprintRecord::forCompany($companyId)
            ->selectRaw('source_type, SUM(carbon_equivalent) as total')
            ->groupBy('source_type')
            ->pluck('total', 'source_type')
            ->toArray();

        return view('waste-sustainability.dashboard', compact('stats', 'recentRecords', 'recentCarbonRecords', 'recordTypeDistribution', 'sourceTypeDistribution'));
    }
}
