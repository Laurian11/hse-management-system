<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class PPEItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppe_items';

    protected $fillable = [
        'reference_number',
        'company_id',
        'name',
        'category',
        'type',
        'description',
        'manufacturer',
        'model_number',
        'sku',
        'total_quantity',
        'available_quantity',
        'issued_quantity',
        'reserved_quantity',
        'minimum_stock_level',
        'reorder_quantity',
        'specifications',
        'standards_compliance',
        'unit_of_measure',
        'has_expiry',
        'expiry_days',
        'replacement_alert_days',
        'requires_inspection',
        'inspection_frequency_days',
        'supplier_id',
        'unit_cost',
        'currency',
        'last_purchase_date',
        'last_purchase_quantity',
        'storage_location',
        'warehouse',
        'storage_conditions',
        'status',
        'notes',
    ];

    protected $casts = [
        'specifications' => 'array',
        'standards_compliance' => 'array',
        'has_expiry' => 'boolean',
        'requires_inspection' => 'boolean',
        'unit_cost' => 'decimal:2',
        'last_purchase_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            if (empty($item->reference_number)) {
                $item->reference_number = $item->generateReferenceNumber();
            }
        });

        static::created(function ($item) {
            ActivityLog::log('create', 'ppe', 'PPEItem', $item->id, "Created PPE item: {$item->name}");
        });

        static::updated(function ($item) {
            ActivityLog::log('update', 'ppe', 'PPEItem', $item->id, "Updated PPE item: {$item->name}");
        });

        static::deleted(function ($item) {
            ActivityLog::log('delete', 'ppe', 'PPEItem', $item->id, "Deleted PPE item: {$item->name}");
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(PPESupplier::class, 'supplier_id');
    }

    public function issuances(): HasMany
    {
        return $this->hasMany(PPEIssuance::class, 'ppe_item_id');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(PPEInspection::class, 'ppe_item_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('available_quantity', '<=', 'minimum_stock_level');
    }

    public function scopeNeedsReorder($query)
    {
        return $query->whereColumn('available_quantity', '<=', 'minimum_stock_level')
                    ->where('status', 'active');
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'PPE-ITM';
        $year = date('Y');
        $month = date('m');
        $sequence = static::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count() + 1;
        
        return "{$prefix}-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function isLowStock(): bool
    {
        return $this->available_quantity <= $this->minimum_stock_level;
    }

    public function needsReorder(): bool
    {
        return $this->isLowStock() && $this->status === 'active';
    }
}

