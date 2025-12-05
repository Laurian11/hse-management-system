<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class FiveSAudit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'audit_date', 'department_id', 'area',
        'audited_by', 'team_leader_id', 'sort_score', 'set_score', 'shine_score',
        'standardize_score', 'sustain_score', 'total_score', 'overall_rating',
        'sort_findings', 'set_findings', 'shine_findings', 'standardize_findings',
        'sustain_findings', 'strengths', 'weaknesses', 'improvement_actions',
        'next_audit_date', 'status', 'notes',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'next_audit_date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($audit) {
            if (empty($audit->reference_number)) {
                $audit->reference_number = $audit->generateReferenceNumber();
            }
            // Auto-calculate total score
            if (!$audit->total_score) {
                $audit->total_score = ($audit->sort_score + $audit->set_score + 
                    $audit->shine_score + $audit->standardize_score + $audit->sustain_score) / 5;
            }
        });

        static::updating(function ($audit) {
            // Recalculate total score if any individual score changed
            if ($audit->isDirty(['sort_score', 'set_score', 'shine_score', 'standardize_score', 'sustain_score'])) {
                $audit->total_score = ($audit->sort_score + $audit->set_score + 
                    $audit->shine_score + $audit->standardize_score + $audit->sustain_score) / 5;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function auditedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_by');
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('overall_rating', $rating);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = '5S-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
