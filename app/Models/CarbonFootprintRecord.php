<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CarbonFootprintRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'record_date', 'source_type', 'source_name',
        'consumption', 'consumption_unit', 'emission_factor', 'carbon_equivalent',
        'department_id', 'location', 'recorded_by', 'notes',
    ];

    protected $casts = [
        'record_date' => 'date',
        'consumption' => 'decimal:2',
        'emission_factor' => 'decimal:4',
        'carbon_equivalent' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($record) {
            if (empty($record->reference_number)) {
                $record->reference_number = $record->generateReferenceNumber();
            }
            // Auto-calculate carbon equivalent if not provided
            if (!$record->carbon_equivalent && $record->consumption && $record->emission_factor) {
                $record->carbon_equivalent = $record->consumption * $record->emission_factor;
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

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeBySourceType($query, $type)
    {
        return $query->where('source_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('record_date', [$startDate, $endDate]);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'CF-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
