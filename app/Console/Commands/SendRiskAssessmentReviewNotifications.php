<?php

namespace App\Console\Commands;

use App\Models\RiskAssessment;
use App\Models\User;
use App\Notifications\RiskAssessmentReviewDueNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendRiskAssessmentReviewNotifications extends Command
{
    protected $signature = 'risk-assessment:send-review-notifications';
    protected $description = 'Send notifications for risk assessments due for review (day before and overdue)';

    public function handle()
    {
        $this->info('Checking for risk assessments due for review...');
        
        $notificationsSent = 0;
        
        // Get assessments due tomorrow
        $dueTomorrow = RiskAssessment::whereNotNull('next_review_date')
            ->whereDate('next_review_date', Carbon::tomorrow())
            ->where('is_active', true)
            ->with(['creator', 'assignedTo', 'department'])
            ->get();
        
        foreach ($dueTomorrow as $assessment) {
            $this->notifyUsers($assessment, 'day_before');
            $notificationsSent++;
        }
        
        // Get overdue assessments (past due date)
        $overdue = RiskAssessment::whereNotNull('next_review_date')
            ->where('next_review_date', '<', now())
            ->where('is_active', true)
            ->with(['creator', 'assignedTo', 'department'])
            ->get();
        
        foreach ($overdue as $assessment) {
            $this->notifyUsers($assessment, 'overdue');
            $notificationsSent++;
        }
        
        $this->info("Sent {$notificationsSent} review notification(s).");
        
        return Command::SUCCESS;
    }
    
    private function notifyUsers(RiskAssessment $assessment, string $type)
    {
        $notifyUsers = collect();
        
        // Notify creator
        if ($assessment->creator) {
            $notifyUsers->push($assessment->creator);
        }
        
        // Notify assigned user
        if ($assessment->assignedTo) {
            $notifyUsers->push($assessment->assignedTo);
        }
        
        // Notify HSE managers
        $hseManagers = User::where('company_id', $assessment->company_id)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['hse_manager', 'hse_officer', 'admin', 'super_admin']);
            })
            ->get();
        
        $notifyUsers = $notifyUsers->merge($hseManagers)->unique('id');
        
        foreach ($notifyUsers as $user) {
            $user->notify(new RiskAssessmentReviewDueNotification($assessment, $type));
        }
    }
}

