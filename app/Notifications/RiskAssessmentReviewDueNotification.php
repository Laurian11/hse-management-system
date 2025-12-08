<?php

namespace App\Notifications;

use App\Models\RiskAssessment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RiskAssessmentReviewDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RiskAssessment $riskAssessment,
        public string $type = 'due' // 'due', 'overdue', 'day_before'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $assessment = $this->riskAssessment;
        
        $subject = match($this->type) {
            'overdue' => "Risk Assessment Review Overdue: {$assessment->reference_number}",
            'day_before' => "Risk Assessment Review Due Tomorrow: {$assessment->reference_number}",
            default => "Risk Assessment Review Due: {$assessment->reference_number}",
        };
        
        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},");
        
        if ($this->type === 'overdue') {
            $daysOverdue = now()->diffInDays($assessment->next_review_date);
            $message->line("The risk assessment {$assessment->reference_number} is **{$daysOverdue} day(s) overdue** for review.");
        } elseif ($this->type === 'day_before') {
            $message->line("The risk assessment {$assessment->reference_number} is **due for review tomorrow**.");
        } else {
            $message->line("The risk assessment {$assessment->reference_number} is **due for review**.");
        }
        
        $message->line("**Reference:** {$assessment->reference_number}")
            ->line("**Title:** {$assessment->title}")
            ->line("**Risk Level:** " . ucfirst($assessment->risk_level))
            ->line("**Next Review Date:** " . ($assessment->next_review_date ? $assessment->next_review_date->format('F j, Y') : 'N/A'))
            ->line("**Review Frequency:** " . ucfirst(str_replace('_', ' ', $assessment->review_frequency ?? 'N/A')));

        $message->action('Review Risk Assessment', route('risk-assessment.risk-assessments.show', $assessment))
            ->line('Please schedule and complete the review as soon as possible.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'risk_assessment_id' => $this->riskAssessment->id,
            'reference_number' => $this->riskAssessment->reference_number,
            'type' => $this->type,
            'next_review_date' => $this->riskAssessment->next_review_date?->toDateString(),
        ];
    }
}

