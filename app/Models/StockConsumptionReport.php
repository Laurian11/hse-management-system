<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StockConsumptionReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'item_name', 'item_category', 'item_code',
        'opening_stock', 'received_quantity', 'consumed_quantity', 'closing_stock', 'unit',
        'report_period_start', 'report_period_end', 'department_id', 'consumption_details',
        'notes', 'prepared_by',
    ];

    protected $casts = [
        'report_period_start' => 'date',
        'report_period_end' => 'date',
        'opening_stock' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'consumed_quantity' => 'decimal:2',
        'closing_stock' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($report) {
            if (empty($report->reference_number)) {
                $report->reference_number = $report->generateReferenceNumber();
            }
            // Calculate closing stock if not provided
            if (is_null($report->closing_stock) && $report->opening_stock !== null) {
                $report->closing_stock = ($report->opening_stock ?? 0) + ($report->received_quantity ?? 0) - ($report->consumed_quantity ?? 0);
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

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'SCR-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
