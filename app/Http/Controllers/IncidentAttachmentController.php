<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class IncidentAttachmentController extends Controller
{
    /**
     * Store a newly uploaded attachment
     */
    public function store(Request $request, Incident $incident)
    {
        if ($incident->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'category' => 'required|in:photo,video,document,witness_statement,interview_recording,policy_manual,training_record,equipment_manual,other',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|string|max:255',
            'is_evidence' => 'boolean',
            'is_confidential' => 'boolean',
        ]);

        $file = $request->file('file');
        $path = $file->store('incident-attachments', 'public');
        
        $attachment = IncidentAttachment::create([
            'incident_id' => $incident->id,
            'company_id' => Auth::user()->company_id,
            'uploaded_by' => Auth::user()->id,
            'file_name' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_extension' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'tags' => $validated['tags'] ?? null,
            'is_evidence' => $validated['is_evidence'] ?? false,
            'is_confidential' => $validated['is_confidential'] ?? false,
        ]);

        return back()->with('success', 'File uploaded successfully!');
    }

    /**
     * Display/download the attachment
     */
    public function show(Incident $incident, IncidentAttachment $attachment)
    {
        if ($attachment->company_id !== Auth::user()->company_id || 
            $attachment->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_name);
    }

    /**
     * Delete the attachment
     */
    public function destroy(Incident $incident, IncidentAttachment $attachment)
    {
        if ($attachment->company_id !== Auth::user()->company_id || 
            $attachment->incident_id !== $incident->id) {
            abort(403, 'Unauthorized');
        }

        // Delete file
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'Attachment deleted successfully!');
    }
}
