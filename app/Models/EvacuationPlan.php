<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class EvacuationPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'title', 'description', 'location', 'plan_type',
        'assembly_points', 'evacuation_routes', 'emergency_exits', 'hazard_zones',
        'evacuation_procedures', 'accountability_procedures', 'special_needs_procedures',
        'roles_and_responsibilities', 'required_equipment', 'communication_methods',
        'diagrams', 'maps', 'last_review_date', 'next_review_date', 'reviewed_by',
        'review_notes', 'is_active',
    ];

    protected $casts = [
        'assembly_points' => 'array',
        'evacuation_routes' => 'array',
        'emergency_exits' => 'array',
        'hazard_zones' => 'array',
        'roles_and_responsibilities' => 'array',
        'required_equipment' => 'array',
        'communication_methods' => 'array',
        'diagrams' => 'array',
        'maps' => 'array',
        'last_review_date' => 'date',
        'next_review_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($plan) {
            if (empty($plan->reference_number)) {
                $plan->reference_number = $plan->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForReview($query)
    {
        return $query->where('next_review_date', '<=', Carbon::now()->toDateString())
                     ->where('is_active', true);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'EP-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
