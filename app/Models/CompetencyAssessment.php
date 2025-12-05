<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class CompetencyAssessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'training_session_id',
        'user_id',
        'assessor_id',
        'title',
        'description',
        'assessment_type',
        'competency_status',
        'score_percentage',
        'passing_score',
        'passed',
        'knowledge_test_results',
        'practical_evaluation_results',
        'observation_checklist',
        'assessor_notes',
        'recommendations',
        'assessment_date',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'knowledge_test_results' => 'array',
        'practical_evaluation_results' => 'array',
        'observation_checklist' => 'array',
        'assessment_date' => 'datetime',
        'completed_at' => 'datetime',
        'passed' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($assessment) {
            if (empty($assessment->reference_number)) {
                $assessment->reference_number = 'CA-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($assessment) {
            ActivityLog::log('create', 'training', 'CompetencyAssessment', $assessment->id, "Created competency assessment: {$assessment->title}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(TrainingCertificate::class, 'competency_assessment_id');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeCompetent($query)
    {
        return $query->where('competency_status', 'competent')
                    ->orWhere('competency_status', 'highly_competent');
    }

    public function scopeNotCompetent($query)
    {
        return $query->where('competency_status', 'not_competent');
    }

    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    // Helper Methods
    public function complete(): bool
    {
        // Auto-determine passed status based on score
        $passed = $this->score_percentage >= $this->passing_score;
        
        // Auto-determine competency status
        $competencyStatus = 'not_competent';
        if ($this->score_percentage >= 90) {
            $competencyStatus = 'highly_competent';
        } elseif ($this->score_percentage >= $this->passing_score) {
            $competencyStatus = 'competent';
        } elseif ($this->score_percentage >= ($this->passing_score * 0.7)) {
            $competencyStatus = 'partially_competent';
        }

        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'passed' => $passed,
            'competency_status' => $competencyStatus,
        ]);
    }

    public function isCompetent(): bool
    {
        return in_array($this->competency_status, ['competent', 'highly_competent']);
    }
}
