# HSE Management System - Complete Data Flow Analysis

## 📊 System Architecture Overview

### Core Architecture Pattern
- **Framework**: Laravel 12 (MVC)
- **Database**: Multi-tenant (company_id isolation)
- **Authentication**: Laravel Sanctum (web sessions)
- **Authorization**: Role-Based Access Control (RBAC)
- **Queue System**: Database queues (for notifications)
- **Email**: Configurable (SMTP/Mailgun/Log)

---

## 🗄️ Database Entity Relationship Diagram

```
┌─────────────┐
│   Company   │ (Root Entity - Multi-tenant)
└──────┬──────┘
       │
       ├─────────────────────────────────────────────┐
       │                                             │
       ▼                                             ▼
┌─────────────┐                            ┌─────────────┐
│  Department  │                            │    User     │
└──────┬──────┘                            └──────┬──────┘
       │                                           │
       │                                           │
       ├──────────────┐                           │
       │              │                           │
       ▼              ▼                           ▼
┌─────────────┐  ┌─────────────┐         ┌─────────────┐
│  ToolboxTalk│  │  Incident   │         │    Role     │
└──────┬──────┘  └─────────────┘         └─────────────┘
       │
       ├─────────────────────────────────────┐
       │                                     │
       ▼                                     ▼
┌──────────────────┐              ┌──────────────────┐
│ToolboxTalkTopic  │              │ToolboxTalkAttendance│
└──────────────────┘              └──────────────────┘
       │                                     │
       │                                     │
       ▼                                     ▼
┌──────────────────┐              ┌──────────────────┐
│ToolboxTalkFeedback│             │  ToolboxTalkTemplate│
└──────────────────┘              └──────────────────┘
```

---

## 🔄 Complete Data Flow Diagrams

### 1. Toolbox Talk Creation Flow

```
User Action (Create Talk)
    │
    ▼
Route: POST /toolbox-talks
    │
    ▼
ToolboxTalkController@store
    │
    ├─► Validates Request (StoreToolboxTalkRequest)
    │
    ├─► Gets Company ID (Auth::user()->company_id)
    │
    ├─► Creates ToolboxTalk Model
    │   ├─► Auto-generates reference_number (TT-YYYYMM-SEQ)
    │   ├─► Sets company_id (multi-tenant isolation)
    │   ├─► Links to: Department, Supervisor, Topic
    │   └─► Saves to database
    │
    ├─► If template used:
    │   └─► Pre-fills from ToolboxTalkTemplate
    │
    └─► Returns Response
        │
        ▼
    View: toolbox-talks.show
        │
        └─► Displays created talk
```

### 2. Topic Creation with Notification Flow

```
User Action (Create Topic)
    │
    ▼
Route: POST /toolbox-topics
    │
    ▼
ToolboxTalkTopicController@store
    │
    ├─► Validates Request
    │   ├─► title, category, difficulty_level
    │   └─► representer_id (required)
    │
    ├─► Creates ToolboxTalkTopic Model
    │   ├─► Links to: Company, Creator, Representer
    │   └─► Saves to database
    │
    ├─► notifyHSEOfficers() Method
    │   │
    │   ├─► Finds HSE Officers:
    │   │   ├─► By Role (hse_officer)
    │   │   └─► By Department (hse_officer_id)
    │   │
    │   └─► For each HSE Officer:
    │       └─► Sends TopicCreatedNotification
    │           │
    │           ├─► Queued (ShouldQueue)
    │           │
    │           └─► Email Sent (via Mail Service)
    │               ├─► Subject: "New Toolbox Talk Topic Created"
    │               ├─► Content: Topic details, representer info
    │               └─► Action: Link to view topic
    │
    └─► Returns Response
        │
        ▼
    View: toolbox-topics.show
```

### 3. Attendance Management Flow

```
User Action (Mark Attendance)
    │
    ▼
Route: POST /toolbox-talks/{id}/mark-attendance
    │
    ▼
ToolboxTalkController@markAttendance
    │
    ├─► Validates Request
    │   ├─► employee_id
    │   ├─► status (present/absent/late/excused)
    │   └─► absence_reason (if absent)
    │
    ├─► Creates/Updates ToolboxTalkAttendance
    │   ├─► Links to: ToolboxTalk, Employee (User)
    │   ├─► Sets check_in_method = 'manual'
    │   ├─► Records check_in_time
    │   └─► Saves to database
    │
    ├─► Updates ToolboxTalk Statistics
    │   ├─► Recalculates total_attendees
    │   ├─► Recalculates present_attendees
    │   └─► Calls calculateAttendanceRate()
    │       └─► Updates attendance_rate field
    │
    └─► Returns Response
        │
        ▼
    View: attendance-management (refreshed)
```

### 4. Biometric Attendance Sync Flow

```
User Action (Sync Biometric)
    │
    ▼
Route: POST /toolbox-talks/{id}/sync-biometric
    │
    ▼
ToolboxTalkController@syncBiometricAttendance
    │
    ├─► Creates ZKTecoService Instance
    │   ├─► Reads config: device_ip, port, api_key
    │   └─► Connects to ZKTeco K40 device
    │
    ├─► Calls processToolboxTalkAttendance()
    │   │
    │   ├─► Gets attendance logs from device
    │   │   ├─► Filters by talk timeframe
    │   │   └─► Returns array of logs
    │   │
    │   ├─► For each log:
    │   │   ├─► findUserByLog() - Matches user by:
    │   │   │   ├─► biometric_template_id
    │   │   │   ├─► employee_id_number
    │   │   │   └─► card_number
    │   │   │
    │   │   ├─► Checks if attendance already exists
    │   │   │
    │   │   └─► Creates ToolboxTalkAttendance
    │   │       ├─► check_in_method = 'biometric'
    │   │       ├─► biometric_template_id
    │   │       ├─► device_id
    │   │       └─► GPS coordinates (from talk)
    │   │
    │   └─► Updates ToolboxTalk statistics
    │
    └─► Returns Response
        │
        ▼
    View: attendance-management (refreshed)
```

### 5. Feedback Submission Flow

```
User Action (Submit Feedback)
    │
    ▼
Route: POST /toolbox-talks/{id}/feedback
    │
    ▼
ToolboxTalkController@submitFeedback
    │
    ├─► Validates Request
    │   ├─► overall_rating (1-5)
    │   ├─► feedback_type
    │   └─► Optional: detailed ratings
    │
    ├─► Auto-detects Sentiment
    │   ├─► If rating >= 4: 'positive'
    │   ├─► If rating <= 2: 'negative'
    │   └─► Else: 'neutral'
    │
    ├─► Creates ToolboxTalkFeedback
    │   ├─► Links to: ToolboxTalk, Employee
    │   ├─► Stores ratings, comments
    │   └─► Saves to database
    │
    ├─► Updates ToolboxTalk
    │   └─► Calls calculateAverageFeedbackScore()
    │       ├─► Averages all feedback ratings
    │       └─► Updates average_feedback_score field
    │
    └─► Returns Response
        │
        ▼
    View: view-feedback (refreshed)
```

### 6. Talk Reminder Notification Flow

```
Scheduled Job (Cron)
    │
    ▼
Command: php artisan talks:send-reminders --type=24h
    │
    ▼
SendTalkReminders@handle
    │
    ├─► Queries ToolboxTalk
    │   ├─► Status = 'scheduled'
    │   └─► scheduled_date between (now + 24h)
    │
    ├─► For each Talk:
    │   │
    │   ├─► Sends to Supervisor
    │   │   └─► TalkReminderNotification
    │   │
    │   └─► Sends to Department Employees
    │       ├─► Queries Users by department_id
    │       └─► For each employee:
    │           └─► TalkReminderNotification
    │
    └─► Each Notification:
        │
        ├─► Queued (ShouldQueue)
        │
        └─► Email Sent
            ├─► Subject: "Reminder: Toolbox Talk Tomorrow"
            ├─► Content: Talk details, time, location
            └─► Action: Link to view talk
```

### 7. Export Functionality Flow

```
User Action (Export Attendance PDF)
    │
    ▼
Route: GET /toolbox-talks/{id}/export/attendance-pdf
    │
    ▼
ToolboxTalkController@exportAttendancePDF
    │
    ├─► Authorization Check
    │   └─► Verifies company_id match
    │
    ├─► Loads Data
    │   ├─► ToolboxTalk (with relationships)
    │   └─► ToolboxTalkAttendance (with employee)
    │
    ├─► Generates PDF
    │   ├─► Uses DomPDF (Barryvdh\DomPDF)
    │   ├─► Loads View: toolbox-talks.exports.attendance-pdf
    │   └─► Renders PDF
    │
    └─► Returns PDF Download
        └─► File: attendance-report-{reference}.pdf
```

---

## 🔗 Module Interconnections

### Toolbox Talk Module Connections

```
ToolboxTalk
    │
    ├─► Department (belongsTo)
    │   └─► Company (belongsTo)
    │       └─► Users (hasMany)
    │
    ├─► Supervisor (belongsTo User)
    │   └─► Role (belongsTo)
    │       └─► Permissions (belongsToMany)
    │
    ├─► Topic (belongsTo ToolboxTalkTopic)
    │   ├─► Representer (belongsTo User)
    │   └─► Creator (belongsTo User)
    │
    ├─► Attendances (hasMany ToolboxTalkAttendance)
    │   └─► Employee (belongsTo User)
    │       └─► Department (belongsTo)
    │
    └─► Feedback (hasMany ToolboxTalkFeedback)
        └─► Employee (belongsTo User)
```

### Data Flow: Topic → Talk → Attendance → Feedback

```
1. Topic Created
   │
   ├─► ToolboxTalkTopic created
   ├─► Notification sent to HSE Officers
   └─► Topic available in library
       │
       ▼
2. Talk Scheduled
   │
   ├─► ToolboxTalk created
   ├─► Links to Topic (topic_id)
   ├─► Links to Department & Supervisor
   └─► Reference number generated
       │
       ▼
3. Talk Conducted
   │
   ├─► Status: scheduled → in_progress
   ├─► Attendance marked (manual/biometric)
   │   └─► ToolboxTalkAttendance created
   │       └─► Updates talk statistics
   │
   └─► Status: in_progress → completed
       │
       ▼
4. Feedback Collected
   │
   ├─► ToolboxTalkFeedback created
   ├─► Sentiment auto-detected
   └─► Updates talk average_feedback_score
       │
       ▼
5. Analytics & Reporting
   │
   ├─► Dashboard aggregates data
   ├─► Reports generated
   └─► Exports available (PDF/Excel)
```

---

## 📥 Request → Response Flow

### Complete Request Lifecycle

```
1. HTTP Request
   │
   ├─► Route Matching (web.php)
   │   └─► Middleware Stack
   │       ├─► web (session, CSRF)
   │       └─► auth (if required)
   │
   ▼
2. Controller Method
   │
   ├─► Authorization Check
   │   └─► Company ID verification
   │
   ├─► Request Validation
   │   └─► Form Request Classes
   │
   ├─► Business Logic
   │   ├─► Model Queries (with scopes)
   │   ├─► Service Calls (ZKTecoService)
   │   ├─► Calculations
   │   └─► Notifications
   │
   └─► Response
       ├─► View Rendering
       ├─► JSON Response
       ├─► Redirect
       └─► File Download (PDF/Excel)
```

---

## 🗃️ Database Relationships Map

### Core Relationships

```php
Company (1) ──< (Many) Users
Company (1) ──< (Many) Departments
Company (1) ──< (Many) ToolboxTalks
Company (1) ──< (Many) Incidents

User (1) ──< (Many) ToolboxTalks (as supervisor)
User (1) ──< (Many) ToolboxTalkAttendances
User (1) ──< (Many) ToolboxTalkFeedbacks
User (1) ──< (Many) ToolboxTalkTopics (as creator)
User (1) ──< (Many) ToolboxTalkTopics (as representer)
User (1) ──< (Many) ActivityLogs
User (1) ──< (Many) UserSessions

Department (1) ──< (Many) Users
Department (1) ──< (Many) ToolboxTalks
Department (1) ──< (Many) Incidents
Department (1) ──< (1) User (hse_officer_id)
Department (1) ──< (1) User (head_of_department_id)

ToolboxTalk (1) ──< (Many) ToolboxTalkAttendances
ToolboxTalk (1) ──< (Many) ToolboxTalkFeedbacks
ToolboxTalk (1) ──< (1) ToolboxTalkTopic
ToolboxTalk (1) ──< (1) Department
ToolboxTalk (1) ──< (1) User (supervisor)

ToolboxTalkTopic (1) ──< (Many) ToolboxTalks
ToolboxTalkTopic (1) ──< (1) User (creator)
ToolboxTalkTopic (1) ──< (1) User (representer)

Role (1) ──< (Many) Users
Role (1) ──< (Many) Permissions (many-to-many)
```

---

## 🔄 Data Synchronization Flows

### 1. Multi-Tenant Data Isolation

```
Every Query Flow:
    │
    ├─► User Login
    │   └─► Sets company_id in session
    │
    ├─► Controller Method
    │   └─► Gets company_id from Auth::user()
    │
    ├─► Model Query
    │   └─► Applies scope: forCompany($companyId)
    │       └─► WHERE company_id = $companyId
    │
    └─► Result: Only company's data returned
```

### 2. Real-time Statistics Updates

```
Event: Attendance Marked
    │
    ├─► ToolboxTalkAttendance created/updated
    │
    ├─► ToolboxTalk@calculateAttendanceRate()
    │   ├─► Counts total_attendees
    │   ├─► Counts present_attendees
    │   ├─► Calculates: (present / total) * 100
    │   └─► Updates attendance_rate field
    │
    └─► View refreshes with new statistics
```

### 3. Feedback Score Aggregation

```
Event: Feedback Submitted
    │
    ├─► ToolboxTalkFeedback created
    │
    ├─► ToolboxTalk@calculateAverageFeedbackScore()
    │   ├─► Queries all feedback for talk
    │   ├─► Calculates AVG(overall_rating)
    │   └─► Updates average_feedback_score field
    │
    └─► Topic@updateFeedbackScore() (if topic linked)
        └─► Aggregates from all talks using topic
```

---

## 📧 Notification Flow Architecture

```
Event Trigger
    │
    ├─► Topic Created
    │   └─► TopicCreatedNotification
    │       ├─► Finds HSE Officers
    │       │   ├─► By Role (hse_officer)
    │       │   └─► By Department (hse_officer_id)
    │       └─► Queues Email
    │
    ├─► Talk Scheduled
    │   └─► TalkReminderNotification (scheduled)
    │       └─► Sent to supervisor & employees
    │
    └─► Talk Reminder (24h/1h)
        └─► TalkReminderNotification
            └─► Sent via cron job
                │
                ▼
        Queue System
            │
            ├─► Database Queue (default)
            │   └─► jobs table
            │
            └─► Queue Worker
                └─► php artisan queue:work
                    │
                    ▼
            Mail Service
                │
                ├─► SMTP (production)
                ├─► Mailgun (production)
                └─► Log (development)
```

---

## 🔐 Authorization & Access Control Flow

```
Request Arrives
    │
    ├─► Auth Middleware
    │   └─► Checks if user logged in
    │
    ├─► Controller Method
    │   └─► Checks company_id match
    │       └─► if ($resource->company_id !== Auth::user()->company_id)
    │           └─► abort(403)
    │
    ├─► Role-Based Access
    │   └─► User->role->permissions
    │       └─► Checks specific permission
    │
    └─► Data Scoping
        └─► Model Scopes
            ├─► forCompany($companyId)
            ├─► forDepartment($departmentId)
            └─► Active users only
```

---

## 📊 Dashboard Data Aggregation Flow

```
Dashboard Request
    │
    ▼
DashboardController@index
    │
    ├─► Gets Company ID
    │   └─► Auth::user()->company_id
    │
    ├─► Aggregates Statistics
    │   ├─► Incident counts
    │   ├─► Toolbox talk counts
    │   ├─► Attendance statistics
    │   └─► User counts
    │
    ├─► Time-based Queries
    │   ├─► Last 6 months trends
    │   ├─► Weekly attendance
    │   └─► Monthly completion rates
    │
    ├─► Department Comparisons
    │   └─► Groups by department_id
    │
    └─► Returns Data
        │
        ▼
    View: dashboard.blade.php
        │
        ├─► Renders Statistics Cards
        ├─► Generates Charts (Chart.js)
        └─► Displays Recent Activity
```

---

## 🔄 Service Integration Flow

### ZKTeco Biometric Service

```
Service Call
    │
    ▼
ZKTecoService
    │
    ├─► Connection
    │   ├─► HTTP API (primary)
    │   └─► TCP Socket (fallback)
    │
    ├─► Methods
    │   ├─► connect() - Test connection
    │   ├─► getUsers() - Get device users
    │   ├─► getAttendanceLogs() - Get logs
    │   ├─► enrollFingerprint() - Enroll user
    │   └─► processToolboxTalkAttendance() - Process talk
    │
    └─► Error Handling
        └─► ZKTecoException thrown
            └─► Logged to ActivityLog
```

---

## 📈 Analytics & Reporting Flow

```
Report Request
    │
    ▼
Controller@reporting
    │
    ├─► Queries Data
    │   ├─► ToolboxTalk (with relationships)
    │   ├─► ToolboxTalkAttendance
    │   ├─► ToolboxTalkFeedback
    │   └─► Department statistics
    │
    ├─► Aggregates Metrics
    │   ├─► Completion rates
    │   ├─► Attendance rates
    │   ├─► Feedback scores
    │   └─► Topic performance
    │
    ├─► Time-based Analysis
    │   ├─► Monthly trends
    │   ├─► Weekly patterns
    │   └─► Department comparisons
    │
    └─► Returns Data
        │
        ▼
    View: reporting.blade.php
        │
        ├─► Charts (Chart.js)
        ├─► Tables
        └─► Export Buttons
            │
            └─► PDF/Excel Generation
```

---

## 🔗 Cross-Module Data Connections

### Toolbox Talk ↔ Incident Connection

```
Incident Reported
    │
    └─► Can create Toolbox Talk
        └─► Topic: "Incident Review"
            └─► Links incident to talk
                └─► Discuss incident in talk
```

### User ↔ All Modules

```
User (Central Entity)
    │
    ├─► Can create ToolboxTalks (as supervisor)
    ├─► Can attend ToolboxTalks (attendance)
    ├─► Can provide Feedback
    ├─► Can create Topics (as creator)
    ├─► Can represent Topics (as representer)
    ├─► Can report Incidents
    ├─► Can receive SafetyCommunications
    └─► All actions logged in ActivityLog
```

---

## 🗂️ File Storage Flow

```
File Upload (Images/Documents)
    │
    ├─► Request Validation
    │   └─► File type, size checks
    │
    ├─► Storage
    │   └─► storage/app/public/
    │       ├─► incident-images/
    │       ├─► toolbox-talk-photos/
    │       └─► documents/
    │
    ├─► Database Storage
    │   └─► Path stored in JSON field
    │       └─► photos, materials, attachments
    │
    └─► Public Access
        └─► Via storage link
            └─► php artisan storage:link
```

---

## 🔄 Queue & Background Processing

```
Notification Triggered
    │
    ├─► Implements ShouldQueue
    │   └─► Added to queue
    │
    ├─► Queue Table (jobs)
    │   ├─► Stores job data
    │   ├─► Tracks status
    │   └─► Handles retries
    │
    ├─► Queue Worker
    │   └─► php artisan queue:work
    │       ├─► Processes jobs
    │       └─► Sends emails
    │
    └─► Failed Jobs
        └─► Stored in failed_jobs table
            └─► Can be retried
```

---

## 📱 API Flow (Future)

```
API Request
    │
    ├─► Route: /api/toolbox-talks
    │
    ├─► Middleware: api, sanctum
    │
    ├─► Controller Method
    │   └─► Returns JSON
    │
    └─► Response
        └─► JSON formatted data
```

---

## 🔍 Search & Filter Flow

```
User Input (Search/Filter)
    │
    ▼
Controller Method
    │
    ├─► Builds Query
    │   ├─► Base query with scopes
    │   ├─► Applies filters
    │   └─► Applies search
    │
    ├─► Executes Query
    │   └─► Returns paginated results
    │
    └─► Returns to View
        │
        ▼
    View: Displays filtered results
```

---

## 📋 Complete User Journey: Creating & Conducting a Talk

```
Step 1: Topic Selection/Creation
    │
    ├─► User creates Topic
    │   ├─► Selects Representer
    │   └─► HSE Officers notified
    │
    ▼
Step 2: Schedule Talk
    │
    ├─► User creates ToolboxTalk
    │   ├─► Links to Topic
    │   ├─► Selects Department
    │   ├─► Assigns Supervisor
    │   └─► Sets date/time
    │
    ▼
Step 3: Talk Scheduled
    │
    ├─► Talk saved with status 'scheduled'
    ├─► Appears in calendar
    └─► Reminders scheduled (24h, 1h)
    │
    ▼
Step 4: Reminder Sent
    │
    ├─► Cron job runs
    ├─► Sends TalkReminderNotification
    └─► Supervisor & employees notified
    │
    ▼
Step 5: Talk Conducted
    │
    ├─► Supervisor starts talk
    │   └─► Status: 'in_progress'
    │
    ├─► Attendance Marked
    │   ├─► Biometric sync OR
    │   └─► Manual marking
    │       └─► ToolboxTalkAttendance created
    │
    └─► Supervisor completes talk
        └─► Status: 'completed'
    │
    ▼
Step 6: Feedback Collected
    │
    ├─► Employees submit feedback
    │   └─► ToolboxTalkFeedback created
    │
    └─► Statistics updated
        ├─► Attendance rate
        └─► Average feedback score
    │
    ▼
Step 7: Reporting & Analytics
    │
    ├─► Data aggregated in dashboard
    ├─► Reports generated
    └─► Exports available
```

---

## 🔐 Security & Data Isolation

### Multi-Tenant Isolation

```
Every Database Query:
    │
    ├─► Gets company_id from Auth::user()
    │
    ├─► Applies Scope
    │   └─► Model::forCompany($companyId)
    │       └─► WHERE company_id = $companyId
    │
    └─► Result: Only company's data
```

### Authorization Checks

```
Controller Method:
    │
    ├─► Checks Resource Ownership
    │   └─► if ($resource->company_id !== Auth::user()->company_id)
    │       └─► abort(403)
    │
    └─► Role-Based Permissions
        └─► User->hasPermission('toolbox-talks.create')
```

---

## 📊 Data Aggregation Patterns

### Statistics Calculation

```
Dashboard Statistics:
    │
    ├─► Real-time Counts
    │   └─► Model::count()
    │
    ├─► Aggregated Metrics
    │   ├─► AVG(attendance_rate)
    │   ├─► AVG(feedback_score)
    │   └─► SUM(attendances)
    │
    └─► Time-based Grouping
        └─► GROUP BY month, week, day
```

---

## 🔄 State Management

### Talk Status Workflow

```
scheduled
    │
    ├─► startTalk() → in_progress
    │   │
    │   └─► completeTalk() → completed
    │
    └─► cancel() → cancelled
```

### Attendance Status

```
present ──► Checked in successfully
absent  ──► Not present
late    ──► Arrived after start time
excused ──► Absent with valid reason
```

---

## 📧 Email Notification Triggers

```
1. Topic Created
   └─► TopicCreatedNotification
       └─► To: HSE Officers

2. Talk Scheduled
   └─► TalkReminderNotification (scheduled)
       └─► To: Supervisor, Employees

3. Talk Reminder (24h)
   └─► TalkReminderNotification (24h)
       └─► To: Supervisor, Employees

4. Talk Reminder (1h)
   └─► TalkReminderNotification (1h)
       └─► To: Supervisor, Employees
```

---

## 🗄️ Database Transaction Flow

```
Complex Operations:
    │
    ├─► DB::beginTransaction()
    │
    ├─► Multiple Model Operations
    │   ├─► Create/Update Models
    │   ├─► Update Relationships
    │   └─► Calculate Statistics
    │
    └─► DB::commit()
        └─► Or DB::rollback() on error
```

---

## 📈 Performance Optimization

### Query Optimization

```
Eager Loading:
    │
    ├─► with(['department', 'supervisor', 'topic'])
    │   └─► Prevents N+1 queries
    │
    └─► Scopes
        └─► forCompany(), active(), completed()
            └─► Reusable query filters
```

### Caching Strategy

```
Cacheable Data:
    │
    ├─► Statistics (5 min TTL)
    ├─► User permissions (session)
    └─► Configuration (config cache)
```

---

## 🔗 Integration Points

### External Services

```
1. ZKTeco K40 Biometric Device
   └─► ZKTecoService
       ├─► HTTP API
       └─► TCP Socket

2. Email Service
   └─► Mail Service
       ├─► SMTP
       ├─► Mailgun
       └─► Log (dev)

3. File Storage
   └─► Laravel Storage
       └─► Local/Cloud
```

---

## 📋 Complete Data Flow Summary

### Input → Processing → Output

```
User Input
    │
    ├─► Form Submission
    ├─► File Upload
    ├─► API Request
    └─► Biometric Data
        │
        ▼
Validation & Authorization
    │
    ├─► Request Validation
    ├─► Company ID Check
    └─► Permission Check
        │
        ▼
Business Logic
    │
    ├─► Model Operations
    ├─► Service Calls
    ├─► Calculations
    └─► Notifications
        │
        ▼
Data Persistence
    │
    ├─► Database Save
    ├─► File Storage
    └─► Activity Logging
        │
        ▼
Response
    │
    ├─► View Rendering
    ├─► JSON Response
    ├─► File Download
    └─► Redirect
```

---

## 🎯 Key Data Flow Patterns

### 1. CRUD Operations
```
Create → Validate → Save → Notify → Redirect
Read   → Query → Filter → Paginate → Display
Update → Validate → Update → Log → Redirect
Delete → Check → Delete → Log → Redirect
```

### 2. Workflow Operations
```
State Change → Validate → Update → Notify → Log
```

### 3. Reporting Operations
```
Query → Aggregate → Format → Export/Display
```

---

*This document provides a complete view of how data flows through the entire HSE Management System.*

