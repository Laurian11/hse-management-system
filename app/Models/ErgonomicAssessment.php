<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ErgonomicAssessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'workstation_location', 'assessed_employee_id',
        'job_title', 'task_description', 'assessment_date', 'assessed_by', 'risk_factors',
        'risk_level', 'findings', 'recommendations', 'control_measures', 'review_date',
        'status', 'notes', 'attachments',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'review_date' => 'date',
        'risk_factors' => 'array',
        'attachments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($assessment) {
            if (empty($assessment->reference_number)) {
                $assessment->reference_number = $assessment->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assessedEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_employee_id');
    }

    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'ERG-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
