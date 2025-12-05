<?php

namespace App\Services;

use App\Models\TrainingCertificate;
use App\Models\User;
use App\Notifications\CertificateExpiryAlertNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CertificateExpiryAlertService
{
    protected $tnaEngine;

    public function __construct(TNAEngineService $tnaEngine)
    {
        $this->tnaEngine = $tnaEngine;
    }

    /**
     * Check and send expiry alerts for certificates
     */
    public function checkAndSendAlerts(): array
    {
        $alertsSent = [
            '60_days' => 0,
            '30_days' => 0,
            '7_days' => 0,
            'expired' => 0,
        ];

        // Check 60 days expiry
        $certificates60Days = TrainingCertificate::needsAlert(60)->get();
        foreach ($certificates60Days as $certificate) {
            $this->sendExpiryAlert($certificate, 60);
            $certificate->sendExpiryAlert(60);
            $alertsSent['60_days']++;
        }

        // Check 30 days expiry
        $certificates30Days = TrainingCertificate::needsAlert(30)->get();
        foreach ($certificates30Days as $certificate) {
            $this->sendExpiryAlert($certificate, 30);
            $certificate->sendExpiryAlert(30);
            $alertsSent['30_days']++;
        }

        // Check 7 days expiry
        $certificates7Days = TrainingCertificate::needsAlert(7)->get();
        foreach ($certificates7Days as $certificate) {
            $this->sendExpiryAlert($certificate, 7);
            $certificate->sendExpiryAlert(7);
            $alertsSent['7_days']++;
        }

        // Mark expired certificates
        $expiredCertificates = TrainingCertificate::active()
            ->where('has_expiry', true)
            ->where('expiry_date', '<', now())
            ->get();

        foreach ($expiredCertificates as $certificate) {
            $certificate->markExpired();
            $this->sendExpiryAlert($certificate, 0); // Expired
            $alertsSent['expired']++;
        }

        Log::info("Certificate Expiry Alert Service: Processed alerts", $alertsSent);

        return $alertsSent;
    }

    /**
     * Send expiry alert to user, supervisor, and HSE manager
     */
    protected function sendExpiryAlert(TrainingCertificate $certificate, int $daysRemaining): void
    {
        $user = $certificate->user;
        $daysText = $daysRemaining > 0 ? "in {$daysRemaining} days" : "has expired";

        // Alert to user
        $this->sendUserAlert($user, $certificate, $daysRemaining);

        // Alert to supervisor (if exists)
        if ($user->directSupervisor) {
            $this->sendSupervisorAlert($user->directSupervisor, $user, $certificate, $daysRemaining);
        }

        // Alert to HSE manager (if 30 days or less)
        if ($daysRemaining <= 30) {
            $hseManagers = User::whereHas('role', function($q) {
                $q->where('name', 'like', '%HSE%')
                  ->orWhere('name', 'like', '%Safety%');
            })->where('company_id', $certificate->company_id)->get();

            foreach ($hseManagers as $hseManager) {
                $this->sendHSEAlert($hseManager, $user, $certificate, $daysRemaining);
            }
        }

        // If expired, trigger refresher training need
        if ($daysRemaining <= 0) {
            $this->tnaEngine->processCertificateExpiryTrigger($certificate);
        }
    }

    /**
     * Send alert to certificate holder
     */
    protected function sendUserAlert(User $user, TrainingCertificate $certificate, int $daysRemaining): void
    {
        Log::info("Certificate Expiry Alert: User", [
            'user_id' => $user->id,
            'certificate_id' => $certificate->id,
            'days_remaining' => $daysRemaining,
        ]);

        // Send email notification
        $user->notify(new CertificateExpiryAlertNotification($certificate, $daysRemaining));
    }

    /**
     * Send alert to supervisor
     */
    protected function sendSupervisorAlert(User $supervisor, User $employee, TrainingCertificate $certificate, int $daysRemaining): void
    {
        Log::info("Certificate Expiry Alert: Supervisor", [
            'supervisor_id' => $supervisor->id,
            'employee_id' => $employee->id,
            'certificate_id' => $certificate->id,
            'days_remaining' => $daysRemaining,
        ]);

        // Send email notification to supervisor
        $supervisor->notify(new CertificateExpiryAlertNotification($certificate, $daysRemaining));
    }

    /**
     * Send alert to HSE manager
     */
    protected function sendHSEAlert(User $hseManager, User $employee, TrainingCertificate $certificate, int $daysRemaining): void
    {
        Log::info("Certificate Expiry Alert: HSE Manager", [
            'hse_manager_id' => $hseManager->id,
            'employee_id' => $employee->id,
            'certificate_id' => $certificate->id,
            'days_remaining' => $daysRemaining,
        ]);

        // Send email notification to HSE manager
        $hseManager->notify(new CertificateExpiryAlertNotification($certificate, $daysRemaining));
    }

    /**
     * Auto-revoke expired certificates and restrict work permissions
     */
    public function revokeExpiredCertificates(): int
    {
        $expiredCount = 0;

        $expiredCertificates = TrainingCertificate::where('status', 'active')
            ->where('has_expiry', true)
            ->where('expiry_date', '<', now())
            ->get();

        foreach ($expiredCertificates as $certificate) {
            $certificate->markExpired();
            $expiredCount++;

            // In a real system, this would trigger work restriction in Permit to Work system
            Log::info("Certificate Auto-Revoked: Work restrictions may apply", [
                'certificate_id' => $certificate->id,
                'user_id' => $certificate->user_id,
            ]);
        }

        return $expiredCount;
    }
}
