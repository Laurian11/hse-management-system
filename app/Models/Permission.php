<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
        'action',
        'resource',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    // Helper Methods
    public function getFullName(): string
    {
        $parts = [$this->module, $this->action];
        
        if ($this->resource) {
            $parts[] = $this->resource;
        }

        return implode(' ', array_map('ucfirst', $parts));
    }

    public function getActionLabel(): string
    {
        $labels = [
            'view' => 'View',
            'create' => 'Create',
            'write' => 'Write',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'print' => 'Print',
            'approve' => 'Approve',
            'reject' => 'Reject',
            'assign' => 'Assign',
            'export' => 'Export',
            'import' => 'Import',
            'manage' => 'Manage',
            'configure' => 'Configure',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    public function getModuleLabel(): string
    {
        $labels = [
            'admin' => 'Administration',
            'incidents' => 'Incident Management',
            'audits' => 'Audits & Inspections',
            'risk_assessments' => 'Risk Assessments',
            'toolbox_talks' => 'Toolbox Talks',
            'safety_communications' => 'Safety Communications',
            'training' => 'Training Management',
            'documents' => 'Document Control',
            'reports' => 'Reports & Analytics',
            'employees' => 'Employee Management',
            'companies' => 'Company Management',
            'departments' => 'Department Management',
        ];

        return $labels[$this->module] ?? ucfirst($this->module);
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_system && $this->roles()->count() === 0;
    }

    public function getRoleCount(): int
    {
        return $this->roles()->count();
    }

    public function getBadge(): string
    {
        $colors = [
            'view' => 'bg-blue-100 text-blue-800',
            'create' => 'bg-green-100 text-green-800',
            'write' => 'bg-emerald-100 text-emerald-800',
            'edit' => 'bg-yellow-100 text-yellow-800',
            'delete' => 'bg-red-100 text-red-800',
            'print' => 'bg-cyan-100 text-cyan-800',
            'approve' => 'bg-purple-100 text-purple-800',
            'manage' => 'bg-indigo-100 text-indigo-800',
        ];

        $color = $colors[$this->action] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . 
               $this->getActionLabel() . '</span>';
    }

    // Static Methods
    public static function getModules(): array
    {
        return [
            'admin' => 'Administration',
            'incidents' => 'Incident Management',
            'audits' => 'Audits & Inspections',
            'risk_assessments' => 'Risk Assessments',
            'toolbox_talks' => 'Toolbox Talks',
            'safety_communications' => 'Safety Communications',
            'training' => 'Training Management',
            'documents' => 'Document Control',
            'reports' => 'Reports & Analytics',
            'employees' => 'Employee Management',
            'companies' => 'Company Management',
            'departments' => 'Department Management',
        ];
    }

    public static function getActions(): array
    {
        return [
            'view' => 'View',
            'create' => 'Create',
            'write' => 'Write',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'print' => 'Print',
            'approve' => 'Approve',
            'reject' => 'Reject',
            'assign' => 'Assign',
            'export' => 'Export',
            'import' => 'Import',
            'manage' => 'Manage',
            'configure' => 'Configure',
        ];
    }

    public static function createDefaultPermissions()
    {
        $modules = self::getModules();
        $actions = self::getActions();
        
        foreach ($modules as $module => $moduleLabel) {
            foreach ($actions as $action => $actionLabel) {
                $name = "{$module}.{$action}";
                
                self::firstOrCreate([
                    'name' => $name,
                    'module' => $module,
                    'action' => $action,
                ], [
                    'display_name' => "{$moduleLabel} - {$actionLabel}",
                    'description' => "Permission to {$actionLabel} in {$moduleLabel}",
                    'is_system' => true,
                    'is_active' => true,
                ]);
            }
        }
    }

    public static function findByName($name)
    {
        return self::where('name', $name)->first();
    }

    public static function findOrCreateByName($name, $attributes = [])
    {
        return self::firstOrCreate(['name' => $name], $attributes);
    }
}
