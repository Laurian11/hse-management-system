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

class TrainingDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;
        
        // Statistics
        $stats = [
            'total_training_needs' => TrainingNeedsAnalysis::where('company_id', $companyId)->count(),
            'validated_tnas' => TrainingNeedsAnalysis::where('company_id', $companyId)->where('status', 'validated')->count(),
            'total_plans' => TrainingPlan::where('company_id', $companyId)->count(),
            'approved_plans' => TrainingPlan::where('company_id', $companyId)->where('status', 'approved')->count(),
            'total_sessions' => TrainingSession::where('company_id', $companyId)->count(),
            'completed_sessions' => TrainingSession::where('company_id', $companyId)->where('status', 'completed')->count(),
            'upcoming_sessions' => TrainingSession::where('company_id', $companyId)->where('status', 'scheduled')->where('scheduled_start', '>', now())->count(),
            'total_certificates' => TrainingCertificate::where('company_id', $companyId)->count(),
            'active_certificates' => TrainingCertificate::where('company_id', $companyId)->where('status', 'active')->count(),
            'expiring_soon' => TrainingCertificate::where('company_id', $companyId)
                ->where('status', 'active')
                ->whereBetween('expiry_date', [now(), now()->addDays(60)])
                ->count(),
            'total_trained_employees' => TrainingRecord::where('company_id', $companyId)
                ->distinct('user_id')
                ->count('user_id'),
        ];

        // Recent Training Needs
        $recentTNAs = TrainingNeedsAnalysis::where('company_id', $companyId)
            ->with(['creator', 'trainingPlans'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming Sessions
        $upcomingSessions = TrainingSession::where('company_id', $companyId)
            ->where('status', 'scheduled')
            ->where('scheduled_start', '>', now())
            ->with(['trainingPlan.trainingNeed', 'instructor'])
            ->orderBy('scheduled_start', 'asc')
            ->limit(5)
            ->get();

        // Training by Priority
        $trainingByPriority = TrainingNeedsAnalysis::where('company_id', $companyId)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority');

        // Training by Status
        $trainingByStatus = TrainingNeedsAnalysis::where('company_id', $companyId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Training by Type
        $trainingByType = TrainingSession::where('company_id', $companyId)
            ->select('session_type', DB::raw('count(*) as count'))
            ->groupBy('session_type')
            ->get()
            ->pluck('count', 'session_type');

        // Monthly Training Activity (last 6 months) - Database agnostic approach
        $sessions = TrainingSession::where('company_id', $companyId)
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
        $topTrainedEmployees = User::where('company_id', $companyId)
            ->withCount('trainingRecords')
            ->orderBy('training_records_count', 'desc')
            ->limit(5)
            ->get();

        // Certificates Expiring Soon
        $expiringCertificates = TrainingCertificate::where('company_id', $companyId)
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
