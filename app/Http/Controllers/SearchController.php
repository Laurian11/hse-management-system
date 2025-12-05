<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Incident;
use App\Models\PPEItem;
use App\Models\TrainingPlan;
use App\Models\RiskAssessment;
use App\Models\ToolboxTalk;

class SearchController extends Controller
{
    /**
     * Global search across all modules
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $companyId = Auth::user()->company_id;
        
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }
        
        $results = [];
        
        // Search Incidents
        $incidents = Incident::forCompany($companyId)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('reference_number', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();
        
        foreach ($incidents as $incident) {
            $results[] = [
                'title' => $incident->title ?? $incident->reference_number,
                'description' => Str::limit($incident->description ?? '', 100),
                'module' => 'Incidents',
                'type' => ucfirst($incident->event_type ?? 'Incident'),
                'url' => route('incidents.show', $incident),
                'icon' => 'fa-exclamation-triangle',
            ];
        }
        
        // Search PPE Items
        $ppeItems = PPEItem::forCompany($companyId)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('reference_number', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();
        
        foreach ($ppeItems as $item) {
            $results[] = [
                'title' => $item->name,
                'description' => $item->category . ' - ' . ($item->type ?? ''),
                'module' => 'PPE Management',
                'type' => 'Item',
                'url' => route('ppe.items.show', $item),
                'icon' => 'fa-hard-hat',
            ];
        }
        
        // Search Training Plans
        $trainingPlans = TrainingPlan::forCompany($companyId)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();
        
        foreach ($trainingPlans as $plan) {
            $results[] = [
                'title' => $plan->title,
                'description' => Str::limit($plan->description ?? '', 100),
                'module' => 'Training',
                'type' => 'Training Plan',
                'url' => route('training.training-plans.show', $plan),
                'icon' => 'fa-graduation-cap',
            ];
        }
        
        // Search Risk Assessments
        $riskAssessments = RiskAssessment::forCompany($companyId)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get();
        
        foreach ($riskAssessments as $assessment) {
            $results[] = [
                'title' => $assessment->title,
                'description' => Str::limit($assessment->description ?? '', 100),
                'module' => 'Risk Assessment',
                'type' => 'Assessment',
                'url' => route('risk-assessment.assessments.show', $assessment),
                'icon' => 'fa-shield-alt',
            ];
        }
        
        // Search Toolbox Talks
        $toolboxTalks = ToolboxTalk::forCompany($companyId)
            ->whereHas('topic', function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%");
            })
            ->orWhere('notes', 'like', "%{$query}%")
            ->limit(5)
            ->get();
        
        foreach ($toolboxTalks as $talk) {
            $results[] = [
                'title' => $talk->topic->title ?? 'Toolbox Talk',
                'description' => Str::limit($talk->notes ?? '', 100),
                'module' => 'Toolbox Talks',
                'type' => 'Talk',
                'url' => route('toolbox-talks.show', $talk),
                'icon' => 'fa-comments',
            ];
        }
        
        return response()->json([
            'results' => $results,
            'total' => count($results)
        ]);
    }
}

