<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HSEDocument;
use App\Models\DocumentVersion;
use App\Models\DocumentTemplate;
use App\Traits\UsesCompanyGroup;
use Carbon\Carbon;

class DocumentManagementDashboardController extends Controller
{
    use UsesCompanyGroup;

    public function dashboard()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();

        $stats = [
            'total_documents' => HSEDocument::whereIn('company_id', $companyGroupIds)->count(),
            'active_documents' => HSEDocument::whereIn('company_id', $companyGroupIds)->active()->count(),
            'pending_approval' => HSEDocument::whereIn('company_id', $companyGroupIds)->where('status', 'under_review')->count(),
            'expiring_soon' => HSEDocument::whereIn('company_id', $companyGroupIds)->expiringSoon(30)->count(),
            'requiring_review' => HSEDocument::whereIn('company_id', $companyGroupIds)->requiringReview()->count(),
            'total_versions' => DocumentVersion::whereHas('document', fn($q) => $q->whereIn('company_id', $companyGroupIds))->count(),
            'total_templates' => DocumentTemplate::whereIn('company_id', $companyGroupIds)->count(),
            'active_templates' => DocumentTemplate::whereIn('company_id', $companyGroupIds)->active()->count(),
        ];

        $recentDocuments = HSEDocument::whereIn('company_id', $companyGroupIds)
            ->with(['creator', 'department', 'approver'])
            ->latest()
            ->limit(10)
            ->get();

        $recentVersions = DocumentVersion::whereHas('document', fn($q) => $q->whereIn('company_id', $companyGroupIds))
            ->with(['document', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        $documentTypeDistribution = HSEDocument::whereIn('company_id', $companyGroupIds)
            ->selectRaw('document_type, COUNT(*) as count')
            ->groupBy('document_type')
            ->pluck('count', 'document_type')
            ->toArray();

        $statusDistribution = HSEDocument::whereIn('company_id', $companyGroupIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('documents.dashboard', compact('stats', 'recentDocuments', 'recentVersions', 'documentTypeDistribution', 'statusDistribution'));
    }
}
