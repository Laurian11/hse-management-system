<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = Incident::where('company_id', $companyId)
            ->with(['reporter', 'assignedTo', 'department', 'investigation', 'rootCauseAnalysis']);
        
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
        
        $incidents = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('incidents.index', compact('incidents'));
    }

    public function dashboard()
    {
        $companyId = Auth::user()->company_id;
        
        $stats = [
            'total' => Incident::where('company_id', $companyId)->count(),
            'open' => Incident::where('company_id', $companyId)->where('status', 'open')->count(),
            'investigating' => Incident::where('company_id', $companyId)->where('status', 'investigating')->count(),
            'closed' => Incident::where('company_id', $companyId)->where('status', 'closed')->count(),
        ];
        
        $recentIncidents = Incident::where('company_id', $companyId)
            ->with(['reporter', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('incidents.dashboard', compact('stats', 'recentIncidents'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        
        $departments = \App\Models\Department::where('company_id', $companyId)->get();
        $users = \App\Models\User::where('company_id', $companyId)->get();
        
        return view('incidents.create', compact('departments', 'users'));
    }

    public function store(StoreIncidentRequest $request)
    {
        $data = $request->validated();
        $data['company_id'] = Auth::user()->company_id;
        $data['reported_by'] = Auth::user()->id;
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

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'Incident reported successfully!');
    }

    public function show(Incident $incident)
    {
        // Check if user can view this incident (same company)
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $incident->load([
            'reporter', 
            'assignedTo', 
            'department', 
            'investigations',
            'investigation',
            'rootCauseAnalysis',
            'capas',
            'attachments',
            'approvedBy'
        ]);
        
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        // Check if user can edit this incident (same company)
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $companyId = Auth::user()->company_id;
        
        $departments = \App\Models\Department::where('company_id', $companyId)->get();
        $users = \App\Models\User::where('company_id', $companyId)->get();
        
        return view('incidents.edit', compact('incident', 'departments', 'users'));
    }

    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        $data = $request->validated();
        $data['incident_date'] = $data['date_occurred'] ?? $incident->incident_date;

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

        return redirect()
            ->route('incidents.show', $incident)
            ->with('success', 'Incident updated successfully!');
    }

    public function destroy(Incident $incident)
    {
        // Check if user can delete this incident (same company)
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $incident->delete();

        return redirect()->route('admin.incidents.index')
            ->with('success', 'Incident deleted successfully!');
    }

    // Specialized methods for incident workflow
    public function assign(Request $request, Incident $incident)
    {
        // Check if user can update this incident (same company)
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $incident->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'investigating',
        ]);

        return back()->with('success', 'Incident assigned successfully!');
    }

    public function investigate(Incident $incident)
    {
        // Check if user can update this incident (same company)
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $incident->update(['status' => 'investigating']);

        return back()->with('success', 'Investigation started!');
    }

    public function close(Request $request, Incident $incident)
    {
        // Check if user can update this incident (same company)
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'resolution_notes' => 'required|string',
        ]);

        $incident->close($request->resolution_notes);

        return back()->with('success', 'Incident closed successfully!');
    }

    public function reopen(Incident $incident)
    {
        // Check if user can update this incident (same company)
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $incident->update([
            'status' => 'open',
            'closure_status' => null,
            'resolution_notes' => null,
            'closed_at' => null,
        ]);

        return back()->with('success', 'Incident reopened!');
    }

    /**
     * Request closure approval
     */
    public function requestClosure(Request $request, Incident $incident)
    {
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

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
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $incident->approveClosure(Auth::user());
        $incident->close();

        return back()->with('success', 'Incident closure approved!');
    }

    /**
     * Reject incident closure
     */
    public function rejectClosure(Request $request, Incident $incident)
    {
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

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
}
