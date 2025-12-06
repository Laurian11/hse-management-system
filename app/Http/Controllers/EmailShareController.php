<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmailShareController extends Controller
{
    /**
     * Share document/report via email
     */
    public function share(Request $request)
    {
        $validated = $request->validate([
            'recipients' => 'required|string',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        // Parse recipients (comma-separated emails)
        $recipients = array_map('trim', explode(',', $validated['recipients']));
        $recipients = array_filter($recipients, fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL));

        if (empty($recipients)) {
            return back()->withErrors(['recipients' => 'Please provide at least one valid email address.']);
        }

        // Handle file attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('email-attachments', 'public');
                $attachmentPaths[] = storage_path('app/public/' . $path);
            }
        }

        // Send email to each recipient
        try {
            foreach ($recipients as $email) {
                Mail::send([], [], function ($message) use ($email, $validated, $attachmentPaths) {
                    $message->to($email)
                        ->subject($validated['subject'])
                        ->html(nl2br(e($validated['content'])));

                    // Attach files
                    foreach ($attachmentPaths as $attachmentPath) {
                        if (file_exists($attachmentPath)) {
                            $message->attach($attachmentPath);
                        }
                    }
                });
            }
        } catch (\Exception $e) {
            // Clean up files even if email fails
            foreach ($attachmentPaths as $path) {
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
            return back()->withErrors(['email' => 'Failed to send email: ' . $e->getMessage()]);
        }

        // Clean up temporary files
        foreach ($attachmentPaths as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        return back()->with('success', 'Email sent successfully to ' . count($recipients) . ' recipient(s).');
    }
}

