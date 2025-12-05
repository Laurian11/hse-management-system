<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Models\TrainingPlan;
use App\Models\TrainingAttendance;
use App\Models\ActivityLog;
use App\Models\User;
use App\Notifications\TrainingSessionScheduledNotification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingSessionController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = TrainingSession::where('company_id', $companyId)
            ->with(['trainingPlan', 'instructor']);
        
        if ($request->has('status')) {
            $query->byStatus($request->get('status'));
        }
        
        if ($request->has('upcoming')) {
            $query->upcoming();
        }
        
        $sessions = $query->orderBy('scheduled_start', 'asc')->paginate(15);
        
        return view('training.training-sessions.index', compact('sessions'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $trainingPlanId = $request->get('training_plan_id');
        
        $trainingPlan = $trainingPlanId 
            ? TrainingPlan::where('company_id', $companyId)->findOrFail($trainingPlanId)
            : null;
        
        $users = \App\Models\User::where('company_id', $companyId)->get();
        $materials = \App\Models\TrainingMaterial::where('company_id', $companyId)->active()->get();
        
        return view('training.training-sessions.create', compact('trainingPlan', 'users', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_plan_id' => 'required|exists:training_plans,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_type' => 'required|in:classroom,e_learning,on_job_training,workshop,simulation,refresher,certification,combination',
            'scheduled_start' => 'required|date|after:now',
            'scheduled_end' => 'required|date|after:scheduled_start',
            'location_name' => 'nullable|string|max:255',
            'location_address' => 'nullable|string',
            'room_number' => 'nullable|string|max:255',
            'virtual_meeting_link' => 'nullable|url',
            'instructor_id' => 'nullable|exists:users,id',
            'external_instructor_name' => 'nullable|string|max:255',
            'co_instructors' => 'nullable|array',
            'max_participants' => 'nullable|integer|min:1',
            'min_participants' => 'nullable|integer|min:1',
            'assigned_materials' => 'nullable|array',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $validated['status'] = 'scheduled';
        $validated['registered_participants'] = 0;
        
        $session = TrainingSession::create($validated);
        
        // Update training plan status
        $trainingPlan = TrainingPlan::find($validated['training_plan_id']);
        if ($trainingPlan->status === 'approved') {
            $trainingPlan->update(['status' => 'scheduled']);
        }
        
        // Send notifications to participants
        // Get participants from training plan or registered users
        $participants = collect();
        
        // If training plan has target audience, get those users
        if ($trainingPlan->target_audience) {
            $audienceIds = is_array($trainingPlan->target_audience) 
                ? $trainingPlan->target_audience 
                : json_decode($trainingPlan->target_audience, true);
            
            if ($audienceIds) {
                $participants = User::where('company_id', Auth::user()->company_id)
                    ->whereIn('id', $audienceIds)
                    ->get();
            }
        }
        
        // Also notify instructor
        if ($session->instructor) {
            $participants->push($session->instructor);
        }
        
        // Send notifications
        foreach ($participants->unique('id') as $participant) {
            $participant->notify(new TrainingSessionScheduledNotification($session));
        }
        
        return redirect()->route('training.training-sessions.show', $session)
            ->with('success', 'Training session scheduled successfully.');
    }

    public function show(TrainingSession $trainingSession)
    {
        if ($trainingSession->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $trainingSession->load([
            'trainingPlan',
            'instructor',
            'attendances.user',
            'competencyAssessments',
            'certificates',
        ]);

        return view('training.training-sessions.show', compact('trainingSession'));
    }

    public function start(TrainingSession $trainingSession)
    {
        if ($trainingSession->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $trainingSession->start();
        
        return redirect()->route('training.training-sessions.show', $trainingSession)
            ->with('success', 'Training session started.');
    }

    public function complete(Request $request, TrainingSession $trainingSession)
    {
        if ($trainingSession->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'completion_notes' => 'nullable|string',
        ]);

        $trainingSession->complete(Auth::user(), $validated['completion_notes'] ?? null);
        
        return redirect()->route('training.training-sessions.show', $trainingSession)
            ->with('success', 'Training session completed.');
    }

    public function markAttendance(Request $request, TrainingSession $trainingSession)
    {
        if ($trainingSession->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_status' => 'required|in:attended,absent,partially_attended,excused',
            'notes' => 'nullable|string',
        ]);

        $attendance = TrainingAttendance::updateOrCreate(
            [
                'training_session_id' => $trainingSession->id,
                'user_id' => $validated['user_id'],
            ],
            [
                'company_id' => $trainingSession->company_id,
                'attendance_status' => $validated['attendance_status'],
                'notes' => $validated['notes'] ?? null,
                'registration_method' => 'manual',
            ]
        );

        if ($validated['attendance_status'] === 'attended') {
            $attendance->checkIn();
        }

        // Update registered participants count
        $trainingSession->update([
            'registered_participants' => $trainingSession->attendances()->count(),
        ]);

        return redirect()->back()
            ->with('success', 'Attendance marked successfully.');
    }

    public function edit(TrainingSession $trainingSession)
    {
        if ($trainingSession->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::where('company_id', $companyId)->get();
        $materials = \App\Models\TrainingMaterial::where('company_id', $companyId)->active()->get();
        $trainingPlans = \App\Models\TrainingPlan::where('company_id', $companyId)->where('status', 'approved')->get();

        return view('training.training-sessions.edit', compact('trainingSession', 'users', 'materials', 'trainingPlans'));
    }

    public function update(Request $request, TrainingSession $trainingSession)
    {
        if ($trainingSession->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_type' => 'required|in:classroom,e_learning,on_job_training,workshop,simulation,refresher,certification,combination',
            'scheduled_start' => 'required|date',
            'scheduled_end' => 'required|date|after:scheduled_start',
            'location_name' => 'nullable|string|max:255',
            'location_address' => 'nullable|string',
            'room_number' => 'nullable|string|max:255',
            'virtual_meeting_link' => 'nullable|url',
            'instructor_id' => 'nullable|exists:users,id',
            'external_instructor_name' => 'nullable|string|max:255',
            'co_instructors' => 'nullable|array',
            'max_participants' => 'nullable|integer|min:1',
            'min_participants' => 'nullable|integer|min:1',
            'assigned_materials' => 'nullable|array',
        ]);

        $trainingSession->update($validated);

        return redirect()->route('training.training-sessions.show', $trainingSession)
            ->with('success', 'Training session updated successfully.');
    }

    public function destroy(TrainingSession $trainingSession)
    {
        if ($trainingSession->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        // Check if session has started or has attendance
        if ($trainingSession->status === 'in_progress' || $trainingSession->status === 'completed') {
            return redirect()->back()
                ->with('error', 'Cannot delete a session that has started or been completed.');
        }

        if ($trainingSession->attendances->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete session with attendance records.');
        }

        $trainingSession->delete();

        return redirect()->route('training.training-sessions.index')
            ->with('success', 'Training session deleted successfully.');
    }

    public function calendar(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        // Get year and month from request, default to current
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $view = $request->get('view', 'month'); // month, week, day
        
        // Get sessions for the month
        $sessions = TrainingSession::where('company_id', $companyId)
            ->whereYear('scheduled_start', $year)
            ->whereMonth('scheduled_start', $month)
            ->with(['trainingPlan.trainingNeed', 'instructor'])
            ->orderBy('scheduled_start')
            ->get();
        
        // Filter by status if provided
        if ($request->has('status')) {
            $sessions = $sessions->where('status', $request->get('status'));
        }
        
        // Filter by session type if provided
        if ($request->has('session_type')) {
            $sessions = $sessions->where('session_type', $request->get('session_type'));
        }
        
        // Statistics
        $stats = [
            'total_sessions' => TrainingSession::where('company_id', $companyId)
                ->whereYear('scheduled_start', $year)
                ->whereMonth('scheduled_start', $month)
                ->count(),
            'scheduled' => TrainingSession::where('company_id', $companyId)
                ->whereYear('scheduled_start', $year)
                ->whereMonth('scheduled_start', $month)
                ->where('status', 'scheduled')
                ->count(),
            'completed' => TrainingSession::where('company_id', $companyId)
                ->whereYear('scheduled_start', $year)
                ->whereMonth('scheduled_start', $month)
                ->where('status', 'completed')
                ->count(),
        ];
        
        // Get departments for filter
        $departments = \App\Models\Department::where('company_id', $companyId)->get();
        
        return view('training.training-sessions.calendar', compact(
            'sessions',
            'year',
            'month',
            'view',
            'stats',
            'departments'
        ));
    }

    public function export(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = TrainingSession::where('company_id', $companyId)
            ->with(['trainingPlan.trainingNeed', 'instructor']);
        
        if ($request->has('status')) {
            $query->byStatus($request->get('status'));
        }
        if ($request->has('upcoming')) {
            $query->upcoming();
        }

        $sessions = $query->orderBy('scheduled_start', 'asc')->get();

        ActivityLog::log('export', 'training', 'TrainingSession', null, 'Exported training sessions list');

        $format = $request->get('format', 'csv');

        if ($format === 'excel') {
            $data = $sessions->map(function($session) {
                return [
                    'Reference Number' => $session->reference_number,
                    'Title' => $session->title,
                    'Training Plan' => $session->trainingPlan->title ?? 'N/A',
                    'Training Need' => $session->trainingPlan->trainingNeed->training_title ?? 'N/A',
                    'Session Type' => ucfirst(str_replace('_', ' ', $session->session_type)),
                    'Status' => ucfirst($session->status),
                    'Scheduled Start' => $session->scheduled_start->format('Y-m-d H:i:s'),
                    'Scheduled End' => $session->scheduled_end->format('Y-m-d H:i:s'),
                    'Actual Start' => $session->actual_start ? $session->actual_start->format('Y-m-d H:i:s') : 'N/A',
                    'Actual End' => $session->actual_end ? $session->actual_end->format('Y-m-d H:i:s') : 'N/A',
                    'Location' => $session->location_name ?? 'N/A',
                    'Instructor' => $session->instructor->name ?? $session->external_instructor_name ?? 'N/A',
                    'Max Participants' => $session->max_participants ?? 'N/A',
                    'Registered Participants' => $session->registered_participants ?? 0,
                    'Created At' => $session->created_at->format('Y-m-d H:i:s'),
                ];
            });

            $exportData = $data->toArray();
            array_unshift($exportData, ['Training Sessions Export']);
            array_unshift($exportData, ['Generated: ' . now()->format('Y-m-d H:i:s')]);
            array_unshift($exportData, []);

            return Excel::create('training-sessions-export-' . date('Y-m-d_His'), function($excel) use ($exportData) {
                $excel->sheet('Training Sessions', function($sheet) use ($exportData) {
                    $sheet->fromArray($exportData, null, 'A1', false, false);
                });
            })->export('xlsx');
        } else {
            $filename = 'training_sessions_export_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($sessions) {
                $file = fopen('php://output', 'w');
                
                fputcsv($file, [
                    'Reference Number', 'Title', 'Training Plan', 'Training Need', 'Session Type', 'Status',
                    'Scheduled Start', 'Scheduled End', 'Actual Start', 'Actual End', 'Location',
                    'Instructor', 'Max Participants', 'Registered Participants', 'Created At'
                ]);
                
                foreach ($sessions as $session) {
                    fputcsv($file, [
                        $session->reference_number,
                        $session->title,
                        $session->trainingPlan->title ?? 'N/A',
                        $session->trainingPlan->trainingNeed->training_title ?? 'N/A',
                        ucfirst(str_replace('_', ' ', $session->session_type)),
                        ucfirst($session->status),
                        $session->scheduled_start->format('Y-m-d H:i:s'),
                        $session->scheduled_end->format('Y-m-d H:i:s'),
                        $session->actual_start ? $session->actual_start->format('Y-m-d H:i:s') : 'N/A',
                        $session->actual_end ? $session->actual_end->format('Y-m-d H:i:s') : 'N/A',
                        $session->location_name ?? 'N/A',
                        $session->instructor->name ?? $session->external_instructor_name ?? 'N/A',
                        $session->max_participants ?? 'N/A',
                        $session->registered_participants ?? 0,
                        $session->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }
}
