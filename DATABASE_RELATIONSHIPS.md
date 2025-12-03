# HSE Management System - Database Relationships Documentation

## 📊 Complete Entity Relationship Overview

This document provides a comprehensive mapping of all database relationships in the HSE Management System.

---

## 🏢 Core Multi-Tenant Structure

### Company (Root Entity)
**Table:** `companies`  
**Multi-tenant:** Yes (root entity)

**Relationships:**
- `hasMany` → `User` (users)
- `hasMany` → `Department` (departments)

**Foreign Keys:**
- None (root entity)

---

## 👥 User Management Module

### User
**Table:** `users`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `Role` (role)
- `belongsTo` → `Department` (department)
- `belongsTo` → `User` (directSupervisor) - Self-referential via `direct_supervisor_id`
- `hasMany` → `User` (subordinates) - Self-referential via `direct_supervisor_id`
- `hasMany` → `ActivityLog` (activityLogs)
- `hasMany` → `UserSession` (userSessions)

**Foreign Keys:**
- `company_id` → `companies.id`
- `role_id` → `roles.id`
- `department_id` → `departments.id`
- `direct_supervisor_id` → `users.id` (self-referential)

**Referenced By:**
- `Incident` (reported_by, assigned_to, approved_by)
- `Department` (head_of_department_id, hse_officer_id)
- `ToolboxTalk` (supervisor_id)
- `ToolboxTalkTopic` (created_by, representer_id)
- `Hazard` (created_by)
- `RiskAssessment` (created_by, assigned_to, approved_by)
- `JSA` (created_by, supervisor_id, approved_by)
- `ControlMeasure` (assigned_to, responsible_party, verified_by)
- `RiskReview` (reviewed_by, assigned_to, approved_by)
- `IncidentInvestigation` (investigator_id, assigned_by)
- `CAPA` (assigned_to, assigned_by, verified_by, closed_by)
- `RootCauseAnalysis` (created_by, approved_by)

### Role
**Table:** `roles`  
**Multi-tenant:** No (global)

**Relationships:**
- `belongsToMany` → `Permission` (permissions) - via `role_permissions` pivot table
- `hasMany` → `User` (users)

**Foreign Keys:**
- None

**Referenced By:**
- `User` (role_id)

### Permission
**Table:** `permissions`  
**Multi-tenant:** No (global)

**Relationships:**
- `belongsToMany` → `Role` (roles) - via `role_permissions` pivot table

**Foreign Keys:**
- None

---

## 🏛️ Department Management

### Department
**Table:** `departments`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `Department` (parentDepartment) - Self-referential via `parent_department_id`
- `hasMany` → `Department` (childDepartments) - Self-referential via `parent_department_id`
- `belongsTo` → `User` (headOfDepartment) - via `head_of_department_id`
- `belongsTo` → `User` (hseOfficer) - via `hse_officer_id`
- `hasMany` → `User` (employees/users) - via `department_id`
- `hasMany` → `Incident` (incidents)
- `hasMany` → `ToolboxTalk` (toolboxTalks)
- `hasMany` → `Hazard` (hazards)
- `hasMany` → `RiskAssessment` (riskAssessments)
- `hasMany` → `JSA` (jsas)
- `hasMany` → `CAPA` (capas)

**Foreign Keys:**
- `company_id` → `companies.id`
- `parent_department_id` → `departments.id` (self-referential)
- `head_of_department_id` → `users.id`
- `hse_officer_id` → `users.id`

**Referenced By:**
- `User` (department_id)
- `Incident` (department_id)
- `ToolboxTalk` (department_id)
- `Hazard` (department_id)
- `RiskAssessment` (department_id)
- `JSA` (department_id)
- `CAPA` (department_id)

---

## 📋 Toolbox Talk Module

### ToolboxTalk
**Table:** `toolbox_talks`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `Department` (department)
- `belongsTo` → `User` (supervisor) - via `supervisor_id`
- `belongsTo` → `ToolboxTalkTopic` (topic)
- `hasMany` → `ToolboxTalkAttendance` (attendances)
- `hasMany` → `ToolboxTalkFeedback` (feedback)

**Foreign Keys:**
- `company_id` → `companies.id`
- `department_id` → `departments.id`
- `supervisor_id` → `users.id`
- `topic_id` → `toolbox_talk_topics.id`

**Referenced By:**
- `ToolboxTalkAttendance` (toolbox_talk_id)
- `ToolboxTalkFeedback` (toolbox_talk_id)

### ToolboxTalkTopic
**Table:** `toolbox_talk_topics`  
**Multi-tenant:** No (global/shared)

**Relationships:**
- `belongsTo` → `User` (creator) - via `created_by`
- `belongsTo` → `User` (representer) - via `representer_id`
- `hasMany` → `ToolboxTalk` (toolboxTalks)

**Foreign Keys:**
- `created_by` → `users.id`
- `representer_id` → `users.id`

**Referenced By:**
- `ToolboxTalk` (topic_id)

### ToolboxTalkAttendance
**Table:** `toolbox_talk_attendances`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `ToolboxTalk` (toolboxTalk)
- `belongsTo` → `User` (attendee) - via `user_id`

**Foreign Keys:**
- `toolbox_talk_id` → `toolbox_talks.id`
- `user_id` → `users.id`
- `company_id` → `companies.id`

### ToolboxTalkFeedback
**Table:** `toolbox_talk_feedback`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `ToolboxTalk` (toolboxTalk)
- `belongsTo` → `User` (user) - via `user_id`

**Foreign Keys:**
- `toolbox_talk_id` → `toolbox_talks.id`
- `user_id` → `users.id`
- `company_id` → `companies.id`

### ToolboxTalkTemplate
**Table:** `toolbox_talk_templates`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (creator) - via `created_by`

**Foreign Keys:**
- `company_id` → `companies.id`
- `created_by` → `users.id`

---

## 🚨 Incident & Accident Management Module

### Incident
**Table:** `incidents`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (reporter) - via `reported_by`
- `belongsTo` → `User` (assignedTo) - via `assigned_to`
- `belongsTo` → `User` (approvedBy) - via `approved_by`
- `belongsTo` → `Department` (department)
- `hasOne` → `IncidentInvestigation` (investigation)
- `hasMany` → `IncidentInvestigation` (investigations)
- `hasOne` → `RootCauseAnalysis` (rootCauseAnalysis)
- `hasMany` → `CAPA` (capas)
- `hasMany` → `IncidentAttachment` (attachments)
- `belongsTo` → `Hazard` (relatedHazard) - via `related_hazard_id`
- `belongsTo` → `RiskAssessment` (relatedRiskAssessment) - via `related_risk_assessment_id`
- `belongsTo` → `JSA` (relatedJSA) - via `related_jsa_id`
- `hasMany` → `ControlMeasure` (controlMeasures)

**Foreign Keys:**
- `company_id` → `companies.id`
- `reported_by` → `users.id`
- `assigned_to` → `users.id`
- `approved_by` → `users.id`
- `department_id` → `departments.id`
- `related_hazard_id` → `hazards.id`
- `related_risk_assessment_id` → `risk_assessments.id`
- `related_jsa_id` → `jsas.id`

**Referenced By:**
- `IncidentInvestigation` (incident_id)
- `RootCauseAnalysis` (incident_id)
- `CAPA` (incident_id)
- `IncidentAttachment` (incident_id)
- `Hazard` (related_incident_id)
- `RiskAssessment` (related_incident_id)
- `RiskReview` (triggering_incident_id)
- `ControlMeasure` (incident_id)

### IncidentInvestigation
**Table:** `incident_investigations`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Incident` (incident)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (investigator) - via `investigator_id`
- `belongsTo` → `User` (assignedBy) - via `assigned_by`
- `hasOne` → `RootCauseAnalysis` (rootCauseAnalysis)

**Foreign Keys:**
- `incident_id` → `incidents.id`
- `company_id` → `companies.id`
- `investigator_id` → `users.id`
- `assigned_by` → `users.id`

**Referenced By:**
- `RootCauseAnalysis` (investigation_id)

### RootCauseAnalysis
**Table:** `root_cause_analyses`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Incident` (incident)
- `belongsTo` → `IncidentInvestigation` (investigation)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (createdBy) - via `created_by`
- `belongsTo` → `User` (approvedBy) - via `approved_by`
- `hasMany` → `CAPA` (capas)

**Foreign Keys:**
- `incident_id` → `incidents.id`
- `investigation_id` → `incident_investigations.id`
- `company_id` → `companies.id`
- `created_by` → `users.id`
- `approved_by` → `users.id`

**Referenced By:**
- `CAPA` (root_cause_analysis_id)

### CAPA (Corrective and Preventive Action)
**Table:** `capas`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Incident` (incident)
- `belongsTo` → `RootCauseAnalysis` (rootCauseAnalysis)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (assignedTo) - via `assigned_to`
- `belongsTo` → `User` (assignedBy) - via `assigned_by`
- `belongsTo` → `User` (verifiedBy) - via `verified_by`
- `belongsTo` → `User` (closedBy) - via `closed_by`
- `belongsTo` → `Department` (department)

**Foreign Keys:**
- `incident_id` → `incidents.id`
- `root_cause_analysis_id` → `root_cause_analyses.id`
- `company_id` → `companies.id`
- `assigned_to` → `users.id`
- `assigned_by` → `users.id`
- `verified_by` → `users.id`
- `closed_by` → `users.id`
- `department_id` → `departments.id`

**Referenced By:**
- `ControlMeasure` (related_capa_id)

### IncidentAttachment
**Table:** `incident_attachments`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Incident` (incident)
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (uploadedBy) - via `uploaded_by`

**Foreign Keys:**
- `incident_id` → `incidents.id`
- `company_id` → `companies.id`
- `uploaded_by` → `users.id`

---

## ⚠️ Risk Assessment & Hazard Management Module

### Hazard
**Table:** `hazards`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (creator) - via `created_by`
- `belongsTo` → `Department` (department)
- `belongsTo` → `Incident` (relatedIncident) - via `related_incident_id`
- `hasMany` → `RiskAssessment` (riskAssessments)
- `hasMany` → `ControlMeasure` (controlMeasures)

**Foreign Keys:**
- `company_id` → `companies.id`
- `created_by` → `users.id`
- `department_id` → `departments.id`
- `related_incident_id` → `incidents.id`

**Referenced By:**
- `RiskAssessment` (hazard_id)
- `ControlMeasure` (hazard_id)

### RiskAssessment
**Table:** `risk_assessments`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `Hazard` (hazard)
- `belongsTo` → `User` (creator) - via `created_by`
- `belongsTo` → `User` (assignedTo) - via `assigned_to`
- `belongsTo` → `User` (approvedBy) - via `approved_by`
- `belongsTo` → `Department` (department)
- `belongsTo` → `Incident` (relatedIncident) - via `related_incident_id`
- `belongsTo` → `JSA` (relatedJSA) - via `related_jsa_id`
- `hasMany` → `ControlMeasure` (controlMeasures)
- `hasMany` → `RiskReview` (reviews)

**Foreign Keys:**
- `company_id` → `companies.id`
- `hazard_id` → `hazards.id`
- `created_by` → `users.id`
- `assigned_to` → `users.id`
- `approved_by` → `users.id`
- `department_id` → `departments.id`
- `related_incident_id` → `incidents.id`
- `related_jsa_id` → `jsas.id`

**Referenced By:**
- `ControlMeasure` (risk_assessment_id)
- `RiskReview` (risk_assessment_id)
- `Incident` (related_risk_assessment_id)
- `JSA` (related_risk_assessment_id)

### JSA (Job Safety Analysis)
**Table:** `jsas`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `User` (creator) - via `created_by`
- `belongsTo` → `User` (supervisor) - via `supervisor_id`
- `belongsTo` → `User` (approvedBy) - via `approved_by`
- `belongsTo` → `Department` (department)
- `belongsTo` → `RiskAssessment` (relatedRiskAssessment) - via `related_risk_assessment_id`
- `hasMany` → `ControlMeasure` (controlMeasures)

**Foreign Keys:**
- `company_id` → `companies.id`
- `created_by` → `users.id`
- `supervisor_id` → `users.id`
- `approved_by` → `users.id`
- `department_id` → `departments.id`
- `related_risk_assessment_id` → `risk_assessments.id`

**Referenced By:**
- `ControlMeasure` (jsa_id)
- `RiskAssessment` (related_jsa_id)
- `Incident` (related_jsa_id)

### ControlMeasure
**Table:** `control_measures`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `RiskAssessment` (riskAssessment)
- `belongsTo` → `Hazard` (hazard)
- `belongsTo` → `JSA` (jsa)
- `belongsTo` → `Incident` (incident)
- `belongsTo` → `User` (assignedTo) - via `assigned_to`
- `belongsTo` → `User` (responsibleParty) - via `responsible_party`
- `belongsTo` → `User` (verifiedBy) - via `verified_by`
- `belongsTo` → `CAPA` (relatedCAPA) - via `related_capa_id`

**Foreign Keys:**
- `company_id` → `companies.id`
- `risk_assessment_id` → `risk_assessments.id`
- `hazard_id` → `hazards.id`
- `jsa_id` → `jsas.id`
- `incident_id` → `incidents.id`
- `assigned_to` → `users.id`
- `responsible_party` → `users.id`
- `verified_by` → `users.id`
- `related_capa_id` → `capas.id`

**Referenced By:**
- None (leaf entity)

### RiskReview
**Table:** `risk_reviews`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `Company` (company)
- `belongsTo` → `RiskAssessment` (riskAssessment)
- `belongsTo` → `User` (reviewedBy) - via `reviewed_by`
- `belongsTo` → `User` (assignedTo) - via `assigned_to`
- `belongsTo` → `User` (approvedBy) - via `approved_by`
- `belongsTo` → `Incident` (triggeringIncident) - via `triggering_incident_id`

**Foreign Keys:**
- `company_id` → `companies.id`
- `risk_assessment_id` → `risk_assessments.id`
- `reviewed_by` → `users.id`
- `assigned_to` → `users.id`
- `approved_by` → `users.id`
- `triggering_incident_id` → `incidents.id`

**Referenced By:**
- None (leaf entity)

---

## 📊 Activity & Session Tracking

### ActivityLog
**Table:** `activity_logs`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `User` (user) - via `user_id`
- `belongsTo` → `Company` (company)

**Foreign Keys:**
- `user_id` → `users.id`
- `company_id` → `companies.id`

### UserSession
**Table:** `user_sessions`  
**Multi-tenant:** Yes (`company_id`)

**Relationships:**
- `belongsTo` → `User` (user)

**Foreign Keys:**
- `user_id` → `users.id`
- `company_id` → `companies.id`

---

## 🔄 Closed-Loop Integration Relationships

### Incident ↔ Risk Assessment Integration

**Incident → Risk Assessment:**
- `Incident.related_risk_assessment_id` → `RiskAssessment.id`
- `Incident.related_hazard_id` → `Hazard.id`
- `Incident.related_jsa_id` → `JSA.id`

**Risk Assessment → Incident:**
- `RiskAssessment.related_incident_id` → `Incident.id`
- `RiskReview.triggering_incident_id` → `Incident.id`

**Hazard → Incident:**
- `Hazard.related_incident_id` → `Incident.id`

### Control Measures Integration

**ControlMeasure** can be linked to:
- `RiskAssessment` (via `risk_assessment_id`)
- `Hazard` (via `hazard_id`)
- `JSA` (via `jsa_id`)
- `Incident` (via `incident_id`)
- `CAPA` (via `related_capa_id`)

This creates a unified control measure system across all risk management modules.

---

## 📈 Relationship Summary Statistics

### Most Connected Models:
1. **User** - Referenced by 15+ models
2. **Company** - Referenced by all multi-tenant models
3. **Department** - Referenced by 7+ models
4. **Incident** - Central to reactive safety management
5. **RiskAssessment** - Central to proactive risk management

### Key Integration Points:
- **User** ↔ **Department** (hierarchical structure)
- **Incident** ↔ **RiskAssessment** (closed-loop integration)
- **Hazard** ↔ **RiskAssessment** ↔ **ControlMeasure** (risk management flow)
- **Incident** → **Investigation** → **RCA** → **CAPA** (incident workflow)
- **ToolboxTalk** ↔ **Topic** ↔ **Attendance** ↔ **Feedback** (training flow)

---

## 🔍 Query Optimization Notes

### Eager Loading Recommendations:

**When loading Incidents:**
```php
$incident->load([
    'reporter', 'assignedTo', 'department', 'company',
    'investigation', 'rootCauseAnalysis', 'capas', 'attachments',
    'relatedHazard', 'relatedRiskAssessment', 'relatedJSA'
]);
```

**When loading Risk Assessments:**
```php
$riskAssessment->load([
    'hazard', 'creator', 'assignedTo', 'department', 'company',
    'controlMeasures', 'reviews', 'relatedIncident', 'relatedJSA'
]);
```

**When loading Toolbox Talks:**
```php
$toolboxTalk->load([
    'company', 'department', 'supervisor', 'topic',
    'attendances.attendee', 'feedback.user'
]);
```

---

## 📝 Notes

1. **Multi-Tenancy:** All business entities include `company_id` for data isolation
2. **Soft Deletes:** Most models use soft deletes (`deleted_at`)
3. **Activity Logging:** Major models auto-log create/update/delete activities
4. **Reference Numbers:** Most models auto-generate reference numbers on creation
5. **Self-Referential:** `User` (supervisor hierarchy) and `Department` (parent-child) use self-referential relationships

---

**Last Updated:** December 2025  
**System Version:** Laravel 12.40.2

