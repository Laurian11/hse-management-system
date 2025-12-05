<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ProcurementRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'item_name', 'description', 'item_category',
        'quantity', 'unit', 'estimated_cost', 'currency', 'justification', 'priority',
        'required_date', 'requested_by', 'department_id', 'status', 'reviewed_by',
        'review_date', 'review_notes', 'approved_by', 'approval_date', 'approval_notes',
        'supplier_id', 'purchase_cost', 'purchase_date', 'received_date', 'notes', 'attachments',
    ];

    protected $casts = [
        'required_date' => 'date',
        'review_date' => 'date',
        'approval_date' => 'date',
        'purchase_date' => 'date',
        'received_date' => 'date',
        'estimated_cost' => 'decimal:2',
        'purchase_cost' => 'decimal:2',
        'attachments' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($request) {
            if (empty($request->reference_number)) {
                $request->reference_number = $request->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePendingApproval($query)
    {
        return $query->whereIn('status', ['submitted', 'under_review']);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'PR-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
