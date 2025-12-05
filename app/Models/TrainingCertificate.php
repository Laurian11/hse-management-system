<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;
use Illuminate\Support\Str;

class TrainingCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'certificate_number',
        'company_id',
        'user_id',
        'training_record_id',
        'training_session_id',
        'competency_assessment_id',
        'certificate_title',
        'certificate_description',
        'certificate_type',
        'issue_date',
        'expiry_date',
        'has_expiry',
        'validity_months',
        'issued_by',
        'issuing_organization',
        'issuing_authority',
        'certificate_file_path',
        'digital_signature',
        'verification_code',
        'status',
        'revocation_reason',
        'revoked_by',
        'revoked_at',
        'expiry_alert_sent_60_days',
        'expiry_alert_sent_30_days',
        'expiry_alert_sent_7_days',
        'last_alert_sent_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'has_expiry' => 'boolean',
        'revoked_at' => 'datetime',
        'last_alert_sent_at' => 'datetime',
        'expiry_alert_sent_60_days' => 'boolean',
        'expiry_alert_sent_30_days' => 'boolean',
        'expiry_alert_sent_7_days' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = 'CERT-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
            
            if (empty($certificate->verification_code)) {
                $certificate->verification_code = Str::upper(Str::random(12));
            }
        });

        static::created(function ($certificate) {
            ActivityLog::log('create', 'training', 'TrainingCertificate', $certificate->id, "Issued certificate: {$certificate->certificate_number}");
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

    public function trainingRecord(): BelongsTo
    {
        return $this->belongsTo(TrainingRecord::class, 'training_record_id');
    }

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function competencyAssessment(): BelongsTo
    {
        return $this->belongsTo(CompetencyAssessment::class, 'competency_assessment_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function revoker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere(function($q) {
                        $q->where('has_expiry', true)
                          ->where('expiry_date', '<', now())
                          ->where('status', 'active');
                    });
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('has_expiry', true)
                    ->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now())
                    ->where('status', 'active');
    }

    public function scopeNeedsAlert($query, $days)
    {
        $alertField = match($days) {
            60 => 'expiry_alert_sent_60_days',
            30 => 'expiry_alert_sent_30_days',
            7 => 'expiry_alert_sent_7_days',
            default => null,
        };

        if (!$alertField) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        return $query->where('has_expiry', true)
                    ->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now())
                    ->where('status', 'active')
                    ->where($alertField, false);
    }

    // Helper Methods
    public function isExpired(): bool
    {
        return $this->has_expiry && 
               $this->expiry_date && 
               $this->expiry_date->isPast() &&
               $this->status === 'active';
    }

    public function daysUntilExpiry(): ?int
    {
        if (!$this->has_expiry || !$this->expiry_date) {
            return null;
        }
        return now()->diffInDays($this->expiry_date, false);
    }

    public function revoke(User $revoker, string $reason): bool
    {
        return $this->update([
            'status' => 'revoked',
            'revoked_by' => $revoker->id,
            'revoked_at' => now(),
            'revocation_reason' => $reason,
        ]);
    }

    public function markExpired(): bool
    {
        if ($this->isExpired()) {
            return $this->update([
                'status' => 'expired',
            ]);
        }
        return false;
    }

    public function sendExpiryAlert(int $days): bool
    {
        $alertField = match($days) {
            60 => 'expiry_alert_sent_60_days',
            30 => 'expiry_alert_sent_30_days',
            7 => 'expiry_alert_sent_7_days',
            default => null,
        };

        if (!$alertField) {
            return false;
        }

        return $this->update([
            $alertField => true,
            'last_alert_sent_at' => now(),
        ]);
    }
}
