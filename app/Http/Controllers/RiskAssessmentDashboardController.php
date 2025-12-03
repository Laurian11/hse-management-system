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

class RiskAssessmentDashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        
        // Overall Statistics
        $stats = [
            'total_hazards' => Hazard::forCompany($companyId)->count(),
            'identified_hazards' => Hazard::forCompany($companyId)->identified()->count(),
            'assessed_hazards' => Hazard::forCompany($companyId)->assessed()->count(),
            'total_risk_assessments' => RiskAssessment::forCompany($companyId)->count(),
            'high_risk_assessments' => RiskAssessment::forCompany($companyId)->highRisk()->count(),
            'due_for_review' => RiskAssessment::forCompany($companyId)->dueForReview()->count(),
            'total_jsas' => JSA::forCompany($companyId)->count(),
            'approved_jsas' => JSA::forCompany($companyId)->approved()->count(),
            'total_control_measures' => ControlMeasure::forCompany($companyId)->count(),
            'implemented_controls' => ControlMeasure::forCompany($companyId)->byStatus('implemented')->count(),
            'verified_controls' => ControlMeasure::forCompany($companyId)->byStatus('verified')->count(),
            'overdue_controls' => ControlMeasure::forCompany($companyId)->overdue()->count(),
            'total_reviews' => RiskReview::forCompany($companyId)->count(),
            'overdue_reviews' => RiskReview::forCompany($companyId)->overdue()->count(),
        ];
        
        // Risk Level Distribution
        $riskLevelDistribution = RiskAssessment::forCompany($companyId)
            ->select('risk_level', DB::raw('count(*) as total'))
            ->groupBy('risk_level')
            ->get()
            ->pluck('total', 'risk_level');
        
        // Hazard Category Distribution
        $hazardCategoryDistribution = Hazard::forCompany($companyId)
            ->select('hazard_category', DB::raw('count(*) as total'))
            ->groupBy('hazard_category')
            ->get()
            ->pluck('total', 'hazard_category');
        
        // Control Type Distribution
        $controlTypeDistribution = ControlMeasure::forCompany($companyId)
            ->select('control_type', DB::raw('count(*) as total'))
            ->groupBy('control_type')
            ->get()
            ->pluck('total', 'control_type');
        
        // Control Status Distribution
        $controlStatusDistribution = ControlMeasure::forCompany($companyId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');
        
        // Monthly Risk Assessments Trend (Last 6 Months)
        $monthlyTrends = RiskAssessment::forCompany($companyId)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Top 5 High-Risk Assessments
        $topHighRisks = RiskAssessment::forCompany($companyId)
            ->highRisk()
            ->with(['hazard', 'department'])
            ->orderBy('risk_score', 'desc')
            ->limit(5)
            ->get();
        
        // Overdue Reviews
        $overdueReviews = RiskReview::forCompany($companyId)
            ->overdue()
            ->with(['riskAssessment'])
            ->orderBy('due_date')
            ->limit(10)
            ->get();
        
        // Recent Activity
        $recentHazards = Hazard::forCompany($companyId)
            ->with(['creator', 'department'])
            ->latest()
            ->limit(5)
            ->get();
        
        $recentRiskAssessments = RiskAssessment::forCompany($companyId)
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
