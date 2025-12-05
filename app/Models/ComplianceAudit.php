<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ComplianceAudit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'audit_title', 'description', 'audit_type',
        'standard', 'audit_date', 'audit_end_date', 'auditor_id', 'external_auditor_name',
        'auditor_organization', 'audit_scope', 'audit_status', 'total_findings',
        'major_non_conformances', 'minor_non_conformances', 'observations',
        'positive_findings', 'overall_result', 'summary', 'strengths', 'weaknesses',
        'recommendations', 'corrective_action_due_date', 'follow_up_audit_date',
        'audit_report_path', 'notes',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'audit_end_date' => 'date',
        'corrective_action_due_date' => 'date',
        'follow_up_audit_date' => 'date',
        'audit_scope' => 'array',
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

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('audit_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('audit_status', $status);
    }

    public function scopeByStandard($query, $standard)
    {
        return $query->where('standard', $standard);
    }

    public function scopeByResult($query, $result)
    {
        return $query->where('overall_result', $result);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'CA-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
