<?php

namespace App\Notifications;

use App\Models\RiskAssessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RiskAssessmentApprovalRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RiskAssessment $riskAssessment
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $riskAssessment = $this->riskAssessment;
        
        $message = (new MailMessage)
            ->subject("Risk Assessment Approval Required: {$riskAssessment->reference_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A risk assessment requires your approval.")
            ->line("**Reference Number:** {$riskAssessment->reference_number}")
            ->line("**Title:** {$riskAssessment->title}");

        if ($riskAssessment->hazard) {
            $message->line("**Hazard:** {$riskAssessment->hazard->title}");
        }

        if ($riskAssessment->department) {
            $message->line("**Department:** {$riskAssessment->department->name}");
        }

        if ($riskAssessment->creator) {
            $message->line("**Created By:** {$riskAssessment->creator->name}");
        }

        $message->line("**Status:** " . ucfirst(str_replace('_', ' ', $riskAssessment->status)))
            ->line("**Created At:** {$riskAssessment->created_at->format('F j, Y g:i A')}");

        $message->action('Review & Approve', route('risk-assessments.show', $riskAssessment))
            ->line('Please review the risk assessment and provide your approval.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'risk_assessment_id' => $this->riskAssessment->id,
            'reference_number' => $this->riskAssessment->reference_number,
            'title' => $this->riskAssessment->title,
            'status' => $this->riskAssessment->status,
        ];
    }
}

