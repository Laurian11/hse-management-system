<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityLog;

class JSA extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jsas';

    protected $fillable = [
        'reference_number',
        'company_id',
        'created_by',
        'supervisor_id',
        'department_id',
        'job_title',
        'job_description',
        'location',
        'work_area',
        'job_date',
        'start_time',
        'end_time',
        'job_steps',
        'team_members',
        'required_qualifications',
        'required_training',
        'equipment_required',
        'materials_required',
        'ppe_required',
        'weather_conditions',
        'site_conditions',
        'special_considerations',
        'emergency_contacts',
        'emergency_procedures',
        'first_aid_location',
        'evacuation_route',
        'overall_risk_level',
        'risk_summary',
        'status',
        'approved_by',
        'approved_at',
        'worker_sign_offs',
        'related_risk_assessment_id',
        'is_active',
    ];

    protected $casts = [
        'job_steps' => 'array',
        'team_members' => 'array',
        'equipment_required' => 'array',
        'materials_required' => 'array',
        'ppe_required' => 'array',
        'worker_sign_offs' => 'array',
        'job_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($jsa) {
            if (empty($jsa->reference_number)) {
                $jsa->reference_number = 'JSA-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });

        static::created(function ($jsa) {
            ActivityLog::log('create', 'risk_assessment', 'JSA', $jsa->id, "Created JSA: {$jsa->job_title}");
        });
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function relatedRiskAssessment(): BelongsTo
    {
        return $this->belongsTo(RiskAssessment::class, 'related_risk_assessment_id');
    }

    public function controlMeasures(): HasMany
    {
        return $this->hasMany(ControlMeasure::class);
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('overall_risk_level', $level);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Helper Methods
    public function getRiskLevelColor(): string
    {
        $colors = [
            'low' => 'text-green-600 bg-green-100',
            'medium' => 'text-yellow-600 bg-yellow-100',
            'high' => 'text-orange-600 bg-orange-100',
            'critical' => 'text-red-600 bg-red-100',
        ];

        return $colors[$this->overall_risk_level] ?? 'text-gray-600 bg-gray-100';
    }
}
