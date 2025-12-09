<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyKPI;
use App\Models\SystemKPI;
use App\Models\User;
use App\Models\UserKPI;
use App\Models\Employee;
use App\Models\EmployeeKPI;
use App\Models\Incident;
use App\Models\RiskAssessment;
use App\Models\TrainingSession;
use App\Models\Audit;
use App\Models\ToolboxTalk;
use App\Models\ActivityLog;
use App\Models\DailyAttendance;
use Carbon\Carbon;

class KPICalculationService
{
    /**
     * Calculate and store Company KPI for a given date
     */
    public function calculateCompanyKPI(Company $company, Carbon $date, string $periodType = 'daily'): CompanyKPI
    {
        $startDate = $this->getPeriodStartDate($date, $periodType);
        $endDate = $this->getPeriodEndDate($date, $periodType);

        // Employee Metrics
        $totalEmployees = Employee::where('company_id', $company->id)->count();
        $activeEmployees = Employee::where('company_id', $company->id)->where('is_active', true)->count();
        $newEmployees = Employee::where('company_id', $company->id)
            ->whereBetween('date_of_hire', [$startDate, $endDate])->count();
        $terminatedEmployees = Employee::where('company_id', $company->id)
            ->whereBetween('date_of_termination', [$startDate, $endDate])->count();
        $turnoverRate = $totalEmployees > 0 ? ($terminatedEmployees / $totalEmployees) * 100 : 0;

        // Incident Metrics
        $totalIncidents = Incident::where('company_id', $company->id)->count();
        $incidentsThisPeriod = Incident::where('company_id', $company->id)
            ->whereBetween('incident_date', [$startDate, $endDate])->count();
        $incidentsResolved = Incident::where('company_id', $company->id)
            ->where('status', 'closed')
            ->whereBetween('resolved_at', [$startDate, $endDate])->count();
        $incidentsPending = Incident::where('company_id', $company->id)
            ->whereIn('status', ['open', 'under_investigation'])->count();
        $incidentRate = $activeEmployees > 0 ? ($incidentsThisPeriod / $activeEmployees) * 1000 : 0;
        $resolutionRate = $incidentsThisPeriod > 0 ? ($incidentsResolved / $incidentsThisPeriod) * 100 : 0;

        // Training Metrics
        $totalTrainings = TrainingSession::whereHas('company', function($q) use ($company) {
            $q->where('id', $company->id);
        })->count();
        $trainingsCompleted = TrainingSession::whereHas('company', function($q) use ($company) {
            $q->where('id', $company->id);
        })->where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])->count();
        $trainingsPending = TrainingSession::whereHas('company', function($q) use ($company) {
            $q->where('id', $company->id);
        })->whereIn('status', ['scheduled', 'in_progress'])->count();
        $completionRate = $totalTrainings > 0 ? ($trainingsCompleted / $totalTrainings) * 100 : 0;
        $employeesTrained = TrainingSession::whereHas('company', function($q) use ($company) {
            $q->where('id', $company->id);
        })->whereBetween('completed_at', [$startDate, $endDate])
            ->distinct('user_id')->count('user_id');
        $trainingCoverage = $activeEmployees > 0 ? ($employeesTrained / $activeEmployees) * 100 : 0;

        // Risk Assessment Metrics
        $totalRiskAssessments = RiskAssessment::where('company_id', $company->id)->count();
        $highRiskItems = RiskAssessment::where('company_id', $company->id)
            ->where('risk_level', 'high')->count();
        $mediumRiskItems = RiskAssessment::where('company_id', $company->id)
            ->where('risk_level', 'medium')->count();
        $lowRiskItems = RiskAssessment::where('company_id', $company->id)
            ->where('risk_level', 'low')->count();
        $averageRiskScore = RiskAssessment::where('company_id', $company->id)
            ->avg('risk_score') ?? 0;

        // Toolbox Talks Metrics
        $toolboxTalksScheduled = ToolboxTalk::where('company_id', $company->id)
            ->whereBetween('scheduled_date', [$startDate, $endDate])->count();
        $toolboxTalksCompleted = ToolboxTalk::where('company_id', $company->id)
            ->where('status', 'completed')
            ->whereBetween('end_time', [$startDate, $endDate])->count();
        $toolboxTalkCompletionRate = $toolboxTalksScheduled > 0 ? ($toolboxTalksCompleted / $toolboxTalksScheduled) * 100 : 0;

        // Calculate overall safety score
        $overallSafetyScore = $this->calculateOverallSafetyScore([
            'incident_rate' => $incidentRate,
            'resolution_rate' => $resolutionRate,
            'training_coverage' => $trainingCoverage,
            'compliance_rate' => 0, // Will be calculated from audits
            'risk_score' => $averageRiskScore,
        ]);

        return CompanyKPI::updateOrCreate(
            [
                'company_id' => $company->id,
                'recorded_date' => $date->format('Y-m-d'),
                'period_type' => $periodType,
            ],
            [
                'total_employees' => $totalEmployees,
                'active_employees' => $activeEmployees,
                'new_employees' => $newEmployees,
                'terminated_employees' => $terminatedEmployees,
                'employee_turnover_rate' => round($turnoverRate, 2),
                'total_incidents' => $totalIncidents,
                'incidents_this_period' => $incidentsThisPeriod,
                'incidents_resolved' => $incidentsResolved,
                'incidents_pending' => $incidentsPending,
                'incident_rate' => round($incidentRate, 2),
                'incident_resolution_rate' => round($resolutionRate, 2),
                'total_trainings' => $totalTrainings,
                'trainings_completed' => $trainingsCompleted,
                'trainings_pending' => $trainingsPending,
                'training_completion_rate' => round($completionRate, 2),
                'employees_trained' => $employeesTrained,
                'training_coverage' => round($trainingCoverage, 2),
                'total_risk_assessments' => $totalRiskAssessments,
                'high_risk_items' => $highRiskItems,
                'medium_risk_items' => $mediumRiskItems,
                'low_risk_items' => $lowRiskItems,
                'average_risk_score' => round($averageRiskScore, 2),
                'toolbox_talks_scheduled' => $toolboxTalksScheduled,
                'toolbox_talks_completed' => $toolboxTalksCompleted,
                'toolbox_talk_completion_rate' => round($toolboxTalkCompletionRate, 2),
                'overall_safety_score' => round($overallSafetyScore, 2),
            ]
        );
    }

    /**
     * Calculate and store System KPI for a given date
     */
    public function calculateSystemKPI(Carbon $date, string $periodType = 'daily'): SystemKPI
    {
        $startDate = $this->getPeriodStartDate($date, $periodType);
        $endDate = $this->getPeriodEndDate($date, $periodType);

        // User Metrics
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $usersLoggedInToday = User::whereDate('last_login_at', $date->format('Y-m-d'))->count();
        $usersLoggedInThisWeek = User::whereBetween('last_login_at', [
            $date->copy()->startOfWeek(),
            $date->copy()->endOfWeek()
        ])->count();
        $usersLoggedInThisMonth = User::whereBetween('last_login_at', [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth()
        ])->count();
        $userActivityRate = $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;

        // Company Metrics
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $newCompanies = Company::whereBetween('created_at', [$startDate, $endDate])->count();
        $inactiveCompanies = Company::where('is_active', false)->count();

        // Activity Metrics
        $totalActivitiesToday = ActivityLog::whereDate('created_at', $date->format('Y-m-d'))->count();
        $totalActivitiesThisWeek = ActivityLog::whereBetween('created_at', [
            $date->copy()->startOfWeek(),
            $date->copy()->endOfWeek()
        ])->count();
        $totalActivitiesThisMonth = ActivityLog::whereBetween('created_at', [
            $date->copy()->startOfMonth(),
            $date->copy()->endOfMonth()
        ])->count();
        $totalActivitiesThisYear = ActivityLog::whereYear('created_at', $date->year)->count();
        $averageActivitiesPerUser = $activeUsers > 0 ? ($totalActivitiesThisMonth / $activeUsers) : 0;

        // Calculate system health score
        $systemHealthScore = $this->calculateSystemHealthScore([
            'uptime' => 100, // Default, should be calculated from monitoring
            'error_rate' => 0,
            'user_activity' => $userActivityRate,
            'response_time' => 0, // Should be from monitoring
        ]);

        return SystemKPI::updateOrCreate(
            [
                'recorded_date' => $date->format('Y-m-d'),
                'period_type' => $periodType,
            ],
            [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'new_users' => $newUsers,
                'inactive_users' => $inactiveUsers,
                'users_logged_in_today' => $usersLoggedInToday,
                'users_logged_in_this_week' => $usersLoggedInThisWeek,
                'users_logged_in_this_month' => $usersLoggedInThisMonth,
                'user_activity_rate' => round($userActivityRate, 2),
                'total_companies' => $totalCompanies,
                'active_companies' => $activeCompanies,
                'new_companies' => $newCompanies,
                'inactive_companies' => $inactiveCompanies,
                'total_activities_today' => $totalActivitiesToday,
                'total_activities_this_week' => $totalActivitiesThisWeek,
                'total_activities_this_month' => $totalActivitiesThisMonth,
                'total_activities_this_year' => $totalActivitiesThisYear,
                'average_activities_per_user' => round($averageActivitiesPerUser, 2),
                'system_health_score' => round($systemHealthScore, 2),
            ]
        );
    }

    /**
     * Calculate and store User KPI for a given date
     */
    public function calculateUserKPI(User $user, Carbon $date, string $periodType = 'daily'): UserKPI
    {
        $startDate = $this->getPeriodStartDate($date, $periodType);
        $endDate = $this->getPeriodEndDate($date, $periodType);

        // Activity Metrics
        $totalLogins = User::where('id', $user->id)->whereNotNull('last_login_at')->count();
        $totalActivities = ActivityLog::where('user_id', $user->id)->count();
        $activitiesThisPeriod = ActivityLog::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $daysActive = ActivityLog::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('created_at')->count();
        $activityRate = $this->getDaysInPeriod($periodType) > 0 
            ? ($daysActive / $this->getDaysInPeriod($periodType)) * 100 : 0;

        // Work Metrics
        $incidentsCreated = Incident::where('created_by', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $incidentsResolved = Incident::where('resolved_by', $user->id)
            ->whereBetween('resolved_at', [$startDate, $endDate])->count();
        $riskAssessmentsCreated = RiskAssessment::where('created_by', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])->count();

        // Calculate performance scores
        $productivityScore = $this->calculateProductivityScore([
            'activities' => $activitiesThisPeriod,
            'tasks_completed' => 0,
            'incidents_resolved' => $incidentsResolved,
        ]);

        $engagementScore = $this->calculateEngagementScore([
            'logins' => $totalLogins,
            'activities' => $activitiesThisPeriod,
            'days_active' => $daysActive,
        ]);

        $overallPerformanceScore = ($productivityScore + $engagementScore) / 2;

        return UserKPI::updateOrCreate(
            [
                'user_id' => $user->id,
                'recorded_date' => $date->format('Y-m-d'),
                'period_type' => $periodType,
            ],
            [
                'total_logins' => $totalLogins,
                'total_activities' => $totalActivities,
                'activities_this_period' => $activitiesThisPeriod,
                'days_active' => $daysActive,
                'activity_rate' => round($activityRate, 2),
                'incidents_created' => $incidentsCreated,
                'incidents_resolved' => $incidentsResolved,
                'risk_assessments_created' => $riskAssessmentsCreated,
                'productivity_score' => round($productivityScore, 2),
                'engagement_score' => round($engagementScore, 2),
                'overall_performance_score' => round($overallPerformanceScore, 2),
            ]
        );
    }

    /**
     * Calculate and store Employee KPI for a given date
     */
    public function calculateEmployeeKPI(Employee $employee, Carbon $date, string $periodType = 'daily'): EmployeeKPI
    {
        $startDate = $this->getPeriodStartDate($date, $periodType);
        $endDate = $this->getPeriodEndDate($date, $periodType);

        // Attendance Metrics
        $daysPresent = DailyAttendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')->count();
        $daysAbsent = DailyAttendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'absent')->count();
        $totalDays = $this->getDaysInPeriod($periodType);
        $attendanceRate = $totalDays > 0 ? ($daysPresent / $totalDays) * 100 : 0;

        // Training Metrics
        $trainingsCompleted = TrainingSession::whereHas('attendances', function($q) use ($employee) {
            $q->where('employee_id', $employee->id)->where('status', 'completed');
        })->whereBetween('completed_at', [$startDate, $endDate])->count();

        // Calculate overall performance score
        $overallPerformanceScore = $this->calculateEmployeePerformanceScore([
            'attendance_rate' => $attendanceRate,
            'training_completion' => 0,
            'safety_score' => 100,
            'compliance_rate' => 100,
        ]);

        return EmployeeKPI::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'recorded_date' => $date->format('Y-m-d'),
                'period_type' => $periodType,
            ],
            [
                'company_id' => $employee->company_id,
                'department_id' => $employee->department_id,
                'days_present' => $daysPresent,
                'days_absent' => $daysAbsent,
                'attendance_rate' => round($attendanceRate, 2),
                'trainings_completed' => $trainingsCompleted,
                'overall_performance_score' => round($overallPerformanceScore, 2),
            ]
        );
    }

    // Helper methods
    protected function getPeriodStartDate(Carbon $date, string $periodType): Carbon
    {
        return match($periodType) {
            'daily' => $date->copy()->startOfDay(),
            'weekly' => $date->copy()->startOfWeek(),
            'monthly' => $date->copy()->startOfMonth(),
            'yearly' => $date->copy()->startOfYear(),
            default => $date->copy()->startOfDay(),
        };
    }

    protected function getPeriodEndDate(Carbon $date, string $periodType): Carbon
    {
        return match($periodType) {
            'daily' => $date->copy()->endOfDay(),
            'weekly' => $date->copy()->endOfWeek(),
            'monthly' => $date->copy()->endOfMonth(),
            'yearly' => $date->copy()->endOfYear(),
            default => $date->copy()->endOfDay(),
        };
    }

    protected function getDaysInPeriod(string $periodType): int
    {
        return match($periodType) {
            'daily' => 1,
            'weekly' => 7,
            'monthly' => 30,
            'yearly' => 365,
            default => 1,
        };
    }

    protected function calculateOverallSafetyScore(array $metrics): float
    {
        // Weighted calculation
        $weights = [
            'incident_rate' => 0.3,
            'resolution_rate' => 0.2,
            'training_coverage' => 0.2,
            'compliance_rate' => 0.2,
            'risk_score' => 0.1,
        ];

        $score = 0;
        foreach ($weights as $metric => $weight) {
            if (isset($metrics[$metric])) {
                $score += ($metrics[$metric] / 100) * $weight * 100;
            }
        }

        return min(100, max(0, $score));
    }

    protected function calculateSystemHealthScore(array $metrics): float
    {
        // Simplified calculation
        return ($metrics['uptime'] * 0.4) + 
               ((100 - min($metrics['error_rate'], 100)) * 0.3) + 
               ($metrics['user_activity'] * 0.3);
    }

    protected function calculateProductivityScore(array $metrics): float
    {
        // Simplified calculation based on activities and tasks
        $baseScore = min(100, ($metrics['activities'] / 10) * 10);
        return min(100, $baseScore);
    }

    protected function calculateEngagementScore(array $metrics): float
    {
        // Based on logins, activities, and active days
        $loginScore = min(30, ($metrics['logins'] / 5) * 30);
        $activityScore = min(40, ($metrics['activities'] / 20) * 40);
        $daysScore = min(30, ($metrics['days_active'] / 5) * 30);
        
        return min(100, $loginScore + $activityScore + $daysScore);
    }

    protected function calculateEmployeePerformanceScore(array $metrics): float
    {
        // Weighted calculation
        return ($metrics['attendance_rate'] * 0.3) +
               ($metrics['training_completion'] * 0.2) +
               ($metrics['safety_score'] * 0.3) +
               ($metrics['compliance_rate'] * 0.2);
    }
}

