<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Incident;
use App\Models\RiskAssessment;
use App\Models\JSA;
use App\Models\TrainingSession;
use App\Models\SafetyCommunication;
use App\Models\ToolboxTalk;
use App\Models\Hazard;
use App\Models\PPEItem;
use App\Models\PPEIssuance;
use App\Models\CAPA;
use App\Models\Department;
use App\Models\User;
use App\Services\CompanyGroupService;
use Carbon\Carbon;

class LandingPageController extends Controller
{
    public function index()
    {
        $companies = Company::where('is_active', true)->get();
        return view('landing', compact('companies'));
    }

    public function reportIncident(Request $request)
    {
        try {
            $validated = $request->validate([
                'reporter_name' => 'required|string|max:255',
                'reporter_email' => 'required|email|max:255',
                'reporter_phone' => 'nullable|string|max:20',
                'incident_type' => 'required|string|max:255',
                'description' => 'required|string|max:1000',
                'location' => 'required|string|max:255',
                'incident_date' => 'required|date',
                'severity' => 'required|in:low,medium,high,critical',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,jpg,png,gif|max:5120',
            ], [
                'reporter_name.required' => 'Please provide your name',
                'reporter_email.required' => 'Please provide your email address',
                'reporter_email.email' => 'Please provide a valid email address',
                'incident_type.required' => 'Please select an incident type',
                'description.required' => 'Please provide a description of the incident',
                'location.required' => 'Please provide the location where the incident occurred',
                'incident_date.required' => 'Please provide the date and time of the incident',
                'incident_date.date' => 'Please provide a valid date and time',
                'severity.required' => 'Please select the severity level',
                'images.max' => 'You can upload a maximum of 5 images',
                'images.*.image' => 'All files must be images',
                'images.*.mimes' => 'Images must be in JPEG, JPG, PNG, or GIF format',
                'images.*.max' => 'Each image must be less than 5MB',
            ]);

            // Map incident_type to event_type
            $eventTypeMap = [
                'Injury' => 'injury_illness',
                'Illness' => 'injury_illness',
                'Near Miss' => 'near_miss',
                'Property Damage' => 'property_damage',
                'Environmental' => 'environmental',
                'Vehicle' => 'vehicle',
                'Other' => 'other',
            ];
            
            $validated['event_type'] = $eventTypeMap[$validated['incident_type']] ?? 'other';
            $validated['title'] = $validated['incident_type'] . ' - ' . substr($validated['description'], 0, 50);
            $validated['status'] = 'reported';
            
            // Company allocation logic
            $companyId = null;
            
            // 1. Check if company_id was explicitly provided in the form
            if ($request->filled('company_id')) {
                $companyId = $request->input('company_id');
                // Validate that the company exists and is active
                $company = Company::where('id', $companyId)->where('is_active', true)->first();
                if ($company) {
                    $companyId = $company->id;
                } else {
                    $companyId = null; // Invalid company ID, will try other methods
                }
            }
            
            // 2. If no company selected, try to auto-detect from email domain
            if (!$companyId && $request->filled('reporter_email')) {
                $email = $request->input('reporter_email');
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $domain = substr(strrchr($email, "@"), 1);
                    
                    // Try to find company by email domain match
                    // Check company email field
                    $company = Company::where('is_active', true)
                        ->where(function($query) use ($domain) {
                            $query->where('email', 'like', '%@' . $domain)
                                  ->orWhere('email', 'like', '%' . $domain . '%');
                        })
                        ->first();
                    
                    if ($company) {
                        $companyId = $company->id;
                    } else {
                        // Try to find by matching user emails in the company
                        $user = \App\Models\User::where('email', 'like', '%@' . $domain)
                            ->whereNotNull('company_id')
                            ->whereHas('company', function($q) {
                                $q->where('is_active', true);
                            })
                            ->first();
                        
                        if ($user && $user->company_id) {
                            $companyId = $user->company_id;
                        }
                    }
                }
            }
            
            $validated['company_id'] = $companyId; // Will be null if no match found

            // Handle image uploads - support both array format and indexed format
            $imagePaths = [];
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                // Handle both formats: images[0], images[1] or images[]
                if (is_array($images)) {
                    foreach ($images as $image) {
                        if ($image && $image->isValid()) {
                            $path = $image->store('incident-images', 'public');
                            $imagePaths[] = $path;
                        }
                    }
                }
            }
            
            $validated['images'] = !empty($imagePaths) ? json_encode($imagePaths) : null;

            $incident = Incident::create($validated);

            return response()->json([
                'success' => true,
                'reference_number' => $incident->reference_number,
                'message' => 'Incident reported successfully. Reference: ' . $incident->reference_number
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error reporting incident: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while reporting the incident. Please try again or contact support.'
            ], 500);
        }
    }

    public function companyDashboard($id)
    {
        $company = Company::findOrFail($id);
        $companyId = $company->id;
        
        // Get company group IDs (parent + all sisters) for data aggregation
        $companyGroupIds = CompanyGroupService::getCompanyGroupIds($companyId);
        $isParentCompany = CompanyGroupService::isParentCompany($companyId);
        $isSisterCompany = CompanyGroupService::isSisterCompany($companyId);
        
        // Calculate days without incident (across company group if parent)
        $lastIncident = Incident::whereIn('company_id', $companyGroupIds)
            ->orderBy('incident_date', 'desc')
            ->first();
        
        $daysWithoutIncident = $lastIncident 
            ? Carbon::parse($lastIncident->incident_date)->diffInDays(now())
            : now()->diffInDays($company->created_at ?? now());
        
        // Total inspections (Risk Assessments + JSAs) - aggregated across company group if parent
        $totalInspections = RiskAssessment::whereIn('company_id', $companyGroupIds)->count() 
            + JSA::whereIn('company_id', $companyGroupIds)->count();
        
        // Calculate safety score based on incidents - aggregated across company group if parent
        $totalIncidents = Incident::whereIn('company_id', $companyGroupIds)->count();
        $criticalIncidents = Incident::whereIn('company_id', $companyGroupIds)
            ->where('severity', 'critical')
            ->count();
        $highIncidents = Incident::whereIn('company_id', $companyGroupIds)
            ->where('severity', 'high')
            ->count();
        
        // Safety score calculation: 100 - (critical * 10 + high * 5 + others * 2), minimum 0
        $safetyScore = max(0, min(100, 100 - ($criticalIncidents * 10) - ($highIncidents * 5) - (($totalIncidents - $criticalIncidents - $highIncidents) * 2)));
        $safetyScore = round($safetyScore);
        
        // Pending trainings (scheduled but not completed) - aggregated across company group if parent
        $pendingTrainings = TrainingSession::whereIn('company_id', $companyGroupIds)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->where('scheduled_start', '>=', now())
            ->count();
        
        // Monthly incidents for last 6 months - aggregated across company group if parent
        $monthlyIncidents = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthLabels[] = $month->format('M');
            $monthIncidentCount = Incident::whereIn('company_id', $companyGroupIds)
                ->whereYear('incident_date', $month->year)
                ->whereMonth('incident_date', $month->month)
                ->count();
            $monthlyIncidents[] = $monthIncidentCount;
        }
        
        // Get recent safety announcements - aggregated across company group if parent
        $announcements = SafetyCommunication::whereIn('company_id', $companyGroupIds)
            ->where('status', 'sent')
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($comm) {
                return $comm->title ?: $comm->message;
            })
            ->toArray();
        
        // If no announcements, use default messages
        if (empty($announcements)) {
            $announcements = [
                'Stay safe and follow all safety protocols',
                'Report any hazards or incidents immediately',
                'Complete all required safety trainings on time'
            ];
        }
        
        // Additional Statistics
        $totalIncidents = Incident::whereIn('company_id', $companyGroupIds)->count();
        $openIncidents = Incident::whereIn('company_id', $companyGroupIds)->whereIn('status', ['reported', 'open'])->count();
        $totalToolboxTalks = ToolboxTalk::whereIn('company_id', $companyGroupIds)->count();
        $completedToolboxTalks = ToolboxTalk::whereIn('company_id', $companyGroupIds)->where('status', 'completed')->count();
        $totalHazards = Hazard::whereIn('company_id', $companyGroupIds)->count();
        $totalCAPAs = CAPA::whereIn('company_id', $companyGroupIds)->count();
        $openCAPAs = CAPA::whereIn('company_id', $companyGroupIds)->whereIn('status', ['open', 'in_progress'])->count();
        $totalPPEItems = PPEItem::whereIn('company_id', $companyGroupIds)->count();
        $lowStockPPE = PPEItem::whereIn('company_id', $companyGroupIds)
            ->whereColumn('available_quantity', '<', 'minimum_stock_level')
            ->count();
        $activePPEIssuances = PPEIssuance::whereIn('company_id', $companyGroupIds)->where('status', 'active')->count();
        $totalDepartments = Department::whereIn('company_id', $companyGroupIds)->count();
        $totalUsers = User::whereIn('company_id', $companyGroupIds)->where('is_active', true)->count();
        $totalTrainingSessions = TrainingSession::whereIn('company_id', $companyGroupIds)->count();
        $completedTrainingSessions = TrainingSession::whereIn('company_id', $companyGroupIds)->where('status', 'completed')->count();
        
        // Incident Severity Distribution
        $severityDistribution = [
            'critical' => Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'critical')->count(),
            'high' => Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'high')->count(),
            'medium' => Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'medium')->count(),
            'low' => Incident::whereIn('company_id', $companyGroupIds)->where('severity', 'low')->count(),
        ];
        
        // Incident Status Distribution
        $incidentStatusDistribution = [
            'reported' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'reported')->count(),
            'open' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'open')->count(),
            'investigating' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'investigating')->count(),
            'resolved' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'resolved')->count(),
            'closed' => Incident::whereIn('company_id', $companyGroupIds)->where('status', 'closed')->count(),
        ];
        
        // Recent Activity
        $recentIncidents = Incident::whereIn('company_id', $companyGroupIds)
            ->with(['reporter', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentToolboxTalks = ToolboxTalk::whereIn('company_id', $companyGroupIds)
            ->with(['department', 'supervisor'])
            ->orderBy('scheduled_date', 'desc')
            ->limit(5)
            ->get();
        
        $recentRiskAssessments = RiskAssessment::whereIn('company_id', $companyGroupIds)
            ->with(['department', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Toolbox Talk Trends (Last 6 Months)
        $toolboxTalkTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthTalks = ToolboxTalk::whereIn('company_id', $companyGroupIds)
                ->whereYear('scheduled_date', $month->year)
                ->whereMonth('scheduled_date', $month->month)
                ->get();
            
            $toolboxTalkTrends[] = [
                'month' => $month->format('M'),
                'total' => $monthTalks->count(),
                'completed' => $monthTalks->where('status', 'completed')->count(),
            ];
        }
        
        $dashboardData = [
            'days_without_incident' => $daysWithoutIncident,
            'total_inspections' => $totalInspections,
            'safety_score' => $safetyScore,
            'pending_trainings' => $pendingTrainings,
            'monthly_incidents' => $monthlyIncidents,
            'month_labels' => $monthLabels,
            'announcements' => $announcements,
            'is_parent_company' => $isParentCompany,
            'is_sister_company' => $isSisterCompany,
            'company_group_count' => count($companyGroupIds),
            // Additional Statistics
            'total_incidents' => $totalIncidents,
            'open_incidents' => $openIncidents,
            'total_toolbox_talks' => $totalToolboxTalks,
            'completed_toolbox_talks' => $completedToolboxTalks,
            'total_hazards' => $totalHazards,
            'total_capas' => $totalCAPAs,
            'open_capas' => $openCAPAs,
            'total_ppe_items' => $totalPPEItems,
            'low_stock_ppe' => $lowStockPPE,
            'active_ppe_issuances' => $activePPEIssuances,
            'total_departments' => $totalDepartments,
            'total_users' => $totalUsers,
            'total_training_sessions' => $totalTrainingSessions,
            'completed_training_sessions' => $completedTrainingSessions,
            // Distributions
            'severity_distribution' => $severityDistribution,
            'incident_status_distribution' => $incidentStatusDistribution,
            'toolbox_talk_trends' => $toolboxTalkTrends,
            // Recent Activity
            'recent_incidents' => $recentIncidents,
            'recent_toolbox_talks' => $recentToolboxTalks,
            'recent_risk_assessments' => $recentRiskAssessments,
        ];

        return view('company-dashboard', compact('company', 'dashboardData'));
    }
}
