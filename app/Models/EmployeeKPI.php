<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeKPI extends Model
{
    protected $table = 'employee_kpis';
    
    protected $fillable = [
        'employee_id',
        'company_id',
        'department_id',
        'recorded_date',
        'period_type',
        'days_present',
        'days_absent',
        'days_late',
        'days_early_leave',
        'attendance_rate',
        'punctuality_rate',
        'total_hours_worked',
        'overtime_hours',
        'incidents_involved',
        'near_misses_reported',
        'safety_observations',
        'safety_violations',
        'safety_score',
        'trainings_completed',
        'trainings_pending',
        'trainings_overdue',
        'certificates_earned',
        'certificates_expired',
        'certificates_expiring_soon',
        'training_completion_rate',
        'certification_compliance',
        'ppe_items_issued',
        'ppe_items_due_for_replacement',
        'ppe_inspections_completed',
        'ppe_inspections_missed',
        'ppe_compliance_rate',
        'toolbox_talks_attended',
        'toolbox_talks_missed',
        'toolbox_talks_scheduled',
        'toolbox_talk_attendance_rate',
        'tasks_assigned',
        'tasks_completed',
        'tasks_overdue',
        'task_completion_rate',
        'average_task_completion_time',
        'quality_issues',
        'quality_score',
        'medical_examinations_completed',
        'medical_examinations_due',
        'sick_leave_days',
        'first_aid_incidents',
        'health_compliance_rate',
        'compliance_requirements_met',
        'compliance_requirements_total',
        'non_compliances',
        'overall_compliance_rate',
        'suggestions_submitted',
        'suggestions_implemented',
        'feedback_provided',
        'engagement_score',
        'overall_performance_score',
        'performance_rating',
        'performance_notes',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'attendance_rate' => 'decimal:2',
        'punctuality_rate' => 'decimal:2',
        'safety_score' => 'decimal:2',
        'training_completion_rate' => 'decimal:2',
        'certification_compliance' => 'decimal:2',
        'ppe_compliance_rate' => 'decimal:2',
        'toolbox_talk_attendance_rate' => 'decimal:2',
        'task_completion_rate' => 'decimal:2',
        'average_task_completion_time' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'health_compliance_rate' => 'decimal:2',
        'overall_compliance_rate' => 'decimal:2',
        'engagement_score' => 'decimal:2',
        'overall_performance_score' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function getPerformanceRatingAttribute($value)
    {
        if (!$value && $this->overall_performance_score) {
            return $this->calculatePerformanceRating($this->overall_performance_score);
        }
        return $value;
    }

    protected function calculatePerformanceRating($score)
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 75) return 'Good';
        if ($score >= 60) return 'Fair';
        if ($score >= 40) return 'Poor';
        return 'Needs Improvement';
    }
}
