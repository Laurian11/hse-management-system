<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncidentInvestigation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'incident_id',
        'company_id',
        'investigator_id',
        'assigned_by',
        'investigation_started_at',
        'investigation_completed_at',
        'due_date',
        'status',
        'what_happened',
        'when_occurred',
        'where_occurred',
        'who_involved',
        'how_occurred',
        'immediate_causes',
        'contributing_factors',
        'witnesses',
        'witness_statements',
        'environmental_conditions',
        'equipment_conditions',
        'procedures_followed',
        'training_received',
        'investigation_team',
        'key_findings',
        'evidence_collected',
        'interviews_conducted',
        'investigator_notes',
        'recommendations',
    ];

    protected $casts = [
        'investigation_started_at' => 'datetime',
        'investigation_completed_at' => 'datetime',
        'due_date' => 'datetime',
        'witnesses' => 'array',
        'witness_statements' => 'array',
        'investigation_team' => 'array',
    ];

    // Relationships
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function investigator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigator_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function rootCauseAnalysis(): HasOne
    {
        return $this->hasOne(RootCauseAnalysis::class, 'investigation_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'in_progress')
                  ->where('due_date', '<', now());
            });
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue' || 
               ($this->status === 'in_progress' && $this->due_date && $this->due_date->isPast());
    }

    public function start(): bool
    {
        return $this->update([
            'status' => 'in_progress',
            'investigation_started_at' => now(),
        ]);
    }

    public function complete(): bool
    {
        return $this->update([
            'status' => 'completed',
            'investigation_completed_at' => now(),
        ]);
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Pending</span>',
            'in_progress' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">In Progress</span>',
            'completed' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>',
            'overdue' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Overdue</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    public function getDaysRemaining(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }
}
