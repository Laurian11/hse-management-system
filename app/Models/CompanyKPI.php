<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyKPI extends Model
{
    protected $table = 'company_kpis';
    
    protected $fillable = [
        'company_id',
        'recorded_date',
        'period_type',
        'total_employees',
        'active_employees',
        'new_employees',
        'terminated_employees',
        'employee_turnover_rate',
        'total_incidents',
        'incidents_this_period',
        'incidents_resolved',
        'incidents_pending',
        'incident_rate',
        'incident_resolution_rate',
        'total_trainings',
        'trainings_completed',
        'trainings_pending',
        'training_completion_rate',
        'employees_trained',
        'training_coverage',
        'total_audits',
        'audits_passed',
        'audits_failed',
        'compliance_rate',
        'non_conformances',
        'non_conformances_resolved',
        'total_risk_assessments',
        'high_risk_items',
        'medium_risk_items',
        'low_risk_items',
        'average_risk_score',
        'ppe_items_issued',
        'ppe_inspections_due',
        'ppe_inspections_completed',
        'ppe_compliance_rate',
        'toolbox_talks_scheduled',
        'toolbox_talks_completed',
        'toolbox_talks_attendance',
        'toolbox_talk_completion_rate',
        'average_attendance_rate',
        'safety_budget_allocated',
        'safety_budget_spent',
        'safety_budget_utilization',
        'overall_safety_score',
        'safety_rating',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'employee_turnover_rate' => 'decimal:2',
        'incident_rate' => 'decimal:2',
        'incident_resolution_rate' => 'decimal:2',
        'training_completion_rate' => 'decimal:2',
        'training_coverage' => 'decimal:2',
        'compliance_rate' => 'decimal:2',
        'average_risk_score' => 'decimal:2',
        'ppe_compliance_rate' => 'decimal:2',
        'toolbox_talk_completion_rate' => 'decimal:2',
        'average_attendance_rate' => 'decimal:2',
        'safety_budget_allocated' => 'decimal:2',
        'safety_budget_spent' => 'decimal:2',
        'safety_budget_utilization' => 'decimal:2',
        'overall_safety_score' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getSafetyRatingAttribute($value)
    {
        if (!$value && $this->overall_safety_score) {
            return $this->calculateSafetyRating($this->overall_safety_score);
        }
        return $value;
    }

    protected function calculateSafetyRating($score)
    {
        if ($score >= 90) return 'Excellent';
        if ($score >= 75) return 'Good';
        if ($score >= 60) return 'Fair';
        return 'Poor';
    }
}
