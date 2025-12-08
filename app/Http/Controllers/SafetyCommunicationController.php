<?php

namespace App\Http\Controllers;

use App\Models\SafetyCommunication;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SafetyCommunicationController extends Controller
{
    use UsesCompanyGroup;

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $query = SafetyCommunication::whereIn('company_id', $companyGroupIds)
            ->with(['creator', 'company']);
        
        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('type')) {
            $query->where('communication_type', $request->type);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority_level', $request->priority);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $communications = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Statistics
        $stats = [
            'total' => SafetyCommunication::whereIn('company_id', $companyGroupIds)->count(),
            'sent' => SafetyCommunication::whereIn('company_id', $companyGroupIds)->sent()->count(),
            'scheduled' => SafetyCommunication::whereIn('company_id', $companyGroupIds)->scheduled()->count(),
            'draft' => SafetyCommunication::whereIn('company_id', $companyGroupIds)->draft()->count(),
            'requires_ack' => SafetyCommunication::whereIn('company_id', $companyGroupIds)->requiresAcknowledgment()->count(),
            'avg_ack_rate' => SafetyCommunication::whereIn('company_id', $companyGroupIds)
                ->whereNotNull('acknowledgment_rate')
                ->avg('acknowledgment_rate') ?? 0,
        ];

        return view('safety-communications.index', compact('communications', 'stats'));
    }

    public function create()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        $roles = Role::whereIn('company_id', $companyGroupIds)->get();
        
        return view('safety-communications.create', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'communication_type' => 'required|in:announcement,alert,bulletin,emergency,reminder,policy_update,training_notice',
            'priority_level' => 'required|in:low,medium,high,critical,emergency',
            'target_audience' => 'required|in:all_employees,specific_departments,specific_roles,specific_locations,management_only,supervisors_only',
            'delivery_method' => 'required|in:digital_signage,mobile_push,email,sms,printed_notice,video_conference,in_person',
            'requires_acknowledgment' => 'boolean',
            'acknowledgment_deadline' => 'required_if:requires_acknowledgment,1|date|after:today',
            'scheduled_send_time' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:scheduled_send_time',
            'target_departments' => 'required_if:target_audience,specific_departments|array',
            'target_roles' => 'required_if:target_audience,specific_roles|array',
            'target_locations' => 'required_if:target_audience,specific_locations|array',
            'attachments' => 'nullable|array',
            'quiz_questions' => 'nullable|array',
            'is_multilingual' => 'boolean',
            'translations' => 'required_if:is_multilingual,1|array',
        ]);

        $communication = SafetyCommunication::create([
            'reference_number' => 'SC-' . date('Ym') . '-TEMP',
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::id(),
            'title' => $request->title,
            'message' => $request->message,
            'communication_type' => $request->communication_type,
            'priority_level' => $request->priority_level,
            'target_audience' => $request->target_audience,
            'target_departments' => $request->target_departments,
            'target_roles' => $request->target_roles,
            'target_locations' => $request->target_locations,
            'delivery_method' => $request->delivery_method,
            'delivery_channels' => $request->delivery_channels ?? [$request->delivery_method],
            'requires_acknowledgment' => $request->boolean('requires_acknowledgment', false),
            'acknowledgment_deadline' => $request->acknowledgment_deadline,
            'scheduled_send_time' => $request->scheduled_send_time,
            'expires_at' => $request->expires_at,
            'status' => $request->scheduled_send_time ? 'scheduled' : 'draft',
            'attachments' => $request->attachments,
            'quiz_questions' => $request->quiz_questions,
            'is_multilingual' => $request->boolean('is_multilingual', false),
            'translations' => $request->translations,
        ]);

        // Generate proper reference number
        $communication->reference_number = $communication->generateReferenceNumber();
        $communication->save();

        return redirect()
            ->route('safety-communications.show', $communication)
            ->with('success', 'Safety communication created successfully!');
    }

    public function show(SafetyCommunication $communication)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($communication->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $communication->load(['creator', 'company']);

        // Calculate recipient count based on target audience
        $recipientCount = $this->calculateRecipientCount($communication);

        return view('safety-communications.show', compact('communication', 'recipientCount'));
    }

    public function edit(SafetyCommunication $communication)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($communication->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        if (!$communication->canBeEdited()) {
            return back()->with('error', 'Cannot edit communications that have been sent or expired.');
        }

        $departments = Department::whereIn('company_id', $companyGroupIds)->active()->get();
        $roles = Role::whereIn('company_id', $companyGroupIds)->get();

        return view('safety-communications.edit', compact('communication', 'departments', 'roles'));
    }

    public function update(Request $request, SafetyCommunication $communication)
    {
        $this->authorize('update', $communication);
        
        if (!$communication->canBeEdited()) {
            return back()->with('error', 'Cannot edit communications that have been sent or expired.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'communication_type' => 'required|in:announcement,alert,bulletin,emergency,reminder,policy_update,training_notice',
            'priority_level' => 'required|in:low,medium,high,critical,emergency',
            'target_audience' => 'required|in:all_employees,specific_departments,specific_roles,specific_locations,management_only,supervisors_only',
            'delivery_method' => 'required|in:digital_signage,mobile_push,email,sms,printed_notice,video_conference,in_person',
            'requires_acknowledgment' => 'boolean',
            'acknowledgment_deadline' => 'required_if:requires_acknowledgment,1|date|after:today',
            'scheduled_send_time' => 'nullable|date|after:now',
            'expires_at' => 'nullable|date|after:scheduled_send_time',
            'target_departments' => 'required_if:target_audience,specific_departments|array',
            'target_roles' => 'required_if:target_audience,specific_roles|array',
            'target_locations' => 'required_if:target_audience,specific_locations|array',
            'attachments' => 'nullable|array',
            'quiz_questions' => 'nullable|array',
        ]);

        $communication->update([
            'title' => $request->title,
            'message' => $request->message,
            'communication_type' => $request->communication_type,
            'priority_level' => $request->priority_level,
            'target_audience' => $request->target_audience,
            'target_departments' => $request->target_departments,
            'target_roles' => $request->target_roles,
            'target_locations' => $request->target_locations,
            'delivery_method' => $request->delivery_method,
            'delivery_channels' => $request->delivery_channels ?? [$request->delivery_method],
            'requires_acknowledgment' => $request->boolean('requires_acknowledgment', false),
            'acknowledgment_deadline' => $request->acknowledgment_deadline,
            'scheduled_send_time' => $request->scheduled_send_time,
            'expires_at' => $request->expires_at,
            'status' => $request->scheduled_send_time ? 'scheduled' : 'draft',
            'attachments' => $request->attachments,
            'quiz_questions' => $request->quiz_questions,
        ]);

        return redirect()
            ->route('safety-communications.show', $communication)
            ->with('success', 'Safety communication updated successfully!');
    }

    public function destroy(SafetyCommunication $communication)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($communication->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        if ($communication->isSent()) {
            return back()->with('error', 'Cannot delete communications that have been sent.');
        }

        $communication->delete();

        return redirect()->route('safety-communications.index')
            ->with('success', 'Safety communication deleted successfully!');
    }

    // Specialized methods

    public function send(SafetyCommunication $communication)
    {
        $this->authorize('update', $communication);
        
        if ($communication->status !== 'draft' && $communication->status !== 'scheduled') {
            return back()->with('error', 'Can only send draft or scheduled communications.');
        }

        // Calculate recipient count
        $recipientCount = $this->calculateRecipientCount($communication);
        
        $communication->update([
            'status' => 'sent',
            'sent_at' => now(),
            'total_recipients' => $recipientCount,
        ]);

        // Send notifications to recipients
        $recipients = $this->getRecipients($communication);
        
        foreach ($recipients as $recipient) {
            $recipient->notify(new \App\Notifications\SafetyCommunicationSentNotification($communication));
        }

        // Here you would implement additional sending logic
        // - Send SMS
        // - Update digital signage
        // - Send mobile push notifications
        // - Generate printed notices

        return back()->with('success', "Communication sent to {$recipientCount} recipients!");
    }

    public function duplicate(SafetyCommunication $communication)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($communication->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $newCommunication = $communication->replicate();
        $newCommunication->reference_number = 'SC-' . date('Ym') . '-TEMP';
        $newCommunication->created_by = Auth::id();
        $newCommunication->status = 'draft';
        $newCommunication->sent_at = null;
        $newCommunication->total_recipients = 0;
        $newCommunication->acknowledged_count = 0;
        $newCommunication->read_count = 0;
        $newCommunication->acknowledgment_rate = 0;
        $newCommunication->save();

        // Generate proper reference number
        $newCommunication->reference_number = $newCommunication->generateReferenceNumber();
        $newCommunication->save();

        return redirect()
            ->route('safety-communications.edit', $newCommunication)
            ->with('success', 'Communication duplicated successfully!');
    }

    public function dashboard()
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        // Recent communications
        $recentCommunications = SafetyCommunication::whereIn('company_id', $companyGroupIds)
            ->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Scheduled communications
        $scheduledCommunications = SafetyCommunication::whereIn('company_id', $companyGroupIds)
            ->scheduled()
            ->where('scheduled_send_time', '>', now())
            ->with(['creator'])
            ->orderBy('scheduled_send_time')
            ->limit(5)
            ->get();

        // Statistics
        $stats = [
            'total_sent' => SafetyCommunication::whereIn('company_id', $companyGroupIds)->sent()->count(),
            'this_month' => SafetyCommunication::whereIn('company_id', $companyGroupIds)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'emergency_sent' => SafetyCommunication::whereIn('company_id', $companyGroupIds)
                ->sent()
                ->where('priority_level', 'emergency')
                ->count(),
            'avg_acknowledgment_rate' => SafetyCommunication::whereIn('company_id', $companyGroupIds)
                ->sent()
                ->whereNotNull('acknowledgment_rate')
                ->avg('acknowledgment_rate') ?? 0,
        ];

        // Communication types breakdown
        $typeBreakdown = SafetyCommunication::whereIn('company_id', $companyGroupIds)
            ->sent()
            ->selectRaw('communication_type, count(*) as count')
            ->groupBy('communication_type')
            ->pluck('count', 'communication_type');

        return view('safety-communications.dashboard', compact(
            'recentCommunications',
            'scheduledCommunications',
            'stats',
            'typeBreakdown'
        ));
    }

    /**
     * Get recipients for a communication
     */
    private function getRecipients(SafetyCommunication $communication)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        $query = User::whereIn('company_id', $companyGroupIds);

        switch ($communication->target_audience) {
            case 'all_employees':
                return $query->get();
            
            case 'specific_departments':
                return $query->whereIn('department_id', $communication->target_departments ?? [])->get();
            
            case 'specific_roles':
                return $query->whereHas('role', function($q) use ($communication) {
                    $q->whereIn('name', $communication->target_roles ?? []);
                })->get();
            
            case 'specific_locations':
                return $query->whereIn('work_location', $communication->target_locations ?? [])->get();
            
            case 'management_only':
                return $query->whereHas('role', function($q) {
                    $q->whereIn('name', ['manager', 'supervisor', 'director', 'admin', 'hse_manager']);
                })->get();
            
            case 'supervisors_only':
                return $query->whereHas('role', function($q) {
                    $q->where('name', 'supervisor');
                })->get();
            
            default:
                return collect();
        }
    }

    private function calculateRecipientCount(SafetyCommunication $communication): int
    {
        return $this->getRecipients($communication)->count();
    }
}
