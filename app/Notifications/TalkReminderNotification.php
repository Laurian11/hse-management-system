<?php

namespace App\Notifications;

use App\Models\ToolboxTalk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TalkReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ToolboxTalk $talk,
        public string $reminderType = '24h' // 24h, 1h, scheduled
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $talk = $this->talk;
        $timeUntil = $talk->scheduled_date->diffForHumans();
        
        $subject = match($this->reminderType) {
            '24h' => "Reminder: Toolbox Talk Tomorrow - {$talk->title}",
            '1h' => "Reminder: Toolbox Talk in 1 Hour - {$talk->title}",
            'scheduled' => "New Toolbox Talk Scheduled - {$talk->title}",
            default => "Toolbox Talk Reminder - {$talk->title}",
        };

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},");

        if ($this->reminderType === 'scheduled') {
            $message->line("A new toolbox talk has been scheduled for you.")
                ->line("**Title:** {$talk->title}")
                ->line("**Date & Time:** {$talk->scheduled_date->format('F j, Y \a\t g:i A')}")
                ->line("**Location:** {$talk->location}")
                ->line("**Duration:** {$talk->duration_minutes} minutes");
        } else {
            $message->line("This is a reminder about an upcoming toolbox talk.")
                ->line("**Title:** {$talk->title}")
                ->line("**Scheduled:** {$talk->scheduled_date->format('F j, Y \a\t g:i A')} ({$timeUntil})")
                ->line("**Location:** {$talk->location}")
                ->line("**Duration:** {$talk->duration_minutes} minutes");
        }

        if ($talk->description) {
            $message->line("**Description:** " . substr($talk->description, 0, 200) . "...");
        }

        $message->action('View Talk Details', route('toolbox-talks.show', $talk))
            ->line('Please ensure you attend this important safety briefing.');

        if ($talk->biometric_required) {
            $message->line('**Note:** Biometric check-in is required for this talk.');
        }

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'talk_id' => $this->talk->id,
            'talk_title' => $this->talk->title,
            'scheduled_date' => $this->talk->scheduled_date->toDateTimeString(),
            'reminder_type' => $this->reminderType,
        ];
    }
}
