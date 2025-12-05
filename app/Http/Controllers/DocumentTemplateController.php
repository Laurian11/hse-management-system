<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentTemplateController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = DocumentTemplate::forCompany($companyId)
            ->with(['creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('template_type')) {
            $query->where('template_type', $request->template_type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $templates = $query->latest()->paginate(15);
        return view('documents.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('documents.templates.create');
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_type' => 'required|in:policy,procedure,form,checklist,report,other',
            'category' => 'nullable|string|max:255',
            'template_content' => 'nullable|string',
            'fields' => 'nullable|array',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['company_id'] = $companyId;
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('document-templates', 'public');
            $validated['file_path'] = $path;
        }

        DocumentTemplate::create($validated);

        return redirect()->route('documents.templates.index')
            ->with('success', 'Document template created successfully.');
    }

    public function show(DocumentTemplate $template)
    {
        $template->load(['creator']);
        return view('documents.templates.show', compact('template'));
    }

    public function edit(DocumentTemplate $template)
    {
        return view('documents.templates.edit', compact('template'));
    }

    public function update(Request $request, DocumentTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_type' => 'required|in:policy,procedure,form,checklist,report,other',
            'category' => 'nullable|string|max:255',
            'template_content' => 'nullable|string',
            'fields' => 'nullable|array',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
                Storage::disk('public')->delete($template->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('document-templates', 'public');
            $validated['file_path'] = $path;
        }

        $template->update($validated);

        return redirect()->route('documents.templates.show', $template)
            ->with('success', 'Document template updated successfully.');
    }

    public function destroy(DocumentTemplate $template)
    {
        // Delete file if exists
        if ($template->file_path && Storage::disk('public')->exists($template->file_path)) {
            Storage::disk('public')->delete($template->file_path);
        }

        $template->delete();

        return redirect()->route('documents.templates.index')
            ->with('success', 'Document template deleted successfully.');
    }
}
