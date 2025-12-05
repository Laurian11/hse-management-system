<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ComplianceRequirement;
use App\Models\PermitLicense;
use App\Models\ComplianceAudit;
use Carbon\Carbon;

class ComplianceDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_requirements' => ComplianceRequirement::forCompany($companyId)->count(),
            'compliant' => ComplianceRequirement::forCompany($companyId)->compliant()->count(),
            'non_compliant' => ComplianceRequirement::forCompany($companyId)->nonCompliant()->count(),
            'due_soon' => ComplianceRequirement::forCompany($companyId)->dueSoon(30)->count(),
            'overdue' => ComplianceRequirement::forCompany($companyId)->overdue()->count(),
            'total_permits' => PermitLicense::forCompany($companyId)->count(),
            'active_permits' => PermitLicense::forCompany($companyId)->active()->count(),
            'expiring_soon' => PermitLicense::forCompany($companyId)->expiringSoon(60)->count(),
            'expired_permits' => PermitLicense::forCompany($companyId)->expired()->count(),
            'total_audits' => ComplianceAudit::forCompany($companyId)->count(),
            'completed_audits' => ComplianceAudit::forCompany($companyId)->where('audit_status', 'completed')->count(),
        ];

        $recentRequirements = ComplianceRequirement::forCompany($companyId)
            ->with(['responsiblePerson', 'department'])
            ->latest()
            ->limit(10)
            ->get();

        $recentPermits = PermitLicense::forCompany($companyId)
            ->with(['responsiblePerson', 'department'])
            ->latest()
            ->limit(10)
            ->get();

        $recentAudits = ComplianceAudit::forCompany($companyId)
            ->with(['auditor'])
            ->latest('audit_date')
            ->limit(10)
            ->get();

        $complianceStatusDistribution = ComplianceRequirement::forCompany($companyId)
            ->selectRaw('compliance_status, COUNT(*) as count')
            ->groupBy('compliance_status')
            ->pluck('count', 'compliance_status')
            ->toArray();

        $permitStatusDistribution = PermitLicense::forCompany($companyId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('compliance.dashboard', compact('stats', 'recentRequirements', 'recentPermits', 'recentAudits', 'complianceStatusDistribution', 'permitStatusDistribution'));
    }
}
