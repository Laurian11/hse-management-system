<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GCALog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gca_logs';

    protected $fillable = [
        'company_id',
        'reference_number',
        'work_permit_id',
        'created_by',
        'gcla_type',
        'check_date',
        'location',
        'description',
        'checklist_items',
        'findings',
        'compliance_status',
        'corrective_actions',
        'action_assigned_to',
        'action_due_date',
        'action_completed',
        'action_completed_at',
        'verified_by',
        'verified_at',
        'verification_notes',
    ];

    protected $casts = [
        'check_date' => 'datetime',
        'action_due_date' => 'date',
        'action_completed' => 'boolean',
        'action_completed_at' => 'datetime',
        'verified_at' => 'datetime',
        'checklist_items' => 'array',
        'findings' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($log) {
            if (empty($log->reference_number)) {
                $log->reference_number = $log->generateReferenceNumber();
            }
        });
    }

    public function generateReferenceNumber(): string
    {
        $company = Company::find($this->company_id);
        $prefix = $company ? strtoupper(substr($company->name, 0, 3)) : 'GCA';
        $year = date('Y');
        $count = self::where('company_id', $this->company_id)
            ->whereYear('created_at', $year)
            ->count() + 1;
        
        return sprintf('%s-GCA-%s-%04d', $prefix, $year, $count);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function workPermit(): BelongsTo
    {
        return $this->belongsTo(WorkPermit::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actionAssignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_assigned_to');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('compliance_status', 'non_compliant');
    }

    public function scopePendingActions($query)
    {
        return $query->where('action_completed', false)
            ->whereNotNull('action_assigned_to');
    }
}
