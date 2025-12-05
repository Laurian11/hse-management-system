<?php

namespace App\Notifications;

use App\Models\CAPA;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CAPAAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CAPA $capa
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $capa = $this->capa;
        
        $priorityColors = [
            'critical' => '#dc2626',
            'high' => '#ea580c',
            'medium' => '#ca8a04',
            'low' => '#16a34a',
        ];

        $message = (new MailMessage)
            ->subject("CAPA Assigned: {$capa->reference_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A Corrective and Preventive Action (CAPA) has been assigned to you.")
            ->line("**CAPA Reference:** {$capa->reference_number}")
            ->line("**Title:** {$capa->title}")
            ->line("**Type:** " . ucfirst($capa->capa_type))
            ->line("**Priority:** " . ucfirst($capa->priority))
            ->line("**Due Date:** " . ($capa->due_date ? $capa->due_date->format('F j, Y') : 'Not set'));

        if ($capa->incident) {
            $message->line("**Related Incident:** {$capa->incident->reference_number}");
        }

        if ($capa->description) {
            $message->line("**Description:** " . substr($capa->description, 0, 200) . (strlen($capa->description) > 200 ? '...' : ''));
        }

        if ($capa->assignedBy) {
            $message->line("**Assigned By:** {$capa->assignedBy->name}");
        }

        $daysRemaining = $capa->due_date ? now()->diffInDays($capa->due_date, false) : null;
        if ($daysRemaining !== null) {
            if ($daysRemaining < 0) {
                $message->line("⚠️ **Status:** Overdue by " . abs($daysRemaining) . " days");
            } elseif ($daysRemaining <= 7) {
                $message->line("⚠️ **Status:** Due in {$daysRemaining} days - Urgent!");
            } else {
                $message->line("**Status:** Due in {$daysRemaining} days");
            }
        }

        $message->action('View CAPA', route('capas.show', $capa))
            ->line('Please review and complete the assigned actions.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'capa_id' => $this->capa->id,
            'reference_number' => $this->capa->reference_number,
            'title' => $this->capa->title,
            'priority' => $this->capa->priority,
            'due_date' => $this->capa->due_date?->toDateString(),
        ];
    }
}

