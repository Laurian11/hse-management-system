<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Incident;

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
        
        // Mock data for demonstration
        $dashboardData = [
            'days_without_incident' => rand(15, 365),
            'total_inspections' => rand(50, 200),
            'safety_score' => rand(85, 99),
            'pending_trainings' => rand(5, 25),
            'monthly_incidents' => [rand(0, 5), rand(0, 5), rand(0, 5), rand(0, 5), rand(0, 5), rand(0, 5)],
            'announcements' => [
                'Monthly safety meeting scheduled for next week',
                'New safety protocols implemented',
                'Emergency drill completed successfully'
            ]
        ];

        return view('company-dashboard', compact('company', 'dashboardData'));
    }
}
