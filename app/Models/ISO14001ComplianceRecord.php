<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ISO14001ComplianceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'clause_reference', 'requirement', 'description',
        'compliance_status', 'evidence', 'assessment_date', 'assessed_by', 'findings',
        'corrective_action', 'corrective_action_due_date', 'corrective_action_assigned_to',
        'corrective_action_completed', 'corrective_action_completed_date', 'verified_by',
        'verification_date', 'verification_notes', 'notes', 'attachments',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'corrective_action_due_date' => 'date',
        'corrective_action_completed_date' => 'date',
        'verification_date' => 'date',
        'corrective_action_completed' => 'boolean',
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

    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function correctiveActionAssignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrective_action_assigned_to');
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

    public function scopeRequiresAction($query)
    {
        return $query->where('corrective_action_completed', false)
                     ->whereNotNull('corrective_action');
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'ISO-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
