<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'company']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('is_critical')) {
            $query->where('is_critical', $request->boolean('is_critical'));
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->latest()->paginate(50);
        $users = User::where('is_active', true)->get();
        $companies = Company::active()->get();
        $modules = ActivityLog::getModules();
        $actions = ActivityLog::getActions();

        return view('admin.activity-logs.index', compact('logs', 'users', 'companies', 'modules', 'actions'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['user', 'company']);

        return view('admin.activity-logs.show', compact('activityLog'));
    }

    public function critical(Request $request)
    {
        $query = ActivityLog::critical()
                           ->with(['user', 'company'])
                           ->latest();

        // Apply same filters as index
        $this->applyFilters($query, $request);

        $logs = $query->paginate(50);
        $users = User::where('is_active', true)->get();
        $companies = Company::active()->get();

        return view('admin.activity-logs.critical', compact('logs', 'users', 'companies'));
    }

    public function loginAttempts(Request $request)
    {
        $query = ActivityLog::whereIn('action', ['login', 'logout', 'failed_login'])
                           ->with(['user', 'company'])
                           ->latest();

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->paginate(50);
        $users = User::where('is_active', true)->get();
        $companies = Company::active()->get();

        return view('admin.activity-logs.login-attempts', compact('logs', 'users', 'companies'));
    }

    public function userActivity(User $user, Request $request)
    {
        $query = ActivityLog::byUser($user->id)
                           ->with(['company'])
                           ->latest();

        // Apply filters
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->paginate(50);
        $modules = ActivityLog::getModules();
        $actions = ActivityLog::getActions();

        return view('admin.activity-logs.user-activity', compact('logs', 'user', 'modules', 'actions'));
    }

    public function companyActivity(Company $company, Request $request)
    {
        $query = ActivityLog::byCompany($company->id)
                           ->with(['user'])
                           ->latest();

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $logs = $query->paginate(50);
        $users = User::where('company_id', $company->id)->where('is_active', true)->get();
        $modules = ActivityLog::getModules();
        $actions = ActivityLog::getActions();

        return view('admin.activity-logs.company-activity', compact('logs', 'company', 'users', 'modules', 'actions'));
    }

    public function dashboard()
    {
        $stats = [
            'total_logs' => ActivityLog::count(),
            'critical_events' => ActivityLog::critical()->count(),
            'login_attempts_today' => ActivityLog::whereIn('action', ['login', 'logout', 'failed_login'])
                                            ->whereDate('created_at', today())
                                            ->count(),
            'failed_logins_today' => ActivityLog::where('action', 'failed_login')
                                             ->whereDate('created_at', today())
                                             ->count(),
        ];

        $recentActivity = ActivityLog::with(['user', 'company'])
                                    ->latest()
                                    ->limit(20)
                                    ->get();

        $criticalEvents = ActivityLog::critical()
                                    ->with(['user', 'company'])
                                    ->latest()
                                    ->limit(10)
                                    ->get();

        $moduleActivity = ActivityLog::selectRaw('module, COUNT(*) as count')
                                   ->where('created_at', '>=', now()->subDays(7))
                                   ->groupBy('module')
                                   ->orderByDesc('count')
                                   ->limit(10)
                                   ->get();

        $topUsers = ActivityLog::selectRaw('user_id, COUNT(*) as count')
                              ->with('user')
                              ->where('created_at', '>=', now()->subDays(7))
                              ->whereNotNull('user_id')
                              ->groupBy('user_id')
                              ->orderByDesc('count')
                              ->limit(10)
                              ->get();

        return view('admin.activity-logs.dashboard', compact('stats', 'recentActivity', 'criticalEvents', 'moduleActivity', 'topUsers'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with(['user', 'company']);

        // Apply same filters as index
        $this->applyFilters($query, $request);

        $logs = $query->latest()->get();

        ActivityLog::log('export', 'admin', 'ActivityLog', null, 'Exported activity logs');

        // Generate CSV
        $filename = 'activity_logs_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Timestamp', 'User', 'Action', 'Module', 'Description', 'Company', 'IP Address', 'Critical']);
            
            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user->name ?? 'System',
                    $log->action,
                    $log->module,
                    $log->description,
                    $log->company->name ?? 'N/A',
                    $log->ip_address ?? 'N/A',
                    $log->is_critical ? 'Yes' : 'No',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $cutoffDate = now()->subDays($request->days);
        $deletedCount = ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        ActivityLog::log('cleanup', 'admin', 'ActivityLog', null, "Cleaned up {$deletedCount} old activity logs");

        return redirect()->back()->with('success', "Cleaned up {$deletedCount} old activity logs.");
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%");
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }
    }
}
