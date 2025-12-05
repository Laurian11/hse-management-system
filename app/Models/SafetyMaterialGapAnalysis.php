<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SafetyMaterialGapAnalysis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'analysis_date', 'material_category', 'required_material',
        'description', 'required_quantity', 'available_quantity', 'gap_quantity', 'priority',
        'impact', 'recommendations', 'department_id', 'analyzed_by', 'procurement_requested',
        'related_procurement_request_id', 'status', 'notes',
    ];

    protected $casts = [
        'analysis_date' => 'date',
        'procurement_requested' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($analysis) {
            if (empty($analysis->reference_number)) {
                $analysis->reference_number = $analysis->generateReferenceNumber();
            }
            // Calculate gap quantity if not provided
            if (is_null($analysis->gap_quantity) && $analysis->required_quantity !== null && $analysis->available_quantity !== null) {
                $analysis->gap_quantity = max(0, $analysis->required_quantity - $analysis->available_quantity);
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

    public function analyzedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyzed_by');
    }

    public function relatedProcurementRequest(): BelongsTo
    {
        return $this->belongsTo(ProcurementRequest::class, 'related_procurement_request_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['identified', 'procurement_requested']);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'GAP-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
