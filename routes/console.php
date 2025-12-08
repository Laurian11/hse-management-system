<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\CertificateExpiryAlertService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Training & Competency Module Scheduled Tasks
Schedule::call(function () {
    app(CertificateExpiryAlertService::class)->checkAndSendAlerts();
})->daily()->at('08:00')->name('training.certificate-expiry-alerts');

Schedule::call(function () {
    app(CertificateExpiryAlertService::class)->revokeExpiredCertificates();
})->daily()->at('09:00')->name('training.revoke-expired-certificates');

// PPE Management Module Scheduled Tasks
Schedule::call(function () {
    $service = app(\App\Services\PPEAlertService::class);
    
    // Process alerts for all companies
    $companies = \App\Models\Company::all();
    foreach ($companies as $company) {
        $service->checkAndSendExpiryAlerts($company->id);
        $service->checkAndSendLowStockAlerts($company->id);
        $service->checkAndSendInspectionAlerts($company->id);
    }
    
    // Update expired issuances (all companies)
    $service->updateExpiredIssuances();
})->daily()->at('08:30')->name('ppe.alerts-and-updates');

// Biometric Attendance Processing - Automatic (Every 5 minutes)
Schedule::call(function () {
    $zkService = app(\App\Services\ZKTecoService::class);
    
    // Get all active toolbox talks happening now or recently
    $talks = \App\Models\ToolboxTalk::where('biometric_required', true)
        ->whereDate('scheduled_date', '<=', now())
        ->where(function($query) {
            // Talks that started and haven't ended yet, or ended within last hour
            $query->where(function($q) {
                $q->where('start_time', '<=', now())
                  ->where(function($sq) {
                      $sq->where('end_time', '>=', now())
                        ->orWhere('end_time', '>=', now()->subHour());
                  });
            });
        })
        ->get();
    
    foreach ($talks as $talk) {
        try {
            $results = $zkService->processToolboxTalkAttendance($talk);
            \Illuminate\Support\Facades\Log::info("Auto-processed attendance for talk {$talk->reference_number}: {$results['new_attendances']} new attendances");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error processing attendance for talk {$talk->reference_number}: " . $e->getMessage());
        }
    }
})->everyFiveMinutes()->name('biometric.auto-process-attendance');

// Auto-end talks and mark overdue (Every hour)
Schedule::command('toolbox-talks:process-status')->hourly()->name('toolbox-talks.process-status');

// Send day-before notifications (Daily at 9 AM)
Schedule::command('toolbox-talks:send-day-before-notifications')->dailyAt('09:00')->name('toolbox-talks.day-before-notifications');

// Risk Assessment Review Notifications (Daily at 8:00 AM)
Schedule::command('risk-assessment:send-review-notifications')->dailyAt('08:00')->name('risk-assessment.review-notifications');
