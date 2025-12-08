<?php

namespace App\Http\Controllers;

use App\Models\TrainingNeedsAnalysis;
use App\Models\TrainingPlan;
use App\Models\TrainingSession;
use App\Models\TrainingRecord;
use App\Models\TrainingCertificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\UsesCompanyGroup;

class TrainingDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        // Statistics
        $stats = [
            'total_training_needs' => TrainingNeedsAnalysis::whereIn('company_id', $companyGroupIds)->count(),
            'validated_tnas' => TrainingNeedsAnalysis::whereIn('company_id', $companyGroupIds)->where('status', 'validated')->count(),
            'total_plans' => TrainingPlan::whereIn('company_id', $companyGroupIds)->count(),
            'approved_plans' => TrainingPlan::whereIn('company_id', $companyGroupIds)->where('status', 'approved')->count(),
            'total_sessions' => TrainingSession::whereIn('company_id', $companyGroupIds)->count(),
            'completed_sessions' => TrainingSession::whereIn('company_id', $companyGroupIds)->where('status', 'completed')->count(),
            'upcoming_sessions' => TrainingSession::whereIn('company_id', $companyGroupIds)->where('status', 'scheduled')->where('scheduled_start', '>', now())->count(),
            'total_certificates' => TrainingCertificate::whereIn('company_id', $companyGroupIds)->count(),
            'active_certificates' => TrainingCertificate::whereIn('company_id', $companyGroupIds)->where('status', 'active')->count(),
            'expiring_soon' => TrainingCertificate::whereIn('company_id', $companyGroupIds)
                ->where('status', 'active')
                ->whereBetween('expiry_date', [now(), now()->addDays(60)])
                ->count(),
            'total_trained_employees' => TrainingRecord::whereIn('company_id', $companyGroupIds)
                ->distinct('user_id')
                ->count('user_id'),
        ];

        // Recent Training Needs
        $recentTNAs = TrainingNeedsAnalysis::whereIn('company_id', $companyGroupIds)
            ->with(['creator', 'trainingPlans'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming Sessions
        $upcomingSessions = TrainingSession::whereIn('company_id', $companyGroupIds)
            ->where('status', 'scheduled')
            ->where('scheduled_start', '>', now())
            ->with(['trainingPlan.trainingNeed', 'instructor'])
            ->orderBy('scheduled_start', 'asc')
            ->limit(5)
            ->get();

        // Training by Priority
        $trainingByPriority = TrainingNeedsAnalysis::whereIn('company_id', $companyGroupIds)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority');

        // Training by Status
        $trainingByStatus = TrainingNeedsAnalysis::whereIn('company_id', $companyGroupIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Training by Type
        $trainingByType = TrainingSession::whereIn('company_id', $companyGroupIds)
            ->select('session_type', DB::raw('count(*) as count'))
            ->groupBy('session_type')
            ->get()
            ->pluck('count', 'session_type');

        // Monthly Training Activity (last 6 months) - Database agnostic approach
        $sessions = TrainingSession::whereIn('company_id', $companyGroupIds)
            ->where('created_at', '>=', now()->subMonths(6))
            ->get();
        
        // Group by month in PHP (database-agnostic)
        $monthlyActivity = $sessions->groupBy(function($session) {
            return $session->created_at->format('Y-m');
        })->map(function($group) {
            return [
                'month' => $group->first()->created_at->format('Y-m'),
                'count' => $group->count()
            ];
        })->values()->sortBy('month');

        // Top Trained Employees
        $topTrainedEmployees = User::whereIn('company_id', $companyGroupIds)
            ->withCount('trainingRecords')
            ->orderBy('training_records_count', 'desc')
            ->limit(5)
            ->get();

        // Certificates Expiring Soon
        $expiringCertificates = TrainingCertificate::whereIn('company_id', $companyGroupIds)
            ->where('status', 'active')
            ->whereBetween('expiry_date', [now(), now()->addDays(60)])
            ->with(['user', 'trainingRecord.trainingSession'])
            ->orderBy('expiry_date', 'asc')
            ->limit(10)
            ->get();

        return view('training.dashboard', compact(
            'stats',
            'recentTNAs',
            'upcomingSessions',
            'trainingByPriority',
            'trainingByStatus',
            'trainingByType',
            'monthlyActivity',
            'topTrainedEmployees',
            'expiringCertificates'
        ));
    }
}
