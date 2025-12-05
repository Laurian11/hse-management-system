<?php

namespace App\Notifications;

use App\Models\PPEItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PPELowStockAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PPEItem $item
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $item = $this->item;
        $available = $item->available_quantity;
        $minimum = $item->minimum_stock_level;
        $shortage = $minimum - $available;

        $subject = "⚠️ Low Stock Alert: {$item->name}";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line("⚠️ **Low Stock Alert:** The following PPE item is below the minimum stock level and requires immediate attention.");

        $message->line("**PPE Item:** {$item->name}")
            ->line("**Reference Number:** {$item->reference_number}")
            ->line("**Category:** " . ucfirst($item->category))
            ->line("**Current Stock:** {$available} units")
            ->line("**Minimum Stock Level:** {$minimum} units")
            ->line("**Shortage:** {$shortage} units")
            ->line("**Total Quantity:** {$item->total_quantity} units")
            ->line("**Issued Quantity:** {$item->issued_quantity} units");

        if ($item->supplier) {
            $message->line("**Preferred Supplier:** {$item->supplier->name}")
                ->line("**Supplier Contact:** {$item->supplier->contact_email}");
        }

        if ($item->unit_cost) {
            $estimatedCost = $shortage * $item->unit_cost;
            $message->line("**Unit Cost:** {$item->currency} " . number_format($item->unit_cost, 2))
                ->line("**Estimated Reorder Cost:** {$item->currency} " . number_format($estimatedCost, 2));
        }

        $message->line("**Action Required:**")
            ->line("1. Review current stock levels")
            ->line("2. Place purchase order with supplier")
            ->line("3. Expedite delivery if critical")
            ->line("4. Update stock levels upon receipt");

        $message->action('View PPE Item', route('ppe.items.show', $item))
            ->line('Please take immediate action to prevent stockout.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'item_id' => $this->item->id,
            'item_name' => $this->item->name,
            'available_quantity' => $this->item->available_quantity,
            'minimum_stock_level' => $this->item->minimum_stock_level,
        ];
    }
}

