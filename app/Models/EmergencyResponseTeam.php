<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmergencyResponseTeam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'team_type', 'description', 'responsibilities',
        'team_members', 'team_leader_id', 'deputy_leader_id', 'last_training_date',
        'next_training_date', 'training_requirements', 'certifications',
        'is_24_7', 'availability_schedule', 'contact_information',
        'assigned_equipment', 'equipment_requirements', 'is_active', 'notes',
    ];

    protected $casts = [
        'team_members' => 'array',
        'certifications' => 'array',
        'availability_schedule' => 'array',
        'contact_information' => 'array',
        'assigned_equipment' => 'array',
        'last_training_date' => 'date',
        'next_training_date' => 'date',
        'is_24_7' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function teamLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function deputyLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deputy_leader_id');
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('team_type', $type);
    }
}
