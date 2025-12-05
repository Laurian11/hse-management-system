# HSE Management System - Complete System Overview

## 📊 Table of Contents
1. [Database Table Relationships](#database-table-relationships)
2. [Data Automation (Triggers & Observers)](#data-automation-triggers--observers)
3. [Email Auto Reminders & Notifications](#email-auto-reminders--notifications)
4. [Auto Assignments](#auto-assignments)
5. [Scheduled Tasks](#scheduled-tasks)

---

## 🗄️ Database Table Relationships

### Core Multi-Tenant Structure

```
Company (Root Entity)
├── hasMany → Users
├── hasMany → Departments
├── hasMany → All Business Entities (company_id foreign key)
└── All tables are company-scoped
```

### Complete Relationship Map

#### 1. User Management Module

**User** (`users`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `Role` (role)
- `belongsTo` → `Department` (department)
- `belongsTo` → `User` (directSupervisor) - Self-referential
- `hasMany` → `User` (subordinates) - Self-referential
- `hasMany` → `ActivityLog` (activityLogs)
- `hasMany` → `UserSession` (userSessions)
- `hasMany` → `ToolboxTalk` (as supervisor)
- `hasMany` → `ToolboxTalkAttendance` (attendances)
- `hasMany` → `ToolboxTalkFeedback` (feedbacks)
- `hasMany` → `Incident` (reportedIncidents, assignedIncidents)
- `hasMany` → `TrainingRecord` (trainingRecords)
- `hasMany` → `TrainingCertificate` (certificates)
- `hasMany` → `TrainingAttendance` (trainingAttendances)
- `hasMany` → `CompetencyAssessment` (competencyAssessments)
- `belongsTo` → `JobCompetencyMatrix` (jobCompetencyMatrix)
- **Referenced by:** 20+ models (reported_by, assigned_to, approved_by, etc.)

**Department** (`departments`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `Department` (parentDepartment) - Self-referential
- `hasMany` → `Department` (childDepartments) - Self-referential
- `belongsTo` → `User` (headOfDepartment)
- `belongsTo` → `User` (hseOfficer)
- `hasMany` → `User` (employees)
- `hasMany` → `ToolboxTalk` (toolboxTalks)
- `hasMany` → `Incident` (incidents)
- `hasMany` → `Hazard` (hazards)
- `hasMany` → `RiskAssessment` (riskAssessments)
- `hasMany` → `JSA` (jsas)
- `hasMany` → `CAPA` (capas)

#### 2. Incident Management Module

**Incident** (`incidents`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (reporter)
- `belongsTo` → `User` (assignedTo)
- `belongsTo` → `User` (approvedBy)
- `belongsTo` → `Department` (department)
- `belongsTo` → `Hazard` (relatedHazard)
- `belongsTo` → `RiskAssessment` (relatedRiskAssessment)
- `belongsTo` → `JSA` (relatedJSA)
- `hasOne` → `IncidentInvestigation` (investigation)
- `hasMany` → `IncidentInvestigation` (investigations)
- `hasOne` → `RootCauseAnalysis` (rootCauseAnalysis)
- `hasMany` → `CAPA` (capas)
- `hasMany` → `ControlMeasure` (controlMeasures)
- `hasMany` → `IncidentAttachment` (attachments)

**IncidentInvestigation** (`incident_investigations`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `Incident` (incident)
- `belongsTo` → `User` (investigator)
- `belongsTo` → `User` (assignedBy)

**RootCauseAnalysis** (`root_cause_analyses`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `Incident` (incident)
- `belongsTo` → `IncidentInvestigation` (investigation)
- `belongsTo` → `User` (createdBy)
- `belongsTo` → `User` (approvedBy)

**CAPA** (`capas`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `Incident` (incident)
- `belongsTo` → `RootCauseAnalysis` (rootCauseAnalysis)
- `belongsTo` → `Department` (department)
- `belongsTo` → `User` (assignedTo)
- `belongsTo` → `User` (assignedBy)
- `belongsTo` → `User` (verifiedBy)
- `belongsTo` → `User` (closedBy)
- `belongsTo` → `TrainingNeedsAnalysis` (relatedTrainingNeed)

#### 3. Risk Assessment Module

**Hazard** (`hazards`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `Department` (department)
- `belongsTo` → `User` (createdBy)
- `hasMany` → `RiskAssessment` (riskAssessments)

**RiskAssessment** (`risk_assessments`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `Department` (department)
- `belongsTo` → `Hazard` (hazard)
- `belongsTo` → `User` (createdBy)
- `belongsTo` → `User` (assignedTo)
- `belongsTo` → `User` (approvedBy)
- `hasMany` → `ControlMeasure` (controlMeasures)
- `hasMany` → `RiskReview` (riskReviews)
- `hasMany` → `JSA` (jsas)

**ControlMeasure** (`control_measures`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `RiskAssessment` (riskAssessment)
- `belongsTo` → `Department` (department)
- `belongsTo` → `User` (assignedTo)
- `belongsTo` → `User` (responsibleParty)
- `belongsTo` → `User` (verifiedBy)
- `belongsTo` → `TrainingNeedsAnalysis` (relatedTrainingNeed)

**JSA** (`jsas`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `Department` (department)
- `belongsTo` → `RiskAssessment` (riskAssessment)
- `belongsTo` → `User` (createdBy)
- `belongsTo` → `User` (supervisor)
- `belongsTo` → `User` (approvedBy)

**RiskReview** (`risk_reviews`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `RiskAssessment` (riskAssessment)
- `belongsTo` → `User` (reviewedBy)
- `belongsTo` → `User` (assignedTo)
- `belongsTo` → `User` (approvedBy)

#### 4. Training & Competency Module

**TrainingNeedsAnalysis** (`training_needs_analyses`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (createdBy)
- `belongsTo` → `Incident` (triggeredByIncident)
- `belongsTo` → `RootCauseAnalysis` (triggeredByRCA)
- `belongsTo` → `CAPA` (triggeredByCAPA)
- `belongsTo` → `ControlMeasure` (triggeredByControlMeasure)
- `belongsTo` → `User` (triggeredByUser)
- `belongsTo` → `JobCompetencyMatrix` (triggeredByJobMatrix)
- `hasMany` → `TrainingPlan` (trainingPlans)

**TrainingPlan** (`training_plans`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `TrainingNeedsAnalysis` (trainingNeedsAnalysis)
- `belongsTo` → `User` (createdBy)
- `hasMany` → `TrainingSession` (trainingSessions)
- `hasMany` → `TrainingMaterial` (trainingMaterials)

**TrainingSession** (`training_sessions`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `TrainingPlan` (trainingPlan)
- `belongsTo` → `User` (instructor)
- `hasMany` → `TrainingAttendance` (attendances)
- `hasMany` → `TrainingRecord` (trainingRecords)

**TrainingRecord** (`training_records`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (user)
- `belongsTo` → `TrainingSession` (trainingSession)
- `belongsTo` → `TrainingPlan` (trainingPlan)

**TrainingCertificate** (`training_certificates`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (user)
- `belongsTo` → `TrainingRecord` (trainingRecord)

**JobCompetencyMatrix** (`job_competency_matrices`)
- `belongsTo` → `Company` (company)
- `hasMany` → `User` (users)

#### 5. Toolbox Talk Module

**ToolboxTalk** (`toolbox_talks`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `Department` (department)
- `belongsTo` → `User` (supervisor)
- `belongsTo` → `ToolboxTalkTopic` (topic)
- `hasMany` → `ToolboxTalkAttendance` (attendances)
- `hasMany` → `ToolboxTalkFeedback` (feedbacks)

**ToolboxTalkTopic** (`toolbox_talk_topics`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (createdBy)
- `belongsTo` → `User` (representer)
- `hasMany` → `ToolboxTalk` (talks)

**ToolboxTalkAttendance** (`toolbox_talk_attendances`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `ToolboxTalk` (toolboxTalk)
- `belongsTo` → `User` (user)

**ToolboxTalkFeedback** (`toolbox_talk_feedbacks`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `ToolboxTalk` (toolboxTalk)
- `belongsTo` → `User` (user)

#### 6. PPE Management Module

**PPESupplier** (`ppe_suppliers`)
- `belongsTo` → `Company` (company)
- `hasMany` → `PPEItem` (ppeItems)

**PPEItem** (`ppe_items`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `PPESupplier` (supplier)
- `hasMany` → `PPEIssuance` (issuances)
- `hasMany` → `PPEInspection` (inspections)

**PPEIssuance** (`ppe_issuances`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `PPEItem` (ppeItem)
- `belongsTo` → `User` (issuedTo)
- `belongsTo` → `User` (issuedBy)
- `belongsTo` → `Department` (department)
- `hasMany` → `PPEInspection` (inspections)

**PPEInspection** (`ppe_inspections`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `PPEIssuance` (ppeIssuance)
- `belongsTo` → `PPEItem` (ppeItem)
- `belongsTo` → `User` (inspectedBy)
- `belongsTo` → `User` (user)

**PPEComplianceReport** (`ppe_compliance_reports`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (generatedBy)

#### 7. Safety Communications Module

**SafetyCommunication** (`safety_communications`)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (createdBy)

---

## ⚙️ Data Automation (Triggers & Observers)

### Model Observers

Observers are registered in `app/Providers/AppServiceProvider.php`:

#### 1. ControlMeasureObserver
**File:** `app/Observers/ControlMeasureObserver.php`

**Triggers:**
- `created` - When control measure is created
- `updated` - When control measure is updated

**Actions:**
- If `control_type === 'administrative'` → Auto-creates Training Need via `TNAEngineService`
- Links training need back to control measure via `related_training_need_id`

**Code:**
```php
public function created(ControlMeasure $controlMeasure): void
{
    if ($controlMeasure->control_type === 'administrative') {
        $this->tnaEngine->processControlMeasureTrigger($controlMeasure);
    }
}
```

#### 2. RootCauseAnalysisObserver
**File:** `app/Observers/RootCauseAnalysisObserver.php`

**Triggers:**
- `updated` - When RCA is updated

**Actions:**
- If `training_gap_identified` changed to `true` → Auto-creates Training Need via `TNAEngineService`
- Links training need to incident and RCA

**Code:**
```php
public function updated(RootCauseAnalysis $rootCauseAnalysis): void
{
    if ($rootCauseAnalysis->wasChanged('training_gap_identified') && 
        $rootCauseAnalysis->training_gap_identified) {
        $this->tnaEngine->processRCATrigger($rootCauseAnalysis);
    }
}
```

#### 3. CAPAObserver
**File:** `app/Observers/CAPAObserver.php`

**Triggers:**
- `created` - When CAPA is created

**Actions:**
- Checks if CAPA title/description contains "training" keywords
- If yes → Auto-creates Training Need via `TNAEngineService`
- Links training need to CAPA, incident, and RCA

**Code:**
```php
public function created(CAPA $capa): void
{
    $this->tnaEngine->processCAPATrigger($capa);
}
```

#### 4. UserObserver
**File:** `app/Observers/UserObserver.php`

**Triggers:**
- `created` - When user is created
- `updated` - When user is updated

**Actions:**
- If user has `job_competency_matrix_id` → Creates Training Needs for all mandatory trainings
- If `job_competency_matrix_id` changed → Creates new training needs based on new matrix
- Compares with old matrix to avoid duplicates

**Code:**
```php
public function created(User $user): void
{
    if ($user->job_competency_matrix_id) {
        $this->tnaEngine->processUserJobChangeTrigger($user);
    }
}

public function updated(User $user): void
{
    if ($user->wasChanged('job_competency_matrix_id') && $user->job_competency_matrix_id) {
        $this->tnaEngine->processUserJobChangeTrigger($user, $oldMatrix);
    }
}
```

### Model Events (booted() methods)

Many models have `booted()` methods that trigger on create/update/delete:

**Examples:**
- `User` - Logs activity on create/update/delete
- `PPEItem` - Generates reference number on create
- `PPEIssuance` - Updates stock quantities on create/update
- `PPEInspection` - Updates issuance last_inspection_date on create
- `Incident` - Generates reference number, logs activity
- All models log activities via `ActivityLog::log()`

---

## 📧 Email Auto Reminders & Notifications

### Notification Classes

All notifications implement `ShouldQueue` for background processing.

#### 1. TopicCreatedNotification
**File:** `app/Notifications/TopicCreatedNotification.php`

**Trigger:** When `ToolboxTalkTopic` is created

**Recipients:**
- All users with role `hse_officer`
- Department HSE officers (from `department.hse_officer_id`)

**Content:**
- Topic title, category, difficulty level
- Estimated duration
- Representer information
- Topic description
- Link to view topic

**Implementation:**
```php
// In ToolboxTalkTopicController@store
$hseOfficers = User::whereHas('role', function($q) {
    $q->where('name', 'hse_officer');
})->orWhereHas('department', function($q) {
    $q->whereNotNull('hse_officer_id');
})->get();

foreach ($hseOfficers as $officer) {
    $officer->notify(new TopicCreatedNotification($topic));
}
```

#### 2. TalkReminderNotification
**File:** `app/Notifications/TalkReminderNotification.php`

**Trigger:** Scheduled via command or cron job

**Types:**
- `24h` - 24 hours before talk
- `1h` - 1 hour before talk
- `scheduled` - When talk is scheduled

**Recipients:**
- Talk supervisor
- Department employees (if department assigned)

**Content:**
- Talk title
- Scheduled date and time
- Location, duration
- Description
- Link to view talk
- Biometric requirement notice

**Command:**
```bash
php artisan talks:send-reminders --type=24h
php artisan talks:send-reminders --type=1h
```

#### 3. TrainingSessionScheduledNotification
**File:** `app/Notifications/TrainingSessionScheduledNotification.php`

**Trigger:** When training session is created/scheduled

**Recipients:**
- All registered participants
- Instructor

**Content:**
- Session title and reference
- Scheduled date and time
- Duration
- Location
- Training plan details
- Instructor information

#### 4. CertificateExpiryAlert (via Service)
**Service:** `app/Services/CertificateExpiryAlertService.php`

**Trigger:** Scheduled daily at 8:00 AM

**Alert Types:**
- **60 Days:** Early warning
- **30 Days:** Urgent reminder
- **7 Days:** Final warning
- **Expired:** Auto-revocation notice

**Recipients:**
- Certificate holder (user)
- Direct supervisor
- HSE manager (if 30 days or less)

**Implementation:**
```php
// Scheduled in routes/console.php
Schedule::call(function () {
    app(CertificateExpiryAlertService::class)->checkAndSendAlerts();
})->daily()->at('08:00');
```

#### 5. IncidentReportedNotification
**File:** `app/Notifications/IncidentReportedNotification.php`

**Trigger:** When incident is reported

**Recipients:**
- Assigned investigator
- HSE manager
- Department head

**Content:**
- Incident details
- Severity and location
- Link to view incident

#### 6. CAPAAssignedNotification
**File:** `app/Notifications/CAPAAssignedNotification.php`

**Trigger:** When CAPA is assigned to user

**Recipients:**
- Assigned user
- Supervisor

**Content:**
- CAPA details
- Due date and priority
- Link to view CAPA

#### 7. RiskAssessmentApprovalRequiredNotification
**File:** `app/Notifications/RiskAssessmentApprovalRequiredNotification.php`

**Trigger:** When risk assessment requires approval

**Recipients:**
- Approver

**Content:**
- Assessment details
- Link to approve

#### 8. ControlMeasureVerificationRequiredNotification
**File:** `app/Notifications/ControlMeasureVerificationRequiredNotification.php`

**Trigger:** When control measure requires verification

**Recipients:**
- Verifier

**Content:**
- Control measure details
- Verification link

### Email Configuration

**Development (Log Mode):**
```env
MAIL_MAILER=log
```

**Production (SMTP):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hesu.co.tz
MAIL_PORT=587
MAIL_USERNAME=noreply@hesu.co.tz
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

**Queue Configuration:**
```env
QUEUE_CONNECTION=database  # or redis
```

---

## 🤖 Auto Assignments

### TNA Engine Service

**File:** `app/Services/TNAEngineService.php`

The TNA (Training Needs Analysis) Engine automatically creates training needs based on various triggers:

#### 1. Control Measure → Training Need
**Trigger:** Control measure created/updated with `control_type = 'administrative'`

**Process:**
```php
public function processControlMeasureTrigger(ControlMeasure $controlMeasure)
{
    // Determine priority based on risk level
    $priority = match($riskLevel) {
        'extreme', 'critical' => 'critical',
        'high' => 'high',
        default => 'medium',
    };
    
    // Create training need
    $tna = TrainingNeedsAnalysis::create([
        'trigger_source' => 'risk_assessment',
        'triggered_by_control_measure_id' => $controlMeasure->id,
        'training_title' => "Training for: {$controlMeasure->title}",
        'priority' => $priority,
        'is_mandatory' => true,
        // ...
    ]);
}
```

#### 2. RCA → Training Need
**Trigger:** Root Cause Analysis updated with `training_gap_identified = true`

**Process:**
```php
public function processRCATrigger(RootCauseAnalysis $rca)
{
    // Determine priority based on incident severity
    $priority = match($incident->severity) {
        'critical' => 'critical',
        'high' => 'high',
        default => 'medium',
    };
    
    // Create training need
    $tna = TrainingNeedsAnalysis::create([
        'trigger_source' => 'incident_rca',
        'triggered_by_rca_id' => $rca->id,
        'training_title' => "Training Gap Identified: ...",
        'priority' => $priority,
        // ...
    ]);
}
```

#### 3. CAPA → Training Need
**Trigger:** CAPA created with training keywords in title/description

**Process:**
```php
public function processCAPATrigger(CAPA $capa)
{
    // Check if CAPA is for training
    $isTrainingCAPA = stripos($capa->title, 'training') !== false ||
                      stripos($capa->description, 'training') !== false;
    
    if ($isTrainingCAPA) {
        // Create training need
        $tna = TrainingNeedsAnalysis::create([
            'trigger_source' => 'incident_rca',
            'triggered_by_capa_id' => $capa->id,
            'training_title' => $capa->title,
            'priority' => $capa->priority,
            // ...
        ]);
    }
}
```

#### 4. New Hire/Job Change → Training Need
**Trigger:** User created/updated with `job_competency_matrix_id`

**Process:**
```php
public function processUserJobChangeTrigger(User $user, ?JobCompetencyMatrix $oldMatrix = null)
{
    $matrix = $user->jobCompetencyMatrix;
    $mandatoryTrainings = $matrix->mandatory_trainings ?? [];
    
    // Create training needs for missing mandatory trainings
    foreach ($mandatoryTrainings as $trainingId) {
        $tna = TrainingNeedsAnalysis::create([
            'trigger_source' => $oldMatrix ? 'job_role_change' : 'new_hire',
            'triggered_by_user_id' => $user->id,
            'training_title' => "Mandatory Training for {$user->job_title}",
            'target_user_ids' => [$user->id],
            'is_mandatory' => true,
            // ...
        ]);
    }
}
```

#### 5. Certificate Expiry → Training Need
**Trigger:** Certificate expiring within 60 days

**Process:**
```php
public function processCertificateExpiryTrigger($certificate)
{
    $daysUntilExpiry = $certificate->daysUntilExpiry();
    
    if ($daysUntilExpiry <= 60) {
        $tna = TrainingNeedsAnalysis::create([
            'trigger_source' => 'certificate_expiry',
            'triggered_by_user_id' => $certificate->user_id,
            'training_title' => "Refresher Training: {$certificate->certificate_title}",
            'priority' => $daysUntilExpiry <= 30 ? 'critical' : 'high',
            'target_user_ids' => [$certificate->user_id],
            // ...
        ]);
    }
}
```

### Auto-Assignment Rules

1. **Training Needs** - Automatically assigned to:
   - Target users (from `target_user_ids`)
   - Department employees (from `target_departments`)
   - Based on job competency matrix

2. **Incident Assignment** - Can be auto-assigned to:
   - Department HSE officer
   - Based on incident type/severity

3. **CAPA Assignment** - Assigned to:
   - User specified in CAPA
   - Department head (if not specified)

---

## ⏰ Scheduled Tasks

**File:** `routes/console.php`

### 1. Certificate Expiry Alerts
**Schedule:** Daily at 8:00 AM
```php
Schedule::call(function () {
    app(CertificateExpiryAlertService::class)->checkAndSendAlerts();
})->daily()->at('08:00')->name('training.certificate-expiry-alerts');
```

**Actions:**
- Checks certificates expiring in 60, 30, 7 days
- Sends alerts to users, supervisors, HSE managers
- Creates refresher training needs for expired certificates

### 2. Expired Certificate Revocation
**Schedule:** Daily at 9:00 AM
```php
Schedule::call(function () {
    app(CertificateExpiryAlertService::class)->revokeExpiredCertificates();
})->daily()->at('09:00')->name('training.revoke-expired-certificates');
```

**Actions:**
- Auto-revokes expired certificates
- Marks status as 'expired'
- Logs work restriction warnings

### 3. PPE Management Alerts
**Schedule:** Daily at 8:30 AM
```php
Schedule::call(function () {
    $service = app(\App\Services\PPEAlertService::class);
    
    // Process alerts for all companies
    $companies = \App\Models\Company::all();
    foreach ($companies as $company) {
        $service->checkAndSendExpiryAlerts($company->id);
        $service->checkAndSendLowStockAlerts($company->id);
        $service->checkAndSendInspectionAlerts($company->id);
    }
    
    // Update expired issuances
    $service->updateExpiredIssuances();
})->daily()->at('08:30')->name('ppe.alerts-and-updates');
```

**Actions:**
- Checks PPE items expiring within 7 days
- Alerts for low stock items
- Alerts for overdue inspections
- Auto-updates expired issuances

### 4. Toolbox Talk Reminders (Manual Command)
**Command:** `php artisan talks:send-reminders --type=24h`

**Can be scheduled via cron:**
```bash
# 24-hour reminders daily at 9 AM
0 9 * * * cd /path && php artisan talks:send-reminders --type=24h

# 1-hour reminders every hour
0 * * * * cd /path && php artisan talks:send-reminders --type=1h
```

**Actions:**
- Sends email reminders for upcoming toolbox talks
- 24 hours before and 1 hour before
- Notifies supervisor and department employees

### Cron Job Setup

**Linux/Mac:**
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

**Windows Task Scheduler:**
- Create task to run: `php artisan schedule:run`
- Set to run every minute

---

## 📋 Summary

### Total System Components

- **Models:** 38+ Eloquent models
- **Observers:** 4 active observers
- **Notifications:** 7 notification classes
- **Scheduled Tasks:** 3 daily scheduled tasks
- **Auto-Assignment Services:** 1 TNA Engine Service
- **Alert Services:** 2 alert services (Certificate, PPE)

### Automation Flow

```
User Action / Scheduled Task
    ↓
Observer / Service Triggered
    ↓
Auto-Create Training Need / Send Alert
    ↓
Email Notification (Queued)
    ↓
User Receives Notification
```

### Key Automation Points

1. **Control Measure (Administrative)** → Training Need
2. **RCA (Training Gap)** → Training Need
3. **CAPA (Training Keywords)** → Training Need
4. **User (Job Matrix)** → Training Needs (Mandatory)
5. **Certificate Expiry** → Training Need (Refresher)
6. **Certificate Expiry** → Email Alerts (60/30/7 days)
7. **PPE Expiry** → Email Alerts (7 days)
8. **PPE Low Stock** → Email Alerts
9. **Toolbox Talk** → Email Reminders (24h/1h)

---

**Last Updated:** December 2025
**System Version:** 1.0.0

