# Complete Form Testing Checklist

**Date:** December 2025  
**System:** HSE Management System  
**Total Forms:** 107 (57 create + 50 edit)

---

## 📋 Testing Instructions

For each form, test:
1. **Form Accessibility** - Can you access the form URL?
2. **Form Display** - Does the form render correctly?
3. **Required Fields** - Are required fields marked?
4. **Validation** - Does validation work on submit?
5. **Data Saving** - Does data save correctly?
6. **Success Message** - Is success message displayed?
7. **Redirect** - Does it redirect after save?

---

## ✅ Admin Module (10 forms)

### Companies
- [ ] **Create:** `http://127.0.0.1:8000/admin/companies/create`
  - [ ] Form loads
  - [ ] Required fields work
  - [ ] Data saves
  - [ ] Redirects after save
- [ ] **Edit:** `http://127.0.0.1:8000/admin/companies/{id}/edit`
  - [ ] Form loads with data
  - [ ] Data updates correctly

### Departments
- [ ] **Create:** `http://127.0.0.1:8000/admin/departments/create`
- [ ] **Edit:** `http://127.0.0.1:8000/admin/departments/{id}/edit`

### Users
- [ ] **Create:** `http://127.0.0.1:8000/admin/users/create`
- [ ] **Edit:** `http://127.0.0.1:8000/admin/users/{id}/edit`

### Roles
- [ ] **Create:** `http://127.0.0.1:8000/admin/roles/create`
- [ ] **Edit:** `http://127.0.0.1:8000/admin/roles/{id}/edit`

### Employees
- [ ] **Create:** `http://127.0.0.1:8000/admin/employees/create`
- [ ] **Edit:** `http://127.0.0.1:8000/admin/employees/{id}/edit`

---

## ✅ Incidents Module (8 forms)

### Incidents
- [ ] **Create:** `http://127.0.0.1:8000/incidents/create`
  - [ ] Event type selection works
  - [ ] Dynamic fields appear
  - [ ] Image upload works
- [ ] **Edit:** `http://127.0.0.1:8000/incidents/{id}/edit`

### Investigations
- [ ] **Create:** `http://127.0.0.1:8000/incidents/{id}/investigations/create`
- [ ] **Edit:** `http://127.0.0.1:8000/incidents/investigations/{id}/edit`

### Root Cause Analysis (RCA)
- [ ] **Create:** `http://127.0.0.1:8000/incidents/{id}/rca/create`
  - [ ] Analysis type selection works
  - [ ] 5 Whys form works
  - [ ] Fishbone form works
- [ ] **Edit:** `http://127.0.0.1:8000/incidents/rca/{id}/edit`

### CAPAs
- [ ] **Create:** `http://127.0.0.1:8000/incidents/{id}/capas/create`
- [ ] **Edit:** `http://127.0.0.1:8000/incidents/capas/{id}/edit`

---

## ✅ PPE Module (7 forms)

### PPE Items
- [ ] **Create:** `http://127.0.0.1:8000/ppe/items/create`
  - [ ] Reference number auto-generates
  - [ ] QR code available after save
- [ ] **Edit:** `http://127.0.0.1:8000/ppe/items/{id}/edit`

### PPE Issuances
- [ ] **Create:** `http://127.0.0.1:8000/ppe/issuances/create`
  - [ ] Transaction type selection works
  - [ ] Stock quantity updates

### PPE Suppliers
- [ ] **Create:** `http://127.0.0.1:8000/ppe/suppliers/create`
- [ ] **Edit:** `http://127.0.0.1:8000/ppe/suppliers/{id}/edit`

### PPE Inspections
- [ ] **Create:** `http://127.0.0.1:8000/ppe/inspections/create`

### PPE Reports
- [ ] **Create:** `http://127.0.0.1:8000/ppe/reports/create`

---

## ✅ Procurement Module (7 forms)

### Procurement Requests
- [ ] **Create:** `http://127.0.0.1:8000/procurement/requests/create`
  - [ ] Supplier suggestions appear
  - [ ] Category selection works
- [ ] **Edit:** `http://127.0.0.1:8000/procurement/requests/{id}/edit`

### Suppliers
- [ ] **Create:** `http://127.0.0.1:8000/procurement/suppliers/create`
- [ ] **Edit:** `http://127.0.0.1:8000/procurement/suppliers/{id}/edit`

### Equipment Certifications
- [ ] **Create:** `http://127.0.0.1:8000/procurement/equipment-certifications/create`

### Stock Reports
- [ ] **Create:** `http://127.0.0.1:8000/procurement/stock-reports/create`
- [ ] **Edit:** `http://127.0.0.1:8000/procurement/stock-reports/{id}/edit`

---

## ✅ Risk Assessment Module (10 forms)

### Risk Assessments
- [ ] **Create:** `http://127.0.0.1:8000/risk-assessment/risk-assessments/create`
- [ ] **Edit:** `http://127.0.0.1:8000/risk-assessment/risk-assessments/{id}/edit`

### Hazards
- [ ] **Create:** `http://127.0.0.1:8000/risk-assessment/hazards/create`
- [ ] **Edit:** `http://127.0.0.1:8000/risk-assessment/hazards/{id}/edit`

### Control Measures
- [ ] **Create:** `http://127.0.0.1:8000/risk-assessment/control-measures/create`
- [ ] **Edit:** `http://127.0.0.1:8000/risk-assessment/control-measures/{id}/edit`

### JSAs
- [ ] **Create:** `http://127.0.0.1:8000/risk-assessment/jsas/create`
- [ ] **Edit:** `http://127.0.0.1:8000/risk-assessment/jsas/{id}/edit`

### Risk Reviews
- [ ] **Create:** `http://127.0.0.1:8000/risk-assessment/risk-reviews/create`
- [ ] **Edit:** `http://127.0.0.1:8000/risk-assessment/risk-reviews/{id}/edit`

---

## ✅ Training Module (6 forms)

### Training Needs
- [ ] **Create:** `http://127.0.0.1:8000/training/training-needs/create`
- [ ] **Edit:** `http://127.0.0.1:8000/training/training-needs/{id}/edit`

### Training Plans
- [ ] **Create:** `http://127.0.0.1:8000/training/training-plans/create`
- [ ] **Edit:** `http://127.0.0.1:8000/training/training-plans/{id}/edit`

### Training Sessions
- [ ] **Create:** `http://127.0.0.1:8000/training/training-sessions/create`
- [ ] **Edit:** `http://127.0.0.1:8000/training/training-sessions/{id}/edit`

---

## ✅ Toolbox Talks Module (3 forms)

### Toolbox Talks
- [ ] **Create:** `http://127.0.0.1:8000/toolbox-talks/create`
- [ ] **Edit:** `http://127.0.0.1:8000/toolbox-talks/{id}/edit`

### Toolbox Topics
- [ ] **Create:** `http://127.0.0.1:8000/toolbox-topics/create`
  - [ ] Learning objectives (array) works
  - [ ] Multiple fields save correctly

---

## ✅ Work Permits Module (6 forms)

### Work Permits
- [ ] **Create:** `http://127.0.0.1:8000/work-permits/create`
- [ ] **Edit:** `http://127.0.0.1:8000/work-permits/{id}/edit`

### Work Permit Types
- [ ] **Create:** `http://127.0.0.1:8000/work-permits/types/create`
- [ ] **Edit:** `http://127.0.0.1:8000/work-permits/types/{id}/edit`

### GCA Logs
- [ ] **Create:** `http://127.0.0.1:8000/work-permits/gca-logs/create`
- [ ] **Edit:** `http://127.0.0.1:8000/work-permits/gca-logs/{id}/edit`

---

## ✅ Inspections Module (12 forms)

### Inspections
- [ ] **Create:** `http://127.0.0.1:8000/inspections/create`
- [ ] **Edit:** `http://127.0.0.1:8000/inspections/{id}/edit`

### Inspection Schedules
- [ ] **Create:** `http://127.0.0.1:8000/inspections/schedules/create`
- [ ] **Edit:** `http://127.0.0.1:8000/inspections/schedules/{id}/edit`

### Inspection Checklists
- [ ] **Create:** `http://127.0.0.1:8000/inspections/checklists/create`
- [ ] **Edit:** `http://127.0.0.1:8000/inspections/checklists/{id}/edit`

### Audits
- [ ] **Create:** `http://127.0.0.1:8000/inspections/audits/create`
- [ ] **Edit:** `http://127.0.0.1:8000/inspections/audits/{id}/edit`

### NCRs (Non-Conformance Reports)
- [ ] **Create:** `http://127.0.0.1:8000/inspections/ncrs/create`
- [ ] **Edit:** `http://127.0.0.1:8000/inspections/ncrs/{id}/edit`

### Audit Findings
- [ ] **Create:** `http://127.0.0.1:8000/inspections/audit-findings/create`
- [ ] **Edit:** `http://127.0.0.1:8000/inspections/audit-findings/{id}/edit`

---

## ✅ Emergency Module (10 forms)

### Emergency Contacts
- [ ] **Create:** `http://127.0.0.1:8000/emergency/contacts/create`
- [ ] **Edit:** `http://127.0.0.1:8000/emergency/contacts/{id}/edit`

### Emergency Equipment
- [ ] **Create:** `http://127.0.0.1:8000/emergency/equipment/create`
- [ ] **Edit:** `http://127.0.0.1:8000/emergency/equipment/{id}/edit`

### Fire Drills
- [ ] **Create:** `http://127.0.0.1:8000/emergency/fire-drills/create`
- [ ] **Edit:** `http://127.0.0.1:8000/emergency/fire-drills/{id}/edit`

### Evacuation Plans
- [ ] **Create:** `http://127.0.0.1:8000/emergency/evacuation-plans/create`
- [ ] **Edit:** `http://127.0.0.1:8000/emergency/evacuation-plans/{id}/edit`

### Response Teams
- [ ] **Create:** `http://127.0.0.1:8000/emergency/response-teams/create`
- [ ] **Edit:** `http://127.0.0.1:8000/emergency/response-teams/{id}/edit`

---

## ✅ Environmental Module (2 forms)

### Waste Management
- [ ] **Create:** `http://127.0.0.1:8000/environmental/waste-management/create`
- [ ] **Edit:** `http://127.0.0.1:8000/environmental/waste-management/{id}/edit`

---

## ✅ Health Module (1 form)

### Health Surveillance
- [ ] **Create:** `http://127.0.0.1:8000/health/surveillance/create`

---

## ✅ Documents Module (6 forms)

### HSE Documents
- [ ] **Create:** `http://127.0.0.1:8000/documents/hse-documents/create`
- [ ] **Edit:** `http://127.0.0.1:8000/documents/hse-documents/{id}/edit`

### Document Versions
- [ ] **Create:** `http://127.0.0.1:8000/documents/versions/create`
- [ ] **Edit:** `http://127.0.0.1:8000/documents/versions/{id}/edit`

### Document Templates
- [ ] **Create:** `http://127.0.0.1:8000/documents/templates/create`
- [ ] **Edit:** `http://127.0.0.1:8000/documents/templates/{id}/edit`

---

## ✅ Compliance Module (6 forms)

### Compliance Requirements
- [ ] **Create:** `http://127.0.0.1:8000/compliance/requirements/create`
- [ ] **Edit:** `http://127.0.0.1:8000/compliance/requirements/{id}/edit`

### Permits & Licenses
- [ ] **Create:** `http://127.0.0.1:8000/compliance/permits-licenses/create`
- [ ] **Edit:** `http://127.0.0.1:8000/compliance/permits-licenses/{id}/edit`

### Compliance Audits
- [ ] **Create:** `http://127.0.0.1:8000/compliance/audits/create`
- [ ] **Edit:** `http://127.0.0.1:8000/compliance/audits/{id}/edit`

---

## ✅ Housekeeping Module (4 forms)

### Housekeeping Inspections
- [ ] **Create:** `http://127.0.0.1:8000/housekeeping/inspections/create`
- [ ] **Edit:** `http://127.0.0.1:8000/housekeeping/inspections/{id}/edit`

### 5S Audits
- [ ] **Create:** `http://127.0.0.1:8000/housekeeping/5s-audits/create`
- [ ] **Edit:** `http://127.0.0.1:8000/housekeeping/5s-audits/{id}/edit`

---

## ✅ Waste & Sustainability Module (4 forms)

### Waste Sustainability Records
- [ ] **Create:** `http://127.0.0.1:8000/waste-sustainability/records/create`
- [ ] **Edit:** `http://127.0.0.1:8000/waste-sustainability/records/{id}/edit`

### Carbon Footprint Records
- [ ] **Create:** `http://127.0.0.1:8000/waste-sustainability/carbon-footprint/create`
- [ ] **Edit:** `http://127.0.0.1:8000/waste-sustainability/carbon-footprint/{id}/edit`

---

## ✅ Notifications Module (4 forms)

### Notification Rules
- [ ] **Create:** `http://127.0.0.1:8000/notifications/rules/create`
- [ ] **Edit:** `http://127.0.0.1:8000/notifications/rules/{id}/edit`

### Escalation Matrices
- [ ] **Create:** `http://127.0.0.1:8000/notifications/escalation-matrices/create`
- [ ] **Edit:** `http://127.0.0.1:8000/notifications/escalation-matrices/{id}/edit`

---

## ✅ Safety Communications Module (1 form)

### Safety Communications
- [ ] **Create:** `http://127.0.0.1:8000/safety-communications/create`

---

## 📊 Test Summary

**Total Forms:** 107  
**Forms Tested:** _____  
**Forms Passed:** _____  
**Forms Failed:** _____  

**Issues Found:**
1. _________________________________
2. _________________________________
3. _________________________________

---

## 🔧 Quick Test Commands

### Test Form Route:
```bash
php artisan route:list --name=incidents.create
```

### Test Form Validation:
```bash
# Submit form with empty required fields
# Should show validation errors
```

### Test Form Submission:
```bash
# Fill all required fields
# Submit form
# Check database for saved record
```

---

**Testing Complete!** ✅

