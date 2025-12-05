<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class WorkPermit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'work_permit_type_id',
        'requested_by',
        'department_id',
        'work_title',
        'work_description',
        'work_location',
        'work_start_date',
        'work_end_date',
        'validity_hours',
        'expiry_date',
        'risk_assessment_id',
        'jsa_id',
        'safety_precautions',
        'required_equipment',
        'gas_test_required',
        'gas_test_results',
        'gas_test_date',
        'gas_tester_id',
        'fire_watch_required',
        'fire_watch_person_id',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'actual_start_date',
        'actual_end_date',
        'closed_by',
        'closed_at',
        'closure_notes',
        'verification_completed',
        'verified_by',
        'verified_at',
        'verification_notes',
        'gcla_compliance_required',
        'gcla_compliance_notes',
        'workers',
        'supervisors',
        'emergency_procedures',
        'notes',
    ];

    protected $casts = [
        'work_start_date' => 'datetime',
        'work_end_date' => 'datetime',
        'expiry_date' => 'datetime',
        'gas_test_date' => 'datetime',
        'approved_at' => 'datetime',
        'actual_start_date' => 'datetime',
        'actual_end_date' => 'datetime',
        'closed_at' => 'datetime',
        'verified_at' => 'datetime',
        'safety_precautions' => 'array',
        'required_equipment' => 'array',
        'workers' => 'array',
        'supervisors' => 'array',
        'gas_test_required' => 'boolean',
        'fire_watch_required' => 'boolean',
        'verification_completed' => 'boolean',
        'gcla_compliance_required' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($permit) {
            if (empty($permit->reference_number)) {
                $permit->reference_number = $permit->generateReferenceNumber();
            }
            
            // Auto-calculate expiry date
            if ($permit->work_start_date && $permit->validity_hours) {
                $permit->expiry_date = Carbon::parse($permit->work_start_date)->addHours($permit->validity_hours);
            }
        });
    }

    public function generateReferenceNumber(): string
    {
        $company = Company::find($this->company_id);
        $prefix = $company ? strtoupper(substr($company->name, 0, 3)) : 'WP';
        $year = date('Y');
        $count = self::where('company_id', $this->company_id)
            ->whereYear('created_at', $year)
            ->count() + 1;
        
        return sprintf('%s-WP-%s-%04d', $prefix, $year, $count);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function workPermitType(): BelongsTo
    {
        return $this->belongsTo(WorkPermitType::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function riskAssessment(): BelongsTo
    {
        return $this->belongsTo(RiskAssessment::class);
    }

    public function jsa(): BelongsTo
    {
        return $this->belongsTo(JSA::class);
    }

    public function gasTester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gas_tester_id');
    }

    public function fireWatchPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fire_watch_person_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(WorkPermitApproval::class);
    }

    public function gcaLogs(): HasMany
    {
        return $this->hasMany(GCALog::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'submitted', 'under_review']);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<=', now())
            ->where('status', '!=', 'closed');
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast() && $this->status !== 'closed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, ['submitted', 'under_review']);
    }

    public function canBeClosed(): bool
    {
        return in_array($this->status, ['active', 'suspended']);
    }
}
