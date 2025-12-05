<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class PPESupplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppe_suppliers';

    protected $fillable = [
        'reference_number',
        'company_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'notes',
        'status',
        'certifications',
        'rating',
        'is_preferred',
    ];

    protected $casts = [
        'certifications' => 'array',
        'rating' => 'array',
        'is_preferred' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($supplier) {
            if (empty($supplier->reference_number)) {
                $supplier->reference_number = $supplier->generateReferenceNumber();
            }
        });

        static::created(function ($supplier) {
            ActivityLog::log('create', 'ppe', 'PPESupplier', $supplier->id, "Created PPE supplier: {$supplier->name}");
        });

        static::updated(function ($supplier) {
            ActivityLog::log('update', 'ppe', 'PPESupplier', $supplier->id, "Updated PPE supplier: {$supplier->name}");
        });

        static::deleted(function ($supplier) {
            ActivityLog::log('delete', 'ppe', 'PPESupplier', $supplier->id, "Deleted PPE supplier: {$supplier->name}");
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function ppeItems(): HasMany
    {
        return $this->hasMany(PPEItem::class, 'supplier_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePreferred($query)
    {
        return $query->where('is_preferred', true);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'PPE-SUP';
        $year = date('Y');
        $month = date('m');
        $sequence = static::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count() + 1;
        
        return "{$prefix}-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}

