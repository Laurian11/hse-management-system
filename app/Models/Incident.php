<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class Incident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'reported_by',
        'assigned_to',
        'department_id',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'incident_type',
        'event_type',
        'title',
        'description',
        'location',
        'location_specific',
        'latitude',
        'longitude',
        'incident_date',
        'severity',
        'status',
        'closure_status',
        'actions_taken',
        'resolution_notes',
        'closed_at',
        'images',
        // Injury/Illness fields
        'injury_type',
        'body_part_affected',
        'nature_of_injury',
        'lost_time_injury',
        'days_lost',
        'medical_treatment_details',
        // Property Damage fields
        'asset_damaged',
        'estimated_cost',
        'damage_description',
        'insurance_claim_filed',
        // Near Miss fields
        'potential_severity',
        'potential_consequences',
        'preventive_measures_taken',
        // Workflow fields
        'approval_workflow',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'investigation_id',
        'root_cause_analysis_id',
        // Regulatory fields
        'regulatory_reporting_required',
        'reported_to_authorities',
        'reported_to_authorities_at',
        'regulatory_reference',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'closed_at' => 'datetime',
        'images' => 'array',
        'approval_workflow' => 'array',
        'estimated_cost' => 'decimal:2',
        'lost_time_injury' => 'boolean',
        'insurance_claim_filed' => 'boolean',
        'reported_to_authorities' => 'boolean',
        'reported_to_authorities_at' => 'datetime',
        'approved_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::creating(function ($incident) {
            if (empty($incident->reference_number)) {
                $incident->reference_number = 'INC-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($incident) {
            ActivityLog::log('create', 'incidents', 'Incident', $incident->id, 'Incident created: ' . $incident->reference_number);
        });

        static::updated(function ($incident) {
            ActivityLog::log('update', 'incidents', 'Incident', $incident->id, 'Incident updated: ' . $incident->reference_number);
        });

        static::deleted(function ($incident) {
            ActivityLog::log('delete', 'incidents', 'Incident', $incident->id, 'Incident deleted: ' . $incident->reference_number);
        });
    }

    /**
     * Relationships
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function investigation(): HasOne
    {
        return $this->hasOne(IncidentInvestigation::class);
    }

    public function investigations(): HasMany
    {
        return $this->hasMany(IncidentInvestigation::class);
    }

    public function rootCauseAnalysis(): HasOne
    {
        return $this->hasOne(RootCauseAnalysis::class);
    }

    public function capas(): HasMany
    {
        return $this->hasMany(CAPA::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(IncidentAttachment::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['reported', 'open']);
    }

    public function scopeInvestigating($query)
    {
        return $query->where('status', 'investigating');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeHigh($query)
    {
        return $query->where('severity', 'high');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('incident_date', '>=', now()->subDays($days));
    }

    public function scopeByEventType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeInjuryIllness($query)
    {
        return $query->where('event_type', 'injury_illness');
    }

    public function scopePropertyDamage($query)
    {
        return $query->where('event_type', 'property_damage');
    }

    public function scopeNearMiss($query)
    {
        return $query->where('event_type', 'near_miss');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('closure_status', 'pending_approval');
    }

    public function scopeApproved($query)
    {
        return $query->where('closure_status', 'approved');
    }

    /**
     * Helper Methods
     */
    public function isOpen(): bool
    {
        return in_array($this->status, ['reported', 'open']);
    }

    public function isInvestigating(): bool
    {
        return $this->status === 'investigating';
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['closed', 'resolved']);
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'reported', 'open' => 'warning',
            'investigating' => 'info',
            'resolved', 'closed' => 'success',
            default => 'secondary',
        };
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?? $this->incident_type ?? 'Incident #' . $this->reference_number;
    }

    public function getReporterNameAttribute(): string
    {
        if ($this->reporter) {
            return $this->reporter->name;
        }
        return $this->attributes['reporter_name'] ?? 'Anonymous';
    }

    public function getReporterEmailAttribute(): string
    {
        if ($this->reporter) {
            return $this->reporter->email;
        }
        return $this->attributes['reporter_email'] ?? '';
    }

    public function hasImages(): bool
    {
        return !empty($this->images) && is_array($this->images) && count($this->images) > 0;
    }

    public function getImageCountAttribute(): int
    {
        return $this->hasImages() ? count($this->images) : 0;
    }

    /**
     * Close the incident
     */
    public function close(string $resolutionNotes = null): bool
    {
        return $this->update([
            'status' => 'closed',
            'resolution_notes' => $resolutionNotes ?? $this->resolution_notes,
            'closed_at' => now(),
        ]);
    }

    /**
     * Reopen the incident
     */
    public function reopen(): bool
    {
        return $this->update([
            'status' => 'open',
            'resolution_notes' => null,
            'closed_at' => null,
        ]);
    }

    /**
     * Assign to user
     */
    public function assignTo(User $user): bool
    {
        return $this->update([
            'assigned_to' => $user->id,
            'status' => 'investigating',
        ]);
    }

    /**
     * Check if incident can be closed
     */
    public function canBeClosed(): bool
    {
        // Check if investigation is completed
        if ($this->investigation && !$this->investigation->isCompleted()) {
            return false;
        }

        // Check if RCA is completed
        if ($this->rootCauseAnalysis && !$this->rootCauseAnalysis->isCompleted()) {
            return false;
        }

        // Check if all CAPAs are closed or verified
        $openCapas = $this->capas()->whereNotIn('status', ['verified', 'closed'])->count();
        if ($openCapas > 0) {
            return false;
        }

        return true;
    }

    /**
     * Request closure approval
     */
    public function requestClosureApproval(array $workflow = null): bool
    {
        return $this->update([
            'closure_status' => 'pending_approval',
            'approval_workflow' => $workflow ?? $this->getDefaultApprovalWorkflow(),
        ]);
    }

    /**
     * Approve closure
     */
    public function approveClosure(User $approver): bool
    {
        return $this->update([
            'closure_status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject closure
     */
    public function rejectClosure(User $rejector, string $reason): bool
    {
        return $this->update([
            'closure_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Get default approval workflow
     */
    protected function getDefaultApprovalWorkflow(): array
    {
        return [
            ['step' => 1, 'role' => 'investigator', 'status' => 'pending'],
            ['step' => 2, 'role' => 'hse_manager', 'status' => 'pending'],
            ['step' => 3, 'role' => 'site_manager', 'status' => 'pending'],
        ];
    }

    /**
     * Check if incident is injury/illness type
     */
    public function isInjuryIllness(): bool
    {
        return $this->event_type === 'injury_illness';
    }

    /**
     * Check if incident is property damage type
     */
    public function isPropertyDamage(): bool
    {
        return $this->event_type === 'property_damage';
    }

    /**
     * Check if incident is near miss type
     */
    public function isNearMiss(): bool
    {
        return $this->event_type === 'near_miss';
    }
}
