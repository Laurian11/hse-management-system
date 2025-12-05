<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Incident;
use App\Models\RiskAssessment;
use App\Models\JSA;
use App\Models\TrainingSession;
use App\Models\SafetyCommunication;
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
        ]);

        $validated['reference_number'] = 'INC-' . strtoupper(uniqid());
        $validated['status'] = 'reported';

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('incident-images', 'public');
                $imagePaths[] = $path;
            }
        }
        
        $validated['images'] = json_encode($imagePaths);

        $incident = Incident::create($validated);

        return response()->json([
            'success' => true,
            'reference_number' => $incident->reference_number,
            'message' => 'Incident reported successfully. Reference: ' . $incident->reference_number
        ]);
    }

    public function companyDashboard($id)
    {
        $company = Company::findOrFail($id);
        $companyId = $company->id; // Use the company's ID explicitly
        
        // Calculate days without incident - only for this company
        $lastIncident = Incident::forCompany($companyId)
            ->orderBy('incident_date', 'desc')
            ->first();
        
        $daysWithoutIncident = $lastIncident 
            ? Carbon::parse($lastIncident->incident_date)->diffInDays(now())
            : now()->diffInDays($company->created_at ?? now());
        
        // Total inspections (Risk Assessments + JSAs) - only for this company
        $totalInspections = RiskAssessment::forCompany($companyId)->count() 
            + JSA::forCompany($companyId)->count();
        
        // Calculate safety score based on incidents - only for this company
        $totalIncidents = Incident::forCompany($companyId)->count();
        $criticalIncidents = Incident::forCompany($companyId)
            ->where('severity', 'critical')
            ->count();
        $highIncidents = Incident::forCompany($companyId)
            ->where('severity', 'high')
            ->count();
        
        // Safety score calculation: 100 - (critical * 10 + high * 5 + others * 2), minimum 0
        $safetyScore = max(0, min(100, 100 - ($criticalIncidents * 10) - ($highIncidents * 5) - (($totalIncidents - $criticalIncidents - $highIncidents) * 2)));
        $safetyScore = round($safetyScore);
        
        // Pending trainings (scheduled but not completed) - only for this company
        $pendingTrainings = TrainingSession::forCompany($companyId)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->where('scheduled_start', '>=', now())
            ->count();
        
        // Monthly incidents for last 6 months - only for this company
        $monthlyIncidents = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthLabels[] = $month->format('M');
            $monthIncidentCount = Incident::forCompany($companyId)
                ->whereYear('incident_date', $month->year)
                ->whereMonth('incident_date', $month->month)
                ->count();
            $monthlyIncidents[] = $monthIncidentCount;
        }
        
        // Get recent safety announcements - only for this company
        $announcements = SafetyCommunication::forCompany($companyId)
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
        
        $dashboardData = [
            'days_without_incident' => $daysWithoutIncident,
            'total_inspections' => $totalInspections,
            'safety_score' => $safetyScore,
            'pending_trainings' => $pendingTrainings,
            'monthly_incidents' => $monthlyIncidents,
            'month_labels' => $monthLabels,
            'announcements' => $announcements
        ];

        return view('company-dashboard', compact('company', 'dashboardData'));
    }
}
