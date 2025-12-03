<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class CAPA extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'capas';

    protected $fillable = [
        'reference_number',
        'incident_id',
        'root_cause_analysis_id',
        'company_id',
        'action_type',
        'title',
        'description',
        'root_cause_addressed',
        'assigned_to',
        'assigned_by',
        'department_id',
        'priority',
        'required_resources',
        'estimated_cost',
        'budget_approved',
        'due_date',
        'started_at',
        'completed_at',
        'verified_at',
        'status',
        'implementation_plan',
        'progress_notes',
        'challenges_encountered',
        'effectiveness_measures',
        'verified_by',
        'verification_notes',
        'effectiveness',
        'effectiveness_evidence',
        'closed_by',
        'closure_notes',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'due_date' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function booted()
    {
        static::creating(function ($capa) {
            if (empty($capa->reference_number)) {
                $capa->reference_number = 'CAPA-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($capa) {
            ActivityLog::log('create', 'incidents', 'CAPA', $capa->id, 'CAPA created: ' . $capa->reference_number);
        });

        static::updated(function ($capa) {
            ActivityLog::log('update', 'incidents', 'CAPA', $capa->id, 'CAPA updated: ' . $capa->reference_number);
        });
    }

    // Relationships
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function rootCauseAnalysis(): BelongsTo
    {
        return $this->belongsTo(RootCauseAnalysis::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // Scopes
    public function scopeCorrective($query)
    {
        return $query->where('action_type', 'corrective');
    }

    public function scopePreventive($query)
    {
        return $query->where('action_type', 'preventive');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function($q) {
                $q->whereIn('status', ['pending', 'in_progress'])
                  ->where('due_date', '<', now());
            });
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Helper Methods
    public function isCorrective(): bool
    {
        return $this->action_type === 'corrective';
    }

    public function isPreventive(): bool
    {
        return $this->action_type === 'preventive';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || 
               (in_array($this->status, ['pending', 'in_progress']) && 
                $this->due_date && $this->due_date->isPast());
    }

    public function start(): bool
    {
        return $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function complete(): bool
    {
        return $this->update([
            'status' => 'under_review',
            'completed_at' => now(),
        ]);
    }

    public function verify(User $verifier, string $notes = null, string $effectiveness = null): bool
    {
        return $this->update([
            'status' => 'verified',
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'verification_notes' => $notes ?? $this->verification_notes,
            'effectiveness' => $effectiveness ?? $this->effectiveness,
        ]);
    }

    public function close(User $closer, string $notes = null): bool
    {
        return $this->update([
            'status' => 'closed',
            'closed_by' => $closer->id,
            'closure_notes' => $notes ?? $this->closure_notes,
        ]);
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Pending</span>',
            'in_progress' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">In Progress</span>',
            'under_review' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Under Review</span>',
            'verified' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Verified</span>',
            'closed' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Closed</span>',
            'overdue' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Overdue</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    public function getPriorityBadge(): string
    {
        $badges = [
            'low' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Low</span>',
            'medium' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Medium</span>',
            'high' => '<span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">High</span>',
            'critical' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Critical</span>',
        ];

        return $badges[$this->priority] ?? '';
    }

    public function getDaysRemaining(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }
}
