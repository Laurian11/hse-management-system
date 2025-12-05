<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class TrainingEffectivenessEvaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'training_plan_id',
        'training_session_id',
        'title',
        'description',
        'evaluation_level',
        'reaction_scores',
        'reaction_feedback',
        'average_knowledge_score',
        'average_skill_score',
        'pass_rate_percentage',
        'behavior_observations',
        'behavior_improvement_percentage',
        'behavior_evaluation_date',
        'kpi_improvements',
        'incident_reduction_percentage',
        'audit_score_improvement',
        'cost_savings',
        'business_impact_notes',
        'overall_effectiveness',
        'effectiveness_notes',
        'recommendations',
        'evaluation_start_date',
        'evaluation_end_date',
        'evaluation_date',
        'evaluated_by',
        'evaluation_team',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reaction_scores' => 'array',
        'behavior_observations' => 'array',
        'kpi_improvements' => 'array',
        'evaluation_team' => 'array',
        'behavior_evaluation_date' => 'date',
        'evaluation_start_date' => 'date',
        'evaluation_end_date' => 'date',
        'evaluation_date' => 'date',
        'average_knowledge_score' => 'decimal:2',
        'average_skill_score' => 'decimal:2',
        'cost_savings' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($evaluation) {
            if (empty($evaluation->reference_number)) {
                $evaluation->reference_number = 'TEE-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($evaluation) {
            ActivityLog::log('create', 'training', 'TrainingEffectivenessEvaluation', $evaluation->id, "Created effectiveness evaluation: {$evaluation->title}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function trainingPlan(): BelongsTo
    {
        return $this->belongsTo(TrainingPlan::class, 'training_plan_id');
    }

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('evaluation_level', $level);
    }

    public function scopeEffective($query)
    {
        return $query->whereIn('overall_effectiveness', ['effective', 'highly_effective']);
    }

    // Helper Methods
    public function getLevelLabel(): string
    {
        return match($this->evaluation_level) {
            'level_1' => 'Level 1: Reaction',
            'level_2' => 'Level 2: Learning',
            'level_3' => 'Level 3: Behavior',
            'level_4' => 'Level 4: Results',
            default => $this->evaluation_level,
        };
    }
}
