<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class TrainingSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'training_plan_id',
        'title',
        'description',
        'session_type',
        'scheduled_start',
        'scheduled_end',
        'actual_start',
        'actual_end',
        'duration_minutes',
        'location_name',
        'location_address',
        'room_number',
        'virtual_meeting_link',
        'instructor_id',
        'external_instructor_name',
        'co_instructors',
        'max_participants',
        'min_participants',
        'registered_participants',
        'assigned_materials',
        'status',
        'cancellation_reason',
        'postponed_to',
        'completed_by',
        'completed_at',
        'completion_notes',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'postponed_to' => 'datetime',
        'co_instructors' => 'array',
        'assigned_materials' => 'array',
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($session) {
            if (empty($session->reference_number)) {
                $session->reference_number = 'TS-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($session) {
            ActivityLog::log('create', 'training', 'TrainingSession', $session->id, "Created training session: {$session->title}");
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

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(TrainingAttendance::class, 'training_session_id');
    }

    public function competencyAssessments(): HasMany
    {
        return $this->hasMany(CompetencyAssessment::class, 'training_session_id');
    }

    public function trainingRecords(): HasMany
    {
        return $this->hasMany(TrainingRecord::class, 'training_session_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(TrainingCertificate::class, 'training_session_id');
    }

    public function effectivenessEvaluations(): HasMany
    {
        return $this->hasMany(TrainingEffectivenessEvaluation::class, 'training_session_id');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_start', '>', now())
                    ->where('status', 'scheduled');
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_end', '<', now());
    }

    // Helper Methods
    public function start(): bool
    {
        return $this->update([
            'status' => 'in_progress',
            'actual_start' => now(),
        ]);
    }

    public function complete(User $completer, string $notes = null): bool
    {
        return $this->update([
            'status' => 'completed',
            'actual_end' => now(),
            'completed_by' => $completer->id,
            'completed_at' => now(),
            'completion_notes' => $notes ?? $this->completion_notes,
        ]);
    }

    public function cancel(string $reason): bool
    {
        return $this->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);
    }

    public function postpone(\DateTime $newDate): bool
    {
        return $this->update([
            'status' => 'postponed',
            'postponed_to' => $newDate,
        ]);
    }

    public function getAttendanceCountAttribute(): int
    {
        return $this->attendances()->where('attendance_status', 'attended')->count();
    }

    public function getAttendancePercentageAttribute(): float
    {
        $total = $this->attendances()->count();
        if ($total === 0) return 0;
        
        $attended = $this->attendances()->where('attendance_status', 'attended')->count();
        return round(($attended / $total) * 100, 2);
    }
}
