<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class HousekeepingInspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'inspection_date', 'department_id',
        'location', 'inspected_by', 'overall_rating', 'score', 'checklist_items',
        'findings', 'recommendations', 'status', 'follow_up_date',
        'follow_up_assigned_to', 'corrective_actions', 'notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'follow_up_date' => 'date',
        'checklist_items' => 'array',
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

    public function followUpAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follow_up_assigned_to');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRequiringFollowUp($query)
    {
        return $query->where('status', 'follow_up_required')
            ->orWhere(function ($q) {
                $q->whereNotNull('follow_up_date')
                  ->where('follow_up_date', '<=', Carbon::now());
            });
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'HK-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
