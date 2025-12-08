<?php

namespace App\Notifications;

use App\Models\RiskAssessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RiskAssessmentStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RiskAssessment $riskAssessment,
        public string $oldStatus,
        public string $newStatus,
        public ?string $changedBy = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $assessment = $this->riskAssessment;
        
        $message = (new MailMessage)
            ->subject("Risk Assessment Status Changed: {$assessment->reference_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The status of risk assessment {$assessment->reference_number} has been changed.")
            ->line("**Previous Status:** " . ucfirst(str_replace('_', ' ', $this->oldStatus)))
            ->line("**New Status:** " . ucfirst(str_replace('_', ' ', $this->newStatus)))
            ->line("**Reference:** {$assessment->reference_number}")
            ->line("**Title:** {$assessment->title}")
            ->line("**Risk Level:** " . ucfirst($assessment->risk_level))
            ->line("**Risk Score:** {$assessment->risk_score}");

        if ($this->changedBy) {
            $message->line("**Changed By:** {$this->changedBy}");
        }

        $message->action('View Risk Assessment', route('risk-assessment.risk-assessments.show', $assessment))
            ->line('Please review the updated risk assessment status.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'risk_assessment_id' => $this->riskAssessment->id,
            'reference_number' => $this->riskAssessment->reference_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}

