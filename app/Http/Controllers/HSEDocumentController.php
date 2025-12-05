<?php

namespace App\Http\Controllers;

use App\Models\HSEDocument;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HSEDocumentController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = HSEDocument::forCompany($companyId)
            ->with(['creator', 'department', 'approver']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('document_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $documents = $query->latest()->paginate(15);
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('documents.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:policy,procedure,form,template,manual,guideline,standard,other',
            'category' => 'required|in:safety,health,environmental,quality,compliance,training,emergency,other',
            'document_code' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'approved_by' => 'nullable|exists:users,id',
            'approval_date' => 'nullable|date',
            'effective_date' => 'nullable|date',
            'review_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:draft,under_review,approved,active,superseded,archived,cancelled',
            'access_level' => 'required|in:public,restricted,confidential,classified',
            'access_permissions' => 'nullable|array',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'retention_years' => 'nullable|integer|min:1|max:100',
            'archival_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $companyId;
        $validated['created_by'] = Auth::id();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents', 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $file->getClientMimeType();
            $validated['file_size'] = $file->getSize();
        }

        HSEDocument::create($validated);

        return redirect()->route('documents.index')
            ->with('success', 'Document created successfully.');
    }

    public function show(HSEDocument $document)
    {
        $document->load(['creator', 'department', 'approver', 'versions']);
        return view('documents.show', compact('document'));
    }

    public function edit(HSEDocument $document)
    {
        $companyId = Auth::user()->company_id;
        $departments = Department::forCompany($companyId)->active()->get();
        $users = \App\Models\User::forCompany($companyId)->active()->get();
        return view('documents.edit', compact('document', 'departments', 'users'));
    }

    public function update(Request $request, HSEDocument $document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:policy,procedure,form,template,manual,guideline,standard,other',
            'category' => 'required|in:safety,health,environmental,quality,compliance,training,emergency,other',
            'document_code' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'approved_by' => 'nullable|exists:users,id',
            'approval_date' => 'nullable|date',
            'effective_date' => 'nullable|date',
            'review_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:draft,under_review,approved,active,superseded,archived,cancelled',
            'access_level' => 'required|in:public,restricted,confidential,classified',
            'access_permissions' => 'nullable|array',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'retention_years' => 'nullable|integer|min:1|max:100',
            'archival_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('documents', 'public');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $file->getClientMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $document->update($validated);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(HSEDocument $document)
    {
        // Delete file if exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }
}
