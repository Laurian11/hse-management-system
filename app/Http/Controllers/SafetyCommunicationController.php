<?php

namespace App\Http\Controllers;

use App\Models\SafetyCommunication;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SafetyCommunicationController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        
        $query = SafetyCommunication::forCompany($companyId)
            ->with(['creator', 'company']);
        
        // Filters
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
            'total' => SafetyCommunication::forCompany($companyId)->count(),
            'sent' => SafetyCommunication::forCompany($companyId)->sent()->count(),
            'scheduled' => SafetyCommunication::forCompany($companyId)->scheduled()->count(),
            'draft' => SafetyCommunication::forCompany($companyId)->draft()->count(),
            'requires_ack' => SafetyCommunication::forCompany($companyId)->requiresAcknowledgment()->count(),
            'avg_ack_rate' => SafetyCommunication::forCompany($companyId)
                ->whereNotNull('acknowledgment_rate')
                ->avg('acknowledgment_rate') ?? 0,
        ];

        return view('safety-communications.index', compact('communications', 'stats'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        
        $departments = Department::where('company_id', $companyId)->get();
        $roles = Role::where('company_id', $companyId)->get();
        
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
        $this->authorize('view', $communication);
        
        $communication->load(['creator', 'company']);

        // Calculate recipient count based on target audience
        $recipientCount = $this->calculateRecipientCount($communication);

        return view('safety-communications.show', compact('communication', 'recipientCount'));
    }

    public function edit(SafetyCommunication $communication)
    {
        $this->authorize('update', $communication);
        
        if (!$communication->canBeEdited()) {
            return back()->with('error', 'Cannot edit communications that have been sent or expired.');
        }

        $companyId = Auth::user()->company_id;
        
        $departments = Department::where('company_id', $companyId)->get();
        $roles = User::where('company_id', $companyId)
            ->distinct()
            ->pluck('role');

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
        $this->authorize('delete', $communication);
        
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

        // Here you would implement the actual sending logic
        // - Send emails
        // - Send SMS
        // - Update digital signage
        // - Send mobile push notifications
        // - Generate printed notices

        return back()->with('success', "Communication sent to {$recipientCount} recipients!");
    }

    public function duplicate(SafetyCommunication $communication)
    {
        $this->authorize('view', $communication);
        
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
        $companyId = Auth::user()->company_id;
        
        // Recent communications
        $recentCommunications = SafetyCommunication::forCompany($companyId)
            ->with(['creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Scheduled communications
        $scheduledCommunications = SafetyCommunication::forCompany($companyId)
            ->scheduled()
            ->where('scheduled_send_time', '>', now())
            ->with(['creator'])
            ->orderBy('scheduled_send_time')
            ->limit(5)
            ->get();

        // Statistics
        $stats = [
            'total_sent' => SafetyCommunication::forCompany($companyId)->sent()->count(),
            'this_month' => SafetyCommunication::forCompany($companyId)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'emergency_sent' => SafetyCommunication::forCompany($companyId)
                ->sent()
                ->where('priority_level', 'emergency')
                ->count(),
            'avg_acknowledgment_rate' => SafetyCommunication::forCompany($companyId)
                ->sent()
                ->whereNotNull('acknowledgment_rate')
                ->avg('acknowledgment_rate') ?? 0,
        ];

        // Communication types breakdown
        $typeBreakdown = SafetyCommunication::forCompany($companyId)
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

    private function calculateRecipientCount(SafetyCommunication $communication): int
    {
        $query = User::where('company_id', $communication->company_id);

        switch ($communication->target_audience) {
            case 'all_employees':
                return $query->count();
            
            case 'specific_departments':
                return $query->whereIn('department_id', $communication->target_departments ?? [])->count();
            
            case 'specific_roles':
                return $query->whereIn('role', $communication->target_roles ?? [])->count();
            
            case 'specific_locations':
                return $query->whereIn('work_location', $communication->target_locations ?? [])->count();
            
            case 'management_only':
                return $query->whereIn('role', ['manager', 'supervisor', 'director'])->count();
            
            case 'supervisors_only':
                return $query->where('role', 'supervisor')->count();
            
            default:
                return 0;
        }
    }
}
