<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'department_id',
        'direct_supervisor_id',
        'employee_id_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'profile_photo',
        'date_of_birth',
        'nationality',
        'blood_group',
        'emergency_contacts',
        'date_of_hire',
        'date_of_termination',
        'employment_type',
        'employment_status',
        'job_title',
        'job_role_code',
        'job_competency_matrix_id',
        'hse_training_history',
        'competency_certificates',
        'known_allergies',
        'biometric_template_id',
        'notes',
        'is_active',
        'deactivated_at',
        'deactivation_reason',
    ];

    protected $casts = [
        'emergency_contacts' => 'array',
        'hse_training_history' => 'array',
        'competency_certificates' => 'array',
        'known_allergies' => 'array',
        'date_of_birth' => 'date',
        'date_of_hire' => 'date',
        'date_of_termination' => 'date',
        'is_active' => 'boolean',
        'deactivated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function directSupervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'direct_supervisor_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'direct_supervisor_id');
    }

    public function jobCompetencyMatrix(): BelongsTo
    {
        return $this->belongsTo(JobCompetencyMatrix::class, 'job_competency_matrix_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('employment_status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where(function($q) {
            $q->where('is_active', false)
              ->orWhere('employment_status', '!=', 'active');
        });
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeWithUserAccess($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function scopeWithoutUserAccess($query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeByEmploymentStatus($query, $status)
    {
        return $query->where('employment_status', $status);
    }

    /**
     * Helper Methods
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->full_name . ' (' . $this->employee_id_number . ')';
    }

    public function hasUserAccess(): bool
    {
        return !is_null($this->user_id);
    }

    public function canLogin(): bool
    {
        return $this->hasUserAccess() && 
               $this->user && 
               $this->user->is_active && 
               !$this->user->isLocked();
    }

    public function getEmploymentDurationAttribute(): ?string
    {
        if (!$this->date_of_hire) {
            return null;
        }

        $endDate = $this->date_of_termination ?? now();
        $years = $this->date_of_hire->diffInYears($endDate);
        $months = $this->date_of_hire->diffInMonths($endDate) % 12;

        if ($years > 0) {
            return $years . ' year' . ($years > 1 ? 's' : '') . 
                   ($months > 0 ? ' ' . $months . ' month' . ($months > 1 ? 's' : '') : '');
        }

        return $months . ' month' . ($months > 1 ? 's' : '');
    }

    public function isTerminated(): bool
    {
        return $this->employment_status === 'terminated' || !is_null($this->date_of_termination);
    }

    public function isOnLeave(): bool
    {
        return $this->employment_status === 'on_leave';
    }

    public function isSuspended(): bool
    {
        return $this->employment_status === 'suspended';
    }

    /**
     * Static Methods
     */
    public static function getEmploymentTypes(): array
    {
        return [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contractor' => 'Contractor',
            'visitor' => 'Visitor',
            'intern' => 'Intern',
        ];
    }

    public static function getEmploymentStatuses(): array
    {
        return [
            'active' => 'Active',
            'on_leave' => 'On Leave',
            'suspended' => 'Suspended',
            'terminated' => 'Terminated',
        ];
    }
}
