<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ToolboxTalkController;
use App\Http\Controllers\ToolboxTalkTopicController;
use App\Http\Controllers\SafetyCommunicationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentInvestigationController;
use App\Http\Controllers\RootCauseAnalysisController;
use App\Http\Controllers\CAPAController;
use App\Http\Controllers\IncidentAttachmentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HazardController;
use App\Http\Controllers\RiskAssessmentController;
use App\Http\Controllers\JSAController;
use App\Http\Controllers\ControlMeasureController;
use App\Http\Controllers\RiskReviewController;
use App\Http\Controllers\RiskAssessmentDashboardController;
use App\Http\Controllers\TrainingNeedsAnalysisController;
use App\Http\Controllers\TrainingPlanController;
use App\Http\Controllers\TrainingSessionController;
use App\Http\Controllers\TrainingDashboardController;
use App\Http\Controllers\TrainingCertificateController;
use App\Http\Controllers\TrainingReportingController;
use App\Http\Controllers\PPEController;
use App\Http\Controllers\PPEItemController;
use App\Http\Controllers\PPEIssuanceController;
use App\Http\Controllers\PPEInspectionController;
use App\Http\Controllers\PPESupplierController;
use App\Http\Controllers\PPEComplianceReportController;
use App\Http\Controllers\WorkPermitController;
use App\Http\Controllers\WorkPermitTypeController;
use App\Http\Controllers\WorkPermitDashboardController;
use App\Http\Controllers\GCALogController;
use App\Http\Controllers\InspectionDashboardController;
use App\Http\Controllers\InspectionScheduleController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectionChecklistController;
use App\Http\Controllers\NonConformanceReportController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AuditFindingController;
use App\Http\Controllers\EmergencyPreparednessDashboardController;
use App\Http\Controllers\FireDrillController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\EmergencyEquipmentController;
use App\Http\Controllers\EvacuationPlanController;
use App\Http\Controllers\EmergencyResponseTeamController;
use App\Http\Controllers\EnvironmentalDashboardController;
use App\Http\Controllers\WasteManagementRecordController;
use App\Http\Controllers\WasteTrackingRecordController;
use App\Http\Controllers\EmissionMonitoringRecordController;
use App\Http\Controllers\SpillIncidentController;
use App\Http\Controllers\ResourceUsageRecordController;
use App\Http\Controllers\ISO14001ComplianceRecordController;
use App\Http\Controllers\HealthWellnessDashboardController;
use App\Http\Controllers\HealthSurveillanceRecordController;
use App\Http\Controllers\FirstAidLogbookEntryController;
use App\Http\Controllers\ErgonomicAssessmentController;
use App\Http\Controllers\WorkplaceHygieneInspectionController;
use App\Http\Controllers\HealthCampaignController;
use App\Http\Controllers\SickLeaveRecordController;
use App\Http\Controllers\ProcurementDashboardController;
use App\Http\Controllers\ProcurementRequestController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\EquipmentCertificationController;
use App\Http\Controllers\StockConsumptionReportController;
use App\Http\Controllers\SafetyMaterialGapAnalysisController;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DocumentManagementDashboardController;
use App\Http\Controllers\HSEDocumentController;
use App\Http\Controllers\DocumentVersionController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\ComplianceDashboardController;
use App\Http\Controllers\ComplianceRequirementController;
use App\Http\Controllers\PermitLicenseController;
use App\Http\Controllers\ComplianceAuditController;
use App\Http\Controllers\HousekeepingDashboardController;
use App\Http\Controllers\HousekeepingInspectionController;
use App\Http\Controllers\FiveSAuditController;
use App\Http\Controllers\WasteSustainabilityDashboardController;
use App\Http\Controllers\WasteSustainabilityRecordController;
use App\Http\Controllers\CarbonFootprintRecordController;
use App\Http\Controllers\NotificationRuleController;
use App\Http\Controllers\EscalationMatrixController;
use App\Http\Controllers\BiometricDeviceController;
use App\Http\Controllers\DailyAttendanceController;
use App\Http\Controllers\ManpowerReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Global Search API
    Route::get('/api/search', [SearchController::class, 'search'])->name('api.search');
    
    // Recent Items
    Route::post('/api/recent-items/track', [\App\Http\Controllers\RecentItemsController::class, 'track'])->name('recent-items.track');
    Route::post('/api/recent-items/clear', [\App\Http\Controllers\RecentItemsController::class, 'clear'])->name('recent-items.clear');
    
    // Email Sharing
    Route::post('/email/share', [\App\Http\Controllers\EmailShareController::class, 'share'])->name('email.share');
    
    // Employee Search API for autocomplete
    Route::get('/api/employees/search', [\App\Http\Controllers\EmployeeController::class, 'search'])->name('api.employees.search');
});

Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::post('/report-incident', [LandingPageController::class, 'reportIncident'])->name('incident.report');
Route::get('/company/{id}/dashboard', [LandingPageController::class, 'companyDashboard'])->name('company.dashboard');

// Toolbox Talk Routes
Route::prefix('toolbox-talks')->name('toolbox-talks.')->group(function () {
    // Static routes (must come before parameterized routes)
    Route::get('/', [ToolboxTalkController::class, 'index'])->middleware('permission:toolbox_talks.view')->name('index');
    Route::get('/schedule', [ToolboxTalkController::class, 'schedule'])->middleware('permission:toolbox_talks.view')->name('schedule');
    Route::get('/dashboard', [ToolboxTalkController::class, 'dashboard'])->middleware('permission:toolbox_talks.view')->name('dashboard');
    Route::get('/create', [ToolboxTalkController::class, 'create'])->middleware('permission:toolbox_talks.create')->name('create');
    Route::get('/attendance', [ToolboxTalkController::class, 'attendance'])->middleware('permission:toolbox_talks.view')->name('attendance');
    Route::get('/feedback', [ToolboxTalkController::class, 'feedback'])->middleware('permission:toolbox_talks.view')->name('feedback');
    Route::post('/{toolboxTalk}/feedback', [ToolboxTalkController::class, 'submitFeedback'])->middleware('permission:toolbox_talks.write')->name('submit-feedback');
    Route::get('/{toolboxTalk}/feedback', [ToolboxTalkController::class, 'viewFeedback'])->middleware('permission:toolbox_talks.view')->name('view-feedback');
    Route::get('/reporting', [ToolboxTalkController::class, 'reporting'])->middleware('permission:toolbox_talks.view')->name('reporting');
    Route::get('/calendar', [ToolboxTalkController::class, 'calendar'])->middleware('permission:toolbox_talks.view')->name('calendar');
    Route::post('/', [ToolboxTalkController::class, 'store'])->middleware('permission:toolbox_talks.create')->name('store');
    Route::post('/bulk-import', [ToolboxTalkController::class, 'bulkImport'])->middleware('permission:toolbox_talks.create')->name('bulk-import');
    Route::get('/bulk-import/template', [ToolboxTalkController::class, 'downloadTemplate'])->middleware('permission:toolbox_talks.create')->name('bulk-import-template');
    
    // Parameterized routes (must come after static routes)
    Route::get('/{toolboxTalk}', [ToolboxTalkController::class, 'show'])->middleware('permission:toolbox_talks.view')->name('show');
    Route::get('/{toolboxTalk}/edit', [ToolboxTalkController::class, 'edit'])->middleware('permission:toolbox_talks.edit')->name('edit');
    Route::put('/{toolboxTalk}', [ToolboxTalkController::class, 'update'])->middleware('permission:toolbox_talks.edit')->name('update');
    Route::delete('/{toolboxTalk}', [ToolboxTalkController::class, 'destroy'])->middleware('permission:toolbox_talks.delete')->name('destroy');
    
    // Specialized routes for specific talks
    Route::post('/{toolboxTalk}/start', [ToolboxTalkController::class, 'startTalk'])->middleware('permission:toolbox_talks.write')->name('start');
    Route::post('/{toolboxTalk}/complete', [ToolboxTalkController::class, 'completeTalk'])->middleware('permission:toolbox_talks.write')->name('complete');
    Route::post('/{toolboxTalk}/mark-attendance', [ToolboxTalkController::class, 'markAttendance'])->middleware('permission:toolbox_talks.write')->name('mark-attendance');
    Route::post('/{toolboxTalk}/sync-biometric', [ToolboxTalkController::class, 'syncBiometricAttendance'])->middleware('permission:toolbox_talks.write')->name('sync-biometric');
    Route::post('/{toolboxTalk}/generate-next', [ToolboxTalkController::class, 'generateNextOccurrence'])->middleware('permission:toolbox_talks.write')->name('generate-next');
    Route::post('/{toolboxTalk}/reschedule', [ToolboxTalkController::class, 'reschedule'])->middleware('permission:toolbox_talks.edit')->name('reschedule');
    Route::get('/{toolboxTalk}/action-items', [ToolboxTalkController::class, 'actionItems'])->middleware('permission:toolbox_talks.view')->name('action-items');
    Route::post('/{toolboxTalk}/action-items', [ToolboxTalkController::class, 'saveActionItems'])->middleware('permission:toolbox_talks.write')->name('save-action-items');
    Route::get('/{toolboxTalk}/attendance', [ToolboxTalkController::class, 'attendanceManagement'])->middleware('permission:toolbox_talks.view')->name('attendance-management');
    
    // Export routes
    Route::get('/{toolboxTalk}/export/attendance-pdf', [ToolboxTalkController::class, 'exportAttendancePDF'])->middleware('permission:toolbox_talks.print')->name('export-attendance-pdf');
    Route::get('/{toolboxTalk}/export/attendance-excel', [ToolboxTalkController::class, 'exportAttendanceExcel'])->middleware('permission:toolbox_talks.export')->name('export-attendance-excel');
    Route::get('/export/reporting-excel', [ToolboxTalkController::class, 'exportReportingExcel'])->middleware('permission:toolbox_talks.export')->name('export-reporting-excel');
});

// Toolbox Talk Reports Routes
Route::prefix('toolbox-talks/reports')->name('toolbox-talks.reports.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\ToolboxTalkReportController::class, 'index'])->name('index');
    Route::get('/department-attendance', [\App\Http\Controllers\ToolboxTalkReportController::class, 'departmentAttendance'])->name('department-attendance');
    Route::get('/employee-attendance', [\App\Http\Controllers\ToolboxTalkReportController::class, 'employeeAttendance'])->name('employee-attendance');
    Route::get('/period', [\App\Http\Controllers\ToolboxTalkReportController::class, 'periodReport'])->name('period');
    Route::get('/companies', [\App\Http\Controllers\ToolboxTalkReportController::class, 'companiesReport'])->name('companies');
});

// Toolbox Talk Templates Routes
Route::prefix('toolbox-talks/templates')->name('toolbox-talks.templates.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\ToolboxTalkTemplateController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\ToolboxTalkTemplateController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\ToolboxTalkTemplateController::class, 'store'])->name('store');
    Route::get('/{template}', [\App\Http\Controllers\ToolboxTalkTemplateController::class, 'show'])->name('show');
    Route::get('/{template}/json', [\App\Http\Controllers\ToolboxTalkTemplateController::class, 'json'])->name('json');
    Route::get('/{template}/edit', [\App\Http\Controllers\ToolboxTalkTemplateController::class, 'edit'])->name('edit');
    Route::put('/{template}', [\App\Http\Controllers\ToolboxTalkTemplateController::class, 'update'])->name('update');
    Route::delete('/{template}', [\App\Http\Controllers\ToolboxTalkTemplateController::class, 'destroy'])->name('destroy');
});

// Toolbox Talk Topics Routes
Route::prefix('toolbox-topics')->name('toolbox-topics.')->group(function () {
    Route::get('/', [ToolboxTalkTopicController::class, 'index'])->name('index');
    Route::get('/library', [ToolboxTalkTopicController::class, 'library'])->name('library');
    Route::get('/create', [ToolboxTalkTopicController::class, 'create'])->name('create');
    Route::post('/', [ToolboxTalkTopicController::class, 'store'])->name('store');
    Route::post('/bulk-import', [ToolboxTalkTopicController::class, 'bulkImport'])->name('bulk-import');
    Route::get('/bulk-import/template', [ToolboxTalkTopicController::class, 'downloadImportTemplate'])->name('bulk-import-template');
    Route::get('/{topic}', [ToolboxTalkTopicController::class, 'show'])->name('show');
    Route::get('/{topic}/edit', [ToolboxTalkTopicController::class, 'edit'])->name('edit');
    Route::put('/{topic}', [ToolboxTalkTopicController::class, 'update'])->name('update');
    Route::delete('/{topic}', [ToolboxTalkTopicController::class, 'destroy'])->name('destroy');
    
    // Specialized routes
    Route::post('/{topic}/duplicate', [ToolboxTalkTopicController::class, 'duplicate'])->name('duplicate');
});

// Safety Communication Routes
Route::prefix('safety-communications')->name('safety-communications.')->group(function () {
    // Reports routes - MUST come before resource routes
    Route::get('/reports', [\App\Http\Controllers\SafetyCommunicationReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/department', [\App\Http\Controllers\SafetyCommunicationReportController::class, 'departmentReport'])->name('reports.department');
    Route::get('/reports/employee', [\App\Http\Controllers\SafetyCommunicationReportController::class, 'employeeReport'])->name('reports.employee');
    Route::get('/reports/period', [\App\Http\Controllers\SafetyCommunicationReportController::class, 'periodReport'])->name('reports.period');
    Route::get('/reports/companies', [\App\Http\Controllers\SafetyCommunicationReportController::class, 'companiesReport'])->name('reports.companies');
    
    Route::get('/', [SafetyCommunicationController::class, 'index'])->name('index');
    Route::get('/dashboard', [SafetyCommunicationController::class, 'dashboard'])->name('dashboard');
    Route::get('/create', [SafetyCommunicationController::class, 'create'])->name('create');
    Route::post('/', [SafetyCommunicationController::class, 'store'])->name('store');
    
    // Export routes
    Route::get('/export/all', [SafetyCommunicationController::class, 'exportAll'])->name('export-all');
    Route::get('/{communication}/export/pdf', [SafetyCommunicationController::class, 'exportPDF'])->name('export-pdf');
    
    Route::get('/{communication}', [SafetyCommunicationController::class, 'show'])->name('show');
    Route::get('/{communication}/edit', [SafetyCommunicationController::class, 'edit'])->name('edit');
    Route::put('/{communication}', [SafetyCommunicationController::class, 'update'])->name('update');
    Route::delete('/{communication}', [SafetyCommunicationController::class, 'destroy'])->name('destroy');
    
    // Bulk actions
    Route::post('/bulk-delete', [SafetyCommunicationController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/bulk-update', [SafetyCommunicationController::class, 'bulkUpdate'])->name('bulk-update');
    Route::post('/bulk-export', [SafetyCommunicationController::class, 'bulkExport'])->name('bulk-export');
    
    // Specialized routes
    Route::post('/{communication}/send', [SafetyCommunicationController::class, 'send'])->name('send');
    Route::post('/{communication}/duplicate', [SafetyCommunicationController::class, 'duplicate'])->name('duplicate');
});

// Incident Management Routes
Route::prefix('incidents')->name('incidents.')->group(function () {
    Route::get('/', [IncidentController::class, 'index'])->name('index');
    Route::get('/dashboard', [IncidentController::class, 'dashboard'])->name('dashboard');
    Route::get('/trend-analysis', [IncidentController::class, 'trendAnalysis'])->name('trend-analysis');
    Route::get('/create', [IncidentController::class, 'create'])->middleware('permission:incidents.create')->name('create');
    Route::post('/', [IncidentController::class, 'store'])->middleware('permission:incidents.create')->name('store');
    
    // Reports routes - MUST come before {incident} routes
    Route::get('/reports', [\App\Http\Controllers\IncidentReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/department', [\App\Http\Controllers\IncidentReportController::class, 'departmentReport'])->name('reports.department');
    Route::get('/reports/employee', [\App\Http\Controllers\IncidentReportController::class, 'employeeReport'])->name('reports.employee');
    Route::get('/reports/period', [\App\Http\Controllers\IncidentReportController::class, 'periodReport'])->name('reports.period');
    Route::get('/reports/companies', [\App\Http\Controllers\IncidentReportController::class, 'companiesReport'])->name('reports.companies');
    
    // Export routes
    Route::post('/export', [IncidentController::class, 'export'])->middleware('permission:incidents.export')->name('export');
    Route::get('/export/all', [IncidentController::class, 'exportAll'])->middleware('permission:incidents.export')->name('export-all');
    
    // Bulk actions
    Route::post('/bulk-delete', [IncidentController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/bulk-update', [IncidentController::class, 'bulkUpdate'])->name('bulk-update');
    
    // Incident-specific routes - MUST come after reports routes
    Route::post('/{incident}/assign-company', [IncidentController::class, 'assignCompany'])->name('assign-company');
    Route::get('/{incident}/export/pdf', [IncidentController::class, 'exportPDF'])->middleware('permission:incidents.print')->name('export-pdf');
    Route::get('/{incident}', [IncidentController::class, 'show'])->name('show');
    Route::get('/{incident}/edit', [IncidentController::class, 'edit'])->middleware('permission:incidents.edit')->name('edit');
    Route::put('/{incident}', [IncidentController::class, 'update'])->middleware('permission:incidents.edit')->name('update');
    Route::delete('/{incident}', [IncidentController::class, 'destroy'])->middleware('permission:incidents.delete')->name('destroy');
    
    // Specialized routes
    Route::post('/{incident}/assign', [IncidentController::class, 'assign'])->name('assign');
    Route::post('/{incident}/investigate', [IncidentController::class, 'investigate'])->name('investigate');
});

// Incident Reports Routes (legacy - keeping for backward compatibility)
Route::prefix('incidents/reports')->name('incidents.reports.')->middleware(['auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\IncidentReportController::class, 'index'])->name('index');
    Route::get('/department', [\App\Http\Controllers\IncidentReportController::class, 'departmentReport'])->name('department');
    Route::get('/employee', [\App\Http\Controllers\IncidentReportController::class, 'employeeReport'])->name('employee');
    Route::get('/period', [\App\Http\Controllers\IncidentReportController::class, 'periodReport'])->name('period');
    Route::get('/companies', [\App\Http\Controllers\IncidentReportController::class, 'companiesReport'])->name('companies');
    Route::post('/{incident}/close', [IncidentController::class, 'close'])->name('close');
    Route::post('/{incident}/reopen', [IncidentController::class, 'reopen'])->name('reopen');
    Route::post('/{incident}/request-closure', [IncidentController::class, 'requestClosure'])->name('request-closure');
    Route::post('/{incident}/approve-closure', [IncidentController::class, 'approveClosure'])->name('approve-closure');
    Route::post('/{incident}/reject-closure', [IncidentController::class, 'rejectClosure'])->name('reject-closure');
    
    // Bulk Operations
    Route::post('/bulk-delete', [IncidentController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/bulk-update', [IncidentController::class, 'bulkUpdate'])->name('bulk-update');
    Route::post('/export', [IncidentController::class, 'export'])->name('export');
    
    // Investigation Routes
    Route::prefix('{incident}/investigations')->name('investigations.')->group(function () {
        Route::get('/create', [IncidentInvestigationController::class, 'create'])->name('create');
        Route::post('/', [IncidentInvestigationController::class, 'store'])->name('store');
        Route::get('/{investigation}', [IncidentInvestigationController::class, 'show'])->name('show');
        Route::get('/{investigation}/edit', [IncidentInvestigationController::class, 'edit'])->name('edit');
        Route::put('/{investigation}', [IncidentInvestigationController::class, 'update'])->name('update');
        Route::post('/{investigation}/start', [IncidentInvestigationController::class, 'start'])->name('start');
        Route::post('/{investigation}/complete', [IncidentInvestigationController::class, 'complete'])->name('complete');
    });
    
    // Root Cause Analysis Routes
    Route::prefix('{incident}/rca')->name('rca.')->group(function () {
        Route::get('/create', [RootCauseAnalysisController::class, 'create'])->name('create');
        Route::post('/', [RootCauseAnalysisController::class, 'store'])->name('store');
        Route::get('/{rootCauseAnalysis}', [RootCauseAnalysisController::class, 'show'])->name('show');
        Route::get('/{rootCauseAnalysis}/edit', [RootCauseAnalysisController::class, 'edit'])->name('edit');
        Route::put('/{rootCauseAnalysis}', [RootCauseAnalysisController::class, 'update'])->name('update');
        Route::post('/{rootCauseAnalysis}/complete', [RootCauseAnalysisController::class, 'complete'])->name('complete');
        Route::post('/{rootCauseAnalysis}/review', [RootCauseAnalysisController::class, 'review'])->name('review');
    });
    
    // CAPA Routes
    Route::prefix('{incident}/capas')->name('capas.')->group(function () {
        Route::get('/create', [CAPAController::class, 'create'])->name('create');
        Route::post('/', [CAPAController::class, 'store'])->name('store');
        Route::get('/{capa}', [CAPAController::class, 'show'])->name('show');
        Route::get('/{capa}/edit', [CAPAController::class, 'edit'])->name('edit');
        Route::put('/{capa}', [CAPAController::class, 'update'])->name('update');
        Route::post('/{capa}/start', [CAPAController::class, 'start'])->name('start');
        Route::post('/{capa}/complete', [CAPAController::class, 'complete'])->name('complete');
        Route::post('/{capa}/verify', [CAPAController::class, 'verify'])->name('verify');
        Route::post('/{capa}/close', [CAPAController::class, 'close'])->name('close');
    });
    
    // Attachment Routes
    Route::prefix('{incident}/attachments')->name('attachments.')->group(function () {
        Route::post('/', [IncidentAttachmentController::class, 'store'])->name('store');
        Route::get('/{attachment}', [IncidentAttachmentController::class, 'show'])->name('show');
        Route::delete('/{attachment}', [IncidentAttachmentController::class, 'destroy'])->name('destroy');
    });
});

// Risk Assessment & Hazard Management Routes
Route::prefix('risk-assessment')->name('risk-assessment.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [RiskAssessmentDashboardController::class, 'index'])->middleware('permission:risk_assessments.view')->name('dashboard');
    
    // Reports routes - MUST come before resource routes
    Route::get('/reports', [\App\Http\Controllers\RiskAssessmentReportController::class, 'index'])->middleware('permission:risk_assessments.view')->name('reports.index');
    Route::get('/reports/department', [\App\Http\Controllers\RiskAssessmentReportController::class, 'departmentReport'])->middleware('permission:risk_assessments.view')->name('reports.department');
    Route::get('/reports/employee', [\App\Http\Controllers\RiskAssessmentReportController::class, 'employeeReport'])->middleware('permission:risk_assessments.view')->name('reports.employee');
    Route::get('/reports/period', [\App\Http\Controllers\RiskAssessmentReportController::class, 'periodReport'])->middleware('permission:risk_assessments.view')->name('reports.period');
    Route::get('/reports/companies', [\App\Http\Controllers\RiskAssessmentReportController::class, 'companiesReport'])->middleware('permission:risk_assessments.view')->name('reports.companies');
    
    // Hazards (HAZID)
    Route::resource('hazards', HazardController::class);
    
    // Risk Assessments (Risk Register)
    Route::get('/risk-assessments', [RiskAssessmentController::class, 'index'])->middleware('permission:risk_assessments.view')->name('risk-assessments.index');
    Route::get('/risk-assessments/create', [RiskAssessmentController::class, 'create'])->middleware('permission:risk_assessments.create')->name('risk-assessments.create');
    Route::post('/risk-assessments', [RiskAssessmentController::class, 'store'])->middleware('permission:risk_assessments.create')->name('risk-assessments.store');
    Route::get('/risk-assessments/{riskAssessment}', [RiskAssessmentController::class, 'show'])->middleware('permission:risk_assessments.view')->name('risk-assessments.show');
    Route::get('/risk-assessments/{riskAssessment}/edit', [RiskAssessmentController::class, 'edit'])->middleware('permission:risk_assessments.edit')->name('risk-assessments.edit');
    Route::put('/risk-assessments/{riskAssessment}', [RiskAssessmentController::class, 'update'])->middleware('permission:risk_assessments.edit')->name('risk-assessments.update');
    Route::delete('/risk-assessments/{riskAssessment}', [RiskAssessmentController::class, 'destroy'])->middleware('permission:risk_assessments.delete')->name('risk-assessments.destroy');
    
    // Export routes
    Route::get('/risk-assessments/export/all', [RiskAssessmentController::class, 'exportAll'])->middleware('permission:risk_assessments.export')->name('risk-assessments.export-all');
    Route::get('/risk-assessments/{riskAssessment}/export/pdf', [RiskAssessmentController::class, 'exportPDF'])->middleware('permission:risk_assessments.print')->name('risk-assessments.export-pdf');
    
    // Bulk actions
    Route::post('/risk-assessments/bulk-delete', [RiskAssessmentController::class, 'bulkDelete'])->middleware('permission:risk_assessments.delete')->name('risk-assessments.bulk-delete');
    Route::post('/risk-assessments/bulk-update', [RiskAssessmentController::class, 'bulkUpdate'])->middleware('permission:risk_assessments.edit')->name('risk-assessments.bulk-update');
    Route::post('/risk-assessments/bulk-export', [RiskAssessmentController::class, 'bulkExport'])->middleware('permission:risk_assessments.export')->name('risk-assessments.bulk-export');
    Route::get('/risk-assessments/{riskAssessment}/copy', [RiskAssessmentController::class, 'copy'])->middleware('permission:risk_assessments.create')->name('risk-assessments.copy');
    
    // Job Safety Analysis (JSA/JHA)
    Route::resource('jsas', JSAController::class);
    Route::post('/jsas/{jsa}/approve', [JSAController::class, 'approve'])->middleware('permission:risk_assessments.approve')->name('jsas.approve');
    
    // Control Measures
    Route::resource('control-measures', ControlMeasureController::class);
    Route::post('/control-measures/{controlMeasure}/verify', [ControlMeasureController::class, 'verify'])->middleware('permission:risk_assessments.write')->name('control-measures.verify');
    
    // Risk Reviews
    Route::resource('risk-reviews', RiskReviewController::class);
    Route::post('/risk-reviews/{riskReview}/complete', [RiskReviewController::class, 'complete'])->middleware('permission:risk_assessments.write')->name('risk-reviews.complete');
});

// Training & Competency Module Routes
Route::prefix('training')->name('training.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [TrainingDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Training Needs Analysis (TNA)
    Route::prefix('training-needs')->name('training-needs.')->group(function () {
        Route::get('/', [TrainingNeedsAnalysisController::class, 'index'])->name('index');
        Route::get('/export', [TrainingNeedsAnalysisController::class, 'export'])->name('export');
        Route::get('/create', [TrainingNeedsAnalysisController::class, 'create'])->name('create');
        Route::post('/', [TrainingNeedsAnalysisController::class, 'store'])->name('store');
        Route::get('/{trainingNeedsAnalysis}', [TrainingNeedsAnalysisController::class, 'show'])->name('show');
        Route::get('/{trainingNeedsAnalysis}/edit', [TrainingNeedsAnalysisController::class, 'edit'])->name('edit');
        Route::put('/{trainingNeedsAnalysis}', [TrainingNeedsAnalysisController::class, 'update'])->name('update');
        Route::delete('/{trainingNeedsAnalysis}', [TrainingNeedsAnalysisController::class, 'destroy'])->name('destroy');
        Route::post('/{trainingNeedsAnalysis}/validate', [TrainingNeedsAnalysisController::class, 'validateTNA'])->name('validate');
        
        // Integration triggers
        Route::post('/from-control-measure/{controlMeasure}', [TrainingNeedsAnalysisController::class, 'createFromControlMeasure'])->name('from-control-measure');
        Route::post('/from-rca/{rootCauseAnalysis}', [TrainingNeedsAnalysisController::class, 'createFromRCA'])->name('from-rca');
        Route::post('/from-capa/{capa}', [TrainingNeedsAnalysisController::class, 'createFromCAPA'])->name('from-capa');
    });
    
    // Training Plans
    Route::prefix('training-plans')->name('training-plans.')->group(function () {
        Route::get('/', [TrainingPlanController::class, 'index'])->name('index');
        Route::get('/export', [TrainingPlanController::class, 'export'])->name('export');
        Route::get('/create', [TrainingPlanController::class, 'create'])->name('create');
        Route::post('/', [TrainingPlanController::class, 'store'])->name('store');
        Route::get('/{trainingPlan}', [TrainingPlanController::class, 'show'])->name('show');
        Route::get('/{trainingPlan}/edit', [TrainingPlanController::class, 'edit'])->name('edit');
        Route::put('/{trainingPlan}', [TrainingPlanController::class, 'update'])->name('update');
        Route::delete('/{trainingPlan}', [TrainingPlanController::class, 'destroy'])->name('destroy');
        Route::post('/{trainingPlan}/approve', [TrainingPlanController::class, 'approve'])->name('approve');
        Route::post('/{trainingPlan}/approve-budget', [TrainingPlanController::class, 'approveBudget'])->name('approve-budget');
    });
    
    // Training Sessions
    Route::prefix('training-sessions')->name('training-sessions.')->group(function () {
        Route::get('/', [TrainingSessionController::class, 'index'])->name('index');
        Route::get('/export', [TrainingSessionController::class, 'export'])->name('export');
        Route::get('/calendar', [TrainingSessionController::class, 'calendar'])->name('calendar');
        Route::get('/create', [TrainingSessionController::class, 'create'])->name('create');
        Route::post('/', [TrainingSessionController::class, 'store'])->name('store');
        Route::get('/{trainingSession}', [TrainingSessionController::class, 'show'])->name('show');
        Route::get('/{trainingSession}/edit', [TrainingSessionController::class, 'edit'])->name('edit');
        Route::put('/{trainingSession}', [TrainingSessionController::class, 'update'])->name('update');
        Route::delete('/{trainingSession}', [TrainingSessionController::class, 'destroy'])->name('destroy');
        Route::post('/{trainingSession}/start', [TrainingSessionController::class, 'start'])->name('start');
        Route::post('/{trainingSession}/complete', [TrainingSessionController::class, 'complete'])->name('complete');
        Route::post('/{trainingSession}/attendance', [TrainingSessionController::class, 'markAttendance'])->name('attendance');
    });
    
    // Training Certificates
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/{certificate}', [TrainingCertificateController::class, 'show'])->name('show');
        Route::get('/{certificate}/generate-pdf', [TrainingCertificateController::class, 'generatePDF'])->name('generate-pdf');
    });
    
    // Training Reporting
    Route::prefix('reporting')->name('reporting.')->group(function () {
        Route::get('/', [TrainingReportingController::class, 'index'])->name('index');
        Route::get('/export', [TrainingReportingController::class, 'export'])->name('export');
    });
});

// Administrative Module Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    // Employee Management
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        
        // Specialized routes
        Route::post('/{employee}/activate', [EmployeeController::class, 'activate'])->name('activate');
        Route::post('/{employee}/deactivate', [EmployeeController::class, 'deactivate'])->name('deactivate');
        Route::get('/{employee}/create-user', [EmployeeController::class, 'createUser'])->name('create-user');
        Route::post('/{employee}/create-user', [EmployeeController::class, 'storeUser'])->name('store-user');
        Route::get('/export', [EmployeeController::class, 'export'])->name('export');
    });
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        
        // Specialized routes
        Route::post('/{user}/activate', [UserController::class, 'activate'])->name('activate');
        Route::post('/{user}/deactivate', [UserController::class, 'deactivate'])->name('deactivate');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::get('/{user}/permissions', [UserController::class, 'permissions'])->name('permissions');
        Route::put('/{user}/permissions', [UserController::class, 'updatePermissions'])->name('update-permissions');
        Route::post('/bulk-import', [UserController::class, 'bulkImport'])->name('bulk-import');
        Route::get('/export', [UserController::class, 'export'])->name('export');
    });

    // Role Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        
        // Specialized routes
        Route::post('/{role}/activate', [RoleController::class, 'activate'])->name('activate');
        Route::post('/{role}/deactivate', [RoleController::class, 'deactivate'])->name('deactivate');
        Route::post('/{role}/duplicate', [RoleController::class, 'duplicate'])->name('duplicate');
    });

    // Department Management
    Route::prefix('departments')->name('departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('create');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::get('/{department}', [DepartmentController::class, 'show'])->name('show');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        
        // Specialized routes
        Route::post('/{department}/activate', [DepartmentController::class, 'activate'])->name('activate');
        Route::post('/{department}/deactivate', [DepartmentController::class, 'deactivate'])->name('deactivate');
        Route::get('/hierarchy', [DepartmentController::class, 'hierarchy'])->name('hierarchy');
        Route::get('/{department}/performance', [DepartmentController::class, 'performance'])->name('performance');
    });

    // Company Management
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::get('/{company}', [CompanyController::class, 'show'])->name('show');
        Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
        Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('destroy');
        
        // Specialized routes
        Route::post('/{company}/activate', [CompanyController::class, 'activate'])->name('activate');
        Route::post('/{company}/deactivate', [CompanyController::class, 'deactivate'])->name('deactivate');
        Route::post('/{company}/upgrade', [CompanyController::class, 'upgrade'])->name('upgrade');
        Route::get('/{company}/users', [CompanyController::class, 'users'])->name('users');
        Route::get('/{company}/departments', [CompanyController::class, 'departments'])->name('departments');
        Route::get('/{company}/statistics', [CompanyController::class, 'statistics'])->name('statistics');
    });

    // Activity Logs
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{activityLog}', [ActivityLogController::class, 'show'])->name('show');
        Route::get('/critical', [ActivityLogController::class, 'critical'])->name('critical');
        Route::get('/login-attempts', [ActivityLogController::class, 'loginAttempts'])->name('login-attempts');
        Route::get('/dashboard', [ActivityLogController::class, 'dashboard'])->name('dashboard');
        Route::get('/users/{user}', [ActivityLogController::class, 'userActivity'])->name('user-activity');
        Route::get('/companies/{company}', [ActivityLogController::class, 'companyActivity'])->name('company-activity');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
        Route::post('/cleanup', [ActivityLogController::class, 'cleanup'])->name('cleanup');
    });
});

// PPE Management Module Routes
Route::prefix('ppe')->name('ppe.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PPEController::class, 'dashboard'])->name('dashboard');
    
    // PPE Inventory Management
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/', [PPEItemController::class, 'index'])->name('index');
        Route::get('/export', [PPEItemController::class, 'export'])->name('export');
        Route::get('/create', [PPEItemController::class, 'create'])->name('create');
        Route::post('/', [PPEItemController::class, 'store'])->name('store');
        Route::get('/{item}', [PPEItemController::class, 'show'])->name('show');
        Route::get('/{item}/edit', [PPEItemController::class, 'edit'])->name('edit');
        Route::put('/{item}', [PPEItemController::class, 'update'])->name('update');
        Route::delete('/{item}', [PPEItemController::class, 'destroy'])->name('destroy');
        Route::post('/{item}/adjust-stock', [PPEItemController::class, 'adjustStock'])->name('adjust-stock');
        
        // Bulk Operations
        Route::post('/bulk-delete', [PPEItemController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-update', [PPEItemController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/bulk-export', [PPEItemController::class, 'bulkExport'])->name('bulk-export');
    });
    
    // PPE Issuance & Returns
    Route::prefix('issuances')->name('issuances.')->group(function () {
        Route::get('/', [PPEIssuanceController::class, 'index'])->name('index');
        Route::get('/create', [PPEIssuanceController::class, 'create'])->name('create');
        Route::post('/', [PPEIssuanceController::class, 'store'])->name('store');
        Route::post('/bulk-issue', [PPEIssuanceController::class, 'bulkIssue'])->name('bulk-issue');
        Route::get('/{issuance}', [PPEIssuanceController::class, 'show'])->name('show');
        Route::post('/{issuance}/return', [PPEIssuanceController::class, 'returnItem'])->name('return');
    });
    
    // PPE Inspections
    Route::prefix('inspections')->name('inspections.')->group(function () {
        Route::get('/', [PPEInspectionController::class, 'index'])->name('index');
        Route::get('/create', [PPEInspectionController::class, 'create'])->name('create');
        Route::post('/', [PPEInspectionController::class, 'store'])->name('store');
        Route::get('/{inspection}', [PPEInspectionController::class, 'show'])->name('show');
    });
    
    // PPE Suppliers
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [PPESupplierController::class, 'index'])->name('index');
        Route::get('/create', [PPESupplierController::class, 'create'])->name('create');
        Route::post('/', [PPESupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}', [PPESupplierController::class, 'show'])->name('show');
        Route::get('/{supplier}/edit', [PPESupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [PPESupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [PPESupplierController::class, 'destroy'])->name('destroy');
    });
    
    // PPE Compliance Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [PPEComplianceReportController::class, 'index'])->name('index');
        Route::get('/create', [PPEComplianceReportController::class, 'create'])->name('create');
        Route::post('/', [PPEComplianceReportController::class, 'store'])->name('store');
        Route::get('/{report}', [PPEComplianceReportController::class, 'show'])->name('show');
    });
});

// Permit to Work (PTW) Module Routes
Route::middleware('auth')->prefix('work-permits')->name('work-permits.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [WorkPermitDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Work Permits
    Route::get('/', [WorkPermitController::class, 'index'])->name('index');
    Route::get('/create', [WorkPermitController::class, 'create'])->name('create');
    Route::post('/', [WorkPermitController::class, 'store'])->name('store');
    
    // Work Permit Types (must come before parameterized routes)
    Route::prefix('types')->name('types.')->group(function () {
        Route::get('/', [WorkPermitTypeController::class, 'index'])->name('index');
        Route::get('/create', [WorkPermitTypeController::class, 'create'])->name('create');
        Route::post('/', [WorkPermitTypeController::class, 'store'])->name('store');
        Route::get('/{workPermitType}', [WorkPermitTypeController::class, 'show'])->name('show');
        Route::get('/{workPermitType}/edit', [WorkPermitTypeController::class, 'edit'])->name('edit');
        Route::put('/{workPermitType}', [WorkPermitTypeController::class, 'update'])->name('update');
        Route::delete('/{workPermitType}', [WorkPermitTypeController::class, 'destroy'])->name('destroy');
    });
    
    // GCLA Logs (must come before parameterized routes)
    Route::prefix('gca-logs')->name('gca-logs.')->group(function () {
        Route::get('/', [GCALogController::class, 'index'])->name('index');
        Route::get('/create', [GCALogController::class, 'create'])->name('create');
        Route::post('/', [GCALogController::class, 'store'])->name('store');
        Route::get('/{gcaLog}', [GCALogController::class, 'show'])->name('show');
        Route::get('/{gcaLog}/edit', [GCALogController::class, 'edit'])->name('edit');
        Route::put('/{gcaLog}', [GCALogController::class, 'update'])->name('update');
        Route::delete('/{gcaLog}', [GCALogController::class, 'destroy'])->name('destroy');
        Route::post('/{gcaLog}/complete-action', [GCALogController::class, 'completeAction'])->name('complete-action');
        Route::post('/{gcaLog}/verify', [GCALogController::class, 'verify'])->name('verify');
    });
    
    // Work Permit parameterized routes (must come after static routes)
    Route::post('/bulk-delete', [WorkPermitController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/bulk-update', [WorkPermitController::class, 'bulkUpdate'])->name('bulk-update');
    Route::post('/bulk-export', [WorkPermitController::class, 'bulkExport'])->name('bulk-export');
    Route::get('/{workPermit}/copy', [WorkPermitController::class, 'copy'])->name('copy');
    
    Route::get('/{workPermit}', [WorkPermitController::class, 'show'])->name('show');
    Route::get('/{workPermit}/edit', [WorkPermitController::class, 'edit'])->name('edit');
    Route::put('/{workPermit}', [WorkPermitController::class, 'update'])->name('update');
    Route::delete('/{workPermit}', [WorkPermitController::class, 'destroy'])->name('destroy');
    
    // Workflow actions
    Route::post('/{workPermit}/submit', [WorkPermitController::class, 'submit'])->name('submit');
    Route::post('/{workPermit}/approve', [WorkPermitController::class, 'approve'])->name('approve');
    Route::post('/{workPermit}/reject', [WorkPermitController::class, 'reject'])->name('reject');
    Route::post('/{workPermit}/activate', [WorkPermitController::class, 'activate'])->name('activate');
    Route::post('/{workPermit}/close', [WorkPermitController::class, 'close'])->name('close');
    Route::post('/{workPermit}/verify', [WorkPermitController::class, 'verify'])->name('verify');
});

// Inspection & Audit Module Routes
Route::middleware('auth')->prefix('inspections')->name('inspections.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [InspectionDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Inspection Schedules
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::get('/', [InspectionScheduleController::class, 'index'])->name('index');
        Route::get('/create', [InspectionScheduleController::class, 'create'])->name('create');
        Route::post('/', [InspectionScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}', [InspectionScheduleController::class, 'show'])->name('show');
        Route::get('/{schedule}/edit', [InspectionScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [InspectionScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [InspectionScheduleController::class, 'destroy'])->name('destroy');
    });
    
    // Inspections
    Route::get('/', [InspectionController::class, 'index'])->name('index');
    Route::get('/create', [InspectionController::class, 'create'])->name('create');
    Route::post('/', [InspectionController::class, 'store'])->name('store');
    Route::get('/{inspection}', [InspectionController::class, 'show'])->name('show');
    Route::get('/{inspection}/edit', [InspectionController::class, 'edit'])->name('edit');
    Route::put('/{inspection}', [InspectionController::class, 'update'])->name('update');
    Route::delete('/{inspection}', [InspectionController::class, 'destroy'])->name('destroy');
    
    // Inspection Checklists
    Route::prefix('checklists')->name('checklists.')->group(function () {
        Route::get('/', [InspectionChecklistController::class, 'index'])->name('index');
        Route::get('/create', [InspectionChecklistController::class, 'create'])->name('create');
        Route::post('/', [InspectionChecklistController::class, 'store'])->name('store');
        Route::get('/{checklist}', [InspectionChecklistController::class, 'show'])->name('show');
        Route::get('/{checklist}/edit', [InspectionChecklistController::class, 'edit'])->name('edit');
        Route::put('/{checklist}', [InspectionChecklistController::class, 'update'])->name('update');
        Route::delete('/{checklist}', [InspectionChecklistController::class, 'destroy'])->name('destroy');
    });
    
    // Non-Conformance Reports (NCR)
    Route::prefix('ncrs')->name('ncrs.')->group(function () {
        Route::get('/', [NonConformanceReportController::class, 'index'])->name('index');
        Route::get('/create', [NonConformanceReportController::class, 'create'])->name('create');
        Route::post('/', [NonConformanceReportController::class, 'store'])->name('store');
        Route::get('/{ncr}', [NonConformanceReportController::class, 'show'])->name('show');
        Route::get('/{ncr}/edit', [NonConformanceReportController::class, 'edit'])->name('edit');
        Route::put('/{ncr}', [NonConformanceReportController::class, 'update'])->name('update');
        Route::delete('/{ncr}', [NonConformanceReportController::class, 'destroy'])->name('destroy');
    });
    
    // Audits
    Route::prefix('audits')->name('audits.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/create', [AuditController::class, 'create'])->name('create');
        Route::post('/', [AuditController::class, 'store'])->name('store');
        Route::get('/{audit}', [AuditController::class, 'show'])->name('show');
        Route::get('/{audit}/edit', [AuditController::class, 'edit'])->name('edit');
        Route::put('/{audit}', [AuditController::class, 'update'])->name('update');
        Route::delete('/{audit}', [AuditController::class, 'destroy'])->name('destroy');
        
        // Audit Findings
        Route::prefix('{audit}/findings')->name('findings.')->group(function () {
            Route::get('/', [AuditFindingController::class, 'index'])->name('index');
            Route::get('/create', [AuditFindingController::class, 'create'])->name('create');
            Route::post('/', [AuditFindingController::class, 'store'])->name('store');
            Route::get('/{finding}', [AuditFindingController::class, 'show'])->name('show');
            Route::get('/{finding}/edit', [AuditFindingController::class, 'edit'])->name('edit');
            Route::put('/{finding}', [AuditFindingController::class, 'update'])->name('update');
            Route::delete('/{finding}', [AuditFindingController::class, 'destroy'])->name('destroy');
        });
    });
    
    // Audit Findings (Standalone)
    Route::prefix('audit-findings')->name('audit-findings.')->group(function () {
        Route::get('/', [AuditFindingController::class, 'index'])->name('index');
        Route::get('/create', [AuditFindingController::class, 'create'])->name('create');
        Route::post('/', [AuditFindingController::class, 'store'])->name('store');
        Route::get('/{finding}', [AuditFindingController::class, 'show'])->name('show');
        Route::get('/{finding}/edit', [AuditFindingController::class, 'edit'])->name('edit');
        Route::put('/{finding}', [AuditFindingController::class, 'update'])->name('update');
        Route::delete('/{finding}', [AuditFindingController::class, 'destroy'])->name('destroy');
    });
});

// Emergency Preparedness & Response Module Routes
Route::middleware('auth')->prefix('emergency')->name('emergency.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [EmergencyPreparednessDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Fire Drills
    Route::prefix('fire-drills')->name('fire-drills.')->group(function () {
        Route::get('/', [FireDrillController::class, 'index'])->name('index');
        Route::get('/create', [FireDrillController::class, 'create'])->name('create');
        Route::post('/', [FireDrillController::class, 'store'])->name('store');
        Route::get('/{fireDrill}', [FireDrillController::class, 'show'])->name('show');
        Route::get('/{fireDrill}/edit', [FireDrillController::class, 'edit'])->name('edit');
        Route::put('/{fireDrill}', [FireDrillController::class, 'update'])->name('update');
        Route::delete('/{fireDrill}', [FireDrillController::class, 'destroy'])->name('destroy');
    });
    
    // Emergency Contacts
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [EmergencyContactController::class, 'index'])->name('index');
        Route::get('/create', [EmergencyContactController::class, 'create'])->name('create');
        Route::post('/', [EmergencyContactController::class, 'store'])->name('store');
        Route::get('/{contact}', [EmergencyContactController::class, 'show'])->name('show');
        Route::get('/{contact}/edit', [EmergencyContactController::class, 'edit'])->name('edit');
        Route::put('/{contact}', [EmergencyContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [EmergencyContactController::class, 'destroy'])->name('destroy');
    });
    
    // Emergency Equipment
    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', [EmergencyEquipmentController::class, 'index'])->name('index');
        Route::get('/create', [EmergencyEquipmentController::class, 'create'])->name('create');
        Route::post('/', [EmergencyEquipmentController::class, 'store'])->name('store');
        Route::get('/{equipment}', [EmergencyEquipmentController::class, 'show'])->name('show');
        Route::get('/{equipment}/edit', [EmergencyEquipmentController::class, 'edit'])->name('edit');
        Route::put('/{equipment}', [EmergencyEquipmentController::class, 'update'])->name('update');
        Route::delete('/{equipment}', [EmergencyEquipmentController::class, 'destroy'])->name('destroy');
    });
    
    // Evacuation Plans
    Route::prefix('evacuation-plans')->name('evacuation-plans.')->group(function () {
        Route::get('/', [EvacuationPlanController::class, 'index'])->name('index');
        Route::get('/create', [EvacuationPlanController::class, 'create'])->name('create');
        Route::post('/', [EvacuationPlanController::class, 'store'])->name('store');
        Route::get('/{plan}', [EvacuationPlanController::class, 'show'])->name('show');
        Route::get('/{plan}/edit', [EvacuationPlanController::class, 'edit'])->name('edit');
        Route::put('/{plan}', [EvacuationPlanController::class, 'update'])->name('update');
        Route::delete('/{plan}', [EvacuationPlanController::class, 'destroy'])->name('destroy');
    });
    
    // Emergency Response Teams
    Route::prefix('response-teams')->name('response-teams.')->group(function () {
        Route::get('/', [EmergencyResponseTeamController::class, 'index'])->name('index');
        Route::get('/create', [EmergencyResponseTeamController::class, 'create'])->name('create');
        Route::post('/', [EmergencyResponseTeamController::class, 'store'])->name('store');
        Route::get('/{team}', [EmergencyResponseTeamController::class, 'show'])->name('show');
        Route::get('/{team}/edit', [EmergencyResponseTeamController::class, 'edit'])->name('edit');
        Route::put('/{team}', [EmergencyResponseTeamController::class, 'update'])->name('update');
        Route::delete('/{team}', [EmergencyResponseTeamController::class, 'destroy'])->name('destroy');
    });
});

// Environmental Management Module Routes
Route::middleware('auth')->prefix('environmental')->name('environmental.')->group(function () {
        Route::get('/dashboard', [EnvironmentalDashboardController::class, 'dashboard'])->name('dashboard');
        
        Route::prefix('waste-management')->name('waste-management.')->group(function () {
            Route::get('/', [WasteManagementRecordController::class, 'index'])->name('index');
            Route::get('/create', [WasteManagementRecordController::class, 'create'])->name('create');
            Route::post('/', [WasteManagementRecordController::class, 'store'])->name('store');
            Route::get('/{record}', [WasteManagementRecordController::class, 'show'])->name('show');
            Route::get('/{record}/edit', [WasteManagementRecordController::class, 'edit'])->name('edit');
            Route::put('/{record}', [WasteManagementRecordController::class, 'update'])->name('update');
            Route::delete('/{record}', [WasteManagementRecordController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('waste-tracking')->name('waste-tracking.')->group(function () {
            Route::get('/', [WasteTrackingRecordController::class, 'index'])->name('index');
            Route::get('/create', [WasteTrackingRecordController::class, 'create'])->name('create');
            Route::post('/', [WasteTrackingRecordController::class, 'store'])->name('store');
            Route::get('/{record}', [WasteTrackingRecordController::class, 'show'])->name('show');
            Route::get('/{record}/edit', [WasteTrackingRecordController::class, 'edit'])->name('edit');
            Route::put('/{record}', [WasteTrackingRecordController::class, 'update'])->name('update');
            Route::delete('/{record}', [WasteTrackingRecordController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('emissions')->name('emissions.')->group(function () {
            Route::get('/', [EmissionMonitoringRecordController::class, 'index'])->name('index');
            Route::get('/create', [EmissionMonitoringRecordController::class, 'create'])->name('create');
            Route::post('/', [EmissionMonitoringRecordController::class, 'store'])->name('store');
            Route::get('/{record}', [EmissionMonitoringRecordController::class, 'show'])->name('show');
            Route::get('/{record}/edit', [EmissionMonitoringRecordController::class, 'edit'])->name('edit');
            Route::put('/{record}', [EmissionMonitoringRecordController::class, 'update'])->name('update');
            Route::delete('/{record}', [EmissionMonitoringRecordController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('spills')->name('spills.')->group(function () {
            Route::get('/', [SpillIncidentController::class, 'index'])->name('index');
            Route::get('/create', [SpillIncidentController::class, 'create'])->name('create');
            Route::post('/', [SpillIncidentController::class, 'store'])->name('store');
            Route::get('/{incident}', [SpillIncidentController::class, 'show'])->name('show');
            Route::get('/{incident}/edit', [SpillIncidentController::class, 'edit'])->name('edit');
            Route::put('/{incident}', [SpillIncidentController::class, 'update'])->name('update');
            Route::delete('/{incident}', [SpillIncidentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('resource-usage')->name('resource-usage.')->group(function () {
            Route::get('/', [ResourceUsageRecordController::class, 'index'])->name('index');
            Route::get('/create', [ResourceUsageRecordController::class, 'create'])->name('create');
            Route::post('/', [ResourceUsageRecordController::class, 'store'])->name('store');
            Route::get('/{record}', [ResourceUsageRecordController::class, 'show'])->name('show');
            Route::get('/{record}/edit', [ResourceUsageRecordController::class, 'edit'])->name('edit');
            Route::put('/{record}', [ResourceUsageRecordController::class, 'update'])->name('update');
            Route::delete('/{record}', [ResourceUsageRecordController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('iso14001')->name('iso14001.')->group(function () {
            Route::get('/', [ISO14001ComplianceRecordController::class, 'index'])->name('index');
            Route::get('/create', [ISO14001ComplianceRecordController::class, 'create'])->name('create');
            Route::post('/', [ISO14001ComplianceRecordController::class, 'store'])->name('store');
            Route::get('/{record}', [ISO14001ComplianceRecordController::class, 'show'])->name('show');
            Route::get('/{record}/edit', [ISO14001ComplianceRecordController::class, 'edit'])->name('edit');
            Route::put('/{record}', [ISO14001ComplianceRecordController::class, 'update'])->name('update');
            Route::delete('/{record}', [ISO14001ComplianceRecordController::class, 'destroy'])->name('destroy');
        });
});

// Health & Wellness Module Routes
Route::middleware('auth')->prefix('health')->name('health.')->group(function () {
        Route::get('/dashboard', [HealthWellnessDashboardController::class, 'dashboard'])->name('dashboard');
        
        Route::prefix('surveillance')->name('surveillance.')->group(function () {
            Route::get('/', [HealthSurveillanceRecordController::class, 'index'])->name('index');
            Route::get('/create', [HealthSurveillanceRecordController::class, 'create'])->name('create');
            Route::post('/', [HealthSurveillanceRecordController::class, 'store'])->name('store');
            Route::get('/{record}', [HealthSurveillanceRecordController::class, 'show'])->name('show');
            Route::get('/{record}/edit', [HealthSurveillanceRecordController::class, 'edit'])->name('edit');
            Route::put('/{record}', [HealthSurveillanceRecordController::class, 'update'])->name('update');
            Route::delete('/{record}', [HealthSurveillanceRecordController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('first-aid')->name('first-aid.')->group(function () {
            Route::get('/', [FirstAidLogbookEntryController::class, 'index'])->name('index');
            Route::get('/create', [FirstAidLogbookEntryController::class, 'create'])->name('create');
            Route::post('/', [FirstAidLogbookEntryController::class, 'store'])->name('store');
            Route::get('/{entry}', [FirstAidLogbookEntryController::class, 'show'])->name('show');
            Route::get('/{entry}/edit', [FirstAidLogbookEntryController::class, 'edit'])->name('edit');
            Route::put('/{entry}', [FirstAidLogbookEntryController::class, 'update'])->name('update');
            Route::delete('/{entry}', [FirstAidLogbookEntryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('ergonomic')->name('ergonomic.')->group(function () {
            Route::get('/', [ErgonomicAssessmentController::class, 'index'])->name('index');
            Route::get('/create', [ErgonomicAssessmentController::class, 'create'])->name('create');
            Route::post('/', [ErgonomicAssessmentController::class, 'store'])->name('store');
            Route::get('/{assessment}', [ErgonomicAssessmentController::class, 'show'])->name('show');
            Route::get('/{assessment}/edit', [ErgonomicAssessmentController::class, 'edit'])->name('edit');
            Route::put('/{assessment}', [ErgonomicAssessmentController::class, 'update'])->name('update');
            Route::delete('/{assessment}', [ErgonomicAssessmentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('hygiene')->name('hygiene.')->group(function () {
            Route::get('/', [WorkplaceHygieneInspectionController::class, 'index'])->name('index');
            Route::get('/create', [WorkplaceHygieneInspectionController::class, 'create'])->name('create');
            Route::post('/', [WorkplaceHygieneInspectionController::class, 'store'])->name('store');
            Route::get('/{inspection}', [WorkplaceHygieneInspectionController::class, 'show'])->name('show');
            Route::get('/{inspection}/edit', [WorkplaceHygieneInspectionController::class, 'edit'])->name('edit');
            Route::put('/{inspection}', [WorkplaceHygieneInspectionController::class, 'update'])->name('update');
            Route::delete('/{inspection}', [WorkplaceHygieneInspectionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('campaigns')->name('campaigns.')->group(function () {
            Route::get('/', [HealthCampaignController::class, 'index'])->name('index');
            Route::get('/create', [HealthCampaignController::class, 'create'])->name('create');
            Route::post('/', [HealthCampaignController::class, 'store'])->name('store');
            Route::get('/{campaign}', [HealthCampaignController::class, 'show'])->name('show');
            Route::get('/{campaign}/edit', [HealthCampaignController::class, 'edit'])->name('edit');
            Route::put('/{campaign}', [HealthCampaignController::class, 'update'])->name('update');
            Route::delete('/{campaign}', [HealthCampaignController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('sick-leave')->name('sick-leave.')->group(function () {
            Route::get('/', [SickLeaveRecordController::class, 'index'])->name('index');
            Route::get('/create', [SickLeaveRecordController::class, 'create'])->name('create');
            Route::post('/', [SickLeaveRecordController::class, 'store'])->name('store');
            Route::get('/{record}', [SickLeaveRecordController::class, 'show'])->name('show');
            Route::get('/{record}/edit', [SickLeaveRecordController::class, 'edit'])->name('edit');
            Route::put('/{record}', [SickLeaveRecordController::class, 'update'])->name('update');
            Route::delete('/{record}', [SickLeaveRecordController::class, 'destroy'])->name('destroy');
        });
});

// Procurement & Resource Management Module Routes
Route::middleware('auth')->prefix('procurement')->name('procurement.')->group(function () {
        Route::get('/dashboard', [ProcurementDashboardController::class, 'dashboard'])->name('dashboard');
        
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('/', [ProcurementRequestController::class, 'index'])->name('index');
            Route::get('/create', [ProcurementRequestController::class, 'create'])->name('create');
            Route::post('/', [ProcurementRequestController::class, 'store'])->name('store');
            Route::get('/{request}', [ProcurementRequestController::class, 'show'])->name('show');
            Route::get('/{request}/edit', [ProcurementRequestController::class, 'edit'])->name('edit');
            Route::put('/{request}', [ProcurementRequestController::class, 'update'])->name('update');
            Route::delete('/{request}', [ProcurementRequestController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('suppliers')->name('suppliers.')->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->name('index');
            Route::get('/create', [SupplierController::class, 'create'])->name('create');
            Route::post('/', [SupplierController::class, 'store'])->name('store');
            Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
            Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
            Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
            Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('equipment-certifications')->name('equipment-certifications.')->group(function () {
            Route::get('/', [EquipmentCertificationController::class, 'index'])->name('index');
            Route::get('/create', [EquipmentCertificationController::class, 'create'])->name('create');
            Route::post('/', [EquipmentCertificationController::class, 'store'])->name('store');
            Route::get('/{certification}', [EquipmentCertificationController::class, 'show'])->name('show');
            Route::get('/{certification}/edit', [EquipmentCertificationController::class, 'edit'])->name('edit');
            Route::put('/{certification}', [EquipmentCertificationController::class, 'update'])->name('update');
            Route::delete('/{certification}', [EquipmentCertificationController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('stock-reports')->name('stock-reports.')->group(function () {
            Route::get('/', [StockConsumptionReportController::class, 'index'])->name('index');
            Route::get('/create', [StockConsumptionReportController::class, 'create'])->name('create');
            Route::post('/', [StockConsumptionReportController::class, 'store'])->name('store');
            Route::get('/{report}', [StockConsumptionReportController::class, 'show'])->name('show');
            Route::get('/{report}/edit', [StockConsumptionReportController::class, 'edit'])->name('edit');
            Route::put('/{report}', [StockConsumptionReportController::class, 'update'])->name('update');
            Route::delete('/{report}', [StockConsumptionReportController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('gap-analysis')->name('gap-analysis.')->group(function () {
            Route::get('/', [SafetyMaterialGapAnalysisController::class, 'index'])->name('index');
            Route::get('/create', [SafetyMaterialGapAnalysisController::class, 'create'])->name('create');
            Route::post('/', [SafetyMaterialGapAnalysisController::class, 'store'])->name('store');
            Route::get('/{analysis}', [SafetyMaterialGapAnalysisController::class, 'show'])->name('show');
            Route::get('/{analysis}/edit', [SafetyMaterialGapAnalysisController::class, 'edit'])->name('edit');
            Route::put('/{analysis}', [SafetyMaterialGapAnalysisController::class, 'update'])->name('update');
            Route::delete('/{analysis}', [SafetyMaterialGapAnalysisController::class, 'destroy'])->name('destroy');
        });
});

// QR Code Routes
Route::middleware('auth')->prefix('qr')->name('qr.')->group(function () {
    Route::get('/{type}/{id}', [QRCodeController::class, 'scan'])->name('scan');
    Route::get('/{type}/{id}/printable', [QRCodeController::class, 'printable'])->name('printable');
});

// Document & Record Management Module Routes
Route::middleware('auth')->prefix('documents')->name('documents.')->group(function () {
    Route::get('/dashboard', [DocumentManagementDashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('hse-documents', HSEDocumentController::class);
    Route::resource('versions', DocumentVersionController::class);
    Route::resource('templates', DocumentTemplateController::class);
});

// Compliance & Legal Module Routes
Route::middleware('auth')->prefix('compliance')->name('compliance.')->group(function () {
    Route::get('/dashboard', [ComplianceDashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('requirements', ComplianceRequirementController::class);
    Route::resource('permits-licenses', PermitLicenseController::class);
    Route::resource('audits', ComplianceAuditController::class);
});

// Housekeeping & Workplace Organization Module Routes
Route::middleware('auth')->prefix('housekeeping')->name('housekeeping.')->group(function () {
    Route::get('/dashboard', [HousekeepingDashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('inspections', HousekeepingInspectionController::class);
    Route::resource('5s-audits', FiveSAuditController::class)->parameters(['5s-audits' => 'audit']);
});

// Waste & Sustainability Module Routes
Route::middleware('auth')->prefix('waste-sustainability')->name('waste-sustainability.')->group(function () {
    Route::get('/dashboard', [WasteSustainabilityDashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('records', WasteSustainabilityRecordController::class);
    Route::resource('carbon-footprint', CarbonFootprintRecordController::class);
});

// Notifications & Alerts Module Routes
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::resource('rules', NotificationRuleController::class);
    Route::resource('escalation-matrices', EscalationMatrixController::class);
});

// Biometric Devices & Daily Attendance Routes
Route::middleware('auth')->prefix('biometric-devices')->name('biometric-devices.')->group(function () {
    Route::get('/', [BiometricDeviceController::class, 'index'])->name('index');
    Route::get('/create', [BiometricDeviceController::class, 'create'])->name('create');
    Route::post('/', [BiometricDeviceController::class, 'store'])->name('store');
    Route::get('/{biometricDevice}', [BiometricDeviceController::class, 'show'])->name('show');
    Route::get('/{biometricDevice}/edit', [BiometricDeviceController::class, 'edit'])->name('edit');
    Route::put('/{biometricDevice}', [BiometricDeviceController::class, 'update'])->name('update');
    Route::delete('/{biometricDevice}', [BiometricDeviceController::class, 'destroy'])->name('destroy');
    Route::post('/{biometricDevice}/test-connection', [BiometricDeviceController::class, 'testConnection'])->name('test-connection');
    Route::post('/{biometricDevice}/sync-users', [BiometricDeviceController::class, 'syncUsers'])->name('sync-users');
    Route::post('/{biometricDevice}/sync-attendance', [BiometricDeviceController::class, 'syncAttendance'])->name('sync-attendance');
    Route::get('/{biometricDevice}/enrollment', [BiometricDeviceController::class, 'enrollment'])->name('enrollment');
    Route::post('/{biometricDevice}/enroll-employee', [BiometricDeviceController::class, 'enrollEmployee'])->name('enroll-employee');
    Route::post('/{biometricDevice}/bulk-enroll', [BiometricDeviceController::class, 'bulkEnroll'])->name('bulk-enroll');
    Route::post('/{biometricDevice}/remove-employee', [BiometricDeviceController::class, 'removeEmployee'])->name('remove-employee');
});

Route::middleware('auth')->prefix('daily-attendance')->name('daily-attendance.')->group(function () {
    Route::get('/', [DailyAttendanceController::class, 'index'])->name('index');
    Route::get('/dashboard', [DailyAttendanceController::class, 'dashboard'])->name('dashboard');
    Route::get('/export/excel', [DailyAttendanceController::class, 'exportExcel'])->name('export-excel');
    Route::get('/export/pdf', [DailyAttendanceController::class, 'exportPdf'])->name('export-pdf');
    Route::post('/manual-check-in', [DailyAttendanceController::class, 'manualCheckIn'])->name('manual-check-in');
    Route::post('/manual-check-out', [DailyAttendanceController::class, 'manualCheckOut'])->name('manual-check-out');
    Route::post('/{dailyAttendance}/approve', [DailyAttendanceController::class, 'approve'])->name('approve');
    Route::post('/{dailyAttendance}/reject', [DailyAttendanceController::class, 'reject'])->name('reject');
    Route::get('/{dailyAttendance}', [DailyAttendanceController::class, 'show'])->name('show');
});

Route::middleware('auth')->prefix('manpower-reports')->name('manpower-reports.')->group(function () {
    Route::get('/', [ManpowerReportController::class, 'index'])->name('index');
    Route::get('/daily', [ManpowerReportController::class, 'dailyReport'])->name('daily');
    Route::get('/weekly', [ManpowerReportController::class, 'weeklyReport'])->name('weekly');
    Route::get('/monthly', [ManpowerReportController::class, 'monthlyReport'])->name('monthly');
    Route::get('/location', [ManpowerReportController::class, 'locationReport'])->name('location');
});

// Settings Routes
Route::middleware('auth')->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
});

// Profile Routes
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
    Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('delete-photo');
});

// API Routes for ZKTeco Bridge (for local bridge service communication)
Route::prefix('api/zkteco')->middleware('auth:sanctum')->group(function () {
    Route::post('/sync', [\App\Http\Controllers\API\ZKTecoBridgeController::class, 'receiveSyncData']);
    Route::post('/heartbeat', [\App\Http\Controllers\API\ZKTecoBridgeController::class, 'heartbeat']);
    Route::get('/bridge-status', [\App\Http\Controllers\API\ZKTecoBridgeController::class, 'getBridgeStatus']);
});
