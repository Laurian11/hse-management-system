<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class PPEInspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppe_inspections';

    protected $fillable = [
        'reference_number',
        'company_id',
        'ppe_issuance_id',
        'ppe_item_id',
        'inspected_by',
        'user_id',
        'inspection_date',
        'inspection_type',
        'condition',
        'inspection_checklist',
        'findings',
        'defects',
        'defect_photos',
        'action_taken',
        'action_notes',
        'next_inspection_date',
        'is_compliant',
        'non_compliance_reason',
        'compliance_issues',
        'status',
        'notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'next_inspection_date' => 'date',
        'inspection_checklist' => 'array',
        'defect_photos' => 'array',
        'compliance_issues' => 'array',
        'is_compliant' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($inspection) {
            if (empty($inspection->reference_number)) {
                $inspection->reference_number = $inspection->generateReferenceNumber();
            }
        });

        static::created(function ($inspection) {
            // Update issuance last inspection date
            if ($inspection->ppeIssuance) {
                $inspection->ppeIssuance->last_inspection_date = $inspection->inspection_date;
                $inspection->ppeIssuance->next_inspection_date = $inspection->next_inspection_date;
                $inspection->ppeIssuance->save();
            }
            
            ActivityLog::log('create', 'ppe', 'PPEInspection', $inspection->id, "Created PPE inspection: {$inspection->reference_number}");
        });

        static::updated(function ($inspection) {
            ActivityLog::log('update', 'ppe', 'PPEInspection', $inspection->id, "Updated PPE inspection: {$inspection->reference_number}");
        });

        static::deleted(function ($inspection) {
            ActivityLog::log('delete', 'ppe', 'PPEInspection', $inspection->id, "Deleted PPE inspection: {$inspection->reference_number}");
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function ppeIssuance(): BelongsTo
    {
        return $this->belongsTo(PPEIssuance::class, 'ppe_issuance_id');
    }

    public function ppeItem(): BelongsTo
    {
        return $this->belongsTo(PPEItem::class, 'ppe_item_id');
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeCompliant($query)
    {
        return $query->where('is_compliant', true);
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('is_compliant', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('inspection_type', $type);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->where('inspection_date', '<', now());
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'PPE-INS';
        $year = date('Y');
        $month = date('m');
        $sequence = static::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count() + 1;
        
        return "{$prefix}-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}

