<?php

namespace App\Http\Controllers;

use App\Models\ToolboxTalk;
use App\Models\ToolboxTalkTopic;
use App\Models\ToolboxTalkTemplate;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ToolboxTalkController extends Controller
{
    public function __construct()
    {
        // Remove middleware calls from constructor - they should be in routes
    }

    public function schedule(Request $request)
    {
        return $this->index($request);
    }

    public function index(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Check if user object exists and has company_id
        if (!$user || !$user->company_id) {
            return redirect()->route('dashboard')->with('error', 'User is not assigned to any company.');
        }
        
        $companyId = $user->company_id;
        
        $query = ToolboxTalk::forCompany($companyId)
            ->with(['department', 'supervisor', 'topic', 'attendances']);
        
        // Quick filters
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('scheduled_date', today());
                    break;
                case 'this_week':
                    $query->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('scheduled_date', now()->month)
                          ->whereYear('scheduled_date', now()->year);
                    break;
                case 'upcoming':
                    $query->where('scheduled_date', '>=', now());
                    break;
                case 'my_talks':
                    $query->where('supervisor_id', Auth::id());
                    break;
            }
        }
        
        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }
        
        $toolboxTalks = $query->orderBy('scheduled_date', 'desc')->paginate(15);
        
        // Statistics
        $stats = [
            'total' => ToolboxTalk::forCompany($companyId)->count(),
            'scheduled' => ToolboxTalk::forCompany($companyId)->scheduled()->count(),
            'completed' => ToolboxTalk::forCompany($companyId)->completed()->count(),
            'upcoming' => ToolboxTalk::forCompany($companyId)->upcoming()->count(),
            'avg_attendance' => ToolboxTalk::forCompany($companyId)
                ->completed()
                ->avg('attendance_rate') ?? 0,
        ];
        
        return view('toolbox-talks.index', compact('toolboxTalks', 'stats'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $topics = ToolboxTalkTopic::active()->get();
        $departments = Department::where('company_id', $companyId)->get();
        $supervisors = User::where('company_id', $companyId)->get();
        $templates = ToolboxTalkTemplate::forCompany($companyId)->active()->get();
        
        // If template is selected, pre-fill form
        $template = null;
        if ($request->has('template_id')) {
            $template = ToolboxTalkTemplate::forCompany($companyId)
                ->findOrFail($request->template_id);
        }
        
        return view('toolbox-talks.create', compact('topics', 'departments', 'supervisors', 'templates', 'template'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'topic_id' => 'nullable|exists:toolbox_talk_topics,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:5|max:60',
            'location' => 'required|string|max:255',
            'talk_type' => 'required|in:safety,health,environment,incident_review,custom',
            'biometric_required' => 'boolean',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'required_if:is_recurring,1|in:daily,weekly,monthly',
        ]);

        $toolboxTalk = ToolboxTalk::create([
            'reference_number' => 'TT-' . date('Ym') . '-TEMP',
            'company_id' => Auth::user()->company_id,
            'title' => $request->title,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'supervisor_id' => $request->supervisor_id,
            'topic_id' => $request->topic_id,
            'status' => 'scheduled',
            'scheduled_date' => $request->scheduled_date,
            'start_time' => $request->scheduled_date . ' ' . $request->start_time,
            'duration_minutes' => $request->duration_minutes,
            'location' => $request->location,
            'talk_type' => $request->talk_type,
            'biometric_required' => $request->boolean('biometric_required', true),
            'is_recurring' => $request->boolean('is_recurring', false),
            'recurrence_pattern' => $request->recurrence_pattern,
        ]);

        // Generate proper reference number
        $toolboxTalk->reference_number = $toolboxTalk->generateReferenceNumber();
        $toolboxTalk->save();

        // Increment topic usage count
        if ($toolboxTalk->topic_id) {
            $toolboxTalk->topic->incrementUsageCount();
        }

        return redirect()
            ->route('toolbox-talks.show', $toolboxTalk)
            ->with('success', 'Toolbox talk scheduled successfully!');
    }

    public function show(ToolboxTalk $toolboxTalk)
    {
        // Check if user can view this toolbox talk (same company)
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        $toolboxTalk->load([
            'department',
            'supervisor',
            'topic',
            'attendances' => function($query) {
                $query->orderBy('check_in_time');
            },
            'feedback' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        // Attendance statistics
        $attendanceStats = [
            'present' => $toolboxTalk->attendances->where('attendance_status', 'present')->count(),
            'absent' => $toolboxTalk->attendances->where('attendance_status', 'absent')->count(),
            'late' => $toolboxTalk->attendances->where('attendance_status', 'late')->count(),
            'excused' => $toolboxTalk->attendances->where('attendance_status', 'excused')->count(),
        ];

        // Feedback statistics
        $feedbackStats = [
            'total' => $toolboxTalk->feedback->count(),
            'average_rating' => $toolboxTalk->feedback->whereNotNull('overall_rating')->avg('overall_rating'),
            'positive' => $toolboxTalk->feedback->where('sentiment', 'positive')->count(),
            'neutral' => $toolboxTalk->feedback->where('sentiment', 'neutral')->count(),
            'negative' => $toolboxTalk->feedback->where('sentiment', 'negative')->count(),
        ];

        return view('toolbox-talks.show', compact('toolboxTalk', 'attendanceStats', 'feedbackStats'));
    }

    public function edit(ToolboxTalk $toolboxTalk)
    {
        // Check if user can edit this toolbox talk (same company)
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        if ($toolboxTalk->status === 'completed') {
            return back()->with('error', 'Cannot edit completed toolbox talks.');
        }

        $companyId = Auth::user()->company_id;
        
        $topics = ToolboxTalkTopic::active()->get();
        $departments = Department::where('company_id', $companyId)->get();
        $supervisors = User::where('company_id', $companyId)->get();

        return view('toolbox-talks.edit', compact('toolboxTalk', 'topics', 'departments', 'supervisors'));
    }

    public function update(Request $request, ToolboxTalk $toolboxTalk)
    {
        // Check if user can update this toolbox talk (same company)
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        if ($toolboxTalk->status === 'completed') {
            return back()->with('error', 'Cannot edit completed toolbox talks.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'topic_id' => 'nullable|exists:toolbox_talk_topics,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:5|max:60',
            'location' => 'required|string|max:255',
            'talk_type' => 'required|in:safety,health,environment,incident_review,custom',
            'biometric_required' => 'boolean',
        ]);

        $toolboxTalk->update([
            'title' => $request->title,
            'description' => $request->description,
            'department_id' => $request->department_id,
            'supervisor_id' => $request->supervisor_id,
            'topic_id' => $request->topic_id,
            'scheduled_date' => $request->scheduled_date,
            'start_time' => $request->scheduled_date . ' ' . $request->start_time,
            'duration_minutes' => $request->duration_minutes,
            'location' => $request->location,
            'talk_type' => $request->talk_type,
            'biometric_required' => $request->boolean('biometric_required', true),
        ]);

        return redirect()
            ->route('toolbox-talks.show', $toolboxTalk)
            ->with('success', 'Toolbox talk updated successfully!');
    }

    public function destroy(ToolboxTalk $toolboxTalk)
    {
        // Check if user can delete this toolbox talk (same company)
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        if ($toolboxTalk->status === 'completed') {
            return back()->with('error', 'Cannot delete completed toolbox talks.');
        }

        $toolboxTalk->delete();

        return redirect()->route('toolbox-talks.index')
            ->with('success', 'Toolbox talk deleted successfully!');
    }

    // Specialized methods for the toolbox talk workflow

    public function startTalk(ToolboxTalk $toolboxTalk)
    {
        // Check if user can update this toolbox talk (same company)
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        if ($toolboxTalk->status !== 'scheduled') {
            return back()->with('error', 'Can only start scheduled talks.');
        }

        $toolboxTalk->update([
            'status' => 'in_progress',
            'start_time' => now(),
        ]);

        return back()->with('success', 'Toolbox talk started!');
    }

    public function completeTalk(ToolboxTalk $toolboxTalk, Request $request)
    {
        // Check if user can update this toolbox talk (same company)
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
        
        if ($toolboxTalk->status !== 'in_progress') {
            return back()->with('error', 'Can only complete talks that are in progress.');
        }

        $request->validate([
            'supervisor_notes' => 'nullable|string',
            'key_points' => 'nullable|string',
            'action_items' => 'nullable|array',
            'photos' => 'nullable|array',
        ]);

        $toolboxTalk->update([
            'status' => 'completed',
            'end_time' => now(),
            'supervisor_notes' => $request->supervisor_notes,
            'key_points' => $request->key_points,
            'action_items' => $request->action_items,
            'photos' => $request->photos,
        ]);

        // Calculate attendance rate
        $toolboxTalk->calculateAttendanceRate();

        return back()->with('success', 'Toolbox talk completed successfully!');
    }

    public function dashboard()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Check if user object exists and has company_id
        if (!$user || !$user->company_id) {
            return redirect()->route('dashboard')->with('error', 'User is not assigned to any company.');
        }
        
        $companyId = $user->company_id;
        
        // Recent talks
        $recentTalks = ToolboxTalk::forCompany($companyId)
            ->with(['department', 'supervisor'])
            ->orderBy('scheduled_date', 'desc')
            ->limit(5)
            ->get();

        // Upcoming talks
        $upcomingTalks = ToolboxTalk::forCompany($companyId)
            ->scheduled()
            ->upcoming()
            ->with(['department', 'supervisor'])
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        // Statistics
        $stats = [
            'total_talks' => ToolboxTalk::forCompany($companyId)->count(),
            'this_month' => ToolboxTalk::forCompany($companyId)
                ->whereMonth('scheduled_date', now()->month)
                ->count(),
            'completed_this_month' => ToolboxTalk::forCompany($companyId)
                ->whereMonth('scheduled_date', now()->month)
                ->completed()
                ->count(),
            'avg_attendance_rate' => ToolboxTalk::forCompany($companyId)
                ->completed()
                ->avg('attendance_rate') ?? 0,
            'avg_feedback_score' => ToolboxTalk::forCompany($companyId)
                ->whereNotNull('average_feedback_score')
                ->avg('average_feedback_score') ?? 0,
        ];

        // Department performance
        $departmentPerformance = Department::where('company_id', $companyId)
            ->with(['toolboxTalks' => function($query) {
                $query->completed();
            }])
            ->get()
            ->map(function($department) {
                $talks = $department->toolboxTalks;
                return [
                    'name' => $department->name,
                    'talks_count' => $talks->count(),
                    'avg_attendance' => $talks->avg('attendance_rate') ?? 0,
                    'avg_feedback' => $talks->avg('average_feedback_score') ?? 0,
                ];
            });

        // Monthly trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthTalks = ToolboxTalk::forCompany($companyId)
                ->whereYear('scheduled_date', $month->year)
                ->whereMonth('scheduled_date', $month->month)
                ->get();
            
            $monthlyTrends[] = [
                'month' => $month->format('M Y'),
                'total' => $monthTalks->count(),
                'completed' => $monthTalks->where('status', 'completed')->count(),
                'avg_attendance' => $monthTalks->where('status', 'completed')->avg('attendance_rate') ?? 0,
            ];
        }

        // Status distribution
        $statusDistribution = [
            'scheduled' => ToolboxTalk::forCompany($companyId)->where('status', 'scheduled')->count(),
            'in_progress' => ToolboxTalk::forCompany($companyId)->where('status', 'in_progress')->count(),
            'completed' => ToolboxTalk::forCompany($companyId)->where('status', 'completed')->count(),
        ];

        // Talk type distribution
        $typeDistribution = ToolboxTalk::forCompany($companyId)
            ->selectRaw('talk_type, count(*) as count')
            ->groupBy('talk_type')
            ->pluck('count', 'talk_type')
            ->toArray();

        // Weekly attendance trend (last 8 weeks)
        $weeklyAttendance = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $weekAttendances = \App\Models\ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($companyId, $weekStart, $weekEnd) {
                $q->where('company_id', $companyId)
                  ->whereBetween('scheduled_date', [$weekStart, $weekEnd]);
            })->get();
            
            $weeklyAttendance[] = [
                'week' => $weekStart->format('M d'),
                'total' => $weekAttendances->count(),
                'present' => $weekAttendances->where('attendance_status', 'present')->count(),
            ];
        }

        // Top performing topics
        $topicIds = ToolboxTalk::forCompany($companyId)
            ->completed()
            ->whereNotNull('topic_id')
            ->pluck('topic_id')
            ->unique();
            
        $topTopics = \App\Models\ToolboxTalkTopic::whereIn('id', $topicIds)
            ->get()
            ->map(function($topic) use ($companyId) {
                $talks = ToolboxTalk::forCompany($companyId)
                    ->where('topic_id', $topic->id)
                    ->completed()
                    ->get();
                
                return [
                    'name' => $topic->title,
                    'count' => $talks->count(),
                    'avg_attendance' => $talks->avg('attendance_rate') ?? 0,
                    'avg_feedback' => $talks->avg('average_feedback_score') ?? 0,
                ];
            })
            ->filter(function($topic) {
                return $topic['count'] > 0;
            })
            ->sortByDesc('avg_attendance')
            ->take(5)
            ->values();

        return view('toolbox-talks.dashboard', compact(
            'recentTalks', 
            'upcomingTalks', 
            'stats', 
            'departmentPerformance',
            'monthlyTrends',
            'statusDistribution',
            'typeDistribution',
            'weeklyAttendance',
            'topTopics'
        ));
    }

    public function attendance(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $companyId = $user->company_id;
        
        // Get all talks with attendance data
        $query = ToolboxTalk::forCompany($companyId)
            ->with(['department', 'supervisor', 'attendances'])
            ->withCount(['attendances', 'attendances as present_count' => function($q) {
                $q->where('attendance_status', 'present');
            }]);

        // Filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%');
            });
        }

        $talks = $query->orderBy('scheduled_date', 'desc')->paginate(15);
        
        // Get attendance statistics
        $allAttendances = \App\Models\ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        $stats = [
            'total_attendances' => $allAttendances->count(),
            'present_count' => $allAttendances->where('attendance_status', 'present')->count(),
            'absent_count' => $allAttendances->where('attendance_status', 'absent')->count(),
            'late_count' => $allAttendances->where('attendance_status', 'late')->count(),
            'average_attendance_rate' => ToolboxTalk::forCompany($companyId)->completed()->avg('attendance_rate') ?? 0,
            'this_month' => \App\Models\ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($companyId) {
                $q->where('company_id', $companyId)->whereMonth('scheduled_date', now()->month);
            })->count(),
            'last_month' => \App\Models\ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($companyId) {
                $q->where('company_id', $companyId)->whereMonth('scheduled_date', now()->subMonth()->month);
            })->count(),
            'biometric_count' => $allAttendances->where('check_in_method', 'biometric')->count(),
            'manual_count' => $allAttendances->where('check_in_method', 'manual')->count(),
        ];

        // Department-wise attendance
        $departments = Department::where('company_id', $companyId)->get();
        $departmentStats = $departments->map(function($dept) use ($companyId) {
            $talks = ToolboxTalk::forCompany($companyId)
                ->where('department_id', $dept->id)
                ->completed()
                ->get();
            
            $totalAttendances = \App\Models\ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($dept) {
                $q->where('department_id', $dept->id);
            })->count();
            
            $presentAttendances = \App\Models\ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($dept) {
                $q->where('department_id', $dept->id);
            })->where('attendance_status', 'present')->count();
            
            return [
                'name' => $dept->name,
                'total_talks' => $talks->count(),
                'total_attendances' => $totalAttendances,
                'present_attendances' => $presentAttendances,
                'attendance_rate' => $totalAttendances > 0 ? ($presentAttendances / $totalAttendances) * 100 : 0,
            ];
        })->filter(function($dept) {
            return $dept['total_talks'] > 0;
        })->sortByDesc('attendance_rate');

        // Monthly attendance trends (last 6 months)
        $attendanceTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthAttendances = \App\Models\ToolboxTalkAttendance::whereHas('toolboxTalk', function($q) use ($companyId, $month) {
                $q->where('company_id', $companyId)
                  ->whereYear('scheduled_date', $month->year)
                  ->whereMonth('scheduled_date', $month->month);
            })->get();
            
            $attendanceTrends[] = [
                'month' => $month->format('M Y'),
                'total' => $monthAttendances->count(),
                'present' => $monthAttendances->where('attendance_status', 'present')->count(),
                'attendance_rate' => $monthAttendances->count() > 0 
                    ? ($monthAttendances->where('attendance_status', 'present')->count() / $monthAttendances->count()) * 100 
                    : 0,
            ];
        }

        // Get departments for filter
        $departmentsList = Department::where('company_id', $companyId)->get();

        return view('toolbox-talks.attendance', compact(
            'talks', 
            'stats', 
            'departmentStats', 
            'attendanceTrends',
            'departmentsList'
        ));
    }

    /**
     * Attendance management for a specific talk
     */
    public function attendanceManagement(ToolboxTalk $toolboxTalk)
    {
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $toolboxTalk->load('attendances.employee');
        $employees = User::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->get();

        return view('toolbox-talks.attendance-management', compact('toolboxTalk', 'employees'));
    }

    public function feedback(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $companyId = $user->company_id;
        
        // Get all feedback for company's talks
        $query = \App\Models\ToolboxTalkFeedback::whereHas('toolboxTalk', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with(['toolboxTalk', 'employee']);

        // Filters
        if ($request->filled('talk_id')) {
            $query->where('toolbox_talk_id', $request->talk_id);
        }

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        if ($request->filled('feedback_type')) {
            $query->where('feedback_type', $request->feedback_type);
        }

        if ($request->filled('rating_min')) {
            $query->where('overall_rating', '>=', $request->rating_min);
        }

        $feedback = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get feedback statistics
        $allFeedback = \App\Models\ToolboxTalkFeedback::whereHas('toolboxTalk', function($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->get();

        $stats = [
            'total_feedback' => $allFeedback->count(),
            'average_rating' => $allFeedback->whereNotNull('overall_rating')->avg('overall_rating') ?? 0,
            'this_month' => $allFeedback->where('created_at', '>=', now()->startOfMonth())->count(),
            'response_rate' => 0, // Calculate based on completed talks
            'positive' => $allFeedback->where('sentiment', 'positive')->count(),
            'neutral' => $allFeedback->where('sentiment', 'neutral')->count(),
            'negative' => $allFeedback->where('sentiment', 'negative')->count(),
        ];

        // Get talks for filter dropdown
        $talks = ToolboxTalk::forCompany($companyId)
            ->completed()
            ->orderBy('scheduled_date', 'desc')
            ->get();

        return view('toolbox-talks.feedback', compact('feedback', 'stats', 'talks'));
    }

    /**
     * Submit feedback for a toolbox talk
     */
    public function submitFeedback(Request $request, ToolboxTalk $toolboxTalk)
    {
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'feedback_type' => 'required|in:quick_rating,detailed_survey,suggestion,complaint',
            'overall_rating' => 'nullable|integer|min:1|max:5',
            'sentiment' => 'nullable|in:positive,neutral,negative',
            'presenter_effectiveness' => 'nullable|integer|min:1|max:5',
            'topic_relevance' => 'nullable|integer|min:1|max:5',
            'content_clarity' => 'nullable|integer|min:1|max:5',
            'engagement_level' => 'nullable|integer|min:1|max:5',
            'timing_appropriateness' => 'nullable|integer|min:1|max:5',
            'material_quality' => 'nullable|integer|min:1|max:5',
            'most_valuable_point' => 'nullable|string|max:500',
            'improvement_suggestion' => 'nullable|string|max:500',
            'specific_comments' => 'nullable|string|max:1000',
            'topic_requests' => 'nullable|array',
            'format_preference' => 'nullable|in:presentation_only,discussion_heavy,hands_on,video_based',
            'would_recommend' => 'nullable|boolean',
            'additional_topics' => 'nullable|string|max:500',
        ]);

        // Auto-detect sentiment if not provided
        $sentiment = $request->sentiment;
        if (!$sentiment && $request->overall_rating) {
            if ($request->overall_rating >= 4) {
                $sentiment = 'positive';
            } elseif ($request->overall_rating <= 2) {
                $sentiment = 'negative';
            } else {
                $sentiment = 'neutral';
            }
        }

        $feedback = \App\Models\ToolboxTalkFeedback::create([
            'toolbox_talk_id' => $toolboxTalk->id,
            'employee_id' => Auth::user()->id,
            'employee_name' => Auth::user()->name,
            'feedback_type' => $request->feedback_type,
            'overall_rating' => $request->overall_rating,
            'sentiment' => $sentiment,
            'presenter_effectiveness' => $request->presenter_effectiveness,
            'topic_relevance' => $request->topic_relevance,
            'content_clarity' => $request->content_clarity,
            'engagement_level' => $request->engagement_level,
            'timing_appropriateness' => $request->timing_appropriateness,
            'material_quality' => $request->material_quality,
            'most_valuable_point' => $request->most_valuable_point,
            'improvement_suggestion' => $request->improvement_suggestion,
            'specific_comments' => $request->specific_comments,
            'topic_requests' => $request->topic_requests,
            'format_preference' => $request->format_preference,
            'would_recommend' => $request->boolean('would_recommend'),
            'additional_topics' => $request->additional_topics,
            'response_method' => 'mobile_app', // Default, can be changed
            'ip_address' => $request->ip(),
        ]);

        // Update talk's average feedback score
        $toolboxTalk->calculateAverageFeedbackScore();

        return back()->with('success', 'Feedback submitted successfully! Thank you for your input.');
    }

    /**
     * View feedback for a specific talk
     */
    public function viewFeedback(ToolboxTalk $toolboxTalk)
    {
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $feedback = $toolboxTalk->feedback()->with('employee')->orderBy('created_at', 'desc')->get();
        $analytics = \App\Models\ToolboxTalkFeedback::getFeedbackAnalytics($toolboxTalk->id);

        return view('toolbox-talks.view-feedback', compact('toolboxTalk', 'feedback', 'analytics'));
    }

    public function reporting(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $companyId = $user->company_id;
        
        $totalTalks = ToolboxTalk::forCompany($companyId)->count();
        $completedTalks = ToolboxTalk::forCompany($companyId)->completed()->count();
        
        // Get reporting statistics
        $stats = [
            'total_talks' => $totalTalks,
            'completion_rate' => $totalTalks > 0 
                ? ($completedTalks / $totalTalks) * 100 
                : 0,
            'participation_rate' => ToolboxTalk::forCompany($companyId)->completed()->avg('attendance_rate') ?? 0,
            'satisfaction_score' => ToolboxTalk::forCompany($companyId)->whereNotNull('average_feedback_score')->avg('average_feedback_score') ?? 0,
        ];

        // Get department performance data
        $departments = Department::where('company_id', $companyId)->get();
        $departmentPerformance = $departments->map(function($dept) {
            $talks = ToolboxTalk::forCompany($dept->company_id)
                ->where('department_id', $dept->id)
                ->completed()
                ->get();
            
            return [
                'name' => $dept->name,
                'total_talks' => $talks->count(),
                'avg_attendance' => $talks->avg('attendance_rate') ?? 0,
                'avg_feedback' => $talks->avg('average_feedback_score') ?? 0,
            ];
        })->filter(function($dept) {
            return $dept['total_talks'] > 0;
        })->sortByDesc('avg_attendance');

        // Get recent activity
        $recentTalks = ToolboxTalk::forCompany($companyId)
            ->with(['department', 'supervisor'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Get monthly attendance trends (last 6 months)
        $attendanceTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthTalks = ToolboxTalk::forCompany($companyId)
                ->whereYear('scheduled_date', $month->year)
                ->whereMonth('scheduled_date', $month->month)
                ->completed()
                ->get();
            
            $attendanceTrends[] = [
                'month' => $month->format('M Y'),
                'avg_attendance' => $monthTalks->avg('attendance_rate') ?? 0,
                'total_talks' => $monthTalks->count(),
            ];
        }

        // Get topic performance
        $topicIds = ToolboxTalk::forCompany($companyId)
            ->completed()
            ->whereNotNull('topic_id')
            ->pluck('topic_id')
            ->unique();
            
        $topics = ToolboxTalkTopic::whereIn('id', $topicIds)
            ->get()
            ->map(function($topic) use ($companyId) {
                $talks = ToolboxTalk::forCompany($companyId)
                    ->where('topic_id', $topic->id)
                    ->completed()
                    ->get();
                
                return [
                    'name' => $topic->title,
                    'count' => $talks->count(),
                    'avg_rating' => $talks->avg('average_feedback_score') ?? 0,
                    'avg_attendance' => $talks->avg('attendance_rate') ?? 0,
                ];
            })
            ->filter(function($topic) {
                return $topic['count'] > 0;
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();

        return view('toolbox-talks.reporting', compact(
            'stats', 
            'departmentPerformance', 
            'recentTalks',
            'attendanceTrends',
            'topics'
        ));
    }

    /**
     * Export attendance report as PDF
     */
    public function exportAttendancePDF(ToolboxTalk $toolboxTalk)
    {
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $attendances = $toolboxTalk->attendances()->with('employee')->get();
        
        $pdf = Pdf::loadView('toolbox-talks.exports.attendance-pdf', [
            'talk' => $toolboxTalk,
            'attendances' => $attendances,
        ]);

        return $pdf->download("attendance-report-{$toolboxTalk->reference_number}.pdf");
    }

    /**
     * Export attendance report as Excel
     */
    public function exportAttendanceExcel(ToolboxTalk $toolboxTalk)
    {
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $attendances = $toolboxTalk->attendances()->with('employee')->get();

        $data = $attendances->map(function($attendance) {
            return [
                'Employee Name' => $attendance->employee_name,
                'Employee ID' => $attendance->employee_id_number,
                'Department' => $attendance->department,
                'Status' => ucfirst($attendance->attendance_status),
                'Check-in Time' => $attendance->check_in_time?->format('Y-m-d H:i:s'),
                'Check-in Method' => ucfirst(str_replace('_', ' ', $attendance->check_in_method)),
                'Absence Reason' => $attendance->absence_reason,
            ];
        });

        // Convert to array format for Excel
        $exportData = $data->toArray();
        array_unshift($exportData, ['Toolbox Talk: ' . $toolboxTalk->title]);
        array_unshift($exportData, ['Reference: ' . $toolboxTalk->reference_number]);
        array_unshift($exportData, ['Date: ' . $toolboxTalk->scheduled_date->format('Y-m-d')]);
        array_unshift($exportData, []); // Empty row
        
        return Excel::create("attendance-report-{$toolboxTalk->reference_number}", function($excel) use ($exportData) {
            $excel->sheet('Attendance', function($sheet) use ($exportData) {
                $sheet->fromArray($exportData, null, 'A1', false, false);
            });
        })->export('xlsx');
    }

    /**
     * Export reporting data as Excel
     */
    public function exportReportingExcel(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $talks = ToolboxTalk::forCompany($companyId)
            ->with(['department', 'supervisor'])
            ->orderBy('scheduled_date', 'desc')
            ->get();

        $data = $talks->map(function($talk) {
            return [
                'Reference' => $talk->reference_number,
                'Title' => $talk->title,
                'Date' => $talk->scheduled_date->format('Y-m-d'),
                'Status' => ucfirst($talk->status),
                'Department' => $talk->department?->name,
                'Supervisor' => $talk->supervisor?->name,
                'Attendance Rate' => number_format($talk->attendance_rate, 2) . '%',
                'Feedback Score' => $talk->average_feedback_score ? number_format($talk->average_feedback_score, 2) : 'N/A',
            ];
        });

        return Excel::create('toolbox-talks-report', function($excel) use ($data) {
            $excel->sheet('Toolbox Talks', function($sheet) use ($data) {
                $sheet->fromArray($data->toArray(), null, 'A1', false, false);
            });
        })->export('xlsx');
    }

    /**
     * Calendar view for toolbox talks
     */
    public function calendar(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $companyId = $user->company_id;

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $view = $request->get('view', 'month'); // month, week, day

        // Get talks for the month
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = ToolboxTalk::forCompany($companyId)
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->with(['department', 'supervisor', 'topic', 'attendances']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('talk_type')) {
            $query->where('talk_type', $request->talk_type);
        }

        $talks = $query->get();

        // Statistics for the month
        $stats = [
            'total' => $talks->count(),
            'scheduled' => $talks->where('status', 'scheduled')->count(),
            'in_progress' => $talks->where('status', 'in_progress')->count(),
            'completed' => $talks->where('status', 'completed')->count(),
            'total_attendances' => $talks->sum(function($talk) {
                return $talk->attendances->count();
            }),
            'avg_attendance_rate' => $talks->where('status', 'completed')->avg('attendance_rate') ?? 0,
        ];

        // Get departments for filter
        $departments = Department::where('company_id', $companyId)->get();

        // Upcoming talks (next 7 days)
        $upcomingTalks = ToolboxTalk::forCompany($companyId)
            ->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(7))
            ->with(['department', 'supervisor'])
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        return view('toolbox-talks.calendar', compact(
            'talks', 
            'year', 
            'month', 
            'view',
            'stats',
            'departments',
            'upcomingTalks'
        ));
    }

    /**
     * Bulk import talks from CSV/Excel
     */
    public function bulkImport(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $companyId = Auth::user()->company_id;
        $file = $request->file('file');
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            $extension = $file->getClientOriginalExtension();
            
            if (in_array($extension, ['xlsx', 'xls'])) {
                // Excel import
                $data = Excel::toArray([], $file);
                $rows = $data[0] ?? [];
                
                // Skip header row
                array_shift($rows);
                
                foreach ($rows as $rowIndex => $row) {
                    try {
                        $talk = ToolboxTalk::create([
                            'reference_number' => 'TT-' . date('Ym') . '-TEMP',
                            'company_id' => $companyId,
                            'title' => $row[0] ?? 'Imported Talk',
                            'description' => $row[1] ?? null,
                            'scheduled_date' => $row[2] ?? now(),
                            'start_time' => ($row[2] ?? now()) . ' ' . ($row[3] ?? '09:00'),
                            'duration_minutes' => (int)($row[4] ?? 15),
                            'location' => $row[5] ?? 'Main Hall',
                            'talk_type' => $row[6] ?? 'safety',
                            'department_id' => !empty($row[7]) ? (int)$row[7] : null,
                            'supervisor_id' => !empty($row[8]) ? (int)$row[8] : null,
                            'status' => 'scheduled',
                            'biometric_required' => isset($row[9]) ? (bool)$row[9] : true,
                        ]);
                        
                        // Generate proper reference number
                        $talk->reference_number = $talk->generateReferenceNumber();
                        $talk->save();
                        
                        $results['success']++;
                    } catch (\Exception $e) {
                        $results['failed']++;
                        $results['errors'][] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                    }
                }
            } else {
                // CSV import
                $handle = fopen($file->getRealPath(), 'r');
                $header = fgetcsv($handle); // Skip header row
                
                $rowIndex = 1;
                while (($row = fgetcsv($handle)) !== false) {
                    try {
                        $talk = ToolboxTalk::create([
                            'reference_number' => 'TT-' . date('Ym') . '-TEMP',
                            'company_id' => $companyId,
                            'title' => $row[0] ?? 'Imported Talk',
                            'description' => $row[1] ?? null,
                            'scheduled_date' => $row[2] ?? now(),
                            'start_time' => ($row[2] ?? now()) . ' ' . ($row[3] ?? '09:00'),
                            'duration_minutes' => (int)($row[4] ?? 15),
                            'location' => $row[5] ?? 'Main Hall',
                            'talk_type' => $row[6] ?? 'safety',
                            'department_id' => !empty($row[7]) ? (int)$row[7] : null,
                            'supervisor_id' => !empty($row[8]) ? (int)$row[8] : null,
                            'status' => 'scheduled',
                            'biometric_required' => isset($row[9]) ? (bool)$row[9] : true,
                        ]);
                        
                        // Generate proper reference number
                        $talk->reference_number = $talk->generateReferenceNumber();
                        $talk->save();
                        
                        $results['success']++;
                    } catch (\Exception $e) {
                        $results['failed']++;
                        $results['errors'][] = "Row " . ($rowIndex + 1) . ": " . $e->getMessage();
                    }
                    $rowIndex++;
                }
                fclose($handle);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $message = "Import completed: {$results['success']} successful, {$results['failed']} failed.";
        if (!empty($results['errors'])) {
            $message .= " Errors: " . implode(', ', array_slice($results['errors'], 0, 5));
        }
        
        return back()->with('success', $message);
    }

    /**
     * Download bulk import template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="toolbox-talks-import-template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'title',
                'description',
                'scheduled_date',
                'start_time',
                'duration_minutes',
                'location',
                'talk_type',
                'department_id',
                'supervisor_id',
                'biometric_required'
            ]);
            
            // Example rows
            fputcsv($file, [
                'Fire Safety Procedures',
                'Fire safety and evacuation procedures',
                '2025-12-15',
                '09:00',
                '30',
                'Main Hall',
                'safety',
                '1',
                '5',
                '1'
            ]);
            
            fputcsv($file, [
                'First Aid Basics',
                'Basic first aid training',
                '2025-12-16',
                '10:00',
                '45',
                'Training Room',
                'health',
                '2',
                '6',
                '0'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Mark attendance manually
     */
    public function markAttendance(Request $request, ToolboxTalk $toolboxTalk)
    {
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late,excused',
            'absence_reason' => 'nullable|string|required_if:status,absent,excused',
        ]);

        $employee = User::findOrFail($request->employee_id);

        $attendance = ToolboxTalkAttendance::updateOrCreate(
            [
                'toolbox_talk_id' => $toolboxTalk->id,
                'employee_id' => $employee->id,
            ],
            [
                'employee_name' => $employee->name,
                'employee_id_number' => $employee->employee_id_number,
                'department' => $employee->department?->name,
                'attendance_status' => $request->status,
                'check_in_time' => $request->status === 'present' ? now() : null,
                'check_in_method' => 'manual',
                'absence_reason' => $request->absence_reason,
                'is_supervisor' => $employee->role?->name === 'supervisor',
                'is_presenter' => $toolboxTalk->supervisor_id === $employee->id,
            ]
        );

        // Update talk statistics
        $toolboxTalk->total_attendees = $toolboxTalk->attendances()->count();
        $toolboxTalk->present_attendees = $toolboxTalk->attendances()->present()->count();
        $toolboxTalk->calculateAttendanceRate();
        $toolboxTalk->save();

        return back()->with('success', 'Attendance marked successfully!');
    }

    /**
     * Sync biometric attendance
     */
    public function syncBiometricAttendance(ToolboxTalk $toolboxTalk)
    {
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $zktecoService = new \App\Services\ZKTecoService();
        $results = $zktecoService->processToolboxTalkAttendance($toolboxTalk);

        return back()->with('success', "Biometric sync completed: {$results['new_attendances']} new attendances recorded.");
    }

    /**
     * Manage action items
     */
    public function actionItems(ToolboxTalk $toolboxTalk)
    {
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $actionItems = $toolboxTalk->action_items ?? [];
        $attendances = $toolboxTalk->attendances()->with('employee')->get();

        return view('toolbox-talks.action-items', compact('toolboxTalk', 'actionItems', 'attendances'));
    }

    /**
     * Save action items
     */
    public function saveActionItems(Request $request, ToolboxTalk $toolboxTalk)
    {
        if ($toolboxTalk->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'action_items' => 'required|array',
            'action_items.*.title' => 'required|string',
            'action_items.*.assigned_to' => 'nullable|exists:users,id',
            'action_items.*.due_date' => 'nullable|date',
            'action_items.*.priority' => 'nullable|in:low,medium,high',
        ]);

        $toolboxTalk->update([
            'action_items' => $request->action_items,
        ]);

        // Update attendance records with assigned actions
        foreach ($request->action_items as $item) {
            if (isset($item['assigned_to'])) {
                $attendance = ToolboxTalkAttendance::where('toolbox_talk_id', $toolboxTalk->id)
                    ->where('employee_id', $item['assigned_to'])
                    ->first();
                
                if ($attendance) {
                    $assignedActions = $attendance->assigned_actions ?? [];
                    $assignedActions[] = $item;
                    $attendance->update(['assigned_actions' => $assignedActions]);
                }
            }
        }

        return back()->with('success', 'Action items saved successfully!');
    }
}
