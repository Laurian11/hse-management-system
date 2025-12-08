<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $message = 'This is a test notification from the HSE Management System.'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Test Email Notification - HSE Management System')
            ->greeting("Hello {$notifiable->name},")
            ->line('This is a test email notification to verify that the email system is working correctly.')
            ->line("**Test Message:** {$this->message}")
            ->line('**Sent At:** ' . now()->format('F j, Y \a\t g:i A'))
            ->line('**Your Email:** ' . $notifiable->email)
            ->line('If you received this email, the notification system is working properly!')
            ->action('Visit Dashboard', route('dashboard'))
            ->line('Thank you for using the HSE Management System.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}

