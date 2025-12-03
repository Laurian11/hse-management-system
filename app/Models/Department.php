<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Department extends Model
{
    protected $fillable = [
        'company_id',
        'parent_department_id',
        'name',
        'code',
        'description',
        'head_of_department_id',
        'hse_officer_id',
        'location',
        'risk_profile',
        'hse_objectives',
        'is_active',
    ];

    protected $casts = [
        'risk_profile' => 'array',
        'hse_objectives' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parentDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_department_id');
    }

    public function childDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_department_id');
    }

    public function headOfDepartment(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_of_department_id');
    }

    public function hseOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hse_officer_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'department_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

    public function toolboxTalks(): HasMany
    {
        return $this->hasMany(ToolboxTalk::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_department_id');
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    // Helper Methods
    public function getFullPath(): string
    {
        $path = [];
        $current = $this;

        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->parentDepartment;
        }

        return implode(' > ', $path);
    }

    public function getEmployeeCount(): int
    {
        return $this->employees()->where('is_active', true)->count();
    }

    public function getAllEmployeesCount(): int
    {
        $count = $this->getEmployeeCount();
        
        foreach ($this->childDepartments as $child) {
            $count += $child->getAllEmployeesCount();
        }

        return $count;
    }

    public function getHierarchyLevel(): int
    {
        $level = 0;
        $current = $this;

        while ($current->parentDepartment) {
            $level++;
            $current = $current->parentDepartment;
        }

        return $level;
    }

    public function canBeDeleted(): bool
    {
        return $this->employees()->count() === 0 && 
               $this->childDepartments()->count() === 0;
    }

    public function getRiskLevel(): string
    {
        if (!$this->risk_profile) {
            return 'Low';
        }

        $riskScore = 0;
        foreach ($this->risk_profile as $factor => $score) {
            $riskScore += $score;
        }

        if ($riskScore >= 15) return 'High';
        if ($riskScore >= 8) return 'Medium';
        return 'Low';
    }

    public function getRiskBadge(): string
    {
        $level = $this->getRiskLevel();
        $colors = [
            'Low' => 'bg-green-100 text-green-800',
            'Medium' => 'bg-yellow-100 text-yellow-800',
            'High' => 'bg-red-100 text-red-800',
        ];

        $color = $colors[$level] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . 
               $level . '</span>';
    }

    public function getHSEObjectivesSummary(): string
    {
        if (!$this->hse_objectives) {
            return 'No objectives set';
        }

        return count($this->hse_objectives) . ' objectives defined';
    }

    public function getActiveIncidentsCount(): int
    {
        return $this->incidents()->whereIn('status', ['open', 'investigating'])->count();
    }

    public function getPendingAuditsCount(): int
    {
        return $this->audits()->whereIn('status', ['planned', 'in_progress'])->count();
    }

    public function getUpcomingTalksCount(): int
    {
        return $this->toolboxTalks()
                   ->where('scheduled_date', '>=', now())
                   ->where('status', 'scheduled')
                   ->count();
    }

    public function getPerformanceMetrics(): array
    {
        return [
            'employee_count' => $this->getAllEmployeesCount(),
            'active_incidents' => $this->getActiveIncidentsCount(),
            'pending_audits' => $this->getPendingAuditsCount(),
            'upcoming_talks' => $this->getUpcomingTalksCount(),
            'risk_level' => $this->getRiskLevel(),
            'hse_objectives' => $this->getHSEObjectivesSummary(),
        ];
    }

    // Static Methods
    public static function getLocations(): array
    {
        return [
            'main_office' => 'Main Office',
            'warehouse' => 'Warehouse',
            'production' => 'Production Area',
            'maintenance' => 'Maintenance Workshop',
            'laboratory' => 'Laboratory',
            'field_site' => 'Field Site',
            'remote' => 'Remote Location',
        ];
    }

    public static function getRiskFactors(): array
    {
        return [
            'chemical_handling' => 'Chemical Handling',
            'heavy_machinery' => 'Heavy Machinery',
            'working_at_height' => 'Working at Height',
            'electrical_work' => 'Electrical Work',
            'confined_spaces' => 'Confined Spaces',
            'fire_hazard' => 'Fire Hazard',
            'noise_exposure' => 'Noise Exposure',
            'ergonomic_risk' => 'Ergonomic Risk',
        ];
    }
}
