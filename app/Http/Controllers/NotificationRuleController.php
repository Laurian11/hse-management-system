<?php

namespace App\Http\Controllers;

use App\Models\NotificationRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationRuleController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = NotificationRule::forCompany($companyId)
            ->with(['creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('trigger_type')) {
            $query->where('trigger_type', $request->trigger_type);
        }

        if ($request->filled('notification_channel')) {
            $query->where('notification_channel', $request->notification_channel);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $rules = $query->latest()->paginate(15);
        return view('notifications.rules.index', compact('rules'));
    }

    public function create()
    {
        return view('notifications.rules.create');
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|in:incident,permit_expiry,ppe_expiry,training_due,certificate_expiry,compliance_due,audit_scheduled,other',
            'notification_channel' => 'required|in:email,sms,push,both',
            'recipients' => 'required|array',
            'recipients.*' => 'required|string',
            'conditions' => 'nullable|array',
            'days_before' => 'nullable|integer|min:0|max:365',
            'days_after' => 'nullable|integer|min:0|max:365',
            'frequency' => 'nullable|in:once,daily,weekly,monthly',
            'notification_time' => 'nullable|date_format:H:i',
            'message_template' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = $companyId;
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        NotificationRule::create($validated);

        return redirect()->route('notifications.rules.index')
            ->with('success', 'Notification rule created successfully.');
    }

    public function show(NotificationRule $rule)
    {
        $rule->load(['creator']);
        return view('notifications.rules.show', compact('rule'));
    }

    public function edit(NotificationRule $rule)
    {
        return view('notifications.rules.edit', compact('rule'));
    }

    public function update(Request $request, NotificationRule $rule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|in:incident,permit_expiry,ppe_expiry,training_due,certificate_expiry,compliance_due,audit_scheduled,other',
            'notification_channel' => 'required|in:email,sms,push,both',
            'recipients' => 'required|array',
            'recipients.*' => 'required|string',
            'conditions' => 'nullable|array',
            'days_before' => 'nullable|integer|min:0|max:365',
            'days_after' => 'nullable|integer|min:0|max:365',
            'frequency' => 'nullable|in:once,daily,weekly,monthly',
            'notification_time' => 'nullable|date_format:H:i',
            'message_template' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $rule->update($validated);

        return redirect()->route('notifications.rules.show', $rule)
            ->with('success', 'Notification rule updated successfully.');
    }

    public function destroy(NotificationRule $rule)
    {
        $rule->delete();

        return redirect()->route('notifications.rules.index')
            ->with('success', 'Notification rule deleted successfully.');
    }
}
