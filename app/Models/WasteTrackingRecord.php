<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class WasteTrackingRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'waste_management_record_id', 'waste_type',
        'category', 'volume', 'unit', 'tracking_date', 'source_location',
        'destination_location', 'contractor_id', 'transport_method', 'vehicle_registration',
        'manifest_number', 'status', 'tracked_by', 'notes',
    ];

    protected $casts = [
        'tracking_date' => 'date',
        'volume' => 'decimal:2',
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

    public function wasteManagementRecord(): BelongsTo
    {
        return $this->belongsTo(WasteManagementRecord::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'contractor_id');
    }

    public function trackedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tracked_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'WTR-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
