<?php

namespace App\Http\Controllers;

use App\Models\EscalationMatrix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EscalationMatrixController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = EscalationMatrix::forCompany($companyId)
            ->with(['defaultAssignee', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('severity_level')) {
            $query->where('severity_level', $request->severity_level);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $matrices = $query->latest()->paginate(15);
        return view('notifications.escalation-matrices.index', compact('matrices'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('notifications.escalation-matrices.create', compact('users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:incident,capa_overdue,permit_expiry,ppe_expiry,training_due,compliance_due,audit_finding,other',
            'severity_level' => 'required|in:low,medium,high,critical',
            'days_overdue' => 'nullable|integer|min:0|max:365',
            'escalation_levels' => 'required|array|min:1',
            'escalation_levels.*.level' => 'required|integer|min:1',
            'escalation_levels.*.delay_minutes' => 'required|integer|min:0',
            'escalation_levels.*.recipient_type' => 'required|string',
            'escalation_levels.*.channel' => 'required|in:email,sms,push',
            'escalation_levels.*.message_template' => 'nullable|string',
            'default_assignee_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = $companyId;
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        EscalationMatrix::create($validated);

        return redirect()->route('notifications.escalation-matrices.index')
            ->with('success', 'Escalation matrix created successfully.');
    }

    public function show(EscalationMatrix $escalationMatrix)
    {
        $escalationMatrix->load(['defaultAssignee', 'creator']);
        return view('notifications.escalation-matrices.show', compact('escalationMatrix'));
    }

    public function edit(EscalationMatrix $escalationMatrix)
    {
        $companyId = Auth::user()->company_id;
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('notifications.escalation-matrices.edit', compact('escalationMatrix', 'users'));
    }

    public function update(Request $request, EscalationMatrix $escalationMatrix)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|in:incident,capa_overdue,permit_expiry,ppe_expiry,training_due,compliance_due,audit_finding,other',
            'severity_level' => 'required|in:low,medium,high,critical',
            'days_overdue' => 'nullable|integer|min:0|max:365',
            'escalation_levels' => 'required|array|min:1',
            'escalation_levels.*.level' => 'required|integer|min:1',
            'escalation_levels.*.delay_minutes' => 'required|integer|min:0',
            'escalation_levels.*.recipient_type' => 'required|string',
            'escalation_levels.*.channel' => 'required|in:email,sms,push',
            'escalation_levels.*.message_template' => 'nullable|string',
            'default_assignee_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $escalationMatrix->update($validated);

        return redirect()->route('notifications.escalation-matrices.show', $escalationMatrix)
            ->with('success', 'Escalation matrix updated successfully.');
    }

    public function destroy(EscalationMatrix $escalationMatrix)
    {
        $escalationMatrix->delete();

        return redirect()->route('notifications.escalation-matrices.index')
            ->with('success', 'Escalation matrix deleted successfully.');
    }
}
