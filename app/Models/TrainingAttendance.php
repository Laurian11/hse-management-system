<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'training_session_id',
        'user_id',
        'attendance_status',
        'registered_at',
        'checked_in_at',
        'checked_out_at',
        'attendance_percentage',
        'registration_method',
        'notes',
        'absence_reason',
        'certificate_eligible',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'certificate_eligible' => 'boolean',
    ];

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

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeAttended($query)
    {
        return $query->where('attendance_status', 'attended');
    }

    public function scopeAbsent($query)
    {
        return $query->where('attendance_status', 'absent');
    }

    // Helper Methods
    public function checkIn(): bool
    {
        return $this->update([
            'attendance_status' => 'attended',
            'checked_in_at' => now(),
        ]);
    }

    public function checkOut(): bool
    {
        return $this->update([
            'checked_out_at' => now(),
        ]);
    }

    public function markAbsent(string $reason = null): bool
    {
        return $this->update([
            'attendance_status' => 'absent',
            'absence_reason' => $reason ?? $this->absence_reason,
        ]);
    }
}
