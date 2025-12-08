<?php

namespace App\Traits;

use App\Services\CompanyGroupService;
use Illuminate\Support\Facades\Auth;

trait UsesCompanyGroup
{
    /**
     * Get the current user's company ID
     */
    protected function getCompanyId(): ?int
    {
        $user = Auth::user();
        return $user ? $user->company_id : null;
    }

    /**
     * Get company group IDs (parent + all sisters) for data aggregation
     * Parent companies see data from all sisters, sister companies see only their own
     */
    protected function getCompanyGroupIds(): array
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return [];
        }
        
        return CompanyGroupService::getCompanyGroupIds($companyId);
    }

    /**
     * Check if current user's company is a parent company
     */
    protected function isParentCompany(): bool
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return false;
        }
        
        return CompanyGroupService::isParentCompany($companyId);
    }

    /**
     * Check if current user's company is a sister company
     */
    protected function isSisterCompany(): bool
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return false;
        }
        
        return CompanyGroupService::isSisterCompany($companyId);
    }
}

