# HSE Management System - Comprehensive Documentation

## 📋 Table of Contents

1. [Full System Data Automation Flow](#full-system-data-automation-flow)
2. [Email Notification System](#email-notification-system)
3. [Toolbox Bulk Import](#toolbox-bulk-import)
4. [Database Table Relationships](#database-table-relationships)

---

## 🔄 Full System Data Automation Flow

### Overview

The HSE Management System implements a **closed-loop operational workflow** where modules automatically trigger actions in other modules, creating a seamless data flow from incident identification through resolution and verification.

### Core Automation Principles

1. **Event-Driven Architecture**: Model Observers trigger automatic actions
2. **Service Layer**: Business logic encapsulated in services
3. **Scheduled Tasks**: Cron jobs for periodic automation
4. **Feedback Loops**: Output from one module feeds back to source modules

---

### 1. Incident → Investigation → RCA → CAPA → Training Flow

```
┌─────────────────┐
│   Incident      │ (Reported)
│   Reported      │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Investigation   │ (Auto-assigned or manual)
│ Created         │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Root Cause      │ (Analysis completed)
│ Analysis (RCA)  │
└────────┬────────┘
         │
         ├─► If training_gap_identified = true
         │   │
         │   ▼
         │   ┌─────────────────┐
         │   │ Training Need   │ (Auto-created via Observer)
         │   │ Auto-Created     │
         │   └─────────────────┘
         │
         ▼
┌─────────────────┐
│ CAPA Created    │ (Corrective/Preventive Action)
└────────┬────────┘
         │
         ├─► If training-related keywords detected
         │   │
         │   ▼
         │   ┌─────────────────┐
         │   │ Training Need   │ (Auto-created via Observer)
         │   │ Auto-Created     │
         │   └─────────────────┘
         │
         ▼
┌─────────────────┐
│ Training Plan   │ (Created from Training Need)
│ Created         │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Training        │ (Scheduled & Conducted)
│ Session         │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Competency      │ (Assessment completed)
│ Assessment      │
└────────┬────────┘
         │
         ├─► If Passed
         │   │
         │   ▼
         │   ┌─────────────────┐
         │   │ Certificate     │ (Auto-issued)
         │   │ Issued          │
         │   └─────────────────┘
         │
         ▼
┌─────────────────┐
│ CAPA Training   │ (Auto-updated)
│ Completed       │
│ training_verified = true
└─────────────────┘
```

**Automation Points:**
- `RootCauseAnalysisObserver` → Detects training gap → Creates TNA
- `CAPAObserver` → Detects training keywords → Creates TNA
- `TrainingSessionController` → Competency passed → Issues certificate
- `TrainingRecord` → Updates CAPA `training_completed = true`

---

### 2. Risk Assessment → Control Measure → Training Flow

```
┌─────────────────┐
│ Risk Assessment │ (Created)
│ Created         │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Control Measure │ (Created with control_type = 'administrative')
│ Created         │
└────────┬────────┘
         │
         ├─► If control_type = 'administrative'
         │   │
         │   ▼
         │   ┌─────────────────┐
         │   │ Training Need   │ (Auto-created via ControlMeasureObserver)
         │   │ Auto-Created     │
         │   └─────────────────┘
         │
         ▼
┌─────────────────┐
│ Training Plan   │ (Created & Approved)
│ Created         │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Training        │ (Completed)
│ Completed       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Control Measure │ (Auto-updated)
│ Training        │
│ Verified        │
│ training_verified = true
└─────────────────┘
```

**Automation Points:**
- `ControlMeasureObserver` → Detects administrative control → Creates TNA
- `TrainingRecord` → Updates control measure `training_verified = true`

---

### 3. Certificate Expiry → Refresher Training Flow

```
┌─────────────────┐
│ Certificate     │ (Expiring within 60 days)
│ Expiry Alert    │
└────────┬────────┘
         │
         ├─► Scheduled Task (Daily 8:00 AM)
         │   │
         │   ▼
         │   ┌─────────────────┐
         │   │ Certificate     │ (Checks expiry)
         │   │ Expiry Service  │
         │   └────────┬────────┘
         │            │
         │            ├─► 60 days: Alert sent
         │            ├─► 30 days: Alert sent
         │            ├─► 7 days: Alert sent
         │            │
         │            ▼
         │   ┌─────────────────┐
         │   │ Training Need   │ (Auto-created for refresher)
         │   │ Auto-Created     │
         │   └─────────────────┘
         │
         ▼
┌─────────────────┐
│ Certificate     │ (Expired)
│ Auto-Revoked    │
│ (Daily 9:00 AM) │
└─────────────────┘
```

**Automation Points:**
- `CertificateExpiryAlertService` (Scheduled) → Creates refresher TNA
- `CertificateExpiryAlertService` (Scheduled) → Auto-revokes expired certificates

---

### 4. New Hire/Job Change → Training Flow

```
┌─────────────────┐
│ User Created    │ (With job_competency_matrix_id)
│ or Job Changed  │
└────────┬────────┘
         │
         ├─► UserObserver triggered
         │   │
         │   ▼
         │   ┌─────────────────┐
         │   │ TNA Engine      │ (Processes job matrix)
         │   │ Service          │
         │   └────────┬────────┘
         │            │
         │            ├─► For each mandatory training in matrix
         │            │   │
         │            ▼
         │   ┌─────────────────┐
         │   │ Training Need   │ (Auto-created)
         │   │ Created         │
         │   └─────────────────┘
         │
         ▼
┌─────────────────┐
│ Training Plans  │ (Created for mandatory trainings)
│ Created         │
└─────────────────┘
```

**Automation Points:**
- `UserObserver` → Detects job matrix assignment → Creates TNAs for mandatory trainings

---

### 5. Scheduled Automation Tasks

**File:** `routes/console.php`

```php
// Daily Certificate Expiry Alerts (8:00 AM)
Schedule::call(function () {
    $service = app(CertificateExpiryAlertService::class);
    $service->checkAndSendAlerts();
})->dailyAt('08:00');

// Daily Expired Certificate Revocation (9:00 AM)
Schedule::call(function () {
    $service = app(CertificateExpiryAlertService::class);
    $service->revokeExpiredCertificates();
})->dailyAt('09:00');
```

**Automation Points:**
- Certificate expiry alerts (60, 30, 7 days)
- Auto-revocation of expired certificates
- Refresher training need creation

---

### 6. Observer-Based Automation

**Model Observers:**

1. **ControlMeasureObserver**
   - Triggers: `created`, `updated`
   - Action: Creates TNA if `control_type = 'administrative'`

2. **RootCauseAnalysisObserver**
   - Triggers: `created`, `updated`
   - Action: Creates TNA if `training_gap_identified = true`

3. **CAPAObserver**
   - Triggers: `created`, `updated`
   - Action: Creates TNA if training keywords detected

4. **UserObserver**
   - Triggers: `created`, `updated`
   - Action: Creates TNAs for mandatory trainings when job matrix assigned

---

### 7. Service Layer Automation

**TNAEngineService** (`app/Services/TNAEngineService.php`)

Methods:
- `processControlMeasureTrigger()` - Creates TNA from control measure
- `processRCATrigger()` - Creates TNA from RCA
- `processCAPATrigger()` - Creates TNA from CAPA
- `processUserJobChangeTrigger()` - Creates TNAs from job matrix
- `processCertificateExpiryTrigger()` - Creates refresher TNA

**CertificateExpiryAlertService** (`app/Services/CertificateExpiryAlertService.php`)

Methods:
- `checkAndSendAlerts()` - Checks and sends expiry alerts
- `revokeExpiredCertificates()` - Auto-revokes expired certificates
- `sendExpiryAlert()` - Sends alerts to users, supervisors, HSE managers

---

## 📧 Email Notification System

### Current Email Notifications

#### 1. Toolbox Talk Topic Created

**Trigger:** When a new `ToolboxTalkTopic` is created

**Notification Class:** `App\Notifications\TopicCreatedNotification`

**Recipients:**
- All users with role `hse_officer`
- Department HSE officers (from `department.hse_officer_id`)

**Content:**
- Topic title
- Category
- Difficulty level
- Estimated duration
- Representer information
- Topic description
- Link to view topic

**Implementation:**
```php
// In ToolboxTalkTopicController@store
$topic = ToolboxTalkTopic::create([...]);

// Notify HSE officers
$hseOfficers = User::whereHas('role', function($q) {
    $q->where('name', 'hse_officer');
})->orWhereHas('department', function($q) {
    $q->whereNotNull('hse_officer_id');
})->get();

foreach ($hseOfficers as $officer) {
    $officer->notify(new TopicCreatedNotification($topic));
}
```

---

#### 2. Toolbox Talk Reminder

**Trigger:** Scheduled via cron job or command

**Notification Class:** `App\Notifications\TalkReminderNotification`

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
- Location
- Duration
- Description
- Link to view talk
- Biometric requirement notice

**Command:**
```bash
php artisan talks:send-reminders --type=24h
php artisan talks:send-reminders --type=1h
```

**Scheduled:**
```bash
# Cron job (daily at 9 AM)
0 9 * * * cd /path && php artisan talks:send-reminders --type=24h

# Cron job (every hour)
0 * * * * cd /path && php artisan talks:send-reminders --type=1h
```

---

#### 3. Certificate Expiry Alerts

**Trigger:** Scheduled daily at 8:00 AM

**Service:** `CertificateExpiryAlertService`

**Alert Types:**
- **60 Days:** Early warning
- **30 Days:** Urgent reminder
- **7 Days:** Final warning
- **Expired:** Auto-revocation notice

**Recipients:**
- Certificate holder (user)
- Direct supervisor
- HSE manager

**Implementation:**
```php
// Scheduled in routes/console.php
Schedule::call(function () {
    $service = app(CertificateExpiryAlertService::class);
    $service->checkAndSendAlerts();
})->dailyAt('08:00');
```

**Email Content:**
- Certificate details
- Expiry date
- Days remaining
- Action required
- Link to view certificate

---

### Email Configuration

#### Development (Log Mode)
```env
MAIL_MAILER=log
```

#### Production (SMTP)
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

#### Production (Mailgun)
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=hesu.co.tz
MAILGUN_SECRET=your-secret
MAILGUN_ENDPOINT=api.mailgun.net
MAIL_FROM_ADDRESS=noreply@hesu.co.tz
MAIL_FROM_NAME="HSE Management System"
```

---

### Queue Configuration

All notifications implement `ShouldQueue` for background processing.

**Database Queue (Default):**
```env
QUEUE_CONNECTION=database
```

**Setup:**
```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

**Redis Queue (Recommended for Production):**
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

### Missing Email Notifications (Potential Enhancements)

1. **Incident Reported Notification**
   - To: Assigned investigator, HSE manager
   - Content: Incident details, severity, location

2. **CAPA Assigned Notification**
   - To: Assigned user, supervisor
   - Content: CAPA details, due date, priority

3. **Training Session Scheduled Notification**
   - To: Participants, instructor
   - Content: Session details, date, location

4. **Risk Assessment Approval Required**
   - To: Approver
   - Content: Assessment details, link to approve

5. **Control Measure Verification Required**
   - To: Verifier
   - Content: Control measure details, verification link

---

## 📥 Toolbox Bulk Import

### Overview

The system supports bulk import of toolbox talks from CSV files, allowing administrators to import multiple talks at once.

### Implementation

**Controller Method:** `ToolboxTalkController@bulkImport`

**Route:** `POST /toolbox-talks/bulk-import`

**File:** `app/Http/Controllers/ToolboxTalkController.php` (lines 1051-1114)

### CSV Format

**Required Columns:**
1. `title` - Talk title
2. `description` - Talk description (optional)
3. `scheduled_date` - Date (YYYY-MM-DD)
4. `start_time` - Time (HH:MM)
5. `duration_minutes` - Duration in minutes
6. `location` - Location name
7. `talk_type` - Type (safety, health, environment, etc.)
8. `department_id` - Department ID (optional)
9. `supervisor_id` - Supervisor user ID (optional)
10. `biometric_required` - Boolean (0/1, optional)

**CSV Example:**
```csv
title,description,scheduled_date,start_time,duration_minutes,location,talk_type,department_id,supervisor_id,biometric_required
"Fire Safety","Fire safety procedures and evacuation",2025-12-15,09:00,30,"Main Hall",safety,1,5,1
"First Aid Basics","Basic first aid training",2025-12-16,10:00,45,"Training Room",health,2,6,0
```

### Import Process

```php
public function bulkImport(Request $request)
{
    // 1. Validate file
    $request->validate([
        'file' => 'required|mimes:csv,txt|max:5120',
    ]);

    // 2. Get company ID (multi-tenant isolation)
    $companyId = Auth::user()->company_id;

    // 3. Read CSV file
    $handle = fopen($file->getRealPath(), 'r');
    $header = fgetcsv($handle); // Skip header

    // 4. Process each row
    while (($row = fgetcsv($handle)) !== false) {
        // 5. Create ToolboxTalk
        $talk = ToolboxTalk::create([
            'reference_number' => 'TT-' . date('Ym') . '-TEMP',
            'company_id' => $companyId,
            'title' => $row[0] ?? 'Imported Talk',
            'description' => $row[1] ?? null,
            'scheduled_date' => $row[2] ?? now(),
            'start_time' => ($row[2] ?? now()) . ' ' . ($row[3] ?? '09:00'),
            'duration_minutes' => (int)($row[4] ?? 15),
            'location' => $row[5] ?? 'Main Hall',
            'talk_type' => $row[6] ?? 'safety',
            'department_id' => !empty($row[7]) ? (int)$row[7] : null,
            'supervisor_id' => !empty($row[8]) ? (int)$row[8] : null,
            'status' => 'scheduled',
            'biometric_required' => isset($row[9]) ? (bool)$row[9] : true,
        ]);

        // 6. Generate proper reference number
        $talk->reference_number = $talk->generateReferenceNumber();
        $talk->save();
    }
}
```

### Features

- ✅ CSV file validation
- ✅ Multi-tenant isolation (company_id)
- ✅ Error handling per row
- ✅ Automatic reference number generation
- ✅ Default values for missing fields
- ✅ Import results summary (success/failed counts)

### Usage

**View:** Add bulk import form to toolbox talks index page

**Form:**
```html
<form action="{{ route('toolbox-talks.bulk-import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" accept=".csv,.txt" required>
    <button type="submit">Import Talks</button>
</form>
```

### Error Handling

- Row-level error tracking
- Continues processing even if one row fails
- Returns summary: "Import completed: X successful, Y failed"
- Error messages for first 5 failed rows

### Enhancements (Potential)

1. **Excel Support**
   - Use `maatwebsite/excel` package
   - Support .xlsx files

2. **Template Download**
   - Provide CSV template download
   - Include column headers and examples

3. **Validation Preview**
   - Show preview before import
   - Highlight validation errors

4. **Batch Processing**
   - Queue large imports
   - Progress tracking

5. **Topic Linking**
   - Support topic_id column
   - Auto-link to existing topics

---

## 🗄️ Database Table Relationships

### Complete Relationship Map

#### Core Multi-Tenant Structure

```
Company (1) ──< (Many) Users
Company (1) ──< (Many) Departments
Company (1) ──< (Many) All Business Entities
```

#### User Management Module

```
User
├── belongsTo → Company
├── belongsTo → Role
├── belongsTo → Department
├── belongsTo → User (directSupervisor) [self-referential]
├── hasMany → User (subordinates) [self-referential]
├── hasMany → ActivityLog
├── hasMany → UserSession
├── hasMany → ToolboxTalk (as supervisor)
├── hasMany → ToolboxTalkAttendance
├── hasMany → ToolboxTalkFeedback
├── hasMany → Incident (as reporter, assigned_to, approved_by)
├── hasMany → TrainingRecord
├── hasMany → TrainingCertificate
└── Referenced by 15+ models
```

#### Department Management

```
Department
├── belongsTo → Company
├── belongsTo → Department (parentDepartment) [self-referential]
├── hasMany → Department (childDepartments) [self-referential]
├── belongsTo → User (headOfDepartment)
├── belongsTo → User (hseOfficer)
├── hasMany → User (employees)
├── hasMany → ToolboxTalk
├── hasMany → Incident
├── hasMany → Hazard
├── hasMany → RiskAssessment
├── hasMany → JSA
└── hasMany → CAPA
```

#### Toolbox Talk Module

```
ToolboxTalk
├── belongsTo → Company
├── belongsTo → Department
├── belongsTo → User (supervisor)
├── belongsTo → ToolboxTalkTopic
├── hasMany → ToolboxTalkAttendance
└── hasMany → ToolboxTalkFeedback

ToolboxTalkTopic
├── belongsTo → User (creator)
├── belongsTo → User (representer)
└── hasMany → ToolboxTalk

ToolboxTalkAttendance
├── belongsTo → ToolboxTalk
└── belongsTo → User (attendee)

ToolboxTalkFeedback
├── belongsTo → ToolboxTalk
└── belongsTo → User
```

#### Incident Management Module

```
Incident
├── belongsTo → Company
├── belongsTo → User (reporter, assignedTo, approvedBy)
├── belongsTo → Department
├── hasOne → IncidentInvestigation
├── hasMany → IncidentInvestigation
├── hasOne → RootCauseAnalysis
├── hasMany → CAPA
├── hasMany → IncidentAttachment
├── belongsTo → Hazard (relatedHazard)
├── belongsTo → RiskAssessment (relatedRiskAssessment)
├── belongsTo → JSA (relatedJSA)
└── hasMany → ControlMeasure

IncidentInvestigation
├── belongsTo → Incident
├── belongsTo → Company
├── belongsTo → User (investigator, assignedBy)
└── hasOne → RootCauseAnalysis

RootCauseAnalysis
├── belongsTo → Incident
├── belongsTo → IncidentInvestigation
├── belongsTo → Company
├── belongsTo → User (createdBy, approvedBy)
└── hasMany → CAPA

CAPA
├── belongsTo → Incident
├── belongsTo → RootCauseAnalysis
├── belongsTo → Company
├── belongsTo → User (assignedTo, assignedBy, verifiedBy, closedBy)
├── belongsTo → Department
└── Referenced by ControlMeasure
```

#### Risk Assessment Module

```
Hazard
├── belongsTo → Company
├── belongsTo → User (creator)
├── belongsTo → Department
├── belongsTo → Incident (relatedIncident)
├── hasMany → RiskAssessment
└── hasMany → ControlMeasure

RiskAssessment
├── belongsTo → Company
├── belongsTo → Hazard
├── belongsTo → User (creator, assignedTo, approvedBy)
├── belongsTo → Department
├── belongsTo → Incident (relatedIncident)
├── belongsTo → JSA (relatedJSA)
├── hasMany → ControlMeasure
└── hasMany → RiskReview

JSA (Job Safety Analysis)
├── belongsTo → Company
├── belongsTo → User (creator, supervisor, approvedBy)
├── belongsTo → Department
├── belongsTo → RiskAssessment (relatedRiskAssessment)
└── hasMany → ControlMeasure

ControlMeasure
├── belongsTo → Company
├── belongsTo → RiskAssessment
├── belongsTo → Hazard
├── belongsTo → JSA
├── belongsTo → Incident
├── belongsTo → User (assignedTo, responsibleParty, verifiedBy)
├── belongsTo → CAPA (relatedCAPA)
└── Referenced by TrainingNeedsAnalysis

RiskReview
├── belongsTo → Company
├── belongsTo → RiskAssessment
├── belongsTo → User (reviewedBy, assignedTo, approvedBy)
└── belongsTo → Incident (triggeringIncident)
```

#### Training & Competency Module

```
TrainingNeedsAnalysis
├── belongsTo → Company
├── belongsTo → User (creator, validator)
├── belongsTo → RiskAssessment
├── belongsTo → ControlMeasure
├── belongsTo → Incident
├── belongsTo → RootCauseAnalysis
├── belongsTo → CAPA
├── belongsTo → User (for user-specific training)
├── belongsTo → JobCompetencyMatrix
└── hasMany → TrainingPlan

TrainingPlan
├── belongsTo → Company
├── belongsTo → TrainingNeedsAnalysis
├── belongsTo → User (instructor, creator, approver)
├── hasMany → TrainingSession
└── Referenced by ControlMeasure, CAPA

TrainingSession
├── belongsTo → Company
├── belongsTo → TrainingPlan
├── belongsTo → User (instructor)
├── hasMany → TrainingAttendance
├── hasMany → CompetencyAssessment
├── hasMany → TrainingRecord
├── hasMany → TrainingCertificate
└── hasMany → TrainingEffectivenessEvaluation

TrainingRecord
├── belongsTo → Company
├── belongsTo → User (trainee)
├── belongsTo → TrainingSession
├── belongsTo → TrainingPlan
├── belongsTo → TrainingNeedsAnalysis
├── belongsTo → TrainingAttendance
├── belongsTo → CompetencyAssessment
└── belongsTo → TrainingCertificate

TrainingCertificate
├── belongsTo → Company
├── belongsTo → User (certificate holder)
├── belongsTo → TrainingRecord
├── belongsTo → TrainingSession
├── belongsTo → CompetencyAssessment
├── belongsTo → User (issuer)
└── belongsTo → User (revoker)

CompetencyAssessment
├── belongsTo → Company
├── belongsTo → User (trainee, assessor)
├── belongsTo → TrainingSession
└── belongsTo → TrainingCertificate
```

### Key Integration Relationships

#### Closed-Loop Integration

1. **Incident → Training → Incident**
   ```
   Incident
   → RootCauseAnalysis (training_gap_identified)
   → TrainingNeedsAnalysis (auto-created)
   → TrainingPlan
   → TrainingSession
   → TrainingRecord
   → CAPA.training_completed = true
   → Incident (loop closed)
   ```

2. **Risk Assessment → Training → Risk Assessment**
   ```
   RiskAssessment
   → ControlMeasure (administrative)
   → TrainingNeedsAnalysis (auto-created)
   → TrainingPlan
   → TrainingSession
   → TrainingRecord
   → ControlMeasure.training_verified = true
   → RiskAssessment (loop closed)
   ```

3. **Certificate → Training → Certificate**
   ```
   Certificate (expiring)
   → TrainingNeedsAnalysis (refresher, auto-created)
   → TrainingPlan
   → TrainingSession
   → TrainingRecord
   → New Certificate (issued)
   → Certificate (loop closed)
   ```

### Relationship Statistics

- **Most Connected Models:**
  1. User - Referenced by 20+ models
  2. Company - Referenced by all multi-tenant models
  3. Department - Referenced by 10+ models
  4. Incident - Central to reactive safety
  5. RiskAssessment - Central to proactive risk management

- **Self-Referential Relationships:**
  - User (supervisor hierarchy)
  - Department (parent-child hierarchy)

- **Many-to-Many Relationships:**
  - Role ↔ Permission (via `role_permissions` pivot table)

### Foreign Key Constraints

All relationships use proper foreign key constraints:
- `ON DELETE CASCADE` for dependent records
- `ON DELETE SET NULL` for optional relationships
- `ON DELETE RESTRICT` for critical relationships

### Query Optimization

**Eager Loading Examples:**

```php
// Loading Incident with all relationships
$incident->load([
    'reporter', 'assignedTo', 'department', 'company',
    'investigation', 'rootCauseAnalysis', 'capas', 'attachments',
    'relatedHazard', 'relatedRiskAssessment', 'relatedJSA'
]);

// Loading Training Session with relationships
$session->load([
    'trainingPlan.trainingNeed',
    'instructor',
    'attendances.user',
    'competencyAssessments',
    'certificates'
]);
```

---

## 📊 Summary

### Automation Coverage

- ✅ Incident → Training automation
- ✅ Risk Assessment → Training automation
- ✅ Certificate Expiry → Training automation
- ✅ New Hire → Training automation
- ✅ Training → Module feedback automation
- ✅ Scheduled task automation

### Email Notification Coverage

- ✅ Toolbox talk topic created
- ✅ Toolbox talk reminders
- ✅ Certificate expiry alerts
- ⚠️ Missing: Incident notifications
- ⚠️ Missing: CAPA notifications
- ⚠️ Missing: Training session notifications

### Bulk Import Coverage

- ✅ Toolbox talks bulk import (CSV)
- ✅ User bulk import (CSV)
- ⚠️ Missing: Excel support for toolbox talks
- ⚠️ Missing: Template downloads

### Database Relationships

- ✅ 50+ tables with proper relationships
- ✅ Foreign key constraints
- ✅ Multi-tenant isolation
- ✅ Soft deletes support
- ✅ Activity logging

---

**Last Updated:** December 2025  
**System Version:** Laravel 12.40.2  
**Status:** Production Ready
