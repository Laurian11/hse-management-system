<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class EmissionMonitoringRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'monitoring_type', 'source', 'location',
        'monitoring_date', 'monitoring_time', 'parameter', 'measured_value', 'unit',
        'permissible_limit', 'compliance_status', 'weather_conditions', 'operating_conditions',
        'monitored_by', 'verified_by', 'verification_date', 'corrective_action', 'notes', 'attachments',
    ];

    protected $casts = [
        'monitoring_date' => 'date',
        'monitoring_time' => 'datetime',
        'verification_date' => 'date',
        'measured_value' => 'decimal:4',
        'permissible_limit' => 'decimal:4',
        'attachments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($record) {
            if (empty($record->reference_number)) {
                $record->reference_number = $record->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function monitoredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'monitored_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeCompliant($query)
    {
        return $query->where('compliance_status', 'compliant');
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('compliance_status', 'non_compliant');
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'EMR-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
