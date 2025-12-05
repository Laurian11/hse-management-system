<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ComplianceRequirement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'requirement_title', 'description',
        'regulatory_body', 'regulation_code', 'requirement_type', 'category',
        'effective_date', 'compliance_due_date', 'compliance_status',
        'responsible_person_id', 'department_id', 'compliance_evidence',
        'non_compliance_issues', 'corrective_actions', 'last_review_date',
        'next_review_date', 'review_frequency_months', 'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'compliance_due_date' => 'date',
        'last_review_date' => 'date',
        'next_review_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($requirement) {
            if (empty($requirement->reference_number)) {
                $requirement->reference_number = $requirement->generateReferenceNumber();
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

    public function scopeByRegulatoryBody($query, $body)
    {
        return $query->where('regulatory_body', $body);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('compliance_status', $status);
    }

    public function scopeCompliant($query)
    {
        return $query->where('compliance_status', 'compliant');
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('compliance_status', 'non_compliant');
    }

    public function scopeDueSoon($query, $days = 30)
    {
        return $query->whereNotNull('compliance_due_date')
            ->where('compliance_due_date', '<=', Carbon::now()->addDays($days))
            ->where('compliance_due_date', '>', Carbon::now());
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('compliance_due_date')
            ->where('compliance_due_date', '<', Carbon::now());
    }

    public function scopeRequiringReview($query)
    {
        return $query->whereNotNull('next_review_date')
            ->where('next_review_date', '<=', Carbon::now());
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'CR-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
