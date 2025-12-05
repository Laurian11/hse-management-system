<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SpillIncident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'incident_date', 'incident_time', 'location',
        'spill_type', 'substance_description', 'estimated_volume', 'volume_unit', 'severity',
        'cause', 'immediate_response', 'containment_measures', 'cleanup_procedures', 'status',
        'environmental_impact', 'environmental_impact_description', 'regulatory_notification_required',
        'regulatory_notification_details', 'reported_by', 'investigated_by', 'department_id',
        'preventive_measures', 'notes', 'attachments',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'incident_time' => 'datetime',
        'estimated_volume' => 'decimal:2',
        'environmental_impact' => 'boolean',
        'regulatory_notification_required' => 'boolean',
        'attachments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($incident) {
            if (empty($incident->reference_number)) {
                $incident->reference_number = $incident->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function investigatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigated_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'SPL-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
