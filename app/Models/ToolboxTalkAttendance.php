<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolboxTalkAttendance extends Model
{
    protected $fillable = [
        'toolbox_talk_id',
        'employee_id',
        'employee_name',
        'employee_id_number',
        'department',
        'attendance_status',
        'check_in_time',
        'check_out_time',
        'check_in_method',
        'biometric_template_id',
        'device_id',
        'check_in_latitude',
        'check_in_longitude',
        'absence_reason',
        'is_supervisor',
        'is_presenter',
        'digital_signature',
        'signature_ip_address',
        'signature_timestamp',
        'participation_notes',
        'engagement_score',
        'feedback_responses',
        'assigned_actions',
        'action_acknowledged',
        'action_acknowledged_at',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'signature_timestamp' => 'datetime',
        'digital_signature' => 'array',
        'feedback_responses' => 'array',
        'assigned_actions' => 'array',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'is_supervisor' => 'boolean',
        'is_presenter' => 'boolean',
        'action_acknowledged' => 'boolean',
        'engagement_score' => 'integer',
    ];

    public function toolboxTalk(): BelongsTo
    {
        return $this->belongsTo(ToolboxTalk::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function scopePresent($query)
    {
        return $query->where('attendance_status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('attendance_status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('attendance_status', 'late');
    }

    public function scopeExcused($query)
    {
        return $query->where('attendance_status', 'excused');
    }

    public function scopeBiometric($query)
    {
        return $query->where('check_in_method', 'biometric');
    }

    public function scopeManual($query)
    {
        return $query->where('check_in_method', 'manual');
    }

    public function scopeMobile($query)
    {
        return $query->where('check_in_method', 'mobile_app');
    }

    public function scopeSupervisor($query)
    {
        return $query->where('is_supervisor', true);
    }

    public function scopePresenter($query)
    {
        return $query->where('is_presenter', true);
    }

    public function isPresent(): bool
    {
        return $this->attendance_status === 'present';
    }

    public function isAbsent(): bool
    {
        return $this->attendance_status === 'absent';
    }

    public function isLate(): bool
    {
        return $this->attendance_status === 'late';
    }

    public function isBiometric(): bool
    {
        return $this->check_in_method === 'biometric';
    }

    public function hasSignature(): bool
    {
        return !empty($this->digital_signature);
    }

    public function getAttendanceStatusBadge(): string
    {
        $badges = [
            'present' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Present</span>',
            'absent' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Absent</span>',
            'late' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Late</span>',
            'excused' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Excused</span>',
        ];

        return $badges[$this->attendance_status] ?? '';
    }

    public function getCheckInMethodBadge(): string
    {
        $badges = [
            'biometric' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Biometric</span>',
            'manual' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Manual</span>',
            'mobile_app' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Mobile</span>',
            'video_conference' => '<span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">Video</span>',
        ];

        return $badges[$this->check_in_method] ?? '';
    }

    public function getEngagementRating(): string
    {
        if (!$this->engagement_score) {
            return 'Not Rated';
        }

        $ratings = [
            1 => 'Poor',
            2 => 'Fair', 
            3 => 'Good',
            4 => 'Very Good',
            5 => 'Excellent',
        ];

        return $ratings[$this->engagement_score] ?? 'Unknown';
    }

    public function checkInWithBiometric(array $biometricData): void
    {
        $this->update([
            'check_in_time' => now(),
            'check_in_method' => 'biometric',
            'biometric_template_id' => $biometricData['template_id'] ?? null,
            'device_id' => $biometricData['device_id'] ?? null,
            'check_in_latitude' => $biometricData['latitude'] ?? null,
            'check_in_longitude' => $biometricData['longitude'] ?? null,
            'attendance_status' => 'present',
        ]);
    }

    public function checkInManually(array $manualData): void
    {
        $this->update([
            'check_in_time' => now(),
            'check_in_method' => 'manual',
            'check_in_latitude' => $manualData['latitude'] ?? null,
            'check_in_longitude' => $manualData['longitude'] ?? null,
            'attendance_status' => $manualData['status'] ?? 'present',
            'absence_reason' => $manualData['absence_reason'] ?? null,
        ]);
    }

    public function addDigitalSignature(array $signatureData): void
    {
        $this->update([
            'digital_signature' => $signatureData['signature'],
            'signature_ip_address' => request()->ip(),
            'signature_timestamp' => now(),
        ]);
    }

    public function acknowledgeActions(): void
    {
        $this->update([
            'action_acknowledged' => true,
            'action_acknowledged_at' => now(),
        ]);
    }

    public function calculateParticipationScore(): int
    {
        $score = 3; // Base score

        // Add points for engagement
        if ($this->feedback_responses && count($this->feedback_responses) > 0) {
            $score += 1;
        }

        // Add points for being on time
        if ($this->attendance_status === 'present') {
            $score += 1;
        }

        // Deduct points for being late
        if ($this->attendance_status === 'late') {
            $score -= 1;
        }

        return max(1, min(5, $score));
    }
}
