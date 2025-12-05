<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Audit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'audit_type', 'title', 'description',
        'scope', 'scope_description', 'planned_start_date', 'planned_end_date',
        'actual_start_date', 'actual_end_date', 'lead_auditor_id', 'audit_team',
        'department_id', 'applicable_standards', 'audit_criteria', 'status', 'result',
        'total_findings', 'critical_findings', 'major_findings', 'minor_findings',
        'observations', 'executive_summary', 'conclusion', 'attachments',
        'follow_up_date', 'follow_up_required', 'follow_up_completed',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'follow_up_date' => 'date',
        'audit_team' => 'array',
        'applicable_standards' => 'array',
        'audit_criteria' => 'array',
        'attachments' => 'array',
        'follow_up_required' => 'boolean',
        'follow_up_completed' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($audit) {
            if (empty($audit->reference_number)) {
                $audit->reference_number = $audit->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function leadAuditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_auditor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(AuditFinding::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeInternal($query)
    {
        return $query->where('audit_type', 'internal');
    }

    public function scopeExternal($query)
    {
        return $query->where('audit_type', 'external');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('follow_up_required', true)
                     ->where('follow_up_completed', false);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'AUD-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
