<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Incident;
use App\Models\ToolboxTalk;
use App\Models\ToolboxTalkAttendance;
use App\Models\ToolboxTalkFeedback;
use App\Models\SafetyCommunication;
use App\Models\User;
use App\Models\Department;
use App\Models\RiskAssessment;
use App\Models\JSA;
use App\Models\Hazard;
use App\Models\CAPA;
use App\Models\TrainingSession;
use App\Models\TrainingCertificate;
use App\Models\TrainingNeedsAnalysis;
use App\Models\PPEItem;
use App\Models\PPEIssuance;
use App\Services\CompanyGroupService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $companyId = $user->company_id;

        // Get company group IDs (parent + all sisters) for data aggregation
        // For super admin without company_id, get all companies
        if (!$companyId) {
            // Load role relationship
            $user->load('role');
            // Check if user is super admin
            $isSuperAdmin = $user->role && $user->role->name === 'super_admin';
            if (!$isSuperAdmin) {
                return redirect()->route('login')->with('error', 'User is not assigned to any company.');
            }
            // Super admin can proceed - will see data from all companies
            $companyGroupIds = \App\Models\Company::where('is_active', true)->pluck('id')->toArray();
            $isParentCompany = false;
        } else {
            $companyGroupIds = CompanyGroupService::getCompanyGroupIds($companyId);
            $isParentCompany = CompanyGroupService::isParentCompany($companyId);
        }

        // Calculate days without incident (across company group if parent)
        $lastIncident = Incident::whereIn('company_id', $companyGroupIds)
            ->orderBy('incident_date', 'desc')
            ->first();
        $daysWithoutIncident = $lastIncident 
            ? Carbon::parse($lastIncident->incident_date)->diffInDays(now())
            : now()->diffInDays(Company::find($companyId)->created_at ?? now());

        // Calculate safety score (across company group if parent)
        $totalIncidents = Incident::whereIn('company_id', $companyGroupIds)->count();
        $criticalIncidents = Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'critical')->count();
        $highIncidents = Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'high')->count();
        $safetyScore = max(0, min(100, 100 - ($criticalIncidents * 10) - ($highIncidents * 5) - (($totalIncidents - $criticalIncidents - $highIncidents) * 2)));
        $safetyScore = round($safetyScore);

        // Overall Statistics (aggregated across company group if parent)
        $stats = [
            // Incidents
            'total_incidents' => Incident::whereIn('company_id', $companyGroupIds)->count(),
            'open_incidents' => Incident::whereIn('company_id', $companyGroupIds)->whereIn('status', ['reported', 'open'])->count(),
            'days_without_incident' => $daysWithoutIncident,
            'safety_score' => $safetyScore,
            
            // Toolbox Talks
            'total_toolbox_talks' => ToolboxTalk::whereIn('company_id', $companyGroupIds)->count(),
            'completed_talks' => ToolboxTalk::whereIn('company_id', $companyGroupIds)->where('status', 'completed')->count(),
            'total_attendances' => ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($companyGroupIds) {
                $q->whereIn('company_id', $companyGroupIds);
            })->count(),
            'total_feedback' => ToolboxTalkFeedback::whereHas('toolboxTalk', function($q) use ($companyGroupIds) {
                $q->whereIn('company_id', $companyGroupIds);
            })->count(),
            
            // Risk Assessment (aggregated across company group if parent)
            'total_hazards' => Hazard::whereIn('company_id', $companyGroupIds)->count(),
            'total_risk_assessments' => RiskAssessment::whereIn('company_id', $companyGroupIds)->count(),
            'high_risk_assessments' => RiskAssessment::whereIn('company_id', $companyGroupIds)->whereIn('risk_level', ['high', 'critical', 'extreme'])->count(),
            'total_jsas' => JSA::whereIn('company_id', $companyGroupIds)->count(),
            'approved_jsas' => JSA::whereIn('company_id', $companyGroupIds)->where('status', 'approved')->count(),
            
            // CAPA (aggregated across company group if parent)
            'total_capas' => CAPA::whereIn('company_id', $companyGroupIds)->count(),
            'open_capas' => CAPA::whereIn('company_id', $companyGroupIds)->whereIn('status', ['open', 'in_progress'])->count(),
            'overdue_capas' => CAPA::whereIn('company_id', $companyGroupIds)
                ->whereIn('status', ['open', 'in_progress'])
                ->where('due_date', '<', now())
                ->count(),
            
            // Training (aggregated across company group if parent)
            'total_training_needs' => TrainingNeedsAnalysis::whereIn('company_id', $companyGroupIds)->count(),
            'pending_training_needs' => TrainingNeedsAnalysis::whereIn('company_id', $companyGroupIds)->whereIn('status', ['identified', 'validated'])->count(),
            'total_training_sessions' => TrainingSession::whereIn('company_id', $companyGroupIds)->count(),
            'upcoming_sessions' => TrainingSession::whereIn('company_id', $companyGroupIds)
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->where('scheduled_start', '>=', now())
                ->count(),
            'total_certificates' => TrainingCertificate::whereIn('company_id', $companyGroupIds)->count(),
            'expiring_certificates' => TrainingCertificate::whereIn('company_id', $companyGroupIds)
                ->where('status', 'active')
                ->where('has_expiry', true)
                ->whereBetween('expiry_date', [now(), now()->addDays(60)])
                ->count(),
            
            // PPE (aggregated across company group if parent)
            'total_ppe_items' => PPEItem::whereIn('company_id', $companyGroupIds)->count(),
            'low_stock_ppe' => PPEItem::whereIn('company_id', $companyGroupIds)
                ->whereColumn('available_quantity', '<', 'minimum_stock_level')
                ->count(),
            'active_ppe_issuances' => PPEIssuance::whereIn('company_id', $companyGroupIds)->where('status', 'active')->count(),
            'expiring_ppe' => PPEIssuance::whereIn('company_id', $companyGroupIds)
                ->where('status', 'active')
                ->whereNotNull('replacement_due_date')
                ->where('replacement_due_date', '<=', now()->addDays(7))
                ->count(),
            
            // Communications & Users (aggregated across company group if parent)
            'total_communications' => SafetyCommunication::whereIn('company_id', $companyGroupIds)->count(),
            'active_users' => User::whereIn('company_id', $companyGroupIds)->where('is_active', true)->count(),
            
            // Company group info
            'is_parent_company' => $isParentCompany,
            'company_group_count' => count($companyGroupIds),
        ];

        // Monthly Incident Trends (Last 6 Months)
        $incidentTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthIncidents = Incident::whereIn('company_id', $companyGroupIds)
                ->whereYear('incident_date', $month->year)
                ->whereMonth('incident_date', $month->month)
                ->get();
            
            $incidentTrends[] = [
                'month' => $month->format('M Y'),
                'total' => $monthIncidents->count(),
                'open' => $monthIncidents->whereIn('status', ['reported', 'open'])->count(),
                'closed' => $monthIncidents->whereIn('status', ['closed', 'resolved'])->count(),
            ];
        }

        // Incident Severity Distribution
        $severityDistribution = [
            'critical' => Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'critical')->count(),
            'high' => Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'high')->count(),
            'medium' => Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'medium')->count(),
            'low' => Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'low')->count(),
        ];

        // Incident Status Distribution
        $incidentStatusDistribution = [
            'reported' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'reported')->count(),
            'open' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'open')->count(),
            'investigating' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'investigating')->count(),
            'resolved' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'resolved')->count(),
            'closed' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'closed')->count(),
        ];

        // Toolbox Talk Trends (Last 6 Months)
        $talkTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthTalks = ToolboxTalk::whereIn('company_id', $companyGroupIds)
                ->whereYear('scheduled_date', $month->year)
                ->whereMonth('scheduled_date', $month->month)
                ->get();
            
            $talkTrends[] = [
                'month' => $month->format('M Y'),
                'total' => $monthTalks->count(),
                'completed' => $monthTalks->where('status', 'completed')->count(),
                'avg_attendance' => $monthTalks->where('status', 'completed')->avg('attendance_rate') ?? 0,
            ];
        }

        // Toolbox Talk Status Distribution
        $talkStatusDistribution = [
            'scheduled' => ToolboxTalk::whereIn('company_id', $companyGroupIds)->where('status', 'scheduled')->count(),
            'in_progress' => ToolboxTalk::whereIn('company_id', $companyGroupIds)->where('status', 'in_progress')->count(),
            'completed' => ToolboxTalk::whereIn('company_id', $companyGroupIds)->where('status', 'completed')->count(),
        ];

        // Weekly Activity (Last 8 Weeks)
        $weeklyActivity = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $weekIncidents = Incident::whereIn('company_id', $companyGroupIds)
                ->whereBetween('incident_date', [$weekStart, $weekEnd])
                ->count();
            
            $weekTalks = ToolboxTalk::whereIn('company_id', $companyGroupIds)
                ->whereBetween('scheduled_date', [$weekStart, $weekEnd])
                ->count();
            
            $weeklyActivity[] = [
                'week' => $weekStart->format('M d'),
                'incidents' => $weekIncidents,
                'talks' => $weekTalks,
            ];
        }

        // Department Performance
        $departments = Department::whereIn('company_id', $companyGroupIds)->get();
        $departmentStats = $departments->map(function($dept) use ($companyGroupIds) {
            $incidents = Incident::whereIn('company_id', $companyGroupIds)
                ->where('department_id', $dept->id)
                ->get();
            
            $talks = ToolboxTalk::whereIn('company_id', $companyGroupIds)
                ->where('department_id', $dept->id)
                ->completed()
                ->get();
            
            return [
                'name' => $dept->name,
                'incidents' => $incidents->count(),
                'open_incidents' => $incidents->whereIn('status', ['reported', 'open'])->count(),
                'talks' => $talks->count(),
                'avg_attendance' => $talks->avg('attendance_rate') ?? 0,
            ];
        })->filter(function($dept) {
            return $dept['incidents'] > 0 || $dept['talks'] > 0;
        })->sortByDesc('incidents');

        // Risk Level Distribution
        $riskLevelDistribution = [
            'extreme' => RiskAssessment::whereIn('company_id', $companyGroupIds)->where('risk_level', 'extreme')->count(),
            'critical' => RiskAssessment::whereIn('company_id', $companyGroupIds)->where('risk_level', 'critical')->count(),
            'high' => RiskAssessment::whereIn('company_id', $companyGroupIds)->where('risk_level', 'high')->count(),
            'medium' => RiskAssessment::whereIn('company_id', $companyGroupIds)->where('risk_level', 'medium')->count(),
            'low' => RiskAssessment::whereIn('company_id', $companyGroupIds)->where('risk_level', 'low')->count(),
        ];

        // Training Trends (Last 6 Months)
        $trainingTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthSessions = TrainingSession::whereIn('company_id', $companyGroupIds)
                ->whereYear('scheduled_start', $month->year)
                ->whereMonth('scheduled_start', $month->month)
                ->get();
            
            $trainingTrends[] = [
                'month' => $month->format('M Y'),
                'total' => $monthSessions->count(),
                'completed' => $monthSessions->where('status', 'completed')->count(),
            ];
        }

        // PPE Category Distribution
        $ppeCategoryDistribution = PPEItem::whereIn('company_id', $companyGroupIds)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Recent Activity
        $recentIncidents = Incident::whereIn('company_id', $companyGroupIds)
            ->with(['reporter', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentTalks = ToolboxTalk::whereIn('company_id', $companyGroupIds)
            ->with(['department', 'supervisor'])
            ->orderBy('scheduled_date', 'desc')
            ->limit(5)
            ->get();

        $recentRiskAssessments = RiskAssessment::whereIn('company_id', $companyGroupIds)
            ->with(['department', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentCAPAs = CAPA::whereIn('company_id', $companyGroupIds)
            ->with(['department', 'assignedTo', 'incident'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'incidentTrends',
            'severityDistribution',
            'incidentStatusDistribution',
            'talkTrends',
            'talkStatusDistribution',
            'weeklyActivity',
            'departmentStats',
            'recentIncidents',
            'recentTalks',
            'riskLevelDistribution',
            'trainingTrends',
            'ppeCategoryDistribution',
            'recentRiskAssessments',
            'recentCAPAs'
        ));
    }
}

