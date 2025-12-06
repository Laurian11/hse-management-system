<?php

namespace App\Http\Controllers;

use App\Models\ToolboxTalkTopic;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use App\Notifications\TopicCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolboxTalkTopicController extends Controller
{
    public function index(Request $request)
    {
        $query = ToolboxTalkTopic::with(['creator', 'toolboxTalks']);
        
        // Filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }
        
        if ($request->filled('seasonal')) {
            $query->where('seasonal_relevance', $request->seasonal);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $topics = $query->orderBy('title')->paginate(20);
        
        // Statistics
        $stats = [
            'total' => ToolboxTalkTopic::count(),
            'active' => ToolboxTalkTopic::active()->count(),
            'mandatory' => ToolboxTalkTopic::mandatory()->count(),
            'most_used' => ToolboxTalkTopic::mostUsed(5)->pluck('title', 'usage_count'),
            'highest_rated' => ToolboxTalkTopic::highestRated(5)->pluck('title', 'average_feedback_score'),
        ];

        return view('toolbox-topics.index', compact('topics', 'stats'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::where('company_id', $companyId)->get();
        $employees = \App\Models\User::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('toolbox-topics.create', compact('departments', 'employees'));
    }

    public function store(Request $request)
    {
        // Convert learning_objectives from string to array if needed
        if ($request->has('learning_objectives') && is_string($request->learning_objectives)) {
            $objectives = array_filter(
                array_map('trim', explode("\n", $request->learning_objectives)),
                fn($item) => !empty($item)
            );
            $request->merge(['learning_objectives' => $objectives]);
        }

        // Convert required_materials from string to array if needed
        if ($request->has('required_materials') && is_string($request->required_materials)) {
            $materials = array_filter(
                array_map('trim', explode("\n", $request->required_materials)),
                fn($item) => !empty($item)
            );
            $request->merge(['required_materials' => $materials]);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:safety,health,environment,incident_review,emergency,equipment,procedural,custom',
            'subcategory' => 'nullable|in:equipment_safety,hazard_recognition,emergency_procedures,ergonomics,waste_management,incident_learnings,ppe,lockout_tagout,chemical_safety,electrical_safety,fall_protection,fire_safety,first_aid,wellness,mental_health,other',
            'difficulty_level' => 'required|in:basic,intermediate,advanced',
            'estimated_duration_minutes' => 'required|integer|min:5|max:60',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'nullable|string|max:500',
            'key_talking_points' => 'nullable|string',
            'real_world_examples' => 'nullable|string',
            'regulatory_references' => 'nullable|string',
            'department_relevance' => 'nullable|array',
            'seasonal_relevance' => 'required|in:all_year,summer,winter,monsoon,extreme_heat,extreme_cold',
            'is_mandatory' => 'boolean',
            'presentation_content' => 'nullable|array',
            'discussion_questions' => 'nullable|array',
            'quiz_questions' => 'nullable|array',
            'required_materials' => 'nullable|array',
            'representer_id' => 'nullable|exists:users,id',
        ]);

        $topic = ToolboxTalkTopic::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'subcategory' => $request->subcategory,
            'difficulty_level' => $request->difficulty_level,
            'estimated_duration_minutes' => $request->estimated_duration_minutes,
            'learning_objectives' => $request->learning_objectives ?? [],
            'key_talking_points' => $request->key_talking_points,
            'real_world_examples' => $request->real_world_examples,
            'regulatory_references' => $request->regulatory_references,
            'department_relevance' => $request->department_relevance ? array_map('intval', $request->department_relevance) : null,
            'seasonal_relevance' => $request->seasonal_relevance,
            'is_mandatory' => $request->boolean('is_mandatory', false),
            'presentation_content' => $request->presentation_content,
            'discussion_questions' => $request->discussion_questions,
            'quiz_questions' => $request->quiz_questions,
            'required_materials' => $request->required_materials ?? [],
            'created_by' => Auth::id(),
            'representer_id' => $request->representer_id,
        ]);

        // Notify HSE department officers
        $this->notifyHSEOfficers($topic);

        return redirect()
            ->route('toolbox-topics.show', $topic)
            ->with('success', 'Topic created successfully!');
    }

    public function show(ToolboxTalkTopic $topic)
    {
        $topic->load(['creator', 'toolboxTalks' => function($query) {
            $query->with(['department', 'supervisor'])
                  ->orderBy('scheduled_date', 'desc')
                  ->limit(10);
        }]);

        // Usage statistics
        $usageStats = [
            'total_uses' => $topic->usage_count,
            'recent_uses' => $topic->toolboxTalks->count(),
            'avg_feedback' => $topic->average_feedback_score,
            'effectiveness' => $topic->effectiveness_rating,
            'last_used' => $topic->toolboxTalks->first()?->scheduled_date,
        ];

        // Department usage
        $departmentUsage = $topic->toolboxTalks
            ->groupBy('department_id')
            ->map(function($talks) {
                $firstTalk = $talks->first();
                return [
                    'count' => $talks->count(),
                    'department' => $firstTalk ? $firstTalk->department : null,
                ];
            })
            ->filter(function($usage) {
                return $usage['department'] !== null;
            });

        return view('toolbox-topics.show', compact('topic', 'usageStats', 'departmentUsage'));
    }

    public function edit(ToolboxTalkTopic $topic)
    {
        $departments = Department::where('company_id', Auth::user()->company_id)->get();
        
        return view('toolbox-topics.edit', compact('topic', 'departments'));
    }

    public function update(Request $request, ToolboxTalkTopic $topic)
    {
        // Convert learning_objectives from string to array if needed
        if ($request->has('learning_objectives') && is_string($request->learning_objectives)) {
            $objectives = array_filter(
                array_map('trim', explode("\n", $request->learning_objectives)),
                fn($item) => !empty($item)
            );
            $request->merge(['learning_objectives' => $objectives]);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:safety,health,environment,incident_review,emergency,equipment,procedural,custom',
            'subcategory' => 'nullable|in:equipment_safety,hazard_recognition,emergency_procedures,ergonomics,waste_management,incident_learnings,ppe,lockout_tagout,chemical_safety,electrical_safety,fall_protection,fire_safety,first_aid,wellness,mental_health,other',
            'difficulty_level' => 'required|in:basic,intermediate,advanced',
            'estimated_duration_minutes' => 'required|integer|min:5|max:60',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'nullable|string|max:500',
            'key_talking_points' => 'nullable|string',
            'real_world_examples' => 'nullable|string',
            'regulatory_references' => 'nullable|string',
            'department_relevance' => 'nullable|array',
            'seasonal_relevance' => 'required|in:all_year,summer,winter,monsoon,extreme_heat,extreme_cold',
            'is_mandatory' => 'boolean',
            'is_active' => 'boolean',
            'presentation_content' => 'nullable|array',
            'discussion_questions' => 'nullable|array',
            'quiz_questions' => 'nullable|array',
            'required_materials' => 'nullable|array',
        ]);

        $topic->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'subcategory' => $request->subcategory,
            'difficulty_level' => $request->difficulty_level,
            'estimated_duration_minutes' => $request->estimated_duration_minutes,
            'learning_objectives' => $request->learning_objectives ?? [],
            'key_talking_points' => $request->key_talking_points,
            'real_world_examples' => $request->real_world_examples,
            'regulatory_references' => $request->regulatory_references,
            'department_relevance' => $request->department_relevance,
            'seasonal_relevance' => $request->seasonal_relevance,
            'is_mandatory' => $request->boolean('is_mandatory', false),
            'is_active' => $request->boolean('is_active', true),
            'presentation_content' => $request->presentation_content,
            'discussion_questions' => $request->discussion_questions,
            'quiz_questions' => $request->quiz_questions,
            'required_materials' => $request->required_materials,
        ]);

        return redirect()
            ->route('toolbox-topics.show', $topic)
            ->with('success', 'Topic updated successfully!');
    }

    public function destroy(ToolboxTalkTopic $topic)
    {
        if ($topic->toolboxTalks()->count() > 0) {
            return back()->with('error', 'Cannot delete topic that has been used in toolbox talks.');
        }

        $topic->delete();

        return redirect()->route('toolbox-topics.index')
            ->with('success', 'Topic deleted successfully!');
    }

    // Specialized methods

    public function library(Request $request)
    {
        $query = ToolboxTalkTopic::active();
        
        // Smart filtering based on department
        $userDepartmentId = Auth::user()->department_id;
        if ($userDepartmentId) {
            $query->where(function($q) use ($userDepartmentId) {
                $q->whereNull('department_relevance')
                  ->orWhereJsonContains('department_relevance', $userDepartmentId);
            });
        }
        
        // Seasonal relevance
        $currentSeason = $this->getCurrentSeason();
        $query->where(function($q) use ($currentSeason) {
            $q->where('seasonal_relevance', 'all_year')
              ->orWhere('seasonal_relevance', $currentSeason);
        });
        
        // Category filtering
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('key_talking_points', 'like', "%{$search}%");
            });
        }
        
        $topics = $query->orderBy('title')->paginate(12);
        
        // Categories for filtering
        $categories = [
            'safety' => 'Safety',
            'health' => 'Health',
            'environment' => 'Environment',
            'incident_review' => 'Incident Review',
            'emergency' => 'Emergency',
            'equipment' => 'Equipment',
            'procedural' => 'Procedural',
            'custom' => 'Custom',
        ];

        return view('toolbox-topics.library', compact('topics', 'categories'));
    }

    public function duplicate(ToolboxTalkTopic $topic)
    {
        $newTopic = $topic->replicate();
        $newTopic->title = $topic->title . ' (Copy)';
        $newTopic->usage_count = 0;
        $newTopic->average_feedback_score = null;
        $newTopic->effectiveness_rating = null;
        $newTopic->created_by = Auth::id();
        $newTopic->save();

        return redirect()
            ->route('toolbox-topics.edit', $newTopic)
            ->with('success', 'Topic duplicated successfully!');
    }

    /**
     * Notify HSE department officers about new topic
     */
    private function notifyHSEOfficers(ToolboxTalkTopic $topic): void
    {
        $companyId = Auth::user()->company_id;
        
        // Get HSE officers by role
        $hseRole = Role::where('name', 'hse_officer')
            ->orWhere('level', 'hse_officer')
            ->first();
        
        $hseOfficers = User::where('company_id', $companyId)
            ->where('is_active', true);
        
        if ($hseRole) {
            $hseOfficers->where('role_id', $hseRole->id);
        } else {
            // Fallback: find by role name in role relationship
            $hseOfficers->whereHas('role', function($q) {
                $q->where('name', 'hse_officer')
                  ->orWhere('level', 'hse_officer');
            });
        }
        
        $hseOfficers = $hseOfficers->get();
        
        // Also get HSE officers assigned to departments
        $departments = Department::where('company_id', $companyId)
            ->whereNotNull('hse_officer_id')
            ->pluck('hse_officer_id')
            ->unique();
        
        $departmentHSEOfficers = User::where('company_id', $companyId)
            ->where('is_active', true)
            ->whereIn('id', $departments)
            ->get();
        
        // Merge and get unique officers
        $allHSEOfficers = $hseOfficers->merge($departmentHSEOfficers)->unique('id');
        
        // Send notifications
        foreach ($allHSEOfficers as $officer) {
            $officer->notify(new TopicCreatedNotification($topic));
        }
    }

    private function getCurrentSeason(): string
    {
        $month = date('n');
        
        if (in_array($month, [12, 1, 2])) {
            return 'winter';
        } elseif (in_array($month, [3, 4, 5])) {
            return 'summer';
        } elseif (in_array($month, [6, 7, 8])) {
            return 'monsoon';
        } else {
            return 'extreme_heat';
        }
    }
}
