<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class WasteManagementRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'waste_type', 'category', 'description',
        'segregation_status', 'storage_location', 'storage_method', 'quantity', 'unit',
        'collection_date', 'disposal_date', 'disposal_method', 'disposal_contractor_id',
        'disposal_certificate_number', 'disposal_certificate_date', 'department_id',
        'recorded_by', 'notes', 'attachments',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'disposal_date' => 'date',
        'disposal_certificate_date' => 'date',
        'quantity' => 'decimal:2',
        'attachments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($record) {
            if (empty($record->reference_number)) {
                $record->reference_number = $record->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function disposalContractor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'disposal_contractor_id');
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

    public function scopeByWasteType($query, $type)
    {
        return $query->where('waste_type', $type);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'WMR-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
