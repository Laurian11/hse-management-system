<?php

namespace App\Notifications;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidentStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Incident $incident,
        public string $oldStatus,
        public string $newStatus,
        public ?string $changedBy = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $incident = $this->incident;
        
        $message = (new MailMessage)
            ->subject("Incident Status Changed: {$incident->reference_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The status of incident {$incident->reference_number} has been changed.")
            ->line("**Previous Status:** " . ucfirst($this->oldStatus))
            ->line("**New Status:** " . ucfirst($this->newStatus))
            ->line("**Incident Reference:** {$incident->reference_number}")
            ->line("**Title:** " . ($incident->title ?? $incident->incident_type))
            ->line("**Severity:** " . ucfirst($incident->severity));

        if ($this->changedBy) {
            $message->line("**Changed By:** {$this->changedBy}");
        }

        $message->action('View Incident', route('incidents.show', $incident))
            ->line('Please review the updated incident status.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'incident_id' => $this->incident->id,
            'reference_number' => $this->incident->reference_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}

