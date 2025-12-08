<?php

namespace App\Traits;

use App\Models\Company;

trait HasCompanyGroupScope
{
    /**
     * Scope to filter by company group (parent + all sisters)
     * This allows parent companies to see data from all sister companies
     */
    public function scopeForCompanyGroup($query, $companyId)
    {
        $company = Company::find($companyId);
        
        if (!$company) {
            // If company doesn't exist, return empty result
            return $query->whereRaw('1 = 0');
        }
        
        // Get all company IDs in the group
        $groupIds = $company->getCompanyGroupIds();
        
        // Filter by company_id in the group
        return $query->whereIn('company_id', $groupIds);
    }

    /**
     * Scope to filter by company or its group
     * If company is a parent, includes all sisters
     * If company is a sister, includes parent and all sisters
     */
    public function scopeForCompanyOrGroup($query, $companyId)
    {
        return $this->scopeForCompanyGroup($query, $companyId);
    }
}

