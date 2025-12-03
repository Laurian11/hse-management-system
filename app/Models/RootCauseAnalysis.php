<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RootCauseAnalysis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'incident_id',
        'investigation_id',
        'company_id',
        'created_by',
        'analysis_type',
        'why_1',
        'why_2',
        'why_3',
        'why_4',
        'why_5',
        'root_cause',
        'human_factors',
        'organizational_factors',
        'technical_factors',
        'environmental_factors',
        'procedural_factors',
        'equipment_factors',
        'direct_cause',
        'contributing_causes',
        'root_causes',
        'systemic_failures',
        'causal_factors',
        'barriers_failed',
        'prevention_possible',
        'lessons_learned',
        'status',
        'completed_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'causal_factors' => 'array',
        'barriers_failed' => 'array',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function investigation(): BelongsTo
    {
        return $this->belongsTo(IncidentInvestigation::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function capas(): HasMany
    {
        return $this->hasMany(CAPA::class, 'root_cause_analysis_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('analysis_type', $type);
    }

    public function scopeFiveWhys($query)
    {
        return $query->where('analysis_type', '5_whys');
    }

    public function scopeFishbone($query)
    {
        return $query->where('analysis_type', 'fishbone');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Helper Methods
    public function isFiveWhys(): bool
    {
        return $this->analysis_type === '5_whys';
    }

    public function isFishbone(): bool
    {
        return $this->analysis_type === 'fishbone';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function complete(): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function review(User $reviewer): bool
    {
        return $this->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewer->id,
        ]);
    }

    public function getFiveWhysChain(): array
    {
        return array_filter([
            'why_1' => $this->why_1,
            'why_2' => $this->why_2,
            'why_3' => $this->why_3,
            'why_4' => $this->why_4,
            'why_5' => $this->why_5,
        ]);
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'draft' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Draft</span>',
            'in_progress' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">In Progress</span>',
            'completed' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Completed</span>',
            'reviewed' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Reviewed</span>',
        ];

        return $badges[$this->status] ?? '';
    }
}
