# HSE Management System - Data Flow Summary

## 🎯 Quick Reference: How Data Flows Through the System

### Core Data Flow Pattern

```
User Action → Route → Middleware → Controller → Model → Database → Response → View
```

---

## 📋 Module Data Flows

### 1. Toolbox Talk Module

**Create Talk:**
```
Form Submit → ToolboxTalkController@store → ToolboxTalk::create → Database
                                                                    ↓
                                                              Reference Generated
                                                                    ↓
                                                              View Redirect
```

**Mark Attendance:**
```
Attendance Form → ToolboxTalkController@markAttendance → ToolboxTalkAttendance::create
                                                                        ↓
                                                              Update Talk Statistics
                                                                        ↓
                                                              calculateAttendanceRate()
```

**Biometric Sync:**
```
Sync Button → ToolboxTalkController@syncBiometricAttendance → ZKTecoService
                                                                        ↓
                                                              Get Device Logs
                                                                        ↓
                                                              Match Users
                                                                        ↓
                                                              Create Attendances
```

**Submit Feedback:**
```
Feedback Form → ToolboxTalkController@submitFeedback → ToolboxTalkFeedback::create
                                                                        ↓
                                                              Auto-detect Sentiment
                                                                        ↓
                                                              Update Talk Score
```

### 2. Topic Management

**Create Topic:**
```
Form Submit → ToolboxTalkTopicController@store → ToolboxTalkTopic::create
                                                                        ↓
                                                              notifyHSEOfficers()
                                                                        ↓
                                                              Find HSE Officers
                                                                        ↓
                                                              Queue Notifications
                                                                        ↓
                                                              Email Sent
```

### 3. Incident Management

**Report Incident:**
```
Form Submit → IncidentController@store → Incident::create
                                                      ↓
                                              Reference Generated
                                                      ↓
                                              ActivityLog::log
                                                      ↓
                                              View Redirect
```

### 4. Dashboard

**Load Dashboard:**
```
GET /dashboard → DashboardController@index → Multiple Queries
                                                      ↓
                                              Aggregate Statistics
                                                      ↓
                                              Time-based Analysis
                                                      ↓
                                              Return to View
                                                      ↓
                                              Charts Rendered
```

---

## 🔗 Key Relationships

### Company (Root Entity)
- Has many Users
- Has many Departments
- Has many ToolboxTalks
- Has many Incidents
- Has many SafetyCommunications

### User (Central Entity)
- Belongs to Company
- Belongs to Department
- Belongs to Role
- Can be Supervisor (ToolboxTalk)
- Can be Representer (ToolboxTalkTopic)
- Can Attend (ToolboxTalkAttendance)
- Can Provide Feedback (ToolboxTalkFeedback)
- Can Report Incidents

### ToolboxTalk (Core Entity)
- Belongs to Company
- Belongs to Department
- Belongs to Supervisor (User)
- Belongs to Topic
- Has many Attendances
- Has many Feedbacks

---

## 🔄 Real-time Updates

### Attendance Rate
```
Attendance Created/Updated
    ↓
Recalculate:
- total_attendees = count(attendances)
- present_attendees = count(present)
- attendance_rate = (present / total) * 100
    ↓
Save to ToolboxTalk
```

### Feedback Score
```
Feedback Created
    ↓
Calculate:
- average_feedback_score = AVG(overall_rating)
    ↓
Save to ToolboxTalk
```

---

## 📧 Notification Flow

```
Event Triggered
    ↓
Notification Created
    ↓
Added to Queue (jobs table)
    ↓
Queue Worker Processes
    ↓
Mail Service Sends
    ↓
Email Delivered
```

**Notifications:**
- Topic Created → HSE Officers
- Talk Reminder (24h) → Supervisor & Employees
- Talk Reminder (1h) → Supervisor & Employees

---

## 🔐 Security Flow

### Multi-Tenant Isolation
```
Every Query:
    ↓
Get company_id from Auth::user()
    ↓
Apply scope: forCompany($companyId)
    ↓
WHERE company_id = $companyId
    ↓
Only company's data returned
```

### Authorization
```
Request Arrives
    ↓
Check Authentication (auth middleware)
    ↓
Check Company Match
    ↓
Check Role Permissions
    ↓
Allow/Deny
```

---

## 📊 Dashboard Aggregation

```
Dashboard Request
    ↓
Query Multiple Models:
- Incident::forCompany()
- ToolboxTalk::forCompany()
- ToolboxTalkAttendance::whereHas()
- User::forCompany()
    ↓
Time-based Grouping:
- Monthly trends
- Weekly patterns
- Department comparisons
    ↓
Return Aggregated Data
    ↓
View Renders Charts
```

---

## 🗄️ Database Operations

### Create Flow
```
Model::create([...])
    ↓
Database INSERT
    ↓
Model Events Triggered
    ↓
ActivityLog::log (if configured)
    ↓
Return Model Instance
```

### Update Flow
```
Model::update([...])
    ↓
Database UPDATE
    ↓
Model Events Triggered
    ↓
ActivityLog::log (if configured)
    ↓
Return Boolean
```

### Delete Flow
```
Model::delete()
    ↓
Soft Delete (if configured)
    ↓
Database UPDATE deleted_at
    ↓
Model Events Triggered
    ↓
ActivityLog::log
```

---

## 🔄 Service Integration

### ZKTeco Biometric
```
Service Call
    ↓
ZKTecoService instantiated
    ↓
Connect to Device
    ↓
Get Attendance Logs
    ↓
Process & Match Users
    ↓
Create Attendances
    ↓
Update Statistics
```

---

## 📈 Analytics Flow

### Statistics Calculation
```
Query Data
    ↓
Group by Time Period
    ↓
Calculate Aggregates:
- COUNT
- AVG
- SUM
- MAX/MIN
    ↓
Format for Charts
    ↓
Return to View
```

---

## 🔍 Search & Filter Flow

```
User Input
    ↓
Controller Receives
    ↓
Build Query with Scopes:
- forCompany()
- active()
- completed()
    ↓
Apply Filters
    ↓
Apply Search
    ↓
Paginate Results
    ↓
Return to View
```

---

## 📤 Export Flow

### PDF Export
```
Export Request
    ↓
Load Data
    ↓
Generate PDF (DomPDF)
    ↓
Load Blade Template
    ↓
Render PDF
    ↓
Return Download
```

### Excel Export
```
Export Request
    ↓
Load Data
    ↓
Generate Excel (Maatwebsite)
    ↓
Format Data
    ↓
Return Download
```

---

## 🎯 Complete User Journey Example

### Creating & Conducting a Talk

```
1. Create Topic
   Topic Created → HSE Officers Notified

2. Schedule Talk
   Talk Created → Links to Topic → Status: scheduled

3. Reminder Sent (24h)
   Cron Job → Notification → Email Sent

4. Talk Started
   Status: scheduled → in_progress

5. Attendance Marked
   Attendance Created → Statistics Updated

6. Talk Completed
   Status: in_progress → completed

7. Feedback Collected
   Feedback Created → Score Updated

8. Analytics Generated
   Dashboard Updated → Reports Available
```

---

## 🔗 Cross-Module Connections

### User ↔ All Modules
- Creates Topics
- Schedules Talks
- Attends Talks
- Provides Feedback
- Reports Incidents
- Receives Notifications

### Department ↔ All Modules
- Has Employees
- Has Talks
- Has Incidents
- Has HSE Officer
- Has Head of Department

### Company ↔ All Modules
- Root entity for all data
- Multi-tenant isolation
- All queries filtered by company_id

---

## 📝 Activity Logging Flow

```
Model Event Triggered
    ↓
ActivityLog::log() called
    ↓
Capture:
- user_id
- company_id
- action
- module
- resource_type
- resource_id
- description
- old_values / new_values
- ip_address
- user_agent
    ↓
Save to activity_logs table
```

---

## 🚀 Performance Optimizations

### Eager Loading
```
Query with Relationships:
Model::with(['department', 'supervisor', 'topic'])->get()
    ↓
Single Query with JOINs
    ↓
Prevents N+1 Queries
```

### Query Scopes
```
Reusable Filters:
Model::forCompany($id)->active()->completed()
    ↓
Applied to All Queries
    ↓
Consistent Filtering
```

### Caching
```
Cacheable Data:
- Statistics (5 min TTL)
- User Permissions (session)
- Configuration (config cache)
```

---

*This summary provides a quick reference for understanding data flow through the HSE Management System.*

