<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ComplianceRequirement;
use App\Models\PermitLicense;
use App\Models\ComplianceAudit;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class ComplianceDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();

        $stats = [
            'total_requirements' => ComplianceRequirement::whereIn('company_id', $companyGroupIds)->count(),
            'compliant' => ComplianceRequirement::whereIn('company_id', $companyGroupIds)->compliant()->count(),
            'non_compliant' => ComplianceRequirement::whereIn('company_id', $companyGroupIds)->nonCompliant()->count(),
            'due_soon' => ComplianceRequirement::whereIn('company_id', $companyGroupIds)->dueSoon(30)->count(),
            'overdue' => ComplianceRequirement::whereIn('company_id', $companyGroupIds)->overdue()->count(),
            'total_permits' => PermitLicense::whereIn('company_id', $companyGroupIds)->count(),
            'active_permits' => PermitLicense::whereIn('company_id', $companyGroupIds)->active()->count(),
            'expiring_soon' => PermitLicense::whereIn('company_id', $companyGroupIds)->expiringSoon(60)->count(),
            'expired_permits' => PermitLicense::whereIn('company_id', $companyGroupIds)->expired()->count(),
            'total_audits' => ComplianceAudit::whereIn('company_id', $companyGroupIds)->count(),
            'completed_audits' => ComplianceAudit::whereIn('company_id', $companyGroupIds)->where('audit_status', 'completed')->count(),
        ];

        $recentRequirements = ComplianceRequirement::whereIn('company_id', $companyGroupIds)
            ->with(['responsiblePerson', 'department'])
            ->latest()
            ->limit(10)
            ->get();

        $recentPermits = PermitLicense::whereIn('company_id', $companyGroupIds)
            ->with(['responsiblePerson', 'department'])
            ->latest()
            ->limit(10)
            ->get();

        $recentAudits = ComplianceAudit::whereIn('company_id', $companyGroupIds)
            ->with(['auditor'])
            ->latest('audit_date')
            ->limit(10)
            ->get();

        $complianceStatusDistribution = ComplianceRequirement::whereIn('company_id', $companyGroupIds)
            ->selectRaw('compliance_status, COUNT(*) as count')
            ->groupBy('compliance_status')
            ->pluck('count', 'compliance_status')
            ->toArray();

        $permitStatusDistribution = PermitLicense::whereIn('company_id', $companyGroupIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('compliance.dashboard', compact('stats', 'recentRequirements', 'recentPermits', 'recentAudits', 'complianceStatusDistribution', 'permitStatusDistribution'));
    }
}
