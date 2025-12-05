<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;
use Carbon\Carbon;

class PPEIssuance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ppe_issuances';

    protected $fillable = [
        'reference_number',
        'company_id',
        'ppe_item_id',
        'issued_to',
        'issued_by',
        'department_id',
        'transaction_type',
        'quantity',
        'issue_date',
        'expected_return_date',
        'actual_return_date',
        'return_condition',
        'return_notes',
        'expiry_date',
        'replacement_due_date',
        'replacement_alert_sent',
        'replacement_alert_sent_at',
        'initial_condition',
        'condition_notes',
        'requires_inspection',
        'next_inspection_date',
        'last_inspection_date',
        'status',
        'notes',
        'reason',
        'serial_numbers',
        'batch_number',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'expiry_date' => 'date',
        'replacement_due_date' => 'date',
        'replacement_alert_sent' => 'boolean',
        'replacement_alert_sent_at' => 'date',
        'next_inspection_date' => 'date',
        'last_inspection_date' => 'date',
        'serial_numbers' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($issuance) {
            if (empty($issuance->reference_number)) {
                $issuance->reference_number = $issuance->generateReferenceNumber();
            }
            
            // Auto-calculate expiry date if item has expiry
            if ($issuance->ppeItem && $issuance->ppeItem->has_expiry && $issuance->ppeItem->expiry_days) {
                $issuance->expiry_date = Carbon::parse($issuance->issue_date)->addDays($issuance->ppeItem->expiry_days);
                $issuance->replacement_due_date = Carbon::parse($issuance->expiry_date)->subDays($issuance->ppeItem->replacement_alert_days ?? 30);
            }
            
            // Auto-calculate next inspection date
            if ($issuance->ppeItem && $issuance->ppeItem->requires_inspection && $issuance->ppeItem->inspection_frequency_days) {
                $issuance->next_inspection_date = Carbon::parse($issuance->issue_date)->addDays($issuance->ppeItem->inspection_frequency_days);
            }
        });

        static::created(function ($issuance) {
            // Update item quantities
            if ($issuance->transaction_type === 'issuance') {
                $item = $issuance->ppeItem;
                $item->issued_quantity += $issuance->quantity;
                $item->available_quantity -= $issuance->quantity;
                $item->save();
            }
            
            ActivityLog::log('create', 'ppe', 'PPEIssuance', $issuance->id, "Created PPE issuance: {$issuance->reference_number}");
        });

        static::updated(function ($issuance) {
            ActivityLog::log('update', 'ppe', 'PPEIssuance', $issuance->id, "Updated PPE issuance: {$issuance->reference_number}");
        });

        static::deleted(function ($issuance) {
            ActivityLog::log('delete', 'ppe', 'PPEIssuance', $issuance->id, "Deleted PPE issuance: {$issuance->reference_number}");
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function ppeItem(): BelongsTo
    {
        return $this->belongsTo(PPEItem::class, 'ppe_item_id');
    }

    public function issuedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_to');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(PPEInspection::class, 'ppe_issuance_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
                    ->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('status', 'active')
                    ->where('replacement_due_date', '<=', now()->addDays($days))
                    ->where('replacement_alert_sent', false);
    }

    public function scopeNeedsInspection($query)
    {
        return $query->where('status', 'active')
                    ->where('requires_inspection', true)
                    ->where(function($q) {
                        $q->whereNull('next_inspection_date')
                          ->orWhere('next_inspection_date', '<=', now());
                    });
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('issued_to', $userId);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'PPE-ISS';
        $year = date('Y');
        $month = date('m');
        $sequence = static::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->count() + 1;
        
        return "{$prefix}-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function isExpired(): bool
    {
        return $this->status === 'active' && $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(): bool
    {
        return $this->status === 'active' 
            && $this->replacement_due_date 
            && $this->replacement_due_date->isPast()
            && !$this->replacement_alert_sent;
    }

    public function needsInspection(): bool
    {
        return $this->status === 'active' 
            && $this->requires_inspection 
            && ($this->next_inspection_date === null || $this->next_inspection_date->isPast());
    }
}

