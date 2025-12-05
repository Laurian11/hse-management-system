<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class NonConformanceReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'reference_number', 'inspection_id', 'title', 'description',
        'severity', 'status', 'identified_date', 'identified_by', 'department_id', 'location',
        'root_cause', 'immediate_action', 'corrective_action_id', 'corrective_action_plan',
        'corrective_action_due_date', 'corrective_action_assigned_to',
        'corrective_action_completed', 'corrective_action_completed_date',
        'verification_required', 'verified_by', 'verified_at', 'verification_notes',
        'closed_by', 'closed_at', 'closure_notes',
    ];

    protected $casts = [
        'identified_date' => 'date',
        'corrective_action_due_date' => 'date',
        'corrective_action_completed_date' => 'date',
        'verified_at' => 'date',
        'closed_at' => 'date',
        'corrective_action_completed' => 'boolean',
        'verification_required' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($ncr) {
            if (empty($ncr->reference_number)) {
                $ncr->reference_number = $ncr->generateReferenceNumber();
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function identifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'identified_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function correctiveAction(): BelongsTo
    {
        return $this->belongsTo(CAPA::class, 'corrective_action_id');
    }

    public function correctiveActionAssignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrective_action_assigned_to');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeRequiresAction($query)
    {
        return $query->whereIn('status', ['open', 'investigating', 'corrective_action'])
                     ->where('corrective_action_completed', false);
    }

    public function generateReferenceNumber(): string
    {
        $prefix = 'NCR-' . Carbon::now()->format('Ymd');
        $latest = static::where('reference_number', 'like', $prefix . '%')
                        ->orderBy('id', 'desc')
                        ->first();
        $nextId = $latest ? (int) substr($latest->reference_number, -3) + 1 : 1;
        return $prefix . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }
}
