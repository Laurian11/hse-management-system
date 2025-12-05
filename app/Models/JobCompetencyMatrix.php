<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class JobCompetencyMatrix extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_number',
        'company_id',
        'job_title',
        'job_role_code',
        'department_id',
        'description',
        'required_competencies',
        'mandatory_trainings',
        'optional_trainings',
        'certification_requirements',
        'experience_requirement_months',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'is_active',
    ];

    protected $casts = [
        'required_competencies' => 'array',
        'mandatory_trainings' => 'array',
        'optional_trainings' => 'array',
        'certification_requirements' => 'array',
        'approved_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($matrix) {
            if (empty($matrix->reference_number)) {
                $matrix->reference_number = 'JCM-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($matrix) {
            ActivityLog::log('create', 'training', 'JobCompetencyMatrix', $matrix->id, "Created job competency matrix: {$matrix->job_title}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'job_competency_matrix_id');
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJobTitle($query, $jobTitle)
    {
        return $query->where('job_title', $jobTitle);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    public function approve(User $approver): bool
    {
        return $this->update([
            'status' => 'active',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
    }

    public function archive(): bool
    {
        return $this->update([
            'status' => 'archived',
            'is_active' => false,
        ]);
    }
}
