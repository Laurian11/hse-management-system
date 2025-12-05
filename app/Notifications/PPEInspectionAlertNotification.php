<?php

namespace App\Notifications;

use App\Models\PPEIssuance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PPEInspectionAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PPEIssuance $issuance
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $issuance = $this->issuance;
        $item = $issuance->ppeItem;
        $lastInspection = $issuance->last_inspection_date 
            ? $issuance->last_inspection_date->format('F j, Y') 
            : 'Never';
        
        $nextInspection = $issuance->next_inspection_date 
            ? $issuance->next_inspection_date->format('F j, Y') 
            : 'Not scheduled';
        
        $inspectionFrequency = $item->inspection_frequency_days ?? 30;
        $daysOverdue = $issuance->next_inspection_date 
            ? now()->diffInDays($issuance->next_inspection_date, false) 
            : null;

        $subject = "PPE Inspection Required: {$item->name}";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder that your Personal Protective Equipment (PPE) requires inspection.");

        if ($daysOverdue !== null && $daysOverdue < 0) {
            $message->line("⚠️ **OVERDUE:** This inspection is overdue by " . abs($daysOverdue) . " days.");
        } else {
            $message->line("⚠️ **Inspection Required:** Please schedule an inspection for your PPE.");
        }

        $message->line("**PPE Item:** {$item->name}")
            ->line("**Category:** " . ucfirst($item->category))
            ->line("**Issue Date:** {$issuance->issue_date->format('F j, Y')}")
            ->line("**Last Inspection:** {$lastInspection}")
            ->line("**Next Inspection Due:** {$nextInspection}")
            ->line("**Inspection Frequency:** Every {$inspectionFrequency} days");

        if ($issuance->department) {
            $message->line("**Department:** {$issuance->department->name}");
        }

        $message->line("**Action Required:**")
            ->line("1. Schedule PPE inspection with safety department")
            ->line("2. Bring PPE item for visual and functional inspection")
            ->line("3. Report any defects or damage immediately")
            ->line("4. Ensure PPE is in good working condition before use");

        $message->action('Schedule Inspection', route('ppe.inspections.create', ['ppe_issuance_id' => $issuance->id]))
            ->line('Regular inspections ensure your safety and compliance with HSE standards.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'issuance_id' => $this->issuance->id,
            'ppe_item_name' => $this->issuance->ppeItem->name,
            'last_inspection_date' => $this->issuance->last_inspection_date?->format('Y-m-d'),
            'next_inspection_date' => $this->issuance->next_inspection_date?->format('Y-m-d'),
        ];
    }
}

