<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PermitLicense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'permit_license_number', 'name', 'description',
        'type', 'category', 'issuing_authority', 'issue_date', 'expiry_date',
        'renewal_due_date', 'status', 'responsible_person_id', 'department_id',
        'file_path', 'conditions', 'renewal_requirements', 'last_renewal_date',
        'renewal_fee', 'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_due_date' => 'date',
        'last_renewal_date' => 'date',
        'renewal_fee' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($permit) {
            if (empty($permit->reference_number)) {
                $permit->reference_number = $permit->generateReferenceNumber();
            }
            // Auto-calculate renewal due date (60 days before expiry)
            if ($permit->expiry_date && !$permit->renewal_due_date) {
                $permit->renewal_due_date = $permit->expiry_date->copy()->subDays(60);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function responsiblePerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_person_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon($query, $days = 60)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::now());
    }

    public function scopeRequiringRenewal($query)
    {
        return $query->whereNotNull('renewal_due_date')
            ->where('renewal_due_date', '<=', Carbon::now())
            ->where('status', '!=', 'expired');
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'PL-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    public function isExpiringSoon($days = 60): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        return $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(Carbon::now()) <= $days;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
