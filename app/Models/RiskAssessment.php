<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class RiskAssessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'hazard_id',
        'created_by',
        'assigned_to',
        'department_id',
        'title',
        'description',
        'assessment_type',
        'severity',
        'likelihood',
        'severity_score',
        'likelihood_score',
        'risk_score',
        'risk_level',
        'existing_controls',
        'existing_controls_effectiveness',
        'residual_severity',
        'residual_likelihood',
        'residual_risk_score',
        'residual_risk_level',
        'is_alarp',
        'alarp_justification',
        'status',
        'assessment_date',
        'next_review_date',
        'review_frequency',
        'related_incident_id',
        'related_jsa_id',
        'approved_by',
        'approved_at',
        'is_active',
    ];

    protected $casts = [
        'severity_score' => 'integer',
        'likelihood_score' => 'integer',
        'risk_score' => 'integer',
        'residual_risk_score' => 'integer',
        'is_alarp' => 'boolean',
        'is_active' => 'boolean',
        'assessment_date' => 'date',
        'next_review_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($assessment) {
            if (empty($assessment->reference_number)) {
                $assessment->reference_number = 'RA-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
            
            // Auto-calculate risk score
            if ($assessment->severity_score && $assessment->likelihood_score) {
                $assessment->risk_score = $assessment->severity_score * $assessment->likelihood_score;
                $assessment->risk_level = static::calculateRiskLevel($assessment->risk_score);
            }
        });

        static::updating(function ($assessment) {
            // Recalculate risk score if severity or likelihood changed
            if ($assessment->isDirty(['severity_score', 'likelihood_score'])) {
                if ($assessment->severity_score && $assessment->likelihood_score) {
                    $assessment->risk_score = $assessment->severity_score * $assessment->likelihood_score;
                    $assessment->risk_level = static::calculateRiskLevel($assessment->risk_score);
                }
            }
        });

        static::created(function ($assessment) {
            ActivityLog::log('create', 'risk_assessment', 'RiskAssessment', $assessment->id, "Created risk assessment: {$assessment->title}");
        });
    }

    public static function calculateRiskLevel($score): string
    {
        if ($score >= 20) return 'extreme';
        if ($score >= 15) return 'critical';
        if ($score >= 10) return 'high';
        if ($score >= 5) return 'medium';
        return 'low';
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function hazard(): BelongsTo
    {
        return $this->belongsTo(Hazard::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function relatedIncident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'related_incident_id');
    }

    public function relatedJSA(): BelongsTo
    {
        return $this->belongsTo(JSA::class, 'related_jsa_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function controlMeasures(): HasMany
    {
        return $this->hasMany(ControlMeasure::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(RiskReview::class);
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical', 'extreme']);
    }

    public function scopeDueForReview($query)
    {
        return $query->where('next_review_date', '<=', now());
    }

    // Helper Methods
    public function getRiskLevelColor(): string
    {
        $colors = [
            'low' => 'text-green-600 bg-green-100',
            'medium' => 'text-yellow-600 bg-yellow-100',
            'high' => 'text-orange-600 bg-orange-100',
            'critical' => 'text-red-600 bg-red-100',
            'extreme' => 'text-red-800 bg-red-200',
        ];

        return $colors[$this->risk_level] ?? 'text-gray-600 bg-gray-100';
    }

    public function isOverdueForReview(): bool
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }
}
