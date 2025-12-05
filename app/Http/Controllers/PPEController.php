<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PPEItem;
use App\Models\PPEIssuance;
use App\Models\PPEInspection;
use App\Models\PPESupplier;
use App\Models\PPEComplianceReport;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class PPEController extends Controller
{
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;
        
        // Statistics
        $stats = [
            'total_items' => PPEItem::forCompany($companyId)->count(),
            'low_stock_items' => PPEItem::forCompany($companyId)->lowStock()->count(),
            'active_issuances' => PPEIssuance::forCompany($companyId)->active()->count(),
            'expired_issuances' => PPEIssuance::forCompany($companyId)->expired()->count(),
            'expiring_soon' => PPEIssuance::forCompany($companyId)->expiringSoon()->count(),
            'overdue_inspections' => PPEIssuance::forCompany($companyId)->needsInspection()->count(),
            'non_compliant' => PPEInspection::forCompany($companyId)->nonCompliant()->count(),
            'total_suppliers' => PPESupplier::forCompany($companyId)->active()->count(),
        ];
        
        // Recent activity
        $recentIssuances = PPEIssuance::forCompany($companyId)
            ->with(['ppeItem', 'issuedTo', 'department'])
            ->latest()
            ->limit(10)
            ->get();
        
        $recentInspections = PPEInspection::forCompany($companyId)
            ->with(['ppeItem', 'user', 'inspectedBy'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Low stock items
        $lowStockItems = PPEItem::forCompany($companyId)
            ->lowStock()
            ->with('supplier')
            ->get();
        
        // Expiring soon
        $expiringSoon = PPEIssuance::forCompany($companyId)
            ->expiringSoon()
            ->with(['ppeItem', 'issuedTo'])
            ->get();
        
        // Overdue inspections
        $overdueInspections = PPEIssuance::forCompany($companyId)
            ->needsInspection()
            ->with(['ppeItem', 'issuedTo'])
            ->get();
        
        // Chart data - Monthly issuances (last 6 months)
        $monthlyIssuances = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthLabels[] = $month->format('M');
            $monthIssuances = PPEIssuance::forCompany($companyId)
                ->whereYear('issue_date', $month->year)
                ->whereMonth('issue_date', $month->month)
                ->count();
            $monthlyIssuances[] = $monthIssuances;
        }
        
        // Chart data - Category distribution
        $categoryDistribution = PPEItem::forCompany($companyId)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
        
        // Chart data - Status distribution
        $statusDistribution = PPEIssuance::forCompany($companyId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        return view('ppe.dashboard', compact(
            'stats', 
            'recentIssuances', 
            'recentInspections', 
            'lowStockItems', 
            'expiringSoon', 
            'overdueInspections',
            'monthlyIssuances',
            'monthLabels',
            'categoryDistribution',
            'statusDistribution'
        ));
    }
}

