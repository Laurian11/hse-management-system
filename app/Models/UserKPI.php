<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserKPI extends Model
{
    protected $table = 'user_kpis';
    
    protected $fillable = [
        'user_id',
        'recorded_date',
        'period_type',
        'total_logins',
        'total_activities',
        'activities_this_period',
        'average_activities_per_day',
        'days_active',
        'activity_rate',
        'incidents_created',
        'incidents_resolved',
        'incidents_assigned',
        'risk_assessments_created',
        'risk_assessments_reviewed',
        'trainings_conducted',
        'trainings_attended',
        'audits_conducted',
        'toolbox_talks_conducted',
        'toolbox_talks_attended',
        'documents_created',
        'documents_reviewed',
        'incident_resolution_rate',
        'average_resolution_time',
        'task_completion_rate',
        'tasks_completed',
        'tasks_pending',
        'tasks_overdue',
        'reports_generated',
        'exports_performed',
        'searches_performed',
        'session_duration_avg',
        'total_session_time',
        'comments_made',
        'approvals_given',
        'assignments_made',
        'notifications_sent',
        'training_certificates_earned',
        'training_certificates_expired',
        'ppe_items_issued',
        'ppe_inspections_completed',
        'compliance_score',
        'errors_made',
        'corrections_made',
        'quality_score',
        'productivity_score',
        'engagement_score',
        'overall_performance_score',
        'performance_rating',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'activity_rate' => 'decimal:2',
        'average_activities_per_day' => 'decimal:2',
        'incident_resolution_rate' => 'decimal:2',
        'average_resolution_time' => 'decimal:2',
        'task_completion_rate' => 'decimal:2',
        'session_duration_avg' => 'decimal:2',
        'compliance_score' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'productivity_score' => 'decimal:2',
        'engagement_score' => 'decimal:2',
        'overall_performance_score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        return 'Poor';
    }
}
