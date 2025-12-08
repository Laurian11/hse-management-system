<?php

namespace App\Notifications;

use App\Models\SafetyCommunication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SafetyCommunicationSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $communication;

    /**
     * Create a new notification instance.
     */
    public function __construct(SafetyCommunication $communication)
    {
        $this->communication = $communication;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $priorityColor = match($this->communication->priority_level) {
            'emergency' => 'red',
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'blue',
            'low' => 'gray',
            default => 'gray',
        };

        return (new MailMessage)
            ->subject('Safety Communication: ' . $this->communication->title)
            ->line('A new safety communication has been sent to you.')
            ->line('**Title:** ' . $this->communication->title)
            ->line('**Type:** ' . ucfirst(str_replace('_', ' ', $this->communication->communication_type)))
            ->line('**Priority:** ' . ucfirst($this->communication->priority_level))
            ->line('**Message:**')
            ->line($this->communication->message)
            ->when($this->communication->requires_acknowledgment, function ($mail) {
                return $mail->line('**Action Required:** This communication requires acknowledgment.')
                    ->when($this->communication->acknowledgment_deadline, function ($mail) {
                        return $mail->line('**Acknowledgment Deadline:** ' . $this->communication->acknowledgment_deadline->format('F j, Y g:i A'));
                    });
            })
            ->action('View Communication', route('safety-communications.show', $this->communication))
            ->line('Thank you for your attention to this matter.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'safety_communication_sent',
            'communication_id' => $this->communication->id,
            'title' => $this->communication->title,
            'reference_number' => $this->communication->reference_number,
            'priority_level' => $this->communication->priority_level,
            'requires_acknowledgment' => $this->communication->requires_acknowledgment,
            'message' => 'A new safety communication has been sent to you.',
        ];
    }
}

