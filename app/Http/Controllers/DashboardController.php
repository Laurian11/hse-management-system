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

        // Overall Statistics
        $stats = [
            'total_incidents' => Incident::where('company_id', $companyId)->count(),
            'open_incidents' => Incident::where('company_id', $companyId)->whereIn('status', ['reported', 'open'])->count(),
            'total_toolbox_talks' => ToolboxTalk::where('company_id', $companyId)->count(),
            'completed_talks' => ToolboxTalk::where('company_id', $companyId)->where('status', 'completed')->count(),
            'total_attendances' => ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->count(),
            'total_feedback' => ToolboxTalkFeedback::whereHas('toolboxTalk', function($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })->count(),
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
            'recentTalks'
        ));
    }
}

