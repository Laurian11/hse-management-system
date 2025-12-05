<?php

namespace App\Notifications;

use App\Models\ProcurementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ProcurementRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ProcurementRequest $procurementRequest,
        public string $action = 'created' // 'created', 'submitted', 'updated'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $request = $this->procurementRequest;
        $requestedBy = $request->requestedBy;
        $department = $request->department;
        
        $subject = match($this->action) {
            'submitted' => "New Procurement Request Submitted: {$request->reference_number}",
            'updated' => "Procurement Request Updated: {$request->reference_number}",
            default => "New Procurement Request: {$request->reference_number}",
        };

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello,")
            ->line("A procurement request has been {$this->action} and requires your attention.")
            ->line("**Reference Number:** {$request->reference_number}")
            ->line("**Item Name:** {$request->item_name}")
            ->line("**Category:** " . ucfirst(str_replace('_', ' ', $request->item_category)))
            ->line("**Quantity:** {$request->quantity} " . ($request->unit ?? ''))
            ->line("**Priority:** " . ucfirst($request->priority))
            ->line("**Status:** " . ucfirst(str_replace('_', ' ', $request->status)));

        if ($request->estimated_cost) {
            $message->line("**Estimated Cost:** " . ($request->currency ?? '') . " " . number_format($request->estimated_cost, 2));
        }

        if ($request->required_date) {
            $message->line("**Required By:** {$request->required_date->format('M d, Y')}");
        }

        if ($requestedBy) {
            $message->line("**Requested By:** {$requestedBy->name} ({$requestedBy->email})");
        }

        if ($department) {
            $message->line("**Department:** {$department->name}");
        }

        if ($request->justification) {
            $message->line("**Justification:** " . substr($request->justification, 0, 200) . (strlen($request->justification) > 200 ? '...' : ''));
        }

        if ($request->description) {
            $message->line("**Description:** " . substr($request->description, 0, 200) . (strlen($request->description) > 200 ? '...' : ''));
        }

        $message->action('View Request', route('procurement.requests.show', $request))
            ->line('Please review this procurement request and take appropriate action.');

        // Generate and attach PDF requisition document
        try {
            $pdf = Pdf::loadView('procurement.requests.pdf', [
                'procurementRequest' => $request
            ])->setPaper('a4', 'portrait');

            $filename = "requisition-{$request->reference_number}.pdf";
            $pdfContent = $pdf->output();

            $message->attachData($pdfContent, $filename, [
                'mime' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the notification
            \Log::warning('Failed to generate PDF for procurement request: ' . $e->getMessage());
        }

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'procurement_request_id' => $this->procurementRequest->id,
            'reference_number' => $this->procurementRequest->reference_number,
            'item_name' => $this->procurementRequest->item_name,
            'action' => $this->action,
            'status' => $this->procurementRequest->status,
        ];
    }
}

