<?php

namespace App\Observers;

use App\Models\ProcurementRequest;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProcurementRequestNotification;

class ProcurementRequestObserver
{
    /**
     * Handle the ProcurementRequest "created" event.
     */
    public function created(ProcurementRequest $procurementRequest): void
    {
        // Log activity
        ActivityLog::log(
            $procurementRequest->company_id,
            'procurement_request_created',
            "Procurement request {$procurementRequest->reference_number} created",
            $procurementRequest->id,
            'App\Models\ProcurementRequest'
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
            $procurementRequest->company_id,
            'procurement_request_updated',
            "Procurement request {$procurementRequest->reference_number} updated",
            $procurementRequest->id,
            'App\Models\ProcurementRequest'
        );

        // Check if status changed to 'submitted'
        if (isset($changes['status']) && $changes['status'] === 'submitted' && $original['status'] !== 'submitted') {
            if (config('procurement.auto_send_notifications') && config('procurement.notify_on.submitted')) {
                $this->sendNotification($procurementRequest, 'submitted');
            }
        }

        // Check if status changed and update notifications are enabled
        if (isset($changes['status']) && config('procurement.auto_send_notifications') && config('procurement.notify_on.updated')) {
            if ($original['status'] !== $changes['status']) {
                $this->sendNotification($procurementRequest, 'updated');
            }
        }
    }

    /**
     * Handle the ProcurementRequest "deleted" event.
     */
    public function deleted(ProcurementRequest $procurementRequest): void
    {
        ActivityLog::log(
            $procurementRequest->company_id,
            'procurement_request_deleted',
            "Procurement request {$procurementRequest->reference_number} deleted",
            $procurementRequest->id,
            'App\Models\ProcurementRequest'
        );
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
}

