<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class EmergencyEquipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'equipment_name', 'equipment_type', 'location', 'serial_number',
        'manufacturer', 'model', 'purchase_date', 'expiry_date', 'last_inspection_date',
        'next_inspection_date', 'inspection_frequency', 'status', 'condition',
        'notes', 'inspected_by', 'inspection_notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'last_inspection_date' => 'date',
        'next_inspection_date' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inspectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDueForInspection($query)
    {
        return $query->where('next_inspection_date', '<=', Carbon::now()->toDateString())
                     ->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::now()->toDateString())
                     ->where('status', 'active');
    }
}
