<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class PPEComplianceReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppe_compliance_reports';

    protected $fillable = [
        'reference_number',
        'company_id',
        'generated_by',
        'report_type',
        'report_period_start',
        'report_period_end',
        'scope',
        'department_id',
        'user_id',
        'ppe_item_id',
        'metrics',
        'total_issuances',
        'active_issuances',
        'expired_issuances',
        'overdue_inspections',
        'non_compliant_count',
        'compliance_rate',
        'usage_rate',
        'summary',
        'findings',
        'recommendations',
        'action_items',
        'status',
        'notes',
    ];

    protected $casts = [
        'report_period_start' => 'date',
        'report_period_end' => 'date',
        'metrics' => 'array',
        'findings' => 'array',
        'recommendations' => 'array',
        'action_items' => 'array',
        'compliance_rate' => 'decimal:2',
        'usage_rate' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($report) {
            if (empty($report->reference_number)) {
                $report->reference_number = $report->generateReferenceNumber();
            }
        });

        static::created(function ($report) {
            ActivityLog::log('create', 'ppe', 'PPEComplianceReport', $report->id, "Created PPE compliance report: {$report->reference_number}");
        });

        static::updated(function ($report) {
            ActivityLog::log('update', 'ppe', 'PPEComplianceReport', $report->id, "Updated PPE compliance report: {$report->reference_number}");
        });

        static::deleted(function ($report) {
            ActivityLog::log('delete', 'ppe', 'PPEComplianceReport', $report->id, "Deleted PPE compliance report: {$report->reference_number}");
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ppeItem(): BelongsTo
    {
        return $this->belongsTo(PPEItem::class, 'ppe_item_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByScope($query, $scope)
    {
        return $query->where('scope', $scope);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'PPE-RPT';
        $year = date('Y');
        $month = date('m');
        $sequence = static::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count() + 1;
        
        return "{$prefix}-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}

