<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class Hazard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'created_by',
        'department_id',
        'title',
        'description',
        'hazard_category',
        'location',
        'process_or_activity',
        'asset_or_equipment',
        'hazard_source',
        'identification_method',
        'at_risk_personnel',
        'exposure_description',
        'status',
        'is_active',
        'related_incident_id',
    ];

    protected $casts = [
        'at_risk_personnel' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::creating(function ($hazard) {
            if (empty($hazard->reference_number)) {
                $hazard->reference_number = 'HAZ-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($hazard) {
            ActivityLog::log('create', 'risk_assessment', 'Hazard', $hazard->id, "Created hazard: {$hazard->title}");
        });

        static::updated(function ($hazard) {
            ActivityLog::log('update', 'risk_assessment', 'Hazard', $hazard->id, "Updated hazard: {$hazard->title}");
        });

        static::deleted(function ($hazard) {
            ActivityLog::log('delete', 'risk_assessment', 'Hazard', $hazard->id, "Deleted hazard: {$hazard->title}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function relatedIncident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'related_incident_id');
    }

    public function riskAssessments(): HasMany
    {
        return $this->hasMany(RiskAssessment::class);
    }

    public function controlMeasures(): HasMany
    {
        return $this->hasMany(ControlMeasure::class);
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('hazard_category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeIdentified($query)
    {
        return $query->where('status', 'identified');
    }

    public function scopeAssessed($query)
    {
        return $query->where('status', 'assessed');
    }

    public function scopeControlled($query)
    {
        return $query->where('status', 'controlled');
    }

    // Helper Methods
    public function getCategoryLabel(): string
    {
        $labels = [
            'physical' => 'Physical',
            'chemical' => 'Chemical',
            'biological' => 'Biological',
            'ergonomic' => 'Ergonomic',
            'psychosocial' => 'Psychosocial',
            'mechanical' => 'Mechanical',
            'electrical' => 'Electrical',
            'fire' => 'Fire',
            'environmental' => 'Environmental',
            'other' => 'Other',
        ];

        return $labels[$this->hazard_category] ?? $this->hazard_category;
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'identified' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Identified</span>',
            'assessed' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Assessed</span>',
            'controlled' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Controlled</span>',
            'closed' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Closed</span>',
            'archived' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Archived</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    public function hasRiskAssessment(): bool
    {
        return $this->riskAssessments()->exists();
    }

    public function hasControls(): bool
    {
        return $this->controlMeasures()->where('status', '!=', 'cancelled')->exists();
    }
}
