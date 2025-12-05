# HSE Management System - Operation Cycle Documentation

## 📋 Overview

The HSE Management System operates on a **multi-layered operational cycle** combining:
- **Event-Driven Automation** (Real-time triggers)
- **Scheduled Tasks** (Daily/Weekly/Monthly automation)
- **User-Driven Workflows** (Manual operations)
- **Closed-Loop Integration** (Module-to-module data flow)

---

## 🔄 Core Operation Cycle Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    SYSTEM OPERATION CYCLE                │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  ┌──────────────┐    ┌──────────────┐    ┌─────────────┐ │
│  │   DAILY      │───▶│   WEEKLY     │───▶│   MONTHLY   │ │
│  │  Operations  │    │  Operations   │    │ Operations  │ │
│  └──────────────┘    └──────────────┘    └─────────────┘ │
│         │                   │                   │        │
│         ▼                   ▼                   ▼        │
│  ┌──────────────────────────────────────────────────┐   │
│  │         EVENT-DRIVEN AUTOMATION LAYER              │   │
│  │  (Observers → Services → Notifications → Actions)  │   │
│  └──────────────────────────────────────────────────┘   │
│         │                   │                   │        │
│         ▼                   ▼                   ▼        │
│  ┌──────────────────────────────────────────────────┐   │
│  │         USER-DRIVEN WORKFLOW LAYER                 │   │
│  │  (Create → Review → Approve → Execute → Verify)  │   │
│  └──────────────────────────────────────────────────┘   │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

---

## 📅 Daily Operation Cycle

### **8:00 AM - Certificate Expiry Alerts**
**Scheduled Task:** `training.certificate-expiry-alerts`

**Process:**
```
1. CertificateExpiryAlertService::checkAndSendAlerts()
   │
   ├─► Scans all TrainingCertificates
   │   ├─► Finds certificates expiring in 60 days
   │   ├─► Finds certificates expiring in 30 days
   │   └─► Finds certificates expiring in 7 days
   │
   ├─► For each expiring certificate:
   │   ├─► Sends email alert to certificate holder
   │   ├─► Sends email alert to supervisor
   │   ├─► Sends email alert to HSE manager
   │   └─► Marks alert as sent (prevents duplicates)
   │
   └─► Creates Training Needs Analysis (TNA) for refresher training
       └─► Links to original certificate
```

**Output:**
- Email notifications sent
- Training needs created for refresher courses
- Dashboard alerts updated

---

### **8:30 AM - PPE Management Alerts**
**Scheduled Task:** `ppe.alerts-and-updates`

**Process:**
```
1. PPEAlertService runs for all companies
   │
   ├─► checkAndSendExpiryAlerts()
   │   ├─► Finds PPE items expiring within 7 days
   │   └─► Sends alerts to assigned users
   │
   ├─► checkAndSendLowStockAlerts()
   │   ├─► Finds PPE items below reorder level
   │   └─► Sends alerts to procurement team
   │
   ├─► checkAndSendInspectionAlerts()
   │   ├─► Finds PPE items due for inspection
   │   └─► Sends alerts to inspection team
   │
   └─► updateExpiredIssuances()
       └─► Auto-updates status of expired PPE issuances
```

**Output:**
- PPE expiry warnings
- Low stock alerts
- Inspection reminders
- Status updates

---

### **9:00 AM - Certificate Revocation**
**Scheduled Task:** `training.revoke-expired-certificates`

**Process:**
```
1. CertificateExpiryAlertService::revokeExpiredCertificates()
   │
   ├─► Finds all certificates with expiry_date < today
   │   └─► Status = 'active'
   │
   ├─► For each expired certificate:
   │   ├─► Updates status to 'expired'
   │   ├─► Logs revocation reason
   │   ├─► Sends notification to user
   │   └─► Creates work restriction warning
   │
   └─► Creates Training Needs Analysis for refresher
```

**Output:**
- Expired certificates revoked
- Work restriction warnings logged
- Refresher training needs created

---

### **Throughout the Day - Event-Driven Automation**

#### **1. Incident Reporting Cycle**
```
User Reports Incident
    │
    ▼
Incident Created
    │
    ├─► Activity Log Entry
    ├─► Notification to HSE Manager
    └─► Auto-assign Investigation (if configured)
        │
        ▼
Investigation Created
    │
    ├─► Investigation Team Notified
    └─► Timeline Started
        │
        ▼
Investigation Completed
    │
    ├─► Root Cause Analysis Triggered
    └─► RCA Created
        │
        ▼
RCA Completed (training_gap_identified = true)
    │
    ├─► RootCauseAnalysisObserver Triggered
    └─► TNAEngineService::processRCATrigger()
        │
        ▼
Training Need Auto-Created
    │
    ├─► Linked to Incident
    ├─► Linked to RCA
    └─► Priority = High (based on incident severity)
        │
        ▼
CAPA Created (if training-related)
    │
    ├─► CAPAObserver Triggered
    └─► TNAEngineService::processCAPATrigger()
        │
        ▼
Training Need Auto-Created (if not already exists)
```

#### **2. Risk Assessment Cycle**
```
Hazard Identified
    │
    ▼
Risk Assessment Created
    │
    ├─► Risk Score Calculated
    ├─► Risk Level Determined
    └─► Control Measures Identified
        │
        ▼
Control Measure Created (control_type = 'administrative')
    │
    ├─► ControlMeasureObserver Triggered
    └─► TNAEngineService::processControlMeasureTrigger()
        │
        ▼
Training Need Auto-Created
    │
    ├─► Linked to Control Measure
    └─► Control Measure updated: training_required = true
```

#### **3. User Management Cycle**
```
New User Created (with job_competency_matrix_id)
    │
    ├─► UserObserver Triggered
    └─► TNAEngineService::processUserJobChangeTrigger()
        │
        ├─► Reads Job Competency Matrix
        ├─► Identifies Mandatory Trainings
        └─► Creates Training Needs for each mandatory training
            │
            ▼
Training Plans Created
    │
    ├─► Training Sessions Scheduled
    └─► Users Notified
```

---

## 📅 Weekly Operation Cycle

### **Weekly Inspections**
```
Inspection Schedule (frequency = 'weekly')
    │
    ├─► System checks next_inspection_date
    ├─► If due_date <= today:
    │   ├─► Status updated to 'due'
    │   ├─► Assigned user notified
    │   └─► Dashboard alert created
    │
    └─► Inspection Conducted
        │
        ├─► Checklist Completed
        ├─► Findings Recorded
        └─► If Non-Compliance Found:
            │
            ▼
Non-Conformance Report (NCR) Created
    │
    ├─► Corrective Action Required
    └─► Linked to Inspection
```

### **Weekly Risk Reviews**
```
Risk Review (review_frequency = 'weekly')
    │
    ├─► System checks due_date
    ├─► If due_date <= today:
    │   ├─► Status updated to 'overdue'
    │   └─► Assigned user notified
    │
    └─► Review Completed
        │
        ├─► Risk Re-assessed
        ├─► Updated Scores Calculated
        └─► Risk Assessment Updated
```

### **Weekly Toolbox Talks**
```
Toolbox Talk Scheduled
    │
    ├─► 24-Hour Reminder Sent
    │   └─► Supervisor & Employees Notified
    │
    ├─► 1-Hour Reminder Sent
    │   └─► Final Notification
    │
    └─► Talk Conducted
        │
        ├─► Attendance Marked
        ├─► Feedback Collected
        └─► Statistics Updated
```

---

## 📅 Monthly Operation Cycle

### **Monthly Inspections**
```
Inspection Schedule (frequency = 'monthly')
    │
    └─► Same process as weekly, but monthly frequency
```

### **Monthly Risk Reviews**
```
Risk Review (review_frequency = 'monthly')
    │
    └─► Same process as weekly, but monthly frequency
```

### **Monthly Reports Generation**
```
End of Month
    │
    ├─► Dashboard Statistics Aggregated
    ├─► Monthly Incident Report Generated
    ├─► Training Completion Report Generated
    ├─► PPE Compliance Report Generated
    └─► Risk Assessment Summary Generated
```

### **Monthly Certificate Expiry Check**
```
Certificate Expiry (60-day window)
    │
    ├─► Daily checks catch certificates
    └─► Monthly summary report generated
```

---

## 🔄 Closed-Loop Integration Cycles

### **Cycle 1: Incident → Training → Verification**
```
1. Incident Occurs
   │
   ▼
2. Investigation Identifies Training Gap
   │
   ▼
3. Training Need Auto-Created
   │
   ▼
4. Training Delivered
   │
   ▼
5. Competency Verified
   │
   ▼
6. Certificate Issued
   │
   ▼
7. Control Measure Updated: training_verified = true
   │
   ▼
8. Risk Score Recalculated (if applicable)
   │
   ▼
9. CAPA Closed (if training was the action)
   │
   ▼
10. Incident Loop Closed
```

### **Cycle 2: Risk Assessment → Control → Training → Verification**
```
1. Risk Assessment Created
   │
   ▼
2. Control Measure Identified (Administrative)
   │
   ▼
3. Training Need Auto-Created
   │
   ▼
4. Training Delivered
   │
   ▼
5. Control Measure Verified
   │
   ▼
6. Residual Risk Recalculated
   │
   ▼
7. Risk Assessment Updated
```

### **Cycle 3: Permit to Work → GCA → Verification**
```
1. Work Permit Created
   │
   ▼
2. Gas Clearance Analysis (GCA) Required
   │
   ▼
3. GCA Log Created
   │
   ├─► Compliance Checked
   └─► If Non-Compliant:
       │
       ▼
4. Corrective Action Required
   │
   ▼
5. Action Completed
   │
   ▼
6. GCA Verified
   │
   ▼
7. Work Permit Activated
```

### **Cycle 4: Inspection → NCR → CAPA → Verification**
```
1. Inspection Conducted
   │
   ▼
2. Non-Compliance Found
   │
   ▼
3. Non-Conformance Report (NCR) Created
   │
   ▼
4. Corrective Action Required
   │
   ▼
5. CAPA Created (linked to NCR)
   │
   ▼
6. CAPA Executed
   │
   ▼
7. Follow-Up Inspection Scheduled
   │
   ▼
8. Verification Completed
   │
   ▼
9. NCR Closed
   │
   ▼
10. CAPA Closed
```

---

## ⚙️ Event-Driven Automation Points

### **Model Observers (Real-Time)**

#### **1. ControlMeasureObserver**
**Trigger:** `ControlMeasure::created` or `updated`
**Condition:** `control_type === 'administrative'`
**Action:**
```
ControlMeasureObserver::created()
    │
    └─► TNAEngineService::processControlMeasureTrigger()
        │
        ├─► Creates TrainingNeedsAnalysis
        ├─► Links to ControlMeasure
        └─► Updates ControlMeasure.training_required = true
```

#### **2. RootCauseAnalysisObserver**
**Trigger:** `RootCauseAnalysis::updated`
**Condition:** `training_gap_identified` changed to `true`
**Action:**
```
RootCauseAnalysisObserver::updated()
    │
    └─► TNAEngineService::processRCATrigger()
        │
        ├─► Creates TrainingNeedsAnalysis
        ├─► Links to Incident
        ├─► Links to RCA
        └─► Sets priority based on incident severity
```

#### **3. CAPAObserver**
**Trigger:** `CAPA::created`
**Condition:** Title/description contains training keywords
**Action:**
```
CAPAObserver::created()
    │
    └─► TNAEngineService::processCAPATrigger()
        │
        ├─► Analyzes CAPA content
        ├─► If training-related:
        │   ├─► Creates TrainingNeedsAnalysis
        │   ├─► Links to CAPA
        │   └─► Inherits priority from CAPA
```

#### **4. UserObserver**
**Trigger:** `User::created` or `updated`
**Condition:** `job_competency_matrix_id` assigned or changed
**Action:**
```
UserObserver::created/updated()
    │
    └─► TNAEngineService::processUserJobChangeTrigger()
        │
        ├─► Reads Job Competency Matrix
        ├─► Identifies Mandatory Trainings
        └─► Creates TrainingNeedsAnalysis for each mandatory training
```

---

## 🔄 Data Flow Patterns

### **Pattern 1: Forward Flow (Input → Processing → Output)**
```
User Input
    │
    ▼
Controller Validation
    │
    ▼
Model Creation/Update
    │
    ├─► Observer Triggered (if applicable)
    ├─► Service Called (if applicable)
    └─► Database Saved
        │
        ▼
Response Generated
    │
    ├─► View Rendered
    ├─► JSON Response
    └─► Redirect
```

### **Pattern 2: Feedback Loop (Output → Input)**
```
Module A Output
    │
    ▼
Observer/Service Detects Change
    │
    ▼
Module B Input Created
    │
    ▼
Module B Processes
    │
    ▼
Module B Output
    │
    ▼
Feedback to Module A
    │
    └─► Module A Updated
```

### **Pattern 3: Scheduled Automation**
```
Cron Job (Every Minute)
    │
    └─► php artisan schedule:run
        │
        ├─► Checks Scheduled Tasks
        ├─► Executes Due Tasks
        └─► Logs Results
            │
            ▼
Scheduled Task Executes
    │
    ├─► Service Called
    ├─► Data Processed
    └─► Notifications Sent
```

---

## 📊 Operational Metrics & Monitoring

### **Daily Metrics**
- Incidents reported
- Inspections conducted
- Toolbox talks completed
- Training sessions delivered
- PPE issuances/returns
- Work permits issued/closed

### **Weekly Metrics**
- Compliance rate
- Training completion rate
- Incident investigation closure rate
- CAPA completion rate
- Inspection schedule adherence

### **Monthly Metrics**
- Total incidents (by type, severity)
- Training effectiveness scores
- PPE compliance percentage
- Risk assessment coverage
- Audit findings summary
- Emergency drill completion

---

## 🔧 System Maintenance Cycles

### **Daily Maintenance**
- Database backups (if configured)
- Cache clearing (if needed)
- Log rotation
- Session cleanup

### **Weekly Maintenance**
- Activity log archiving
- Old record cleanup (soft deletes)
- Performance optimization
- Report generation

### **Monthly Maintenance**
- Full system backup
- Database optimization
- User access review
- Compliance report generation

---

## 🎯 Key Operational Principles

1. **Data Isolation**: All operations are company-scoped
2. **Audit Trail**: All actions are logged via ActivityLog
3. **Automation First**: System auto-triggers actions where possible
4. **User Override**: Manual intervention always available
5. **Feedback Loops**: Outputs feed back to source modules
6. **Real-Time Updates**: Observers trigger immediately on model changes
7. **Scheduled Automation**: Daily tasks run automatically via cron

---

## 📋 Operation Cycle Summary

| Cycle Type | Frequency | Automation Level | Key Activities |
|------------|-----------|------------------|----------------|
| **Daily** | Every day | High | Certificate alerts, PPE alerts, Certificate revocation |
| **Weekly** | Every week | Medium | Inspections, Risk reviews, Toolbox talks |
| **Monthly** | Every month | Medium | Reports, Summaries, Compliance checks |
| **Event-Driven** | Real-time | High | Incident workflows, Training needs, Control measures |
| **User-Driven** | On-demand | Low | Manual operations, Approvals, Data entry |

---

**Last Updated:** December 2025  
**System Version:** 1.0.0

