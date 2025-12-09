<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role_id',
        'permissions',
        'employee_id_number',
        'phone',
        'profile_photo',
        'date_of_birth',
        'nationality',
        'blood_group',
        'emergency_contacts',
        'date_of_hire',
        'employment_type',
        'job_title',
        'job_role_code',
        'job_competency_matrix_id',
        'direct_supervisor_id',
        'hse_training_history',
        'competency_certificates',
        'known_allergies',
        'biometric_template_id',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
        'must_change_password',
        'password_changed_at',
        'is_active',
        'deactivated_at',
        'deactivation_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'biometric_template_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'emergency_contacts' => 'array',
            'hse_training_history' => 'array',
            'competency_certificates' => 'array',
            'known_allergies' => 'array',
            'date_of_birth' => 'date',
            'date_of_hire' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function directSupervisor()
    {
        return $this->belongsTo(User::class, 'direct_supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'direct_supervisor_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function userSessions()
    {
        return $this->hasMany(UserSession::class);
    }

    public function jobCompetencyMatrix()
    {
        return $this->belongsTo(JobCompetencyMatrix::class, 'job_competency_matrix_id');
    }

    public function trainingRecords()
    {
        return $this->hasMany(TrainingRecord::class, 'user_id');
    }

    public function trainingAttendances()
    {
        return $this->hasMany(TrainingAttendance::class, 'user_id');
    }

    public function competencyAssessments()
    {
        return $this->hasMany(CompetencyAssessment::class, 'user_id');
    }

    public function certificates()
    {
        return $this->hasMany(TrainingCertificate::class, 'user_id');
    }

    public function activeCertificates()
    {
        return $this->hasMany(TrainingCertificate::class, 'user_id')
                    ->where('status', 'active')
                    ->where(function($q) {
                        $q->where('has_expiry', false)
                          ->orWhere('expiry_date', '>', now());
                    });
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    /**
     * Helper Methods
     */
    public function hasPermission($permission)
    {
        // Check role permissions first
        if ($this->role && $this->role->hasPermission($permission)) {
            return true;
        }

        // Check user-specific permissions
        return in_array($permission, $this->permissions ?? []);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function givePermission($permission): self
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
        return $this;
    }

    public function revokePermission($permission): self
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_values(array_diff($permissions, [$permission]));
        $this->update(['permissions' => $permissions]);
        return $this;
    }

    public function syncPermissions(array $permissions): self
    {
        $this->update(['permissions' => array_values($permissions)]);
        return $this;
    }

    public function getAllPermissions(): array
    {
        $permissions = [];
        
        // Get role permissions
        if ($this->role) {
            $permissions = array_merge($permissions, $this->role->getPermissionNames());
        }
        
        // Get user-specific permissions
        $permissions = array_merge($permissions, $this->permissions ?? []);
        
        return array_unique($permissions);
    }

    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function canLogin()
    {
        return $this->is_active && !$this->isLocked();
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->employee_id_number . ')';
    }

    /**
     * Events
     */
    protected static function booted()
    {
        static::created(function ($user) {
            ActivityLog::log('create', 'users', 'User', $user->id, 'User created: ' . $user->name);
        });

        static::updated(function ($user) {
            ActivityLog::log('update', 'users', 'User', $user->id, 'User updated: ' . $user->name);
        });

        static::deleted(function ($user) {
            ActivityLog::log('delete', 'users', 'User', $user->id, 'User deleted: ' . $user->name);
        });
    }
}
