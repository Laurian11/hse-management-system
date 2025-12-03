# HSE Management System - Complete Architecture & Data Flow

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                      CLIENT LAYER                               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐        │
│  │   Web Browser│  │  Mobile App   │  │  API Client  │        │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘        │
└─────────┼──────────────────┼──────────────────┼────────────────┘
          │                  │                  │
          └──────────────────┴──────────────────┘
                             │
                    ┌────────▼────────┐
                    │  Laravel Routes │
                    │   (web.php)     │
                    └────────┬────────┘
                             │
          ┌──────────────────┼──────────────────┐
          │                  │                  │
    ┌─────▼─────┐    ┌──────▼──────┐    ┌─────▼─────┐
    │Middleware │    │ Middleware  │    │Middleware │
    │   (web)   │    │   (auth)    │    │   (CSRF)  │
    └─────┬─────┘    └──────┬──────┘    └─────┬─────┘
          │                  │                  │
          └──────────────────┼──────────────────┘
                             │
                    ┌────────▼────────┐
                    │   Controllers   │
                    │  (MVC Pattern) │
                    └────────┬────────┘
                             │
          ┌──────────────────┼──────────────────┐
          │                  │                  │
    ┌─────▼─────┐    ┌──────▼──────┐    ┌─────▼─────┐
    │  Models   │    │  Services   │    │FormRequest│
    │ (Eloquent)│    │ (Business)  │    │(Validation)│
    └─────┬─────┘    └──────┬──────┘    └─────┬─────┘
          │                  │                  │
          └──────────────────┼──────────────────┘
                             │
                    ┌────────▼────────┐
                    │    Database     │
                    │  (Multi-tenant) │
                    └────────────────┘
```

---

## 🔄 Complete Data Flow: Topic Creation Example

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. USER ACTION                                                  │
│    User fills form at /toolbox-topics/create                   │
│    - Title, Category, Description                               │
│    - Selects Representer (Employee)                            │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. HTTP REQUEST                                                  │
│    POST /toolbox-topics                                          │
│    Headers: CSRF Token, Session                                  │
│    Body: Form data + representer_id                             │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. ROUTE MATCHING                                                │
│    Route: toolbox-topics.store                                   │
│    Middleware: web, auth                                         │
│    Controller: ToolboxTalkTopicController@store                  │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│ 4. AUTHORIZATION CHECK                                           │
│    ✓ User authenticated (Auth::check())                         │
│    ✓ Company ID exists (Auth::user()->company_id)                │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│ 5. REQUEST VALIDATION                                            │
│    Validates:                                                   │
│    - title (required, max:255)                                  │
│    - category (required, in: safety, health...)                  │
│    - representer_id (required, exists:users,id)                  │
│    - difficulty_level, estimated_duration_minutes...            │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│ 6. MODEL CREATION                                                │
│    ToolboxTalkTopic::create([                                    │
│        'title' => $request->title,                               │
│        'representer_id' => $request->representer_id,             │
│        'created_by' => Auth::id(),                               │
│        'company_id' => Auth::user()->company_id,                 │
│        ...                                                       │
│    ])                                                            │
│                                                                  │
│    Database: INSERT INTO toolbox_talk_topics                     │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│ 7. NOTIFICATION TRIGGER                                          │
│    notifyHSEOfficers($topic)                                     │
│    │                                                             │
│    ├─► Query HSE Officers:                                       │
│    │   - By Role (hse_officer)                                   │
│    │   - By Department (hse_officer_id)                          │
│    │                                                             │
│    └─► For each officer:                                         │
│        └─► $officer->notify(new TopicCreatedNotification($topic))│
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│ 8. QUEUE PROCESSING                                              │
│    TopicCreatedNotification implements ShouldQueue               │
│    │                                                             │
│    ├─► Job added to 'jobs' table                                 │
│    │                                                             │
│    └─► Queue Worker processes:                                   │
│        └─► Sends email via Mail Service                          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│ 9. EMAIL DELIVERY                                                │
│    Mail Service (SMTP/Mailgun/Log)                               │
│    │                                                             │
│    ├─► From: noreply@hesu.co.tz                                  │
│    ├─► To: HSE Officer email                                     │
│    ├─► Subject: "New Toolbox Talk Topic Created: {title}"        │
│    └─► Content: Topic details + representer info + link          │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│ 10. RESPONSE                                                     │
│     Redirect to: /toolbox-topics/{topic}                        │
│     With: success message                                        │
│                                                                  │
│     View renders: toolbox-topics.show                           │
│     - Displays topic details                                     │
│     - Shows representer information                             │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔗 Entity Relationship Diagram (Complete)

```
                    ┌─────────────┐
                    │   Company   │ (Root - Multi-tenant)
                    └──────┬──────┘
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Department │    │    User     │    │    Role     │
└──────┬──────┘    └──────┬──────┘    └──────┬──────┘
       │                  │                  │
       │                  │                  │
       │                  │                  │
       ├──────────────┐   │                  │
       │              │   │                  │
       ▼              ▼   ▼                  ▼
┌─────────────┐  ┌─────────────┐    ┌─────────────┐
│ToolboxTalk  │  │  Incident   │    │ Permission │
└──────┬──────┘  └─────────────┘    └────────────┘
       │
       ├─────────────────────────────────────┐
       │                                     │
       ▼                                     ▼
┌──────────────────┐              ┌──────────────────┐
│ToolboxTalkTopic  │              │ToolboxTalkAttendance│
└──────┬───────────┘              └──────────┬───────┘
       │                                     │
       │                                     │
       ▼                                     ▼
┌──────────────────┐              ┌──────────────────┐
│ToolboxTalkFeedback│             │ToolboxTalkTemplate│
└──────────────────┘              └──────────────────┘
```

---

## 📊 Data Flow: Complete Talk Lifecycle

```
┌─────────────────────────────────────────────────────────────────┐
│ PHASE 1: PLANNING                                               │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─► Topic Created
    │   ├─► ToolboxTalkTopic saved
    │   ├─► Representer assigned
    │   └─► HSE Officers notified
    │
    └─► Talk Scheduled
        ├─► ToolboxTalk created
        ├─► Links to Topic, Department, Supervisor
        ├─► Reference number generated (TT-YYYYMM-SEQ)
        └─► Status: 'scheduled'
            │
            ▼
┌─────────────────────────────────────────────────────────────────┐
│ PHASE 2: PREPARATION                                             │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─► Reminder Scheduled (24h before)
    │   └─► Cron job: talks:send-reminders --type=24h
    │       └─► TalkReminderNotification sent
    │
    └─► Reminder Sent (1h before)
        └─► Cron job: talks:send-reminders --type=1h
            └─► TalkReminderNotification sent
                │
                ▼
┌─────────────────────────────────────────────────────────────────┐
│ PHASE 3: EXECUTION                                              │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─► Talk Started
    │   ├─► Status: 'scheduled' → 'in_progress'
    │   └─► start_time recorded
    │
    ├─► Attendance Marked
    │   ├─► Biometric Sync OR Manual Entry
    │   ├─► ToolboxTalkAttendance created
    │   └─► Statistics updated
    │       ├─► total_attendees++
    │       ├─► present_attendees++ (if present)
    │       └─► attendance_rate recalculated
    │
    └─► Talk Completed
        ├─► Status: 'in_progress' → 'completed'
        ├─► end_time recorded
        └─► Supervisor notes, key points saved
            │
            ▼
┌─────────────────────────────────────────────────────────────────┐
│ PHASE 4: FEEDBACK & ANALYSIS                                     │
└─────────────────────────────────────────────────────────────────┘
    │
    ├─► Feedback Collected
    │   ├─► ToolboxTalkFeedback created
    │   ├─► Sentiment auto-detected
    │   └─► average_feedback_score updated
    │
    ├─► Action Items Assigned
    │   └─► Stored in action_items JSON field
    │
    └─► Analytics Generated
        ├─► Dashboard statistics updated
        ├─► Reports available
        └─► Exports generated (PDF/Excel)
```

---

## 🔄 Cross-Module Data Connections

### Toolbox Talk ↔ User Connections

```
User (Employee)
    │
    ├─► Can be Supervisor
    │   └─► ToolboxTalk.supervisor_id
    │
    ├─► Can be Representer
    │   └─► ToolboxTalkTopic.representer_id
    │
    ├─► Can be Creator
    │   └─► ToolboxTalkTopic.created_by
    │
    ├─► Can Attend
    │   └─► ToolboxTalkAttendance.employee_id
    │
    ├─► Can Provide Feedback
    │   └─► ToolboxTalkFeedback.employee_id
    │
    └─► Can Report Incidents
        └─► Incident.reported_by
```

### Department ↔ All Modules

```
Department
    │
    ├─► Has Employees
    │   └─► User.department_id
    │
    ├─► Has Talks
    │   └─► ToolboxTalk.department_id
    │
    ├─► Has Incidents
    │   └─► Incident.department_id
    │
    ├─► Has HSE Officer
    │   └─► Department.hse_officer_id → User
    │
    └─► Has Head of Department
        └─► Department.head_of_department_id → User
```

---

## 🔐 Multi-Tenant Data Isolation Flow

```
Every Request:
    │
    ├─► User Login
    │   └─► Session stores: user_id, company_id
    │
    ├─► Controller Method
    │   └─► Gets: $companyId = Auth::user()->company_id
    │
    ├─► Model Query
    │   └─► Applies: Model::forCompany($companyId)
    │       └─► WHERE company_id = $companyId
    │
    └─► Result
        └─► Only company's data returned
```

---

## 📧 Notification Flow Architecture

```
Event → Notification → Queue → Email Service → Delivery

Topic Created
    │
    └─► TopicCreatedNotification
        ├─► Finds HSE Officers
        │   ├─► Role-based (hse_officer)
        │   └─► Department-based (hse_officer_id)
        │
        └─► Queues Email
            │
            └─► Queue Worker
                └─► Mail Service
                    └─► Email Sent (hesu.co.tz)
```

---

## 🗄️ Database Schema Relationships

### Primary Relationships

```
Company (1) ──< (Many) Users
Company (1) ──< (Many) Departments
Company (1) ──< (Many) ToolboxTalks
Company (1) ──< (Many) Incidents
Company (1) ──< (Many) SafetyCommunications

User (1) ──< (Many) ToolboxTalks (supervisor)
User (1) ──< (Many) ToolboxTalkAttendances
User (1) ──< (Many) ToolboxTalkFeedbacks
User (1) ──< (Many) ToolboxTalkTopics (creator)
User (1) ──< (Many) ToolboxTalkTopics (representer)
User (1) ──< (Many) Incidents (reporter)
User (1) ──< (Many) ActivityLogs

Department (1) ──< (Many) Users
Department (1) ──< (Many) ToolboxTalks
Department (1) ──< (Many) Incidents
Department (1) ──< (1) User (hse_officer)
Department (1) ──< (1) User (head_of_department)

ToolboxTalk (1) ──< (Many) ToolboxTalkAttendances
ToolboxTalk (1) ──< (Many) ToolboxTalkFeedbacks
ToolboxTalk (1) ──< (1) ToolboxTalkTopic
ToolboxTalk (1) ──< (1) Department
ToolboxTalk (1) ──< (1) User (supervisor)

ToolboxTalkTopic (1) ──< (Many) ToolboxTalks
ToolboxTalkTopic (1) ──< (1) User (creator)
ToolboxTalkTopic (1) ──< (1) User (representer)
```

---

## 🔄 Real-time Data Updates

### Attendance Rate Calculation

```
Attendance Marked
    │
    ├─► ToolboxTalkAttendance created/updated
    │
    ├─► ToolboxTalk statistics updated:
    │   ├─► total_attendees = count(attendances)
    │   ├─► present_attendees = count(attendances where status='present')
    │   └─► calculateAttendanceRate()
    │       └─► attendance_rate = (present / total) * 100
    │
    └─► View refreshes with new statistics
```

### Feedback Score Aggregation

```
Feedback Submitted
    │
    ├─► ToolboxTalkFeedback created
    │
    ├─► ToolboxTalk updated:
    │   └─► calculateAverageFeedbackScore()
    │       ├─► average_feedback_score = AVG(overall_rating)
    │       └─► Saved to database
    │
    └─► Topic updated (if topic linked):
        └─► updateFeedbackScore()
            └─► Aggregates from all talks using topic
```

---

## 📊 Dashboard Data Aggregation

```
Dashboard Request
    │
    ├─► Queries Multiple Models:
    │   ├─► Incident::forCompany($companyId)->count()
    │   ├─► ToolboxTalk::forCompany($companyId)->count()
    │   ├─► ToolboxTalkAttendance::whereHas('toolboxTalk', ...)
    │   └─► User::forCompany($companyId)->active()->count()
    │
    ├─► Time-based Aggregations:
    │   ├─► Last 6 months trends
    │   ├─► Weekly attendance patterns
    │   └─► Monthly completion rates
    │
    ├─► Department Comparisons:
    │   └─► Groups by department_id
    │
    └─► Returns to View:
        └─► Charts rendered with Chart.js
```

---

## 🔗 Service Integration Points

### ZKTeco Biometric Service

```
Service Call Flow:
    │
    ├─► ZKTecoService instantiated
    │   ├─► Reads config (device_ip, port, api_key)
    │   └─► Connects to device
    │
    ├─► Methods Called:
    │   ├─► connect() - Test connection
    │   ├─► getAttendanceLogs() - Get logs
    │   └─► processToolboxTalkAttendance() - Process talk
    │
    └─► Data Flow:
        Device → Service → Model → Database
```

---

## 📋 Complete Request Lifecycle

```
HTTP Request
    │
    ├─► Route Matching
    │   └─► web.php finds matching route
    │
    ├─► Middleware Stack
    │   ├─► web (session, CSRF)
    │   └─► auth (if required)
    │
    ├─► Controller Method
    │   ├─► Authorization
    │   ├─► Validation
    │   ├─► Business Logic
    │   └─► Response
    │
    └─► Response
        ├─► View (HTML)
        ├─► JSON
        ├─► Redirect
        └─► File Download
```

---

*This document provides a complete view of system architecture and data flow.*

