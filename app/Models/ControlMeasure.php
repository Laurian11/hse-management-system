<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class ControlMeasure extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'risk_assessment_id',
        'hazard_id',
        'jsa_id',
        'incident_id',
        'title',
        'description',
        'control_type',
        'effectiveness_level',
        'status',
        'assigned_to',
        'responsible_party',
        'target_completion_date',
        'actual_completion_date',
        'verification_date',
        'verified_by',
        'estimated_cost',
        'actual_cost',
        'resources_required',
        'verification_method',
        'verification_results',
        'is_effective',
        'effectiveness_notes',
        'maintenance_frequency',
        'maintenance_requirements',
        'last_maintenance_date',
        'next_maintenance_date',
        'related_capa_id',
        'related_training_need_id',
        'related_training_plan_id',
        'training_required',
        'training_verified',
        'training_verified_at',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'verification_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'is_effective' => 'boolean',
        'is_active' => 'boolean',
        'training_required' => 'boolean',
        'training_verified' => 'boolean',
        'training_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($control) {
            if (empty($control->reference_number)) {
                $control->reference_number = 'CM-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($control) {
            ActivityLog::log('create', 'risk_assessment', 'ControlMeasure', $control->id, "Created control measure: {$control->title}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function riskAssessment(): BelongsTo
    {
        return $this->belongsTo(RiskAssessment::class);
    }

    public function hazard(): BelongsTo
    {
        return $this->belongsTo(Hazard::class);
    }

    public function jsa(): BelongsTo
    {
        return $this->belongsTo(JSA::class);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function responsibleParty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_party');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function relatedCAPA(): BelongsTo
    {
        return $this->belongsTo(CAPA::class, 'related_capa_id');
    }

    public function relatedTrainingNeed(): BelongsTo
    {
        return $this->belongsTo(TrainingNeedsAnalysis::class, 'related_training_need_id');
    }

    public function relatedTrainingPlan(): BelongsTo
    {
        return $this->belongsTo(TrainingPlan::class, 'related_training_plan_id');
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

    public function scopeByControlType($query, $type)
    {
        return $query->where('control_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_completion_date', '<', now())
                    ->whereIn('status', ['planned', 'in_progress']);
    }

    // Helper Methods
    public function getControlTypeLabel(): string
    {
        $labels = [
            'elimination' => 'Elimination',
            'substitution' => 'Substitution',
            'engineering' => 'Engineering Controls',
            'administrative' => 'Administrative Controls',
            'ppe' => 'Personal Protective Equipment',
            'combination' => 'Combination',
        ];

        return $labels[$this->control_type] ?? $this->control_type;
    }

    public function getHierarchyLevel(): int
    {
        $levels = [
            'elimination' => 1,
            'substitution' => 2,
            'engineering' => 3,
            'administrative' => 4,
            'ppe' => 5,
            'combination' => 3,
        ];

        return $levels[$this->control_type] ?? 4;
    }

    public function isOverdue(): bool
    {
        return $this->target_completion_date && 
               $this->target_completion_date->isPast() && 
               in_array($this->status, ['planned', 'in_progress']);
    }

    public function requiresTraining(): bool
    {
        return $this->control_type === 'administrative' || $this->training_required;
    }

    public function verifyTraining(): bool
    {
        return $this->update([
            'training_verified' => true,
            'training_verified_at' => now(),
        ]);
    }
}
