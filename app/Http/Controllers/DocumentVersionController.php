<?php

namespace App\Http\Controllers;

use App\Models\DocumentVersion;
use App\Models\HSEDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentVersionController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = DocumentVersion::whereHas('document', fn($q) => $q->where('company_id', $companyId))
            ->with(['document', 'creator', 'reviewer', 'approver']);

        if ($request->filled('document_id')) {
            $query->where('hse_document_id', $request->document_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $versions = $query->latest()->paginate(15);
        $documents = HSEDocument::forCompany($companyId)->get();
        
        return view('documents.versions.index', compact('versions', 'documents'));
    }

    public function create(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $documents = HSEDocument::forCompany($companyId)->active()->get();
        $documentId = $request->get('document_id');
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        
        return view('documents.versions.create', compact('documents', 'documentId', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'hse_document_id' => 'required|exists:hse_documents,id',
            'version_number' => 'required|string|max:50',
            'change_summary' => 'nullable|string',
            'reviewed_by' => 'nullable|exists:users,id',
            'approved_by' => 'nullable|exists:users,id',
            'review_date' => 'nullable|date',
            'approval_date' => 'nullable|date',
            'status' => 'required|in:draft,under_review,approved,rejected',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'is_current' => 'nullable|boolean',
            'rejection_reason' => 'nullable|string',
        ]);

        // Verify document belongs to company
        $document = HSEDocument::forCompany($companyId)->findOrFail($validated['hse_document_id']);

        $validated['created_by'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('document-versions', 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
        }

        DocumentVersion::create($validated);

        return redirect()->route('documents.versions.index')
            ->with('success', 'Document version created successfully.');
    }

    public function show(DocumentVersion $version)
    {
        $version->load(['document', 'creator', 'reviewer', 'approver']);
        return view('documents.versions.show', compact('version'));
    }

    public function edit(DocumentVersion $version)
    {
        $companyId = Auth::user()->company_id;
        $documents = HSEDocument::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('documents.versions.edit', compact('version', 'documents', 'users'));
    }

    public function update(Request $request, DocumentVersion $version)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'version_number' => 'required|string|max:50',
            'change_summary' => 'nullable|string',
            'reviewed_by' => 'nullable|exists:users,id',
            'approved_by' => 'nullable|exists:users,id',
            'review_date' => 'nullable|date',
            'approval_date' => 'nullable|date',
            'status' => 'required|in:draft,under_review,approved,rejected',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'is_current' => 'nullable|boolean',
            'rejection_reason' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($version->file_path && Storage::disk('public')->exists($version->file_path)) {
                Storage::disk('public')->delete($version->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('document-versions', 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
        }

        $version->update($validated);

        return redirect()->route('documents.versions.show', $version)
            ->with('success', 'Document version updated successfully.');
    }

    public function destroy(DocumentVersion $version)
    {
        // Delete file if exists
        if ($version->file_path && Storage::disk('public')->exists($version->file_path)) {
            Storage::disk('public')->delete($version->file_path);
        }

        $version->delete();

        return redirect()->route('documents.versions.index')
            ->with('success', 'Document version deleted successfully.');
    }
}
