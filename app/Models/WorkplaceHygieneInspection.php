<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class WorkplaceHygieneInspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'inspection_date', 'area_inspected', 'department_id',
        'inspected_by', 'inspection_items', 'findings', 'non_compliance_details',
        'corrective_actions', 'corrective_action_due_date', 'corrective_action_assigned_to',
        'corrective_action_completed', 'corrective_action_completed_date', 'verified_by',
        'verification_date', 'overall_status', 'notes', 'attachments',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'corrective_action_due_date' => 'date',
        'corrective_action_completed_date' => 'date',
        'verification_date' => 'date',
        'inspection_items' => 'array',
        'findings' => 'array',
        'corrective_action_completed' => 'boolean',
        'attachments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($inspection) {
            if (empty($inspection->reference_number)) {
                $inspection->reference_number = $inspection->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
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

    public function scopeByStatus($query, $status)
    {
        return $query->where('overall_status', $status);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'WHI-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
