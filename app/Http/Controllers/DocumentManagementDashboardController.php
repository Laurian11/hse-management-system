<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HSEDocument;
use App\Models\DocumentVersion;
use App\Models\DocumentTemplate;
use Carbon\Carbon;

class DocumentManagementDashboardController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;

        $stats = [
            'total_documents' => HSEDocument::forCompany($companyId)->count(),
            'active_documents' => HSEDocument::forCompany($companyId)->active()->count(),
            'pending_approval' => HSEDocument::forCompany($companyId)->where('status', 'under_review')->count(),
            'expiring_soon' => HSEDocument::forCompany($companyId)->expiringSoon(30)->count(),
            'requiring_review' => HSEDocument::forCompany($companyId)->requiringReview()->count(),
            'total_versions' => DocumentVersion::whereHas('document', fn($q) => $q->where('company_id', $companyId))->count(),
            'total_templates' => DocumentTemplate::forCompany($companyId)->count(),
            'active_templates' => DocumentTemplate::forCompany($companyId)->active()->count(),
        ];

        $recentDocuments = HSEDocument::forCompany($companyId)
            ->with(['creator', 'department', 'approver'])
            ->latest()
            ->limit(10)
            ->get();

        $recentVersions = DocumentVersion::whereHas('document', fn($q) => $q->where('company_id', $companyId))
            ->with(['document', 'creator'])
            ->latest()
            ->limit(10)
            ->get();

        $documentTypeDistribution = HSEDocument::forCompany($companyId)
            ->selectRaw('document_type, COUNT(*) as count')
            ->groupBy('document_type')
            ->pluck('count', 'document_type')
            ->toArray();

        $statusDistribution = HSEDocument::forCompany($companyId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('documents.dashboard', compact('stats', 'recentDocuments', 'recentVersions', 'documentTypeDistribution', 'statusDistribution'));
    }
}
