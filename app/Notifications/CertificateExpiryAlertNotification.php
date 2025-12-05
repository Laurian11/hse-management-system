<?php

namespace App\Notifications;

use App\Models\TrainingCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateExpiryAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TrainingCertificate $certificate,
        public int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $certificate = $this->certificate;
        $user = $certificate->user;
        $daysText = $this->daysRemaining > 0 
            ? "in {$this->daysRemaining} days" 
            : "has expired";

        $subject = match(true) {
            $this->daysRemaining <= 0 => "URGENT: Certificate Expired - {$certificate->certificate_title}",
            $this->daysRemaining <= 7 => "URGENT: Certificate Expiring Soon - {$certificate->certificate_title}",
            $this->daysRemaining <= 30 => "Important: Certificate Expiring - {$certificate->certificate_title}",
            default => "Reminder: Certificate Expiring - {$certificate->certificate_title}",
        };

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},");

        if ($this->daysRemaining <= 0) {
            $message->line("⚠️ **URGENT:** The following certificate has **EXPIRED** and requires immediate action:");
        } elseif ($this->daysRemaining <= 7) {
            $message->line("⚠️ **URGENT:** The following certificate is expiring very soon:");
        } elseif ($this->daysRemaining <= 30) {
            $message->line("⚠️ **Important:** The following certificate is expiring soon:");
        } else {
            $message->line("This is a reminder that the following certificate is expiring:");
        }

        $message->line("**Certificate:** {$certificate->certificate_title}")
            ->line("**Certificate Number:** {$certificate->certificate_number}")
            ->line("**Holder:** {$user->name}")
            ->line("**Issued By:** {$certificate->issuing_organization}")
            ->line("**Issue Date:** {$certificate->issue_date->format('F j, Y')}")
            ->line("**Expiry Date:** {$certificate->expiry_date->format('F j, Y')}")
            ->line("**Days Remaining:** {$daysText}");

        if ($this->daysRemaining <= 0) {
            $message->line("**Status:** ❌ EXPIRED - Certificate is no longer valid")
                ->line("**Action Required:**")
                ->line("1. Complete refresher training immediately")
                ->line("2. Obtain new certificate")
                ->line("3. Update certificate in system")
                ->line("⚠️ Work restrictions may apply until certificate is renewed");
        } elseif ($this->daysRemaining <= 7) {
            $message->line("**Action Required:**")
                ->line("1. Schedule refresher training immediately")
                ->line("2. Apply for certificate renewal")
                ->line("3. Complete renewal before expiry date");
        } elseif ($this->daysRemaining <= 30) {
            $message->line("**Action Required:**")
                ->line("1. Plan for refresher training")
                ->line("2. Begin certificate renewal process")
                ->line("3. Ensure renewal is completed before expiry");
        } else {
            $message->line("**Action Required:**")
                ->line("1. Plan ahead for certificate renewal")
                ->line("2. Schedule refresher training if required");
        }

        if ($certificate->trainingRecord) {
            $message->action('View Training Record', route('training.records.show', $certificate->trainingRecord));
        }

        $message->line('Please take appropriate action to ensure compliance.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'certificate_id' => $this->certificate->id,
            'certificate_title' => $this->certificate->certificate_title,
            'days_remaining' => $this->daysRemaining,
            'expiry_date' => $this->certificate->expiry_date->format('Y-m-d'),
        ];
    }
}

