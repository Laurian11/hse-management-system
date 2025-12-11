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
        // #region agent log
        $logPath = base_path('.cursor/debug.log');
        $logData = json_encode(['id'=>'log_'.time().'_cgs_entry','timestamp'=>time()*1000,'location'=>'app/Services/CompanyGroupService.php:15','message'=>'getCompanyGroupIds called','data'=>['company_id'=>$companyId],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'D'])."\n";
        @file_put_contents($logPath, $logData, FILE_APPEND);
        // #endregion
        
        if (!$companyId) {
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_cgs_empty','timestamp'=>time()*1000,'location'=>'app/Services/CompanyGroupService.php:18','message'=>'No company_id provided','data'=>[],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'D'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
            return [];
        }

        try {
            $company = Company::find($companyId);
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_cgs_find','timestamp'=>time()*1000,'location'=>'app/Services/CompanyGroupService.php:25','message'=>'Company find result','data'=>['found'=>$company?true:false],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'B'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
        } catch (\Exception $e) {
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_cgs_find_err','timestamp'=>time()*1000,'location'=>'app/Services/CompanyGroupService.php:30','message'=>'Company find failed','data'=>['message'=>$e->getMessage()],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'B'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
            throw $e;
        }
        
        if (!$company) {
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_cgs_notfound','timestamp'=>time()*1000,'location'=>'app/Services/CompanyGroupService.php:36','message'=>'Company not found, returning original ID','data'=>[],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'D'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
            return [$companyId]; // Return original ID if company not found
        }

        try {
            $result = $company->getCompanyGroupIds();
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_cgs_result','timestamp'=>time()*1000,'location'=>'app/Services/CompanyGroupService.php:43','message'=>'Company group IDs retrieved','data'=>['count'=>count($result)],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'D'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
            return $result;
        } catch (\Exception $e) {
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_cgs_result_err','timestamp'=>time()*1000,'location'=>'app/Services/CompanyGroupService.php:50','message'=>'getCompanyGroupIds failed','data'=>['message'=>$e->getMessage()],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'D'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
            throw $e;
        }
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

