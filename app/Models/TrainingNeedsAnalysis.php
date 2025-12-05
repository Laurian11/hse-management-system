<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class TrainingNeedsAnalysis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'trigger_source',
        'triggered_by_risk_assessment_id',
        'triggered_by_control_measure_id',
        'triggered_by_incident_id',
        'triggered_by_rca_id',
        'triggered_by_capa_id',
        'triggered_by_user_id',
        'triggered_by_job_matrix_id',
        'training_title',
        'training_description',
        'gap_analysis',
        'priority',
        'training_type',
        'target_departments',
        'target_job_roles',
        'target_user_ids',
        'estimated_participants',
        'is_mandatory',
        'is_regulatory',
        'regulatory_reference',
        'regulatory_deadline',
        'status',
        'created_by',
        'validated_by',
        'validated_at',
        'validation_notes',
    ];

    protected $casts = [
        'target_departments' => 'array',
        'target_job_roles' => 'array',
        'target_user_ids' => 'array',
        'is_mandatory' => 'boolean',
        'is_regulatory' => 'boolean',
        'regulatory_deadline' => 'date',
        'validated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($tna) {
            if (empty($tna->reference_number)) {
                $tna->reference_number = 'TNA-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($tna) {
            ActivityLog::log('create', 'training', 'TrainingNeedsAnalysis', $tna->id, "Created training need: {$tna->training_title}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function triggeredByRiskAssessment(): BelongsTo
    {
        return $this->belongsTo(RiskAssessment::class, 'triggered_by_risk_assessment_id');
    }

    public function triggeredByControlMeasure(): BelongsTo
    {
        return $this->belongsTo(ControlMeasure::class, 'triggered_by_control_measure_id');
    }

    public function triggeredByIncident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'triggered_by_incident_id');
    }

    public function triggeredByRCA(): BelongsTo
    {
        return $this->belongsTo(RootCauseAnalysis::class, 'triggered_by_rca_id');
    }

    public function triggeredByCAPA(): BelongsTo
    {
        return $this->belongsTo(CAPA::class, 'triggered_by_capa_id');
    }

    public function triggeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }

    public function triggeredByJobMatrix(): BelongsTo
    {
        return $this->belongsTo(JobCompetencyMatrix::class, 'triggered_by_job_matrix_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function trainingPlans(): HasMany
    {
        return $this->hasMany(TrainingPlan::class, 'training_need_id');
    }

    public function trainingPlan(): HasOne
    {
        return $this->hasOne(TrainingPlan::class, 'training_need_id');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByTriggerSource($query, $source)
    {
        return $query->where('trigger_source', $source);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeRegulatory($query)
    {
        return $query->where('is_regulatory', true);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    // Helper Methods
    public function validate(User $validator, string $notes = null): bool
    {
        return $this->update([
            'status' => 'validated',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'validation_notes' => $notes ?? $this->validation_notes,
        ]);
    }

    public function isCritical(): bool
    {
        return $this->priority === 'critical' || 
               ($this->is_regulatory && $this->regulatory_deadline && $this->regulatory_deadline->isPast());
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'identified' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Identified</span>',
            'validated' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Validated</span>',
            'planned' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Planned</span>',
            'in_progress' => '<span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">In Progress</span>',
            'completed' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelled</span>',
            'on_hold' => '<span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">On Hold</span>',
        ];

        return $badges[$this->status] ?? '';
    }
}
