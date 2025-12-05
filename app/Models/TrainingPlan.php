<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class TrainingPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'training_need_id',
        'title',
        'description',
        'training_type',
        'planned_start_date',
        'planned_end_date',
        'duration_hours',
        'duration_days',
        'delivery_method',
        'instructor_id',
        'external_instructor_name',
        'external_instructor_qualifications',
        'training_location_id',
        'location_name',
        'location_address',
        'training_materials',
        'additional_resources',
        'estimated_cost',
        'actual_cost',
        'budget_approved',
        'budget_approved_by',
        'budget_approved_at',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'training_materials' => 'array',
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'budget_approved' => 'boolean',
        'budget_approved_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($plan) {
            if (empty($plan->reference_number)) {
                $plan->reference_number = 'TP-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($plan) {
            ActivityLog::log('create', 'training', 'TrainingPlan', $plan->id, "Created training plan: {$plan->title}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function trainingNeed(): BelongsTo
    {
        return $this->belongsTo(TrainingNeedsAnalysis::class, 'training_need_id');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function budgetApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'budget_approved_by');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'training_plan_id');
    }

    public function controlMeasures(): HasMany
    {
        return $this->hasMany(ControlMeasure::class, 'related_training_plan_id');
    }

    public function capas(): HasMany
    {
        return $this->hasMany(CAPA::class, 'related_training_plan_id');
    }

    public function effectivenessEvaluations(): HasMany
    {
        return $this->hasMany(TrainingEffectivenessEvaluation::class, 'training_plan_id');
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

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    // Helper Methods
    public function approve(User $approver): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
    }

    public function approveBudget(User $approver): bool
    {
        return $this->update([
            'budget_approved' => true,
            'budget_approved_by' => $approver->id,
            'budget_approved_at' => now(),
        ]);
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'draft' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Draft</span>',
            'approved' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Approved</span>',
            'scheduled' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Scheduled</span>',
            'in_progress' => '<span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">In Progress</span>',
            'completed' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelled</span>',
            'on_hold' => '<span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">On Hold</span>',
        ];

        return $badges[$this->status] ?? '';
    }
}
