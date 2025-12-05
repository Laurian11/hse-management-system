<?php

namespace App\Notifications;

use App\Models\PPEIssuance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PPEExpiryAlertNotification extends Notification implements ShouldQueue
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
        $expiryDate = $issuance->expiry_date ? $issuance->expiry_date->format('F j, Y') : 'N/A';
        $replacementDate = $issuance->replacement_due_date ? $issuance->replacement_due_date->format('F j, Y') : 'N/A';
        
        $daysUntilExpiry = $issuance->expiry_date 
            ? now()->diffInDays($issuance->expiry_date, false) 
            : null;

        $subject = "PPE Replacement Due: {$item->name}";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line("This is a reminder that your Personal Protective Equipment (PPE) requires replacement soon.");

        $message->line("**PPE Item:** {$item->name}")
            ->line("**Category:** " . ucfirst($item->category))
            ->line("**Issue Date:** {$issuance->issue_date->format('F j, Y')}");

        if ($issuance->expiry_date) {
            $message->line("**Expiry Date:** {$expiryDate}");
            if ($daysUntilExpiry !== null) {
                if ($daysUntilExpiry <= 0) {
                    $message->line("**Status:** ❌ EXPIRED - Replacement required immediately");
                } elseif ($daysUntilExpiry <= 7) {
                    $message->line("**Status:** ⚠️ Expiring in {$daysUntilExpiry} days");
                } else {
                    $message->line("**Days Until Expiry:** {$daysUntilExpiry} days");
                }
            }
        }

        if ($issuance->replacement_due_date) {
            $message->line("**Replacement Due Date:** {$replacementDate}");
        }

        if ($issuance->department) {
            $message->line("**Department:** {$issuance->department->name}");
        }

        $message->line("**Action Required:**")
            ->line("1. Return expired/damaged PPE to safety department")
            ->line("2. Request replacement PPE")
            ->line("3. Ensure new PPE is properly fitted and inspected");

        $message->action('View PPE Issuance', route('ppe.issuances.show', $issuance))
            ->line('Please ensure you have adequate PPE before the expiry date.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'issuance_id' => $this->issuance->id,
            'ppe_item_name' => $this->issuance->ppeItem->name,
            'expiry_date' => $this->issuance->expiry_date?->format('Y-m-d'),
            'replacement_due_date' => $this->issuance->replacement_due_date?->format('Y-m-d'),
        ];
    }
}

