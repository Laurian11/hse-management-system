<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class TrainingRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'user_id',
        'training_session_id',
        'training_plan_id',
        'training_need_id',
        'training_title',
        'training_description',
        'training_type',
        'training_date',
        'completed_at',
        'duration_hours',
        'attendance_id',
        'attendance_status',
        'competency_assessment_id',
        'competency_status',
        'certificate_id',
        'certificate_issued',
        'certificate_issue_date',
        'certificate_expiry_date',
        'status',
        'notes',
        'feedback',
    ];

    protected $casts = [
        'training_date' => 'date',
        'completed_at' => 'datetime',
        'certificate_issue_date' => 'date',
        'certificate_expiry_date' => 'date',
        'certificate_issued' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($record) {
            if (empty($record->reference_number)) {
                $record->reference_number = 'TR-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($record) {
            ActivityLog::log('create', 'training', 'TrainingRecord', $record->id, "Created training record: {$record->training_title}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function trainingPlan(): BelongsTo
    {
        return $this->belongsTo(TrainingPlan::class, 'training_plan_id');
    }

    public function trainingNeed(): BelongsTo
    {
        return $this->belongsTo(TrainingNeedsAnalysis::class, 'training_need_id');
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(TrainingAttendance::class, 'attendance_id');
    }

    public function competencyAssessment(): BelongsTo
    {
        return $this->belongsTo(CompetencyAssessment::class, 'competency_assessment_id');
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(TrainingCertificate::class, 'certificate_id');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeWithCertificates($query)
    {
        return $query->where('certificate_issued', true);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('certificate_expiry_date')
                    ->where('certificate_expiry_date', '<=', now()->addDays($days))
                    ->where('certificate_expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('certificate_expiry_date')
                    ->where('certificate_expiry_date', '<', now());
    }

    // Helper Methods
    public function isExpired(): bool
    {
        return $this->certificate_expiry_date && $this->certificate_expiry_date->isPast();
    }

    public function daysUntilExpiry(): ?int
    {
        if (!$this->certificate_expiry_date) {
            return null;
        }
        return now()->diffInDays($this->certificate_expiry_date, false);
    }
}
