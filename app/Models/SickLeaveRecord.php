<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SickLeaveRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'user_id', 'leave_start_date', 'leave_end_date',
        'days_absent', 'leave_type', 'reason', 'medical_certificate_provided', 'work_related',
        'related_incident_id', 'related_first_aid_id', 'treatment_received', 'follow_up_required',
        'return_to_work_date', 'return_to_work_status', 'medical_clearance_notes', 'recorded_by',
        'notes', 'attachments',
    ];

    protected $casts = [
        'leave_start_date' => 'date',
        'leave_end_date' => 'date',
        'return_to_work_date' => 'date',
        'work_related' => 'boolean',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedIncident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'related_incident_id');
    }

    public function relatedFirstAid(): BelongsTo
    {
        return $this->belongsTo(FirstAidLogbookEntry::class, 'related_first_aid_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeWorkRelated($query)
    {
        return $query->where('work_related', true);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'SLR-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
