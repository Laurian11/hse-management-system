<?php

namespace App\Http\Controllers;

use App\Models\Hazard;
use App\Models\RiskAssessment;
use App\Models\JSA;
use App\Models\ControlMeasure;
use App\Models\RiskReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\UsesCompanyGroup;

class RiskAssessmentDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function index()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        // Overall Statistics
        $stats = [
            'total_hazards' => Hazard::whereIn('company_id', $companyGroupIds)->count(),
            'identified_hazards' => Hazard::whereIn('company_id', $companyGroupIds)->identified()->count(),
            'assessed_hazards' => Hazard::whereIn('company_id', $companyGroupIds)->assessed()->count(),
            'total_risk_assessments' => RiskAssessment::whereIn('company_id', $companyGroupIds)->count(),
            'high_risk_assessments' => RiskAssessment::whereIn('company_id', $companyGroupIds)->highRisk()->count(),
            'due_for_review' => RiskAssessment::whereIn('company_id', $companyGroupIds)->dueForReview()->count(),
            'total_jsas' => JSA::whereIn('company_id', $companyGroupIds)->count(),
            'approved_jsas' => JSA::whereIn('company_id', $companyGroupIds)->approved()->count(),
            'total_control_measures' => ControlMeasure::whereIn('company_id', $companyGroupIds)->count(),
            'implemented_controls' => ControlMeasure::whereIn('company_id', $companyGroupIds)->byStatus('implemented')->count(),
            'verified_controls' => ControlMeasure::whereIn('company_id', $companyGroupIds)->byStatus('verified')->count(),
            'overdue_controls' => ControlMeasure::whereIn('company_id', $companyGroupIds)->overdue()->count(),
            'total_reviews' => RiskReview::whereIn('company_id', $companyGroupIds)->count(),
            'overdue_reviews' => RiskReview::whereIn('company_id', $companyGroupIds)->overdue()->count(),
        ];
        
        // Risk Level Distribution
        $riskLevelDistribution = RiskAssessment::whereIn('company_id', $companyGroupIds)
            ->select('risk_level', DB::raw('count(*) as total'))
            ->groupBy('risk_level')
            ->get()
            ->pluck('total', 'risk_level');
        
        // Hazard Category Distribution
        $hazardCategoryDistribution = Hazard::whereIn('company_id', $companyGroupIds)
            ->select('hazard_category', DB::raw('count(*) as total'))
            ->groupBy('hazard_category')
            ->get()
            ->pluck('total', 'hazard_category');
        
        // Control Type Distribution
        $controlTypeDistribution = ControlMeasure::whereIn('company_id', $companyGroupIds)
            ->select('control_type', DB::raw('count(*) as total'))
            ->groupBy('control_type')
            ->get()
            ->pluck('total', 'control_type');
        
        // Control Status Distribution
        $controlStatusDistribution = ControlMeasure::whereIn('company_id', $companyGroupIds)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');
        
        // Monthly Risk Assessments Trend (Last 6 Months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthAssessments = RiskAssessment::whereIn('company_id', $companyGroupIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->get();
            
            $monthlyTrends[] = [
                'month' => $month->format('Y-m'),
                'total' => $monthAssessments->count(),
            ];
        }
        
        // Top 5 High-Risk Assessments
        $topHighRisks = RiskAssessment::whereIn('company_id', $companyGroupIds)
            ->highRisk()
            ->with(['hazard', 'department'])
            ->orderBy('risk_score', 'desc')
            ->limit(5)
            ->get();
        
        // Overdue Reviews
        $overdueReviews = RiskReview::whereIn('company_id', $companyGroupIds)
            ->overdue()
            ->with(['riskAssessment'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();
        
        // Recent Activity
        $recentHazards = Hazard::whereIn('company_id', $companyGroupIds)
            ->with(['creator', 'department'])
            ->latest()
            ->limit(5)
            ->get();
        
        $recentRiskAssessments = RiskAssessment::whereIn('company_id', $companyGroupIds)
            ->with(['creator', 'hazard'])
            ->latest()
            ->limit(5)
            ->get();
        
        return view('risk-assessment.dashboard', compact(
            'stats',
            'riskLevelDistribution',
            'hazardCategoryDistribution',
            'controlTypeDistribution',
            'controlStatusDistribution',
            'monthlyTrends',
            'topHighRisks',
            'overdueReviews',
            'recentHazards',
            'recentRiskAssessments'
        ));
    }
}
