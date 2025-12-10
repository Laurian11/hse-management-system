<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidentReportedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Incident $incident
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $incident = $this->incident;
        
        $severityColors = [
            'critical' => '#dc2626',
            'high' => '#ea580c',
            'medium' => '#ca8a04',
            'low' => '#16a34a',
        ];

        $message = (new MailMessage)
            ->subject("New Incident Reported: {$incident->reference_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new incident has been reported and requires your attention.")
            ->line("**Incident Reference:** {$incident->reference_number}")
            ->line("**Type:** " . ucfirst($incident->incident_type))
            ->line("**Severity:** " . ucfirst($incident->severity))
            ->line("**Location:** {$incident->location}")
            ->line("**Reported By:** {$incident->reporter->name}")
            ->line("**Reported At:** {$incident->reported_at->format('F j, Y g:i A')}");

        if ($incident->description) {
            $message->line("**Description:** " . substr($incident->description, 0, 200) . (strlen($incident->description) > 200 ? '...' : ''));
        }

        if ($incident->assignedTo) {
            $message->line("**Assigned To:** {$incident->assignedTo->name}");
        }

        $message->action('View Incident', route('incidents.show', $incident))
            ->line('Please review and take appropriate action.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'incident_id' => $this->incident->id,
            'reference_number' => $this->incident->reference_number,
            'incident_type' => $this->incident->incident_type,
            'severity' => $this->incident->severity,
        ];
    }
}







