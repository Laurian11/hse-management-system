<?php

namespace App\Http\Controllers;

use App\Models\ToolboxTalkTemplate;
use App\Models\ToolboxTalkTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolboxTalkTemplateController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        
        $templates = ToolboxTalkTemplate::forCompany($companyId)
            ->with('topic')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('toolbox-talks.templates.index', compact('templates'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $topics = ToolboxTalkTopic::active()->get();
        
        return view('toolbox-talks.templates.create', compact('topics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description_content' => 'nullable|string',
            'topic_id' => 'nullable|exists:toolbox_talk_topics,id',
            'talk_type' => 'required|in:safety,health,environment,incident_review,custom',
            'duration_minutes' => 'required|integer|min:5|max:60',
            'key_points' => 'nullable|string',
            'regulatory_references' => 'nullable|string',
            'materials' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template = ToolboxTalkTemplate::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'description' => $request->description,
            'title' => $request->title,
            'description_content' => $request->description_content,
            'topic_id' => $request->topic_id,
            'talk_type' => $request->talk_type,
            'duration_minutes' => $request->duration_minutes,
            'key_points' => $request->key_points,
            'regulatory_references' => $request->regulatory_references,
            'materials' => $request->materials ?? [],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('toolbox-talks.templates.show', $template)
            ->with('success', 'Template created successfully!');
    }

    public function show(ToolboxTalkTemplate $template)
    {
        if ($template->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $template->load('topic');
        
        return view('toolbox-talks.templates.show', compact('template'));
    }

    public function edit(ToolboxTalkTemplate $template)
    {
        if ($template->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $companyId = Auth::user()->company_id;
        $topics = ToolboxTalkTopic::active()->get();
        
        return view('toolbox-talks.templates.edit', compact('template', 'topics'));
    }

    public function update(Request $request, ToolboxTalkTemplate $template)
    {
        if ($template->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'description_content' => 'nullable|string',
            'topic_id' => 'nullable|exists:toolbox_talk_topics,id',
            'talk_type' => 'required|in:safety,health,environment,incident_review,custom',
            'duration_minutes' => 'required|integer|min:5|max:60',
            'key_points' => 'nullable|string',
            'regulatory_references' => 'nullable|string',
            'materials' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'title' => $request->title,
            'description_content' => $request->description_content,
            'topic_id' => $request->topic_id,
            'talk_type' => $request->talk_type,
            'duration_minutes' => $request->duration_minutes,
            'key_points' => $request->key_points,
            'regulatory_references' => $request->regulatory_references,
            'materials' => $request->materials ?? [],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('toolbox-talks.templates.show', $template)
            ->with('success', 'Template updated successfully!');
    }

    public function destroy(ToolboxTalkTemplate $template)
    {
        if ($template->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $template->delete();

        return redirect()
            ->route('toolbox-talks.templates.index')
            ->with('success', 'Template deleted successfully!');
    }

    public function json(ToolboxTalkTemplate $template)
    {
        if ($template->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        return response()->json($template);
    }
}

