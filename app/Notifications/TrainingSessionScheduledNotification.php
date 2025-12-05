<?php

namespace App\Notifications;

use App\Models\TrainingSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingSessionScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TrainingSession $session
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $session = $this->session;
        $timeUntil = $session->scheduled_start->diffForHumans();
        
        $message = (new MailMessage)
            ->subject("Training Session Scheduled: {$session->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A training session has been scheduled and you are registered to attend.")
            ->line("**Session Title:** {$session->title}")
            ->line("**Reference:** {$session->reference_number}")
            ->line("**Scheduled Date:** {$session->scheduled_start->format('F j, Y')}")
            ->line("**Scheduled Time:** {$session->scheduled_start->format('g:i A')}")
            ->line("**Duration:** " . ($session->scheduled_end->diffInMinutes($session->scheduled_start)) . " minutes")
            ->line("**Time Until Session:** {$timeUntil}");

        if ($session->location_name) {
            $message->line("**Location:** {$session->location_name}");
        }

        if ($session->trainingPlan) {
            $message->line("**Training Plan:** {$session->trainingPlan->title}");
        }

        if ($session->instructor) {
            $message->line("**Instructor:** {$session->instructor->name}");
        } elseif ($session->external_instructor_name) {
            $message->line("**Instructor:** {$session->external_instructor_name}");
        }

        if ($session->description) {
            $message->line("**Description:** " . substr($session->description, 0, 200) . (strlen($session->description) > 200 ? '...' : ''));
        }

        $message->action('View Session', route('training.training-sessions.show', $session))
            ->line('Please ensure you are available for this training session.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'session_id' => $this->session->id,
            'reference_number' => $this->session->reference_number,
            'title' => $this->session->title,
            'scheduled_start' => $this->session->scheduled_start->toDateTimeString(),
        ];
    }
}

