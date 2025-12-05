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

        if (!$companyId) {
            return redirect()->route('login')->with('error', 'User is not assigned to any company.');
        }

        // Calculate days without incident
        $lastIncident = Incident::where('company_id', $companyId)
            ->orderBy('incident_date', 'desc')
            ->first();
        $daysWithoutIncident = $lastIncident 
            ? Carbon::parse($lastIncident->incident_date)->diffInDays(now())
            : now()->diffInDays(Company::find($companyId)->created_at ?? now());

        // Calculate safety score
        $totalIncidents = Incident::where('company_id', $companyId)->count();
        $criticalIncidents = Incident::where('company_id', $companyId)->where('severity', 'critical')->count();
        $highIncidents = Incident::where('company_id', $companyId)->where('severity', 'high')->count();
        $safetyScore = max(0, min(100, 100 - ($criticalIncidents * 10) - ($highIncidents * 5) - (($totalIncidents - $criticalIncidents - $highIncidents) * 2)));
        $safetyScore = round($safetyScore);

        // Overall Statistics
        $stats = [
            // Incidents
            'total_incidents' => Incident::where('company_id', $companyId)->count(),
            'open_incidents' => Incident::where('company_id', $companyId)->whereIn('status', ['reported', 'open'])->count(),
            'days_without_incident' => $daysWithoutIncident,
            'safety_score' => $safetyScore,
            
            // Toolbox Talks
            'total_toolbox_talks' => ToolboxTalk::where('company_id', $companyId)->count(),
            'completed_talks' => ToolboxTalk::where('company_id', $companyId)->where('status', 'completed')->count(),
            'total_attendances' => ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->count(),
            'total_feedback' => ToolboxTalkFeedback::whereHas('toolboxTalk', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->count(),
            
            // Risk Assessment
            'total_hazards' => Hazard::where('company_id', $companyId)->count(),
            'total_risk_assessments' => RiskAssessment::where('company_id', $companyId)->count(),
            'high_risk_assessments' => RiskAssessment::where('company_id', $companyId)->whereIn('risk_level', ['high', 'critical', 'extreme'])->count(),
            'total_jsas' => JSA::where('company_id', $companyId)->count(),
            'approved_jsas' => JSA::where('company_id', $companyId)->where('status', 'approved')->count(),
            
            // CAPA
            'total_capas' => CAPA::where('company_id', $companyId)->count(),
            'open_capas' => CAPA::where('company_id', $companyId)->whereIn('status', ['open', 'in_progress'])->count(),
            'overdue_capas' => CAPA::where('company_id', $companyId)
                ->whereIn('status', ['open', 'in_progress'])
                ->where('due_date', '<', now())
                ->count(),
            
            // Training
            'total_training_needs' => TrainingNeedsAnalysis::where('company_id', $companyId)->count(),
            'pending_training_needs' => TrainingNeedsAnalysis::where('company_id', $companyId)->whereIn('status', ['identified', 'validated'])->count(),
            'total_training_sessions' => TrainingSession::where('company_id', $companyId)->count(),
            'upcoming_sessions' => TrainingSession::where('company_id', $companyId)
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->where('scheduled_start', '>=', now())
                ->count(),
            'total_certificates' => TrainingCertificate::where('company_id', $companyId)->count(),
            'expiring_certificates' => TrainingCertificate::where('company_id', $companyId)
                ->where('status', 'active')
                ->where('has_expiry', true)
                ->whereBetween('expiry_date', [now(), now()->addDays(60)])
                ->count(),
            
            // PPE
            'total_ppe_items' => PPEItem::where('company_id', $companyId)->count(),
            'low_stock_ppe' => PPEItem::where('company_id', $companyId)
                ->whereColumn('available_quantity', '<', 'minimum_stock_level')
                ->count(),
            'active_ppe_issuances' => PPEIssuance::where('company_id', $companyId)->where('status', 'active')->count(),
            'expiring_ppe' => PPEIssuance::where('company_id', $companyId)
                ->where('status', 'active')
                ->whereNotNull('replacement_due_date')
                ->where('replacement_due_date', '<=', now()->addDays(7))
                ->count(),
            
            // Communications & Users
            'total_communications' => SafetyCommunication::where('company_id', $companyId)->count(),
            'active_users' => User::where('company_id', $companyId)->where('is_active', true)->count(),
        ];

        // Monthly Incident Trends (Last 6 Months)
        $incidentTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthIncidents = Incident::where('company_id', $companyId)
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
            'critical' => Incident::where('company_id', $companyId)->where('severity', 'critical')->count(),
            'high' => Incident::where('company_id', $companyId)->where('severity', 'high')->count(),
            'medium' => Incident::where('company_id', $companyId)->where('severity', 'medium')->count(),
            'low' => Incident::where('company_id', $companyId)->where('severity', 'low')->count(),
        ];

        // Incident Status Distribution
        $incidentStatusDistribution = [
            'reported' => Incident::where('company_id', $companyId)->where('status', 'reported')->count(),
            'open' => Incident::where('company_id', $companyId)->where('status', 'open')->count(),
            'investigating' => Incident::where('company_id', $companyId)->where('status', 'investigating')->count(),
            'resolved' => Incident::where('company_id', $companyId)->where('status', 'resolved')->count(),
            'closed' => Incident::where('company_id', $companyId)->where('status', 'closed')->count(),
        ];

        // Toolbox Talk Trends (Last 6 Months)
        $talkTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthTalks = ToolboxTalk::where('company_id', $companyId)
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
            'scheduled' => ToolboxTalk::where('company_id', $companyId)->where('status', 'scheduled')->count(),
            'in_progress' => ToolboxTalk::where('company_id', $companyId)->where('status', 'in_progress')->count(),
            'completed' => ToolboxTalk::where('company_id', $companyId)->where('status', 'completed')->count(),
        ];

        // Weekly Activity (Last 8 Weeks)
        $weeklyActivity = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $weekIncidents = Incident::where('company_id', $companyId)
                ->whereBetween('incident_date', [$weekStart, $weekEnd])
                ->count();
            
            $weekTalks = ToolboxTalk::where('company_id', $companyId)
                ->whereBetween('scheduled_date', [$weekStart, $weekEnd])
                ->count();
            
            $weeklyActivity[] = [
                'week' => $weekStart->format('M d'),
                'incidents' => $weekIncidents,
                'talks' => $weekTalks,
            ];
        }

        // Department Performance
        $departments = Department::where('company_id', $companyId)->get();
        $departmentStats = $departments->map(function($dept) use ($companyId) {
            $incidents = Incident::where('company_id', $companyId)
                ->where('department_id', $dept->id)
                ->get();
            
            $talks = ToolboxTalk::where('company_id', $companyId)
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
            'extreme' => RiskAssessment::where('company_id', $companyId)->where('risk_level', 'extreme')->count(),
            'critical' => RiskAssessment::where('company_id', $companyId)->where('risk_level', 'critical')->count(),
            'high' => RiskAssessment::where('company_id', $companyId)->where('risk_level', 'high')->count(),
            'medium' => RiskAssessment::where('company_id', $companyId)->where('risk_level', 'medium')->count(),
            'low' => RiskAssessment::where('company_id', $companyId)->where('risk_level', 'low')->count(),
        ];

        // Training Trends (Last 6 Months)
        $trainingTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthSessions = TrainingSession::where('company_id', $companyId)
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
        $ppeCategoryDistribution = PPEItem::where('company_id', $companyId)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        // Recent Activity
        $recentIncidents = Incident::where('company_id', $companyId)
            ->with(['reporter', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentTalks = ToolboxTalk::where('company_id', $companyId)
            ->with(['department', 'supervisor'])
            ->orderBy('scheduled_date', 'desc')
            ->limit(5)
            ->get();

        $recentRiskAssessments = RiskAssessment::where('company_id', $companyId)
            ->with(['department', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentCAPAs = CAPA::where('company_id', $companyId)
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

