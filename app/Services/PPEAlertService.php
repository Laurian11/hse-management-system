<?php

namespace App\Services;

use App\Models\PPEIssuance;
use App\Models\PPEItem;
use App\Models\User;
use App\Notifications\PPEExpiryAlertNotification;
use App\Notifications\PPELowStockAlertNotification;
use App\Notifications\PPEInspectionAlertNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PPEAlertService
{
    /**
     * Check and send alerts for expiring PPE items
     */
    public function checkAndSendExpiryAlerts($companyId = null)
    {
        $query = PPEIssuance::where('status', 'active')
            ->whereNotNull('replacement_due_date')
            ->where('replacement_due_date', '<=', now()->addDays(7))
            ->where(function($q) {
                $q->where('replacement_alert_sent', false)
                  ->orWhereNull('replacement_alert_sent');
            })
            ->with(['ppeItem', 'issuedTo', 'department']);
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        $expiringIssuances = $query->get();

        foreach ($expiringIssuances as $issuance) {
            $this->sendExpiryAlert($issuance);
        }

        return count($expiringIssuances);
    }

    /**
     * Send expiry alert for a specific issuance
     */
    public function sendExpiryAlert(PPEIssuance $issuance)
    {
        // Mark as sent
        $issuance->update([
            'replacement_alert_sent' => true,
            'replacement_alert_sent_at' => now(),
        ]);

        // Log the alert
        $expiryDate = $issuance->expiry_date ? $issuance->expiry_date->format('Y-m-d') : 'N/A';
        Log::info("PPE Expiry Alert: {$issuance->ppeItem->name} for {$issuance->issuedTo->name} replacement due on {$expiryDate}");

        // Send email notification
        $issuance->issuedTo->notify(new PPEExpiryAlertNotification($issuance));
    }

    /**
     * Check and send alerts for low stock items
     */
    public function checkAndSendLowStockAlerts($companyId = null)
    {
        $query = PPEItem::lowStock()->active();
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $lowStockItems = $query->with('supplier')->get();

        foreach ($lowStockItems as $item) {
            $this->sendLowStockAlert($item);
        }

        return count($lowStockItems);
    }

    /**
     * Send low stock alert for a specific item
     */
    public function sendLowStockAlert(PPEItem $item)
    {
        // Log the alert
        Log::info("PPE Low Stock Alert: {$item->name} has only {$item->available_quantity} available (minimum: {$item->minimum_stock_level})");

        // Send email notification to procurement/HSE manager
        $hseManagers = User::where('company_id', $item->company_id)
            ->whereHas('role', function($q) {
                $q->where('name', 'like', '%hse%')
                  ->orWhere('name', 'like', '%safety%')
                  ->orWhere('name', 'like', '%manager%');
            })
            ->get();
        
        foreach ($hseManagers as $manager) {
            $manager->notify(new PPELowStockAlertNotification($item));
        }
    }

    /**
     * Check and send alerts for overdue inspections
     */
    public function checkAndSendInspectionAlerts($companyId = null)
    {
        $query = PPEIssuance::needsInspection()->active();
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        $overdueInspections = $query->with(['ppeItem', 'issuedTo'])->get();

        foreach ($overdueInspections as $issuance) {
            $this->sendInspectionAlert($issuance);
        }

        return count($overdueInspections);
    }

    /**
     * Send inspection alert for a specific issuance
     */
    public function sendInspectionAlert(PPEIssuance $issuance)
    {
        // Log the alert
        Log::info("PPE Inspection Alert: {$issuance->ppeItem->name} for {$issuance->issuedTo->name} needs inspection");

        // Send email notification
        $issuance->issuedTo->notify(new PPEInspectionAlertNotification($issuance));
    }

    /**
     * Auto-update expired issuances
     */
    public function updateExpiredIssuances($companyId = null)
    {
        $query = PPEIssuance::where('status', 'active')
            ->where(function($q) {
                $q->where(function($q2) {
                    $q2->whereNotNull('expiry_date')
                       ->where('expiry_date', '<', now());
                })->orWhere(function($q2) {
                    $q2->whereNotNull('replacement_due_date')
                       ->where('replacement_due_date', '<', now());
                });
            });
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        $expired = $query->update(['status' => 'expired']);

        return $expired;
    }
}

