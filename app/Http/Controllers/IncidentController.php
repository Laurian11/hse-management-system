<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Notifications\IncidentReportedNotification;
use App\Notifications\IncidentStatusChangedNotification;
use App\Traits\ChecksPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class IncidentController extends Controller
{
    use ChecksPermissions;
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $isSuperAdmin = $user->role && $user->role->name === 'super_admin';
        
        // Super admins can see all incidents including unassigned ones
        if ($isSuperAdmin) {
            $companyGroupIds = null; // Don't filter by company for super admin
            $query = Incident::with(['reporter', 'assignedTo', 'department', 'company', 'investigation', 'rootCauseAnalysis']);
            
            // Filter for unassigned incidents if requested
            if ($request->has('unassigned') && $request->boolean('unassigned')) {
                $query->whereNull('company_id');
            }
        } else {
            $companyGroupIds = \App\Services\CompanyGroupService::getCompanyGroupIds($companyId);
            $query = Incident::whereIn('company_id', $companyGroupIds)
                ->with(['reporter', 'assignedTo', 'department', 'investigation', 'rootCauseAnalysis']);
        }
        
        // Apply filters
        if ($request->has('filter')) {
            $filter = $request->get('filter');
            switch ($filter) {
                case 'open':
                    $query->open();
                    break;
                case 'investigating':
                    $query->investigating();
                    break;
                case 'injury':
                    $query->injuryIllness();
                    break;
                case 'property':
                    $query->propertyDamage();
                    break;
                case 'near_miss':
                    $query->nearMiss();
                    break;
                case 'critical':
                    $query->critical();
                    break;
            }
        }
        
        if ($request->has('status') && $request->get('status')) {
            $query->byStatus($request->get('status'));
        }
        
        if ($request->has('severity') && $request->get('severity')) {
            $query->bySeverity($request->get('severity'));
        }
        
        if ($request->has('event_type') && $request->get('event_type')) {
            $query->byEventType($request->get('event_type'));
        }
        
        if ($request->has('date_from') && $request->get('date_from')) {
            $query->where('incident_date', '>=', $request->get('date_from'));
        }
        
        if ($request->has('date_to') && $request->get('date_to')) {
            $query->where('incident_date', '<=', $request->get('date_to'));
        }
        
        // Sorting
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort column
        $allowedSortColumns = ['reference_number', 'title', 'event_type', 'severity', 'status', 'department_id', 'incident_date', 'created_at'];
        if (!in_array($sortColumn, $allowedSortColumns)) {
            $sortColumn = 'created_at';
        }
        
        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $incidents = $query->orderBy($sortColumn, $sortDirection)->paginate(15);
        
        // Append query parameters to pagination links
        $incidents->appends($request->query());
        
        // Get unassigned count for super admins
        $unassignedCount = $isSuperAdmin ? Incident::whereNull('company_id')->count() : 0;
        $companies = $isSuperAdmin ? \App\Models\Company::where('is_active', true)->get() : collect();
        
        return view('incidents.index', compact('incidents', 'unassignedCount', 'companies', 'isSuperAdmin'));
    }
    
    /**
     * Assign an incident to a company (Super Admin only)
     */
    public function assignCompany(Request $request, Incident $incident)
    {
        $user = Auth::user();
        
        // Only super admins can assign incidents
        if (!$user->role || $user->role->name !== 'super_admin') {
            abort(403, 'Only super administrators can assign incidents to companies.');
        }
        
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);
        
        $company = \App\Models\Company::findOrFail($request->company_id);
        
        $incident->update([
            'company_id' => $company->id,
        ]);
        
        \App\Models\ActivityLog::log('update', 'incidents', 'Incident', $incident->id, 
            "Incident {$incident->reference_number} assigned to company: {$company->name}");
        
        return redirect()->back()->with('success', "Incident {$incident->reference_number} has been assigned to {$company->name}.");
    }

    public function dashboard()
    {
        $companyId = Auth::user()->company_id;
        $companyGroupIds = \App\Services\CompanyGroupService::getCompanyGroupIds($companyId);
        
        $stats = [
            'total' => Incident::whereIn('company_id', $companyGroupIds)->count(),
            'open' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'open')->count(),
            'investigating' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'investigating')->count(),
            'closed' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'closed')->count(),
        ];
        
        $recentIncidents = Incident::whereIn('company_id', $companyGroupIds)
            ->with(['reporter', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('incidents.dashboard', compact('stats', 'recentIncidents'));
    }

    public function create(Request $request)
    {
        if (!auth()->user()->hasPermission('incidents.create')) {
            abort(403, 'You do not have permission to create incidents.');
        }
        $companyId = Auth::user()->company_id;
        $companyGroupIds = \App\Services\CompanyGroupService::getCompanyGroupIds($companyId);
        
        $departments = \App\Models\Department::whereIn('company_id', $companyGroupIds)->get();
        $users = \App\Models\User::whereIn('company_id', $companyGroupIds)->get();
        
        $copyFrom = null;
        if ($request->has('copy_from')) {
            $copyFrom = Incident::where('company_id', $companyId)
                ->findOrFail($request->get('copy_from'));
        }
        
        return view('incidents.create', compact('departments', 'users', 'copyFrom'));
    }

    public function store(StoreIncidentRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $data['company_id'] = $user->company_id;
        $data['reported_by'] = $user->id;
        $data['reporter_name'] = $user->name;
        $data['reporter_email'] = $user->email;
        $data['reporter_phone'] = $user->phone;
        $data['incident_date'] = $data['date_occurred'];
        $data['status'] = 'open';
        $data['incident_type'] = $data['title']; // Use title as incident type for now

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('incident-images', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $incident = Incident::create($data);

        // Send notifications
        $notifyUsers = collect();
        
        // Notify assigned user if assigned
        if ($incident->assignedTo) {
            $notifyUsers->push($incident->assignedTo);
        }
        
        // Notify HSE managers
        $hseManagers = \App\Models\User::where('company_id', $user->company_id)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['hse_manager', 'hse_officer', 'admin']);
            })
            ->get();
        
        $notifyUsers = $notifyUsers->merge($hseManagers)->unique('id');
        
        foreach ($notifyUsers as $notifyUser) {
            $notifyUser->notify(new IncidentReportedNotification($incident));
        }

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'Incident reported successfully!');
    }

    public function show(Incident $incident)
    {
        // Check if user can view this incident (same company or super admin)
        $this->authorizeCompanyResource($incident->company_id);
        
        $incident->load([
            'reporter', 
            'assignedTo', 
            'department', 
            'investigations',
            'investigation',
            'rootCauseAnalysis',
            'capas.relatedTrainingNeed',
            'capas.assignedTo',
            'attachments',
            'approvedBy',
            // Risk Assessment Integration
            'relatedHazard',
            'relatedRiskAssessment',
            'relatedJSA',
            'controlMeasures'
        ]);
        
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        if (!auth()->user()->hasPermission('incidents.edit')) {
            abort(403, 'You do not have permission to edit incidents.');
        }
        // Check if user can edit this incident (same company or super admin)
        $this->authorizeCompanyResource($incident->company_id);
        
        $user = Auth::user();
        $companyId = $user->company_id;
        
        // For super admin, get all departments and users
        if (!$companyId) {
            $departments = \App\Models\Department::all();
            $users = \App\Models\User::all();
        } else {
            $departments = \App\Models\Department::where('company_id', $companyId)->get();
            $users = \App\Models\User::where('company_id', $companyId)->get();
        }
        
        return view('incidents.edit', compact('incident', 'departments', 'users'));
    }

    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        // Check if user can update this incident (same company or super admin)
        $this->authorizeCompanyResource($incident->company_id);

        $data = $request->validated();
        $data['incident_date'] = $data['date_occurred'] ?? $incident->incident_date;
        $data['incident_type'] = $data['title']; // Use title as incident type for now

        // Track status change for notifications
        $oldStatus = $incident->status;
        $statusChanged = isset($data['status']) && $data['status'] !== $oldStatus;

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('incident-images', 'public');
                $imagePaths[] = $path;
            }
            // Merge with existing images
            $existingImages = $incident->images ?? [];
            $data['images'] = array_merge($existingImages, $imagePaths);
        }

        $incident->update($data);
        
        // Send notification if status changed
        if ($statusChanged) {
            $this->notifyStatusChange($incident, $oldStatus, $data['status']);
        }

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'Incident updated successfully!');
    }

    public function destroy(Incident $incident)
    {
        if (!auth()->user()->hasPermission('incidents.delete')) {
            abort(403, 'You do not have permission to delete incidents.');
        }
        // Check if user can delete this incident (same company or super admin)
        $this->authorizeCompanyResource($incident->company_id);

        $incident->delete();

        return redirect()->route('incidents.index')
            ->with('success', 'Incident deleted successfully!');
    }

    // Specialized methods for incident workflow
    public function assign(Request $request, Incident $incident)
    {
        // Check if user can update this incident (same company or super admin)
        $this->authorizeCompanyResource($incident->company_id);

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $oldStatus = $incident->status;
        $incident->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'investigating',
        ]);
        
        // Send notification if status changed
        if ($oldStatus !== 'investigating') {
            $this->notifyStatusChange($incident, $oldStatus, 'investigating');
        }

        return back()->with('success', 'Incident assigned successfully!');
    }

    public function investigate(Incident $incident)
    {
        // Check if user can update this incident (same company or super admin)
        $this->authorizeCompanyResource($incident->company_id);

        $oldStatus = $incident->status;
        $incident->update(['status' => 'investigating']);
        
        // Send notification if status changed
        if ($oldStatus !== 'investigating') {
            $this->notifyStatusChange($incident, $oldStatus, 'investigating');
        }

        return back()->with('success', 'Investigation started!');
    }

    public function close(Request $request, Incident $incident)
    {
        // Check if user can update this incident (same company or super admin)
        $this->authorizeCompanyResource($incident->company_id);

        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $oldStatus = $incident->status;
        $incident->close($request->resolution_notes);
        
        // Send notification if status changed
        if ($oldStatus !== 'closed') {
            $this->notifyStatusChange($incident, $oldStatus, 'closed');
        }

        return back()->with('success', 'Incident closed successfully!');
    }

    public function reopen(Incident $incident)
    {
        // Check if user can update this incident (same company or super admin)
        $this->authorizeCompanyResource($incident->company_id);

        $oldStatus = $incident->status;
        $incident->update([
            'status' => 'open',
            'closure_status' => null,
            'resolution_notes' => null,
            'closed_at' => null,
        ]);
        
        // Send notification if status changed
        if ($oldStatus !== 'open') {
            $this->notifyStatusChange($incident, $oldStatus, 'open');
        }

        return back()->with('success', 'Incident reopened!');
    }

    /**
     * Request closure approval
     */
    public function requestClosure(Request $request, Incident $incident)
    {
        $this->authorizeCompanyResource($incident->company_id);

        // Validate that incident can be closed
        if (!$incident->canBeClosed()) {
            return back()->with('error', 'Incident cannot be closed. Please ensure investigation, RCA, and all CAPAs are completed.');
        }

        $workflow = $request->input('workflow', null);
        $incident->requestClosureApproval($workflow);

        return back()->with('success', 'Closure approval requested!');
    }

    /**
     * Approve incident closure
     */
    public function approveClosure(Incident $incident)
    {
        $this->authorizeCompanyResource($incident->company_id);

        $incident->approveClosure(Auth::user());
        $incident->close();

        return back()->with('success', 'Incident closure approved!');
    }

    /**
     * Reject incident closure
     */
    public function rejectClosure(Request $request, Incident $incident)
    {
        $this->authorizeCompanyResource($incident->company_id);

        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $incident->rejectClosure(Auth::user(), $request->rejection_reason);

        return back()->with('success', 'Closure request rejected.');
    }

    /**
     * Trend Analysis Dashboard
     */
    public function trendAnalysis()
    {
        $companyId = Auth::user()->company_id;
        
        // Overall Statistics
        $stats = [
            'total_incidents' => Incident::forCompany($companyId)->count(),
            'injury_illness' => Incident::forCompany($companyId)->injuryIllness()->count(),
            'property_damage' => Incident::forCompany($companyId)->propertyDamage()->count(),
            'near_miss' => Incident::forCompany($companyId)->nearMiss()->count(),
            'open' => Incident::forCompany($companyId)->open()->count(),
            'investigating' => Incident::forCompany($companyId)->investigating()->count(),
            'closed' => Incident::forCompany($companyId)->closed()->count(),
        ];

        // Monthly Trends (Last 12 Months)
        $monthlyTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthIncidents = Incident::forCompany($companyId)
                ->whereYear('incident_date', $month->year)
                ->whereMonth('incident_date', $month->month)
                ->get();
            
            $monthlyTrends[] = [
                'month' => $month->format('M Y'),
                'total' => $monthIncidents->count(),
                'injury_illness' => $monthIncidents->where('event_type', 'injury_illness')->count(),
                'property_damage' => $monthIncidents->where('event_type', 'property_damage')->count(),
                'near_miss' => $monthIncidents->where('event_type', 'near_miss')->count(),
            ];
        }

        // Severity Distribution
        $severityDistribution = [
            'critical' => Incident::forCompany($companyId)->critical()->count(),
            'high' => Incident::forCompany($companyId)->high()->count(),
            'medium' => Incident::forCompany($companyId)->bySeverity('medium')->count(),
            'low' => Incident::forCompany($companyId)->bySeverity('low')->count(),
        ];

        // Event Type Distribution
        $eventTypeDistribution = [
            'injury_illness' => Incident::forCompany($companyId)->injuryIllness()->count(),
            'property_damage' => Incident::forCompany($companyId)->propertyDamage()->count(),
            'near_miss' => Incident::forCompany($companyId)->nearMiss()->count(),
            'environmental' => Incident::forCompany($companyId)->byEventType('environmental')->count(),
            'other' => Incident::forCompany($companyId)->byEventType('other')->count(),
        ];

        // Department Analysis
        $departments = \App\Models\Department::where('company_id', $companyId)->get();
        $departmentStats = $departments->map(function($dept) use ($companyId) {
            $incidents = Incident::forCompany($companyId)
                ->where('department_id', $dept->id)
                ->get();
            
            return [
                'name' => $dept->name,
                'total' => $incidents->count(),
                'open' => $incidents->whereIn('status', ['reported', 'open'])->count(),
                'closed' => $incidents->whereIn('status', ['closed', 'resolved'])->count(),
                'severity_breakdown' => [
                    'critical' => $incidents->where('severity', 'critical')->count(),
                    'high' => $incidents->where('severity', 'high')->count(),
                    'medium' => $incidents->where('severity', 'medium')->count(),
                    'low' => $incidents->where('severity', 'low')->count(),
                ],
            ];
        })->filter(fn($dept) => $dept['total'] > 0)->sortByDesc('total');

        // Top Root Causes (if RCA exists)
        $topRootCauses = \App\Models\RootCauseAnalysis::forCompany($companyId)
            ->whereNotNull('root_cause')
            ->selectRaw('root_cause, COUNT(*) as count')
            ->groupBy('root_cause')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('incidents.trend-analysis', compact(
            'stats',
            'monthlyTrends',
            'severityDistribution',
            'eventTypeDistribution',
            'departmentStats',
            'topRootCauses'
        ));
    }
    
    /**
     * Bulk delete incidents
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:incidents,id'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        
        $deleted = Incident::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->delete();
        
        return redirect()->route('incidents.index')
            ->with('success', "Successfully deleted {$deleted} incident(s).");
    }
    
    /**
     * Bulk update incidents status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:incidents,id',
            'status' => 'required|in:reported,open,investigating,resolved,closed'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        $status = $request->input('status');
        
        $updated = Incident::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->update(['status' => $status]);
        
        return redirect()->route('incidents.index')
            ->with('success', "Successfully updated {$updated} incident(s) status to {$status}.");
    }
    
    /**
     * Export selected incidents
     */
    public function export(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:incidents,id'
        ]);
        
        $companyId = Auth::user()->company_id;
        $ids = $request->input('ids');
        
        $incidents = Incident::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->with(['reporter', 'assignedTo', 'department'])
            ->get();
        
        $filename = 'incidents_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($incidents) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Reference Number',
                'Title',
                'Event Type',
                'Severity',
                'Status',
                'Department',
                'Reported By',
                'Assigned To',
                'Incident Date',
                'Location',
                'Description'
            ]);
            
            // Data
            foreach ($incidents as $incident) {
                fputcsv($file, [
                    $incident->reference_number,
                    $incident->title ?? $incident->incident_type,
                    $incident->event_type,
                    $incident->severity,
                    $incident->status,
                    $incident->department->name ?? 'N/A',
                    $incident->reporter->name ?? 'N/A',
                    $incident->assignedTo->name ?? 'N/A',
                    $incident->incident_date->format('Y-m-d H:i:s'),
                    $incident->location,
                    $incident->description
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export all incidents (filtered by current filters)
     */
    public function exportAll(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $isSuperAdmin = $user->role && $user->role->name === 'super_admin';
        
        // Build query same as index method
        if ($isSuperAdmin) {
            $query = Incident::with(['reporter', 'assignedTo', 'department', 'company']);
        } else {
            $companyGroupIds = \App\Services\CompanyGroupService::getCompanyGroupIds($companyId);
            $query = Incident::whereIn('company_id', $companyGroupIds)
                ->with(['reporter', 'assignedTo', 'department']);
        }
        
        // Apply same filters as index
        if ($request->has('filter')) {
            $filter = $request->get('filter');
            switch ($filter) {
                case 'open':
                    $query->open();
                    break;
                case 'investigating':
                    $query->investigating();
                    break;
                case 'injury':
                    $query->injuryIllness();
                    break;
                case 'property':
                    $query->propertyDamage();
                    break;
                case 'near_miss':
                    $query->nearMiss();
                    break;
                case 'critical':
                    $query->critical();
                    break;
            }
        }
        
        if ($request->has('status') && $request->get('status')) {
            $query->byStatus($request->get('status'));
        }
        
        if ($request->has('severity') && $request->get('severity')) {
            $query->bySeverity($request->get('severity'));
        }
        
        if ($request->has('event_type') && $request->get('event_type')) {
            $query->byEventType($request->get('event_type'));
        }
        
        if ($request->has('date_from') && $request->get('date_from')) {
            $query->where('incident_date', '>=', $request->get('date_from'));
        }
        
        if ($request->has('date_to') && $request->get('date_to')) {
            $query->where('incident_date', '<=', $request->get('date_to'));
        }
        
        $incidents = $query->orderBy('created_at', 'desc')->get();
        
        $format = $request->get('format', 'excel');
        
        if ($format === 'pdf') {
            return $this->exportToPDF($incidents, $request);
        }
        
        return $this->exportToExcel($incidents);
    }
    
    /**
     * Export incidents to Excel (CSV)
     */
    private function exportToExcel($incidents)
    {
        $filename = 'incidents_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($incidents) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Reference Number',
                'Title',
                'Event Type',
                'Severity',
                'Status',
                'Department',
                'Reported By',
                'Assigned To',
                'Incident Date',
                'Location',
                'Description',
                'Actions Taken',
                'Resolution Notes',
                'Created At'
            ]);
            
            // Data
            foreach ($incidents as $incident) {
                fputcsv($file, [
                    $incident->reference_number,
                    $incident->title ?? $incident->incident_type,
                    $incident->event_type ?? 'N/A',
                    ucfirst($incident->severity ?? 'N/A'),
                    ucfirst($incident->status ?? 'N/A'),
                    $incident->department?->name ?? 'N/A',
                    $incident->reporter?->name ?? $incident->reporter_name ?? 'N/A',
                    $incident->assignedTo?->name ?? 'N/A',
                    $incident->incident_date ? $incident->incident_date->format('Y-m-d H:i:s') : 'N/A',
                    $incident->location ?? 'N/A',
                    $incident->description ?? 'N/A',
                    $incident->actions_taken ?? 'N/A',
                    $incident->resolution_notes ?? 'N/A',
                    $incident->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export incidents to PDF
     */
    private function exportToPDF($incidents, $request)
    {
        $pdf = Pdf::loadView('incidents.exports.pdf', [
            'incidents' => $incidents,
            'filters' => $request->all(),
        ]);
        
        return $pdf->download('incidents_export_' . date('Y-m-d_His') . '.pdf');
    }
    
    /**
     * Export single incident to PDF
     */
    public function exportPDF(Incident $incident)
    {
        if (!auth()->user()->hasPermission('incidents.print')) {
            abort(403, 'You do not have permission to print incidents.');
        }
        $this->authorizeCompanyResource($incident->company_id);
        
        $incident->load(['reporter', 'assignedTo', 'department', 'company', 'investigation', 'rootCauseAnalysis', 'capas', 'attachments']);
        
        $pdf = Pdf::loadView('incidents.exports.single-pdf', [
            'incident' => $incident,
        ]);
        
        return $pdf->download("incident-{$incident->reference_number}.pdf");
    }
    
    /**
     * Notify relevant users about status change
     */
    private function notifyStatusChange(Incident $incident, string $oldStatus, string $newStatus)
    {
        $user = Auth::user();
        $notifyUsers = collect();
        
        // Notify reporter
        if ($incident->reporter) {
            $notifyUsers->push($incident->reporter);
        }
        
        // Notify assigned user
        if ($incident->assignedTo) {
            $notifyUsers->push($incident->assignedTo);
        }
        
        // Notify HSE managers
        $hseManagers = \App\Models\User::where('company_id', $incident->company_id)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['hse_manager', 'hse_officer', 'admin', 'super_admin']);
            })
            ->get();
        
        $notifyUsers = $notifyUsers->merge($hseManagers)->unique('id');
        
        // Don't notify the user who made the change
        $notifyUsers = $notifyUsers->reject(function($notifyUser) use ($user) {
            return $notifyUser->id === $user->id;
        });
        
        foreach ($notifyUsers as $notifyUser) {
            $notifyUser->notify(new IncidentStatusChangedNotification(
                $incident,
                $oldStatus,
                $newStatus,
                $user->name
            ));
        }
    }
}
