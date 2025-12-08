<?php

namespace App\Notifications;

use App\Models\ControlMeasure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ControlMeasureVerificationRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ControlMeasure $controlMeasure
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $controlMeasure = $this->controlMeasure;
        
        $message = (new MailMessage)
            ->subject("Control Measure Verification Required: {$controlMeasure->reference_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A control measure requires your verification.")
            ->line("**Reference Number:** {$controlMeasure->reference_number}")
            ->line("**Title:** {$controlMeasure->title}")
            ->line("**Control Type:** " . ucfirst($controlMeasure->control_type))
            ->line("**Effectiveness:** " . ucfirst($controlMeasure->effectiveness ?? 'Not assessed'));

        if ($controlMeasure->riskAssessment) {
            $message->line("**Risk Assessment:** {$controlMeasure->riskAssessment->reference_number}");
        }

        if ($controlMeasure->hazard) {
            $message->line("**Hazard:** {$controlMeasure->hazard->title}");
        }

        if ($controlMeasure->assignedTo) {
            $message->line("**Assigned To:** {$controlMeasure->assignedTo->name}");
        }

        if ($controlMeasure->due_date) {
            $daysRemaining = now()->diffInDays($controlMeasure->due_date, false);
            if ($daysRemaining < 0) {
                $message->line("⚠️ **Status:** Verification overdue by " . abs($daysRemaining) . " days");
            } elseif ($daysRemaining <= 7) {
                $message->line("⚠️ **Status:** Verification due in {$daysRemaining} days - Urgent!");
            } else {
                $message->line("**Status:** Verification due in {$daysRemaining} days");
            }
        }

        $message->action('Verify Control Measure', route('risk-assessments.control-measures.show', $controlMeasure))
            ->line('Please verify the implementation and effectiveness of this control measure.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'control_measure_id' => $this->controlMeasure->id,
            'reference_number' => $this->controlMeasure->reference_number,
            'title' => $this->controlMeasure->title,
            'control_type' => $this->controlMeasure->control_type,
        ];
    }
}





