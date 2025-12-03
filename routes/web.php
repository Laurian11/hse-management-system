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
});

Route::get('/', [LandingPageController::class, 'index'])->name('landing');
Route::post('/report-incident', [LandingPageController::class, 'reportIncident'])->name('incident.report');
Route::get('/company/{id}/dashboard', [LandingPageController::class, 'companyDashboard'])->name('company.dashboard');

// Toolbox Talk Routes
Route::prefix('toolbox-talks')->name('toolbox-talks.')->group(function () {
    // Static routes (must come before parameterized routes)
    Route::get('/', [ToolboxTalkController::class, 'index'])->name('index');
    Route::get('/schedule', [ToolboxTalkController::class, 'schedule'])->name('schedule');
    Route::get('/dashboard', [ToolboxTalkController::class, 'dashboard'])->name('dashboard');
    Route::get('/create', [ToolboxTalkController::class, 'create'])->name('create');
    Route::get('/attendance', [ToolboxTalkController::class, 'attendance'])->name('attendance');
    Route::get('/feedback', [ToolboxTalkController::class, 'feedback'])->name('feedback');
    Route::post('/{toolboxTalk}/feedback', [ToolboxTalkController::class, 'submitFeedback'])->name('submit-feedback');
    Route::get('/{toolboxTalk}/feedback', [ToolboxTalkController::class, 'viewFeedback'])->name('view-feedback');
    Route::get('/reporting', [ToolboxTalkController::class, 'reporting'])->name('reporting');
    Route::get('/calendar', [ToolboxTalkController::class, 'calendar'])->name('calendar');
    Route::post('/', [ToolboxTalkController::class, 'store'])->name('store');
    Route::post('/bulk-import', [ToolboxTalkController::class, 'bulkImport'])->name('bulk-import');
    
    // Parameterized routes (must come after static routes)
    Route::get('/{toolboxTalk}', [ToolboxTalkController::class, 'show'])->name('show');
    Route::get('/{toolboxTalk}/edit', [ToolboxTalkController::class, 'edit'])->name('edit');
    Route::put('/{toolboxTalk}', [ToolboxTalkController::class, 'update'])->name('update');
    Route::delete('/{toolboxTalk}', [ToolboxTalkController::class, 'destroy'])->name('destroy');
    
    // Specialized routes for specific talks
    Route::post('/{toolboxTalk}/start', [ToolboxTalkController::class, 'startTalk'])->name('start');
    Route::post('/{toolboxTalk}/complete', [ToolboxTalkController::class, 'completeTalk'])->name('complete');
    Route::post('/{toolboxTalk}/mark-attendance', [ToolboxTalkController::class, 'markAttendance'])->name('mark-attendance');
    Route::post('/{toolboxTalk}/sync-biometric', [ToolboxTalkController::class, 'syncBiometricAttendance'])->name('sync-biometric');
    Route::get('/{toolboxTalk}/action-items', [ToolboxTalkController::class, 'actionItems'])->name('action-items');
    Route::post('/{toolboxTalk}/action-items', [ToolboxTalkController::class, 'saveActionItems'])->name('save-action-items');
    Route::get('/{toolboxTalk}/attendance', [ToolboxTalkController::class, 'attendanceManagement'])->name('attendance-management');
    
    // Export routes
    Route::get('/{toolboxTalk}/export/attendance-pdf', [ToolboxTalkController::class, 'exportAttendancePDF'])->name('export-attendance-pdf');
    Route::get('/{toolboxTalk}/export/attendance-excel', [ToolboxTalkController::class, 'exportAttendanceExcel'])->name('export-attendance-excel');
    Route::get('/export/reporting-excel', [ToolboxTalkController::class, 'exportReportingExcel'])->name('export-reporting-excel');
});

// Toolbox Talk Topics Routes
Route::prefix('toolbox-topics')->name('toolbox-topics.')->group(function () {
    Route::get('/', [ToolboxTalkTopicController::class, 'index'])->name('index');
    Route::get('/library', [ToolboxTalkTopicController::class, 'library'])->name('library');
    Route::get('/create', [ToolboxTalkTopicController::class, 'create'])->name('create');
    Route::post('/', [ToolboxTalkTopicController::class, 'store'])->name('store');
    Route::get('/{topic}', [ToolboxTalkTopicController::class, 'show'])->name('show');
    Route::get('/{topic}/edit', [ToolboxTalkTopicController::class, 'edit'])->name('edit');
    Route::put('/{topic}', [ToolboxTalkTopicController::class, 'update'])->name('update');
    Route::delete('/{topic}', [ToolboxTalkTopicController::class, 'destroy'])->name('destroy');
    
    // Specialized routes
    Route::post('/{topic}/duplicate', [ToolboxTalkTopicController::class, 'duplicate'])->name('duplicate');
});

// Safety Communication Routes
Route::prefix('safety-communications')->name('safety-communications.')->group(function () {
    Route::get('/', [SafetyCommunicationController::class, 'index'])->name('index');
    Route::get('/dashboard', [SafetyCommunicationController::class, 'dashboard'])->name('dashboard');
    Route::get('/create', [SafetyCommunicationController::class, 'create'])->name('create');
    Route::post('/', [SafetyCommunicationController::class, 'store'])->name('store');
    Route::get('/{communication}', [SafetyCommunicationController::class, 'show'])->name('show');
    Route::get('/{communication}/edit', [SafetyCommunicationController::class, 'edit'])->name('edit');
    Route::put('/{communication}', [SafetyCommunicationController::class, 'update'])->name('update');
    Route::delete('/{communication}', [SafetyCommunicationController::class, 'destroy'])->name('destroy');
    
    // Specialized routes
    Route::post('/{communication}/send', [SafetyCommunicationController::class, 'send'])->name('send');
    Route::post('/{communication}/duplicate', [SafetyCommunicationController::class, 'duplicate'])->name('duplicate');
});

// Incident Management Routes
Route::prefix('incidents')->name('incidents.')->group(function () {
    Route::get('/', [IncidentController::class, 'index'])->name('index');
    Route::get('/dashboard', [IncidentController::class, 'dashboard'])->name('dashboard');
    Route::get('/trend-analysis', [IncidentController::class, 'trendAnalysis'])->name('trend-analysis');
    Route::get('/create', [IncidentController::class, 'create'])->name('create');
    Route::post('/', [IncidentController::class, 'store'])->name('store');
    Route::get('/{incident}', [IncidentController::class, 'show'])->name('show');
    Route::get('/{incident}/edit', [IncidentController::class, 'edit'])->name('edit');
    Route::put('/{incident}', [IncidentController::class, 'update'])->name('update');
    Route::delete('/{incident}', [IncidentController::class, 'destroy'])->name('destroy');
    
    // Specialized routes
    Route::post('/{incident}/assign', [IncidentController::class, 'assign'])->name('assign');
    Route::post('/{incident}/investigate', [IncidentController::class, 'investigate'])->name('investigate');
    Route::post('/{incident}/close', [IncidentController::class, 'close'])->name('close');
    Route::post('/{incident}/reopen', [IncidentController::class, 'reopen'])->name('reopen');
    Route::post('/{incident}/request-closure', [IncidentController::class, 'requestClosure'])->name('request-closure');
    Route::post('/{incident}/approve-closure', [IncidentController::class, 'approveClosure'])->name('approve-closure');
    Route::post('/{incident}/reject-closure', [IncidentController::class, 'rejectClosure'])->name('reject-closure');
    
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

// Administrative Module Routes
Route::prefix('admin')->name('admin.')->group(function () {
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
