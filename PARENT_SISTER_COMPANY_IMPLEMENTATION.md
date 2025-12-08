# Parent-Sister Company Structure Implementation

## Overview

This implementation allows one parent company to own multiple sister companies, enabling data aggregation and management across the company group.

## Database Changes

### Migration
- Added `parent_company_id` column to `companies` table
- Foreign key constraint to `companies.id` (self-referential)
- Indexed for performance

### Company Model Updates

**New Relationships:**
- `parentCompany()` - BelongsTo relationship to parent company
- `sisterCompanies()` - HasMany relationship to sister companies

**New Methods:**
- `getCompanyGroupIds()` - Returns array of all company IDs in the group (parent + sisters)
- `isParentCompany()` - Checks if company is a parent
- `isSisterCompany()` - Checks if company is a sister
- `getRootParentCompany()` - Gets root parent (handles nested structures)
- `getAllDescendantCompanies()` - Gets all descendants recursively

**New Scopes:**
- `scopeParentCompanies()` - Filter parent companies only
- `scopeSisterCompanies($parentId)` - Filter sister companies
- `scopeByParentCompany($parentId)` - Filter by parent company group
- `scopeInCompanyGroup($companyId)` - Filter companies in same group

## Service Layer

### CompanyGroupService

Helper service for company group operations:

```php
use App\Services\CompanyGroupService;

// Get all company IDs in group
$groupIds = CompanyGroupService::getCompanyGroupIds($companyId);

// Check company type
$isParent = CompanyGroupService::isParentCompany($companyId);
$isSister = CompanyGroupService::isSisterCompany($companyId);

// Get relationships
$parent = CompanyGroupService::getParentCompany($companyId);
$sisters = CompanyGroupService::getSisterCompanies($companyId);
```

## Usage in Controllers

### Basic Usage

```php
use App\Services\CompanyGroupService;

public function index()
{
    $companyId = Auth::user()->company_id;
    
    // Get company group IDs (parent + all sisters)
    $companyGroupIds = CompanyGroupService::getCompanyGroupIds($companyId);
    
    // Filter data across company group
    $incidents = Incident::whereIn('company_id', $companyGroupIds)->get();
    
    // Or use for single company
    $incidents = Incident::where('company_id', $companyId)->get();
}
```

### Dashboard Aggregation

Parent companies see aggregated data from all sister companies:

```php
$companyGroupIds = CompanyGroupService::getCompanyGroupIds($companyId);

// Aggregate statistics
$totalIncidents = Incident::whereIn('company_id', $companyGroupIds)->count();
$totalUsers = User::whereIn('company_id', $companyGroupIds)->count();
```

## Company Management

### Creating a Sister Company

1. Go to Admin → Companies → Create
2. Select a **Parent Company** from the dropdown
3. Fill in company details
4. Save

### Viewing Company Hierarchy

- Company index shows parent-sister relationships
- Company detail page shows:
  - Parent company (if sister)
  - Sister companies (if parent)
  - Group statistics (aggregated)

## Data Filtering Behavior

### Parent Company Users
- See data from **all sister companies** in dashboards
- Can manage sister companies
- See aggregated statistics

### Sister Company Users
- See data from **their own company only**
- Can see parent company in company list
- Cannot manage other sister companies

### Super Admin
- Sees all companies
- Can manage all relationships
- Full access to all data

## Implementation Status

✅ **Completed:**
- Database migration
- Company model relationships and methods
- CompanyGroupService helper
- CompanyController updates (create/edit/show)
- DashboardController partial update (incidents, toolbox talks)

⚠️ **Partially Completed:**
- DashboardController (some statistics still use single company)
- Other dashboard controllers (need company group filtering)

📝 **To Do:**
- Update all dashboard controllers to use company group filtering
- Update company index view to show hierarchy
- Update company create/edit views with parent selection
- Add company group filtering to all module controllers
- Add visual indicators for parent/sister companies in UI

## Best Practices

1. **Always use CompanyGroupService** for getting company group IDs
2. **Use `whereIn('company_id', $groupIds)`** for parent company data aggregation
3. **Use `where('company_id', $companyId)`** for sister company data isolation
4. **Check company type** before showing aggregated data
5. **Validate parent selection** to prevent circular references

## Example: Updating a Controller

```php
use App\Services\CompanyGroupService;

public function index()
{
    $companyId = Auth::user()->company_id;
    $companyGroupIds = CompanyGroupService::getCompanyGroupIds($companyId);
    
    // For parent companies: show all sister company data
    // For sister companies: show only their data
    $query = YourModel::whereIn('company_id', $companyGroupIds);
    
    // ... rest of controller logic
}
```

## Notes

- Parent companies can have multiple sister companies
- Sister companies belong to one parent only
- Circular references are prevented (company cannot be its own parent)
- Parent companies cannot become sister companies
- Data is aggregated at dashboard level for parent companies
- Individual records remain scoped to their specific company

