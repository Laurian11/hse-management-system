# Training & Competency Module - Integration Complete

## ✅ Integration Points Added

### 1. Incident Module Integration ✅

**File:** `resources/views/incidents/show.blade.php`

**Added Features:**
- **RCA Section:** Training gap identification display with button to create training need
  - Shows training gap description if identified
  - Button to create training need from RCA (if not already created)
  - Link to view existing training need if already created

- **CAPA Section:** Training integration for each CAPA
  - Shows training completion status badge
  - Shows "Training Planned" badge if training need exists
  - Button to create training need from CAPA (for training-related CAPAs)
  - Link to view training need if already created

**Controller Updates:**
- `IncidentController::show()` - Now loads training relationships for CAPAs

### 2. Risk Assessment Module Integration ✅

**File:** `app/Http/Controllers/ControlMeasureController.php`

**Added Features:**
- `ControlMeasureController::show()` - Now loads training relationships
  - `relatedTrainingNeed`
  - `relatedTrainingPlan`

**Automatic Triggers:**
- When `ControlMeasure` with `control_type = 'administrative'` is created
- `ControlMeasureObserver` automatically creates training need via TNA Engine

### 3. Scheduled Tasks ✅

**File:** `routes/console.php`

**Added Tasks:**
1. **Daily Certificate Expiry Alerts** (8:00 AM)
   - Checks for certificates expiring in 60, 30, and 7 days
   - Sends alerts to users, supervisors, and HSE managers
   - Creates refresher training needs for expiring certificates

2. **Daily Expired Certificate Revocation** (9:00 AM)
   - Auto-revokes expired certificates
   - Updates certificate status to 'expired'
   - Can trigger work restrictions (if integrated with Permit to Work)

---

## 🔄 Complete Closed-Loop Workflow

### Input Loop (Automatic Triggers)

1. **Risk Assessment → Training**
   - Administrative control created → TNA auto-created
   - Visible in control measure view

2. **Incident RCA → Training**
   - Training gap identified in RCA → TNA auto-created
   - Button in incident RCA tab to create/view training

3. **CAPA → Training**
   - Training-related CAPA created → TNA auto-created
   - Status badges and buttons in incident CAPA tab

4. **New Hire/Job Change → Training**
   - User created/changed with job matrix → TNA auto-created
   - Mandatory trainings from competency matrix

5. **Certificate Expiry → Training**
   - Certificate expiring within 60 days → Refresher TNA auto-created
   - Daily scheduled task checks and creates

### Output Loop (Automatic Feedback)

1. **Training → Risk Assessment**
   - Training completed & verified → Control measure `training_verified = true`
   - Risk score can be recalculated

2. **Training → Incident/CAPA**
   - Training completed → CAPA `training_completed = true`
   - CAPA can be auto-closed if all actions complete

3. **Training → Certificate**
   - Competency assessment passed → Certificate auto-issued
   - Expiry dates set automatically

4. **Certificate Expiry → Work Restrictions**
   - Certificate expired → Status auto-revoked
   - Can trigger work permit restrictions

---

## 📋 Usage Examples

### From Incident View

1. **Create Training from RCA:**
   - Go to Incident → RCA Tab
   - If training gap identified, click "Create Training Need"
   - Training need created with link back to RCA

2. **Create Training from CAPA:**
   - Go to Incident → CAPAs Tab
   - For training-related CAPAs, click "Create Training"
   - Training need created with link back to CAPA

### From Risk Assessment

1. **Automatic Training Creation:**
   - Create control measure with `control_type = 'administrative'`
   - Training need automatically created
   - View in control measure details

### Scheduled Tasks

Run manually:
```bash
php artisan schedule:run
```

Or set up cron:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🎯 Next Steps

### Views to Create (Priority Order)

1. **Training Needs Analysis**
   - `resources/views/training/training-needs/index.blade.php`
   - `resources/views/training/training-needs/create.blade.php`
   - `resources/views/training/training-needs/show.blade.php`

2. **Training Plans**
   - `resources/views/training/training-plans/index.blade.php`
   - `resources/views/training/training-plans/create.blade.php`
   - `resources/views/training/training-plans/show.blade.php`

3. **Training Sessions**
   - `resources/views/training/training-sessions/index.blade.php`
   - `resources/views/training/training-sessions/create.blade.php`
   - `resources/views/training/training-sessions/show.blade.php`

4. **User Training Dashboard**
   - `resources/views/training/my-training.blade.php`
   - `resources/views/training/my-certificates.blade.php`

### Additional Controllers (Optional)

- `CompetencyAssessmentController`
- `TrainingCertificateController`
- `TrainingRecordController`
- `JobCompetencyMatrixController`
- `TrainingEffectivenessEvaluationController`

### Email Notifications

Create notification classes:
- `CertificateExpiryAlert` (60, 30, 7 days)
- `TrainingSessionReminder`
- `TrainingCompletionNotification`
- `CompetencyAssessmentResult`

---

## ✅ Integration Status

| Module | Integration Type | Status |
|--------|-----------------|--------|
| Incident Module | View Integration | ✅ Complete |
| Risk Assessment | Observer Auto-Trigger | ✅ Complete |
| CAPA Module | View Integration + Observer | ✅ Complete |
| User Management | Observer Auto-Trigger | ✅ Complete |
| Certificate Management | Scheduled Tasks | ✅ Complete |
| Permit to Work | Ready for Integration | ⏳ Pending |

---

## 🔧 Configuration

### Running Migrations

```bash
php artisan migrate
```

Migrations will run in order automatically.

### Testing Integration

1. **Test Control Measure Trigger:**
   - Create administrative control measure
   - Check if training need auto-created

2. **Test RCA Trigger:**
   - Mark training gap in RCA
   - Check if training need auto-created

3. **Test CAPA Trigger:**
   - Create CAPA with "training" in title/description
   - Check if training need auto-created

4. **Test Certificate Expiry:**
   - Create certificate with expiry date
   - Run scheduled task manually
   - Check if alerts sent and training need created

---

*Integration Completed: 2025-12-04*
*Status: Backend Complete + View Integration Complete - Training Views Pending*
