<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class RiskReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'risk_assessment_id',
        'reviewed_by',
        'assigned_to',
        'review_type',
        'trigger_description',
        'triggering_incident_id',
        'scheduled_date',
        'review_date',
        'due_date',
        'status',
        'review_findings',
        'changes_identified',
        'control_effectiveness',
        'updated_severity',
        'updated_likelihood',
        'updated_risk_score',
        'updated_risk_level',
        'risk_change',
        'risk_change_reason',
        'next_review_date',
        'next_review_frequency',
        'requires_new_controls',
        'requires_control_modification',
        'recommended_actions',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'review_date' => 'date',
        'due_date' => 'date',
        'next_review_date' => 'date',
        'updated_risk_score' => 'integer',
        'requires_new_controls' => 'boolean',
        'requires_control_modification' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($review) {
            if (empty($review->reference_number)) {
                $review->reference_number = 'RR-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($review) {
            $assessmentTitle = $review->riskAssessment->title ?? 'N/A';
            ActivityLog::log('create', 'risk_assessment', 'RiskReview', $review->id, "Created risk review for assessment: {$assessmentTitle}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function riskAssessment(): BelongsTo
    {
        return $this->belongsTo(RiskAssessment::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function triggeringIncident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'triggering_incident_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByReviewType($query, $type)
    {
        return $query->where('review_type', $type);
    }

    public function scopeScheduled($query)
    {
        return $query->where('review_type', 'scheduled');
    }

    public function scopeTriggered($query)
    {
        return $query->where('review_type', '!=', 'scheduled');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['scheduled', 'in_progress']);
    }

    // Helper Methods
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               in_array($this->status, ['scheduled', 'in_progress']);
    }
}
