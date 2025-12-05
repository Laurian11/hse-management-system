<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkPermitType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'required_precautions',
        'required_equipment',
        'default_validity_hours',
        'max_validity_hours',
        'requires_risk_assessment',
        'requires_jsa',
        'requires_gas_test',
        'requires_fire_watch',
        'is_active',
        'approval_levels',
    ];

    protected $casts = [
        'required_precautions' => 'array',
        'required_equipment' => 'array',
        'requires_risk_assessment' => 'boolean',
        'requires_jsa' => 'boolean',
        'requires_gas_test' => 'boolean',
        'requires_fire_watch' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function workPermits(): HasMany
    {
        return $this->hasMany(WorkPermit::class);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
