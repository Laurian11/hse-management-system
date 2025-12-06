<?php

namespace App\Observers;

use App\Models\ProcurementRequest;
use App\Models\PPEItem;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProcurementRequestNotification;
use Carbon\Carbon;

class ProcurementRequestObserver
{
    /**
     * Handle the ProcurementRequest "created" event.
     */
    public function created(ProcurementRequest $procurementRequest): void
    {
        // Log activity
        ActivityLog::log(
            'create',
            'procurement',
            'ProcurementRequest',
            $procurementRequest->id,
            "Procurement request {$procurementRequest->reference_number} created"
        );

        // Send notification if configured
        if (config('procurement.auto_send_notifications') && config('procurement.notify_on.created')) {
            if ($procurementRequest->status !== 'draft') {
                $this->sendNotification($procurementRequest, 'created');
            }
        }
    }

    /**
     * Handle the ProcurementRequest "updated" event.
     */
    public function updated(ProcurementRequest $procurementRequest): void
    {
        $changes = $procurementRequest->getChanges();
        $original = $procurementRequest->getOriginal();

        // Log activity
        ActivityLog::log(
            'update',
            'procurement',
            'ProcurementRequest',
            $procurementRequest->id,
            "Procurement request {$procurementRequest->reference_number} updated"
        );

        // Auto-create PPE items when status changes to "received"
        if (isset($changes['status']) && $changes['status'] === 'received' && $original['status'] !== 'received') {
            $this->createPPEItemsFromProcurement($procurementRequest);
        }

        // Check if status changed to 'submitted'
        if (isset($changes['status']) && $changes['status'] === 'submitted' && $original['status'] !== 'submitted') {
            if (config('procurement.auto_send_notifications') && config('procurement.notify_on.submitted')) {
                $this->sendNotification($procurementRequest, 'submitted');
            }
        }

        // Check for overdue requests
        if ($procurementRequest->required_date && $procurementRequest->required_date->isPast() && 
            in_array($procurementRequest->status, ['submitted', 'under_review', 'approved'])) {
            $this->sendNotification($procurementRequest, 'overdue');
        }

        // Check if status changed and update notifications are enabled
        if (isset($changes['status']) && config('procurement.auto_send_notifications') && config('procurement.notify_on.updated')) {
            if ($original['status'] !== $changes['status']) {
                $this->sendNotification($procurementRequest, 'updated');
            }
        }

        // Send notification to requester on status change
        if (isset($changes['status']) && $procurementRequest->requestedBy) {
            $this->notifyRequester($procurementRequest, $original['status'], $changes['status']);
        }
    }

    /**
     * Handle the ProcurementRequest "deleted" event.
     */
    public function deleted(ProcurementRequest $procurementRequest): void
    {
        ActivityLog::log(
            'delete',
            'procurement',
            'ProcurementRequest',
            $procurementRequest->id,
            "Procurement request {$procurementRequest->reference_number} deleted"
        );
    }

    /**
     * Automatically create PPE items when procurement request is received
     */
    private function createPPEItemsFromProcurement(ProcurementRequest $procurementRequest): void
    {
        // Only create PPE items if category is PPE or safety_equipment
        if (!in_array($procurementRequest->item_category, ['ppe', 'safety_equipment'])) {
            return;
        }

        // Check if PPE item already exists with same name
        $existingItem = PPEItem::forCompany($procurementRequest->company_id)
            ->where('name', $procurementRequest->item_name)
            ->first();

        if ($existingItem) {
            // Update existing item stock
            $existingItem->total_quantity += $procurementRequest->quantity;
            $existingItem->available_quantity += $procurementRequest->quantity;
            $existingItem->last_purchase_date = $procurementRequest->received_date ?? now();
            $existingItem->last_purchase_quantity = $procurementRequest->quantity;
            if ($procurementRequest->purchase_cost) {
                $existingItem->unit_cost = $procurementRequest->purchase_cost / $procurementRequest->quantity;
                $existingItem->currency = $procurementRequest->currency ?? 'TZS';
            }
            if ($procurementRequest->supplier_id) {
                $existingItem->supplier_id = $procurementRequest->supplier_id;
            }
            $existingItem->save();

            ActivityLog::log(
                'update',
                'ppe',
                'PPEItem',
                $existingItem->id,
                "Updated stock from procurement request {$procurementRequest->reference_number}: +{$procurementRequest->quantity}"
            );
        } else {
            // Create new PPE item
            $ppeItem = PPEItem::create([
                'company_id' => $procurementRequest->company_id,
                'name' => $procurementRequest->item_name,
                'category' => $procurementRequest->item_category === 'ppe' ? 'ppe' : 'safety_equipment',
                'description' => $procurementRequest->description,
                'total_quantity' => $procurementRequest->quantity,
                'available_quantity' => $procurementRequest->quantity,
                'issued_quantity' => 0,
                'reserved_quantity' => 0,
                'minimum_stock_level' => max(1, (int)($procurementRequest->quantity * 0.2)), // 20% of received quantity
                'unit_of_measure' => $procurementRequest->unit ?? 'pieces',
                'supplier_id' => $procurementRequest->supplier_id,
                'unit_cost' => $procurementRequest->purchase_cost ? ($procurementRequest->purchase_cost / $procurementRequest->quantity) : null,
                'currency' => $procurementRequest->currency ?? 'TZS',
                'last_purchase_date' => $procurementRequest->received_date ?? now(),
                'last_purchase_quantity' => $procurementRequest->quantity,
                'status' => 'active',
                'notes' => "Auto-created from procurement request: {$procurementRequest->reference_number}",
            ]);

            ActivityLog::log(
                'create',
                'ppe',
                'PPEItem',
                $ppeItem->id,
                "Created PPE item from procurement request {$procurementRequest->reference_number}"
            );
        }
    }

    /**
     * Send notification to procurement emails
     */
    private function sendNotification(ProcurementRequest $procurementRequest, string $action): void
    {
        $emails = config('procurement.notification_emails');
        
        if (empty($emails)) {
            return;
        }

        $emailList = is_array($emails) ? $emails : array_map('trim', explode(',', $emails));
        $emailList = array_filter($emailList, fn($email) => !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL));

        if (empty($emailList)) {
            return;
        }

        foreach ($emailList as $email) {
            Notification::route('mail', $email)
                ->notify(new ProcurementRequestNotification($procurementRequest, $action));
        }
    }

    /**
     * Notify requester about status changes
     */
    private function notifyRequester(ProcurementRequest $procurementRequest, string $oldStatus, string $newStatus): void
    {
        if (!$procurementRequest->requestedBy || !$procurementRequest->requestedBy->email) {
            return;
        }

        // Only notify on significant status changes
        $significantChanges = [
            'draft' => ['submitted', 'approved', 'rejected'],
            'submitted' => ['under_review', 'approved', 'rejected'],
            'under_review' => ['approved', 'rejected'],
            'approved' => ['purchased', 'received'],
            'purchased' => ['received'],
        ];

        if (isset($significantChanges[$oldStatus]) && in_array($newStatus, $significantChanges[$oldStatus])) {
            Notification::route('mail', $procurementRequest->requestedBy->email)
                ->notify(new ProcurementRequestNotification($procurementRequest, 'status_changed'));
        }
    }
}

