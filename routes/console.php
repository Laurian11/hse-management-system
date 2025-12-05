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
