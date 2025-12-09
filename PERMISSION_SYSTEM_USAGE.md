# User Permission System - Usage Guide

## Overview

The HSE Management System now includes a comprehensive permission system that allows you to assign granular module activity permissions to users. Users can have permissions assigned directly (which override role permissions) or inherit permissions from their role.

## Available Actions

The system supports the following actions for each module:
- **View** - View/list items
- **Create** - Create new items
- **Write** - Write/create content
- **Edit** - Edit existing items
- **Delete** - Delete items
- **Print** - Print/export documents
- **Approve** - Approve items
- **Reject** - Reject items
- **Assign** - Assign items to others
- **Export** - Export data
- **Import** - Import data
- **Manage** - Full management access
- **Configure** - Configuration access

## Modules

Permissions are organized by module:
- Administration
- Incident Management
- Audits & Inspections
- Risk Assessments
- Toolbox Talks
- Safety Communications
- Training Management
- Document Control
- Reports & Analytics
- Employee Management
- Company Management
- Department Management

## Managing User Permissions

### Via Admin Interface

1. Navigate to **Administration > Users**
2. Click on a user to view their details
3. Click the **"Permissions"** button
4. Select/deselect permissions by module
5. Use quick action buttons to select all permissions of a specific action type
6. Click **"Save Permissions"**

### Programmatically

```php
// Give a permission to a user
$user->givePermission('incidents.view');

// Revoke a permission
$user->revokePermission('incidents.delete');

// Sync multiple permissions
$user->syncPermissions(['incidents.view', 'incidents.create', 'incidents.edit']);

// Check if user has permission
if ($user->hasPermission('incidents.delete')) {
    // User can delete incidents
}

// Check if user has any of multiple permissions
if ($user->hasAnyPermission(['incidents.view', 'incidents.create'])) {
    // User has at least one permission
}

// Check if user has all permissions
if ($user->hasAllPermissions(['incidents.view', 'incidents.create'])) {
    // User has all required permissions
}
```

## Using Permissions in Views

### Blade Directives

```blade
{{-- Check single permission --}}
@can('incidents.view')
    <a href="{{ route('incidents.index') }}">View Incidents</a>
@endcan

{{-- Check multiple permissions (any) --}}
@canAny(['incidents.view', 'incidents.create'])
    <button>View or Create</button>
@endcanAny

{{-- Check multiple permissions (all) --}}
@canAll(['incidents.view', 'incidents.edit'])
    <button>View and Edit</button>
@endcanAll

{{-- Hide elements without permission --}}
@can('incidents.delete')
    <button class="text-red-600">Delete</button>
@endcan

{{-- Show print button only if user has print permission --}}
@can('incidents.print')
    <button onclick="window.print()">
        <i class="fas fa-print"></i> Print
    </button>
@endcan
```

### In Controllers

```php
// Check permission before allowing action
public function destroy(Incident $incident)
{
    if (!auth()->user()->hasPermission('incidents.delete')) {
        abort(403, 'You do not have permission to delete incidents.');
    }
    
    $incident->delete();
    return redirect()->route('incidents.index')
        ->with('success', 'Incident deleted successfully.');
}

// Check multiple permissions
public function export()
{
    if (!auth()->user()->hasAnyPermission(['incidents.view', 'incidents.export'])) {
        abort(403, 'You do not have permission to export incidents.');
    }
    
    // Export logic
}
```

## Permission Naming Convention

Permissions follow the pattern: `{module}.{action}`

Examples:
- `incidents.view` - View incidents
- `incidents.create` - Create incidents
- `incidents.edit` - Edit incidents
- `incidents.delete` - Delete incidents
- `incidents.print` - Print incident reports
- `incidents.export` - Export incidents
- `toolbox_talks.view` - View toolbox talks
- `toolbox_talks.create` - Create toolbox talks
- `admin.users.manage` - Manage users

## Permission Hierarchy

1. **User-specific permissions** - Highest priority, override role permissions
2. **Role permissions** - Default permissions for the role
3. **Default role permissions** - System-defined default permissions

If a user has a permission assigned directly, it will be granted regardless of role settings.

## Best Practices

1. **Use role-based permissions for common access patterns** - Assign permissions to roles rather than individual users when possible
2. **Use user-specific permissions for exceptions** - Only assign permissions directly to users when they need different access than their role
3. **Check permissions in both views and controllers** - Always validate permissions in controllers, use view checks for UI display
4. **Document custom permissions** - If creating custom permissions, document their purpose
5. **Regular permission audits** - Periodically review user permissions to ensure they're still appropriate

## Example: Protecting an Edit Button

```blade
{{-- In your view --}}
<div class="flex space-x-2">
    @can('incidents.view')
        <a href="{{ route('incidents.show', $incident) }}" class="btn btn-primary">
            View
        </a>
    @endcan
    
    @can('incidents.edit')
        <a href="{{ route('incidents.edit', $incident) }}" class="btn btn-secondary">
            Edit
        </a>
    @endcan
    
    @can('incidents.delete')
        <form action="{{ route('incidents.destroy', $incident) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    @endcan
    
    @can('incidents.print')
        <button onclick="window.print()" class="btn btn-info">
            <i class="fas fa-print"></i> Print
        </button>
    @endcan
</div>
```

## Creating Default Permissions

To ensure all modules have the necessary permissions, run:

```php
Permission::createDefaultPermissions();
```

This will create permissions for all modules and actions if they don't already exist.

