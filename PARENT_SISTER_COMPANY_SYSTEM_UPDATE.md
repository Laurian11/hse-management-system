# Parent-Sister Company Feature - System-Wide Update

## Overview
This document outlines the system-wide implementation of the parent-sister company feature, allowing parent companies to view aggregated data from all their sister companies.

## Implementation Summary

### 1. Core Infrastructure

#### Trait: `UsesCompanyGroup`
- **Location**: `app/Traits/UsesCompanyGroup.php`
- **Purpose**: Provides reusable methods for controllers to get company group IDs
- **Methods**:
  - `getCompanyId()`: Returns current user's company ID
  - `getCompanyGroupIds()`: Returns array of company IDs (parent + all sisters)
  - `isParentCompany()`: Checks if current company is a parent
  - `isSisterCompany()`: Checks if current company is a sister

#### Service: `CompanyGroupService`
- **Location**: `app/Services/CompanyGroupService.php`
- **Purpose**: Centralized service for company group operations
- **Methods**:
  - `getCompanyGroupIds($companyId)`: Get all company IDs in the group
  - `isParentCompany($companyId)`: Check if company is a parent
  - `isSisterCompany($companyId)`: Check if company is a sister

### 2. Updated Controllers

#### Dashboard Controllers (All Updated)
All dashboard controllers now use company group filtering:

1. **Main Dashboard** (`DashboardController.php`)
   - ✅ All statistics aggregated across company group
   - ✅ Charts and trends include all sister companies
   - ✅ Recent activity from all companies in group

2. **Environmental Dashboard** (`EnvironmentalDashboardController.php`)
   - ✅ Waste records aggregated
   - ✅ Emission monitoring aggregated
   - ✅ Spill incidents aggregated

3. **Health & Wellness Dashboard** (`HealthWellnessDashboardController.php`)
   - ✅ Health surveillance aggregated
   - ✅ First aid records aggregated
   - ✅ Ergonomic assessments aggregated

4. **Procurement Dashboard** (`ProcurementDashboardController.php`)
   - ✅ Procurement requests aggregated
   - ✅ Supplier data aggregated
   - ✅ Equipment certifications aggregated

5. **Waste & Sustainability Dashboard** (`WasteSustainabilityDashboardController.php`)
   - ✅ Waste records aggregated
   - ✅ Carbon footprint aggregated

6. **Document Management Dashboard** (`DocumentManagementDashboardController.php`)
   - ✅ Documents aggregated
   - ✅ Versions aggregated
   - ✅ Templates aggregated

7. **Compliance Dashboard** (`ComplianceDashboardController.php`)
   - ✅ Compliance requirements aggregated
   - ✅ Permits aggregated
   - ✅ Audits aggregated

8. **Housekeeping Dashboard** (`HousekeepingDashboardController.php`)
   - ✅ Inspections aggregated
   - ✅ 5S audits aggregated

9. **Work Permit Dashboard** (`WorkPermitDashboardController.php`)
   - ✅ Work permits aggregated
   - ✅ GCA logs aggregated

10. **Emergency Preparedness Dashboard** (`EmergencyPreparednessDashboardController.php`)
    - ✅ Fire drills aggregated
    - ✅ Emergency equipment aggregated
    - ✅ Evacuation plans aggregated

11. **Inspection Dashboard** (`InspectionDashboardController.php`)
    - ✅ Inspections aggregated
    - ✅ NCRs aggregated
    - ✅ Audits aggregated

12. **Training Dashboard** (`TrainingDashboardController.php`)
    - ✅ Training needs aggregated
    - ✅ Training sessions aggregated
    - ✅ Certificates aggregated

13. **Risk Assessment Dashboard** (`RiskAssessmentDashboardController.php`)
    - ✅ Hazards aggregated
    - ✅ Risk assessments aggregated
    - ✅ JSAs aggregated
    - ✅ Control measures aggregated

#### Module Controllers (Key Updates)

1. **Incident Controller** (`IncidentController.php`)
   - ✅ Index method uses company group filtering
   - ✅ Dashboard uses company group filtering
   - ✅ Create method loads departments/users from company group

2. **Toolbox Talk Controller** (`ToolboxTalkController.php`)
   - ✅ Index method uses company group filtering
   - ✅ Statistics use company group filtering

3. **Risk Assessment Controller** (`RiskAssessmentController.php`)
   - ✅ Index method uses company group filtering
   - ✅ Create method loads hazards/departments/users from company group
   - ✅ Statistics use company group filtering

4. **Hazard Controller** (`HazardController.php`)
   - ✅ Index method uses company group filtering
   - ✅ Create method loads departments from company group
   - ✅ Statistics use company group filtering

5. **JSA Controller** (`JSAController.php`)
   - ✅ Index method uses company group filtering
   - ✅ Create method loads departments/users/risk assessments from company group
   - ✅ Statistics use company group filtering

6. **CAPA Controller** (`CAPAController.php`)
   - ✅ All methods use company group authorization checks
   - ✅ Create/edit methods load users/departments from company group
   - ✅ All authorization checks updated to support company groups

### 3. How It Works

#### For Parent Companies:
- When a parent company user logs in, `getCompanyGroupIds()` returns:
  - The parent company's ID
  - All sister company IDs
- All queries use `whereIn('company_id', $companyGroupIds)` instead of `where('company_id', $companyId)`
- This aggregates data from all companies in the group

#### For Sister Companies:
- When a sister company user logs in, `getCompanyGroupIds()` returns:
  - The sister company's ID only
  - (Parent company data is NOT included for sister companies)
- Queries use `whereIn('company_id', $companyGroupIds)` which effectively filters to just their company
- Sister companies see only their own data

### 4. Data Isolation Rules

1. **Parent Companies**:
   - Can see aggregated data from all sister companies
   - Can manage all sister companies
   - Statistics include all companies in the group

2. **Sister Companies**:
   - Can see only their own data
   - Cannot see other sister companies' data
   - Cannot see parent company's data (unless they are the parent)
   - Statistics show only their own data

### 5. Remaining Work

#### Controllers to Update:
- [ ] RiskAssessmentController (index, create methods)
- [ ] HazardController
- [ ] JSAController
- [ ] CAPAController
- [ ] TrainingSessionController
- [ ] TrainingNeedsAnalysisController
- [ ] PPEItemController
- [ ] PPEIssuanceController
- [ ] WorkPermitController
- [ ] InspectionController
- [ ] AuditController
- [ ] And other module controllers...

#### Views to Update:
- [ ] Add company group indicators to dashboards
- [ ] Show "Aggregated Data" badges on parent company dashboards
- [ ] Update filters to show company selection for parent companies

#### Models to Consider:
- [ ] Update `forCompany()` scopes to support company groups (optional)
- [ ] Or create new `forCompanyGroup()` scopes

### 6. Usage Example

```php
use App\Traits\UsesCompanyGroup;

class MyController extends Controller
{
    use UsesCompanyGroup;

    public function index()
    {
        $companyId = $this->getCompanyId();
        $companyGroupIds = $this->getCompanyGroupIds();
        
        // For parent companies, this includes all sister companies
        // For sister companies, this is just their own ID
        $items = MyModel::whereIn('company_id', $companyGroupIds)->get();
        
        return view('my.index', compact('items'));
    }
}
```

### 7. Testing Checklist

- [ ] Parent company dashboard shows aggregated statistics
- [ ] Sister company dashboard shows only their data
- [ ] Parent company can view all sister companies' records
- [ ] Sister company cannot view other companies' records
- [ ] All charts and graphs aggregate correctly
- [ ] Filters work correctly for both parent and sister companies
- [ ] Reports include correct data scope

### 8. Notes

- The implementation uses `whereIn('company_id', $companyGroupIds)` pattern
- This ensures backward compatibility - standalone companies work as before
- Parent companies automatically get aggregated views
- Sister companies maintain data isolation
- No changes needed to database schema (already done)
- No changes needed to models (relationships already exist)

## Status: ✅ Core Infrastructure Complete, Module Controllers In Progress

