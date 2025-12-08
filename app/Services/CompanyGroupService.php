<?php

namespace App\Services;

use App\Models\Company;

class CompanyGroupService
{
    /**
     * Get all company IDs in a company group (parent + all sisters)
     * 
     * @param int|null $companyId
     * @return array
     */
    public static function getCompanyGroupIds(?int $companyId): array
    {
        if (!$companyId) {
            return [];
        }

        $company = Company::find($companyId);
        
        if (!$company) {
            return [$companyId]; // Return original ID if company not found
        }

        return $company->getCompanyGroupIds();
    }

    /**
     * Check if a company is a parent company
     * 
     * @param int|null $companyId
     * @return bool
     */
    public static function isParentCompany(?int $companyId): bool
    {
        if (!$companyId) {
            return false;
        }

        $company = Company::find($companyId);
        
        return $company ? $company->isParentCompany() : false;
    }

    /**
     * Check if a company is a sister company
     * 
     * @param int|null $companyId
     * @return bool
     */
    public static function isSisterCompany(?int $companyId): bool
    {
        if (!$companyId) {
            return false;
        }

        $company = Company::find($companyId);
        
        return $company ? $company->isSisterCompany() : false;
    }

    /**
     * Get parent company for a given company
     * 
     * @param int|null $companyId
     * @return Company|null
     */
    public static function getParentCompany(?int $companyId): ?Company
    {
        if (!$companyId) {
            return null;
        }

        $company = Company::find($companyId);
        
        return $company ? $company->parentCompany : null;
    }

    /**
     * Get all sister companies for a given company
     * 
     * @param int|null $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getSisterCompanies(?int $companyId)
    {
        if (!$companyId) {
            return collect();
        }

        $company = Company::find($companyId);
        
        if (!$company) {
            return collect();
        }

        // If this is a parent company, return its sisters
        if ($company->isParentCompany()) {
            return $company->sisterCompanies;
        }

        // If this is a sister company, return parent's sisters (excluding self)
        if ($company->isSisterCompany() && $company->parentCompany) {
            return $company->parentCompany->sisterCompanies()->where('id', '!=', $companyId)->get();
        }

        return collect();
    }
}

