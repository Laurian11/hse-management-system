<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditFinding extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'audit_id', 'reference_number', 'finding_type', 'severity', 'title',
        'description', 'clause_reference', 'evidence', 'root_cause',
        'corrective_action_required', 'corrective_action_plan', 'corrective_action_due_date',
        'corrective_action_assigned_to', 'corrective_action_completed',
        'corrective_action_completed_date', 'verified_by', 'verified_at',
        'verification_notes', 'status',
    ];

    protected $casts = [
        'corrective_action_due_date' => 'date',
        'corrective_action_completed_date' => 'date',
        'verified_at' => 'date',
        'corrective_action_required' => 'boolean',
        'corrective_action_completed' => 'boolean',
    ];

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class);
    }

    public function correctiveActionAssignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrective_action_assigned_to');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeRequiresAction($query)
    {
        return $query->where('corrective_action_required', true)
                     ->where('corrective_action_completed', false);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public static function generateReferenceNumber(Audit $audit): string
    {
        $prefix = $audit->reference_number . '-FND-';
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
