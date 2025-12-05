<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Inspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'inspection_schedule_id', 'inspection_checklist_id',
        'title', 'description', 'inspection_date', 'inspected_by', 'department_id', 'location',
        'checklist_responses', 'overall_status', 'total_items', 'compliant_items',
        'non_compliant_items', 'na_items', 'findings', 'observations', 'recommendations',
        'requires_follow_up', 'follow_up_date', 'follow_up_assigned_to',
        'follow_up_completed', 'follow_up_completed_date', 'attachments',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'follow_up_date' => 'date',
        'follow_up_completed_date' => 'date',
        'checklist_responses' => 'array',
        'findings' => 'array',
        'attachments' => 'array',
        'requires_follow_up' => 'boolean',
        'follow_up_completed' => 'boolean',
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

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(InspectionSchedule::class, 'inspection_schedule_id');
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(InspectionChecklist::class, 'inspection_checklist_id');
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function followUpAssignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follow_up_assigned_to');
    }

    public function nonConformanceReports(): HasMany
    {
        return $this->hasMany(NonConformanceReport::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeCompliant($query)
    {
        return $query->where('overall_status', 'compliant');
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('overall_status', 'non_compliant');
    }

    public function scopeRequiresFollowUp($query)
    {
        return $query->where('requires_follow_up', true)
                     ->where('follow_up_completed', false);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'INS-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
