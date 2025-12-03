<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ToolboxTalk extends Model
{
    protected $fillable = [
        'reference_number',
        'company_id',
        'department_id',
        'supervisor_id',
        'topic_id',
        'title',
        'description',
        'status',
        'scheduled_date',
        'start_time',
        'end_time',
        'location',
        'talk_type',
        'duration_minutes',
        'materials',
        'photos',
        'action_items',
        'supervisor_notes',
        'key_points',
        'regulatory_references',
        'biometric_required',
        'zk_device_id',
        'latitude',
        'longitude',
        'total_attendees',
        'present_attendees',
        'attendance_rate',
        'average_feedback_score',
        'is_recurring',
        'recurrence_pattern',
        'next_occurrence',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'materials' => 'array',
        'photos' => 'array',
        'action_items' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'attendance_rate' => 'decimal:2',
        'average_feedback_score' => 'decimal:2',
        'next_occurrence' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ToolboxTalkTopic::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ToolboxTalkAttendance::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(ToolboxTalkFeedback::class);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'TT';
        $year = date('Y');
        $month = date('m');
        $sequence = $this->whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count() + 1;
        
        return "{$prefix}-{$year}{$month}-{$sequence}";
    }

    public function calculateAttendanceRate(): void
    {
        $this->attendance_rate = $this->total_attendees > 0 
            ? ($this->present_attendees / $this->total_attendees) * 100 
            : 0;
        $this->save();
    }

    public function calculateAverageFeedbackScore(): void
    {
        $averageScore = $this->feedback()
            ->whereNotNull('overall_rating')
            ->avg('overall_rating');
        
        $this->average_feedback_score = $averageScore ? round($averageScore, 2) : null;
        $this->save();
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now())
                     ->orderBy('scheduled_date');
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_date', '<', now())
                     ->orderBy('scheduled_date', 'desc');
    }
}
