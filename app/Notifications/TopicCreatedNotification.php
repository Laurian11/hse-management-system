<?php

namespace App\Notifications;

use App\Models\ToolboxTalkTopic;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TopicCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ToolboxTalkTopic $topic
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $topic = $this->topic;
        $representer = $topic->representer;
        
        $message = (new MailMessage)
            ->subject("New Toolbox Talk Topic Created: {$topic->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new toolbox talk topic has been created and requires your review.")
            ->line("**Topic Title:** {$topic->title}")
            ->line("**Category:** " . ucfirst($topic->category))
            ->line("**Difficulty Level:** " . ucfirst($topic->difficulty_level))
            ->line("**Estimated Duration:** {$topic->estimated_duration_minutes} minutes");

        if ($representer) {
            $message->line("**Representer:** {$representer->name} ({$representer->email})");
        }

        if ($topic->description) {
            $message->line("**Description:** " . substr($topic->description, 0, 200) . "...");
        }

        $message->action('View Topic', route('toolbox-topics.show', $topic))
            ->line('Please review this topic and ensure it meets HSE standards.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'topic_id' => $this->topic->id,
            'topic_title' => $this->topic->title,
            'category' => $this->topic->category,
            'representer_id' => $this->topic->representer_id,
        ];
    }
}
