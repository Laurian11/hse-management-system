<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'parent_company_id',
        'settings',
        'timezone',
        'currency',
        'language',
        'license_type',
        'license_expiry',
        'max_users',
        'max_departments',
        'features',
        'phone',
        'email',
        'website',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'hse_policies',
        'safety_standards',
        'industry_type',
        'employee_count',
        'compliance_certifications',
        'is_active',
        'deactivated_at',
        'deactivation_reason',
    ];

    protected $casts = [
        'settings' => 'array',
        'license_expiry' => 'date',
        'features' => 'array',
        'hse_policies' => 'array',
        'safety_standards' => 'array',
        'compliance_certifications' => 'array',
        'is_active' => 'boolean',
        'deactivated_at' => 'datetime',
    ];

    // Relationships
    public function parentCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_company_id');
    }

    public function sisterCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'parent_company_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    // Get all companies in the group (parent + all sisters)
    public function getCompanyGroupIds(): array
    {
        $ids = [$this->id];
        
        // If this is a parent company, include all sister companies
        if ($this->isParentCompany()) {
            $ids = array_merge($ids, $this->sisterCompanies()->pluck('id')->toArray());
        }
        // If this is a sister company, include parent and all sisters
        elseif ($this->parent_company_id) {
            $parent = $this->parentCompany;
            if ($parent) {
                $ids[] = $parent->id;
                $ids = array_merge($ids, $parent->sisterCompanies()->where('id', '!=', $this->id)->pluck('id')->toArray());
            }
        }
        
        return array_unique($ids);
    }

    // Check if company is a parent company
    public function isParentCompany(): bool
    {
        return $this->sisterCompanies()->exists();
    }

    // Check if company is a sister company
    public function isSisterCompany(): bool
    {
        return $this->parent_company_id !== null;
    }

    // Get root parent company (handles nested structures)
    public function getRootParentCompany(): ?Company
    {
        $current = $this;
        while ($current->parent_company_id) {
            $current = $current->parentCompany;
            if (!$current) {
                break;
            }
        }
        return $current === $this ? null : $current;
    }

    // Get all descendant companies (sisters and their sisters recursively)
    public function getAllDescendantCompanies(): \Illuminate\Support\Collection
    {
        $descendants = collect();
        $sisters = $this->sisterCompanies;
        
        foreach ($sisters as $sister) {
            $descendants->push($sister);
            $descendants = $descendants->merge($sister->getAllDescendantCompanies());
        }
        
        return $descendants;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLicenseType($query, $licenseType)
    {
        return $query->where('license_type', $licenseType);
    }

    public function scopeByIndustry($query, $industry)
    {
        return $query->where('industry_type', $industry);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeParentCompanies($query)
    {
        return $query->whereNull('parent_company_id');
    }

    public function scopeSisterCompanies($query, $parentCompanyId = null)
    {
        if ($parentCompanyId) {
            return $query->where('parent_company_id', $parentCompanyId);
        }
        return $query->whereNotNull('parent_company_id');
    }

    public function scopeByParentCompany($query, $parentCompanyId)
    {
        return $query->where(function($q) use ($parentCompanyId) {
            $q->where('id', $parentCompanyId)
              ->orWhere('parent_company_id', $parentCompanyId);
        });
    }

    public function scopeInCompanyGroup($query, $companyId)
    {
        $company = Company::find($companyId);
        if (!$company) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }
        
        $groupIds = $company->getCompanyGroupIds();
        return $query->whereIn('id', $groupIds);
    }

    public function scopeLicenseExpiringSoon($query, $days = 30)
    {
        return $query->where('license_expiry', '<=', now()->addDays($days))
                    ->where('license_expiry', '>=', now());
    }

    public function scopeLicenseExpired($query)
    {
        return $query->where('license_expiry', '<', now());
    }

    // Helper Methods
    public function getLicenseTypeLabel(): string
    {
        $labels = [
            'basic' => 'Basic',
            'professional' => 'Professional',
            'enterprise' => 'Enterprise',
        ];

        return $labels[$this->license_type] ?? ucfirst($this->license_type);
    }

    public function getLicenseBadge(): string
    {
        $colors = [
            'basic' => 'bg-gray-100 text-gray-800',
            'professional' => 'bg-blue-100 text-blue-800',
            'enterprise' => 'bg-purple-100 text-purple-800',
        ];

        $color = $colors[$this->license_type] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . 
               $this->getLicenseTypeLabel() . '</span>';
    }

    public function getLicenseStatus(): string
    {
        if ($this->license_expiry < now()) {
            return 'Expired';
        }

        if ($this->license_expiry <= now()->addDays(30)) {
            return 'Expiring Soon';
        }

        return 'Active';
    }

    public function getLicenseStatusBadge(): string
    {
        $status = $this->getLicenseStatus();
        $colors = [
            'Active' => 'bg-green-100 text-green-800',
            'Expiring Soon' => 'bg-yellow-100 text-yellow-800',
            'Expired' => 'bg-red-100 text-red-800',
        ];

        $color = $colors[$status] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . 
               $status . '</span>';
    }

    public function getDaysUntilLicenseExpiry(): int
    {
        return now()->diffInDays($this->license_expiry, false);
    }

    public function getUserCount(): int
    {
        return $this->users()->count();
    }

    public function getActiveUserCount(): int
    {
        return $this->users()->where('is_active', true)->count();
    }

    public function getDepartmentCount(): int
    {
        return $this->departments()->count();
    }

    public function getActiveDepartmentCount(): int
    {
        return $this->departments()->where('is_active', true)->count();
    }

    public function getLicenseUsagePercentage(): float
    {
        if ($this->max_users === 0) {
            return 0;
        }

        return round(($this->getActiveUserCount() / $this->max_users) * 100, 2);
    }

    public function getDepartmentUsagePercentage(): float
    {
        if ($this->max_departments === 0) {
            return 0;
        }

        return round(($this->getActiveDepartmentCount() / $this->max_departments) * 100, 2);
    }

    public function isOverUserLimit(): bool
    {
        return $this->getActiveUserCount() > $this->max_users;
    }

    public function isOverDepartmentLimit(): bool
    {
        return $this->getActiveDepartmentCount() > $this->max_departments;
    }

    public function canAddUser(): bool
    {
        return $this->getActiveUserCount() < $this->max_users;
    }

    public function canAddDepartment(): bool
    {
        return $this->getActiveDepartmentCount() < $this->max_departments;
    }

    public function hasFeature($feature): bool
    {
        if (!$this->features) {
            return false;
        }

        return in_array($feature, $this->features);
    }

    public function getIndustryTypeLabel(): string
    {
        $types = self::getIndustryTypes();
        return $types[$this->industry_type] ?? ucfirst($this->industry_type);
    }

    public function getFullAddress(): string
    {
        $parts = [];

        if ($this->address) {
            $parts[] = $this->address;
        }

        if ($this->city && $this->state) {
            $parts[] = "{$this->city}, {$this->state}";
        } elseif ($this->city) {
            $parts[] = $this->city;
        } elseif ($this->state) {
            $parts[] = $this->state;
        }

        if ($this->country) {
            $parts[] = $this->country;
        }

        if ($this->postal_code) {
            $parts[] = $this->postal_code;
        }

        return implode(', ', $parts);
    }

    public function getDetailedStatistics(): array
    {
        return [
            'users' => [
                'total' => $this->getUserCount(),
                'active' => $this->getActiveUserCount(),
                'inactive' => $this->getUserCount() - $this->getActiveUserCount(),
                'usage_percentage' => $this->getLicenseUsagePercentage(),
                'limit' => $this->max_users,
                'remaining' => $this->max_users - $this->getActiveUserCount(),
            ],
            'departments' => [
                'total' => $this->getDepartmentCount(),
                'active' => $this->getActiveDepartmentCount(),
                'inactive' => $this->getDepartmentCount() - $this->getActiveDepartmentCount(),
                'usage_percentage' => $this->getDepartmentUsagePercentage(),
                'limit' => $this->max_departments,
                'remaining' => $this->max_departments - $this->getActiveDepartmentCount(),
            ],
            'license' => [
                'type' => $this->getLicenseTypeLabel(),
                'status' => $this->getLicenseStatus(),
                'expiry_date' => $this->license_expiry->format('Y-m-d'),
                'days_until_expiry' => $this->getDaysUntilLicenseExpiry(),
                'is_expired' => $this->license_expiry < now(),
            ],
        ];
    }

    public function getLicenseInformation(): array
    {
        return [
            'type' => $this->license_type,
            'expiry' => $this->license_expiry,
            'features' => $this->features ?? [],
            'user_limit' => $this->max_users,
            'department_limit' => $this->max_departments,
            'is_active' => $this->is_active,
            'usage' => [
                'users' => $this->getLicenseUsagePercentage(),
                'departments' => $this->getDepartmentUsagePercentage(),
            ],
        ];
    }

    public function getHSEMetrics(): array
    {
        return [
            'policies_count' => count($this->hse_policies ?? []),
            'standards_count' => count($this->safety_standards ?? []),
            'certifications_count' => count($this->compliance_certifications ?? []),
            'industry_type' => $this->getIndustryTypeLabel(),
            'employee_count' => $this->employee_count,
            'compliance_score' => $this->calculateComplianceScore(),
        ];
    }

    public function calculateComplianceScore(): int
    {
        $score = 0;
        $maxScore = 100;

        // HSE Policies (30 points)
        if ($this->hse_policies && count($this->hse_policies) > 0) {
            $score += 30;
        }

        // Safety Standards (25 points)
        if ($this->safety_standards && count($this->safety_standards) > 0) {
            $score += 25;
        }

        // Compliance Certifications (25 points)
        if ($this->compliance_certifications && count($this->compliance_certifications) > 0) {
            $score += 25;
        }

        // Active License (20 points)
        if ($this->license_expiry > now()) {
            $score += 20;
        }

        return $score;
    }

    // Static Methods
    public static function getLicenseTypes(): array
    {
        return [
            'basic' => 'Basic',
            'professional' => 'Professional',
            'enterprise' => 'Enterprise',
        ];
    }

    public static function getIndustryTypes(): array
    {
        return [
            'construction' => 'Construction',
            'manufacturing' => 'Manufacturing',
            'oil_gas' => 'Oil & Gas',
            'mining' => 'Mining',
            'transportation' => 'Transportation',
            'healthcare' => 'Healthcare',
            'education' => 'Education',
            'government' => 'Government',
            'hospitality' => 'Hospitality',
            'retail' => 'Retail',
            'technology' => 'Technology',
            'other' => 'Other',
        ];
    }

    public static function getCountries(): array
    {
        return [
            'United States' => 'United States',
            'Canada' => 'Canada',
            'United Kingdom' => 'United Kingdom',
            'Australia' => 'Australia',
            'Germany' => 'Germany',
            'France' => 'France',
            'Japan' => 'Japan',
            'China' => 'China',
            'India' => 'India',
            'Brazil' => 'Brazil',
            'Mexico' => 'Mexico',
            'South Africa' => 'South Africa',
            'Nigeria' => 'Nigeria',
            'Kenya' => 'Kenya',
            'Egypt' => 'Egypt',
            'UAE' => 'United Arab Emirates',
            'Saudi Arabia' => 'Saudi Arabia',
            'Singapore' => 'Singapore',
            'Malaysia' => 'Malaysia',
            'Indonesia' => 'Indonesia',
        ];
    }

    public static function getAvailableFeatures(): array
    {
        return [
            'advanced_reporting' => 'Advanced Reporting',
            'api_access' => 'API Access',
            'biometric_integration' => 'Biometric Integration',
            'mobile_app' => 'Mobile Application',
            'multi_language' => 'Multi-Language Support',
            'custom_workflows' => 'Custom Workflows',
            'audit_trail' => 'Extended Audit Trail',
            'data_export' => 'Data Export',
            'backup_service' => 'Automated Backup',
            'priority_support' => 'Priority Support',
        ];
    }
}
