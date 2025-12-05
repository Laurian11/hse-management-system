<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class EquipmentCertification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'equipment_name', 'equipment_type', 'serial_number',
        'manufacturer', 'model', 'certification_type', 'certificate_number', 'certification_date',
        'expiry_date', 'next_due_date', 'certified_by', 'certifier_name', 'certification_details',
        'status', 'department_id', 'location', 'notes', 'attachments',
    ];

    protected $casts = [
        'certification_date' => 'date',
        'expiry_date' => 'date',
        'next_due_date' => 'date',
        'attachments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($certification) {
            if (empty($certification->reference_number)) {
                $certification->reference_number = $certification->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function certifiedBy(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'certified_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::now()->toDateString())
                     ->where('status', 'valid');
    }

    public function scopeDue($query)
    {
        return $query->where('next_due_date', '<=', Carbon::now()->toDateString())
                     ->where('status', 'valid');
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'EC-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
