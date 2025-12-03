<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'action',
        'module',
        'resource_type',
        'resource_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'session_id',
        'is_critical',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'is_critical' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByResource($query, $resourceType, $resourceId = null)
    {
        $query->where('resource_type', $resourceType);
        
        if ($resourceId) {
            $query->where('resource_id', $resourceId);
        }
        
        return $query;
    }

    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Helper Methods
    public function getActionLabel(): string
    {
        $labels = [
            'login' => 'Login',
            'logout' => 'Logout',
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'view' => 'Viewed',
            'export' => 'Exported',
            'import' => 'Imported',
            'approve' => 'Approved',
            'reject' => 'Rejected',
            'assign' => 'Assigned',
            'start' => 'Started',
            'complete' => 'Completed',
            'send' => 'Sent',
            'duplicate' => 'Duplicated',
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

    public function getResourceLabel(): string
    {
        $labels = [
            'User' => 'User',
            'Company' => 'Company',
            'Department' => 'Department',
            'Role' => 'Role',
            'Permission' => 'Permission',
            'Incident' => 'Incident',
            'Audit' => 'Audit',
            'ToolboxTalk' => 'Toolbox Talk',
            'SafetyCommunication' => 'Safety Communication',
        ];

        return $labels[$this->resource_type] ?? $this->resource_type;
    }

    public function getChangesSummary(): string
    {
        if (!$this->old_values && !$this->new_values) {
            return '';
        }

        $changes = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                
                if ($oldValue !== $newValue) {
                    $changes[] = "{$key}: {$oldValue} → {$newValue}";
                }
            }
        } elseif ($this->new_values) {
            foreach ($this->new_values as $key => $value) {
                $changes[] = "{$key}: {$value}";
            }
        } elseif ($this->old_values) {
            foreach ($this->old_values as $key => $value) {
                $changes[] = "{$key}: {$value} (removed)";
            }
        }

        return implode(', ', array_slice($changes, 0, 3));
    }

    public function getActionBadge(): string
    {
        $colors = [
            'login' => 'bg-green-100 text-green-800',
            'logout' => 'bg-gray-100 text-gray-800',
            'create' => 'bg-blue-100 text-blue-800',
            'update' => 'bg-yellow-100 text-yellow-800',
            'delete' => 'bg-red-100 text-red-800',
            'approve' => 'bg-purple-100 text-purple-800',
            'reject' => 'bg-red-100 text-red-800',
            'export' => 'bg-indigo-100 text-indigo-800',
            'import' => 'bg-indigo-100 text-indigo-800',
        ];

        $color = $colors[$this->action] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . 
               $this->getActionLabel() . '</span>';
    }

    public function getCriticalBadge(): string
    {
        if (!$this->is_critical) {
            return '';
        }

        return '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Critical</span>';
    }

    public function getFullDescription(): string
    {
        $parts = [
            $this->user?->name ?? 'System',
            $this->getActionLabel(),
            $this->getResourceLabel(),
        ];

        if ($this->resource_id) {
            $parts[] = "#{$this->resource_id}";
        }

        $description = implode(' ', $parts);

        if ($this->changesSummary()) {
            $description .= ' - ' . $this->changesSummary();
        }

        return $description;
    }

    // Static Methods
    public static function log($action, $module, $resourceType = null, $resourceId = null, $description = null, $oldValues = null, $newValues = null, $isCritical = false)
    {
        $data = [
            'action' => $action,
            'module' => $module,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'is_critical' => $isCritical,
        ];

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
            $data['company_id'] = auth()->user()->company_id;
        }

        $data['ip_address'] = request()->ip();
        $data['user_agent'] = request()->userAgent();
        $data['session_id'] = session()->getId();

        return self::create($data);
    }

    public static function logLogin($user)
    {
        return self::log('login', 'admin', 'User', $user->id, "User {$user->name} logged in", null, null, false);
    }

    public static function logLogout($user)
    {
        return self::log('logout', 'admin', 'User', $user->id, "User {$user->name} logged out", null, null, false);
    }

    public static function logFailedLogin($email, $ip)
    {
        return self::log('failed_login', 'admin', 'User', null, "Failed login attempt for {$email}", null, ['email' => $email, 'ip' => $ip], true);
    }

    public static function logPasswordChange($user)
    {
        return self::log('password_change', 'admin', 'User', $user->id, "User {$user->name} changed password", null, null, true);
    }

    public static function logPermissionChange($user, $oldPermissions, $newPermissions)
    {
        return self::log('permission_change', 'admin', 'User', $user->id, "Permissions changed for {$user->name}", $oldPermissions, $newPermissions, true);
    }

    public static function getRecentActivity($limit = 50, $companyId = null)
    {
        $query = self::with(['user', 'company'])
                     ->latest()
                     ->limit($limit);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get();
    }

    public static function getCriticalEvents($limit = 20, $companyId = null)
    {
        $query = self::critical()
                     ->with(['user', 'company'])
                     ->latest()
                     ->limit($limit);

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get();
    }

    public static function getLoginAttempts($hours = 24, $companyId = null)
    {
        $query = self::whereIn('action', ['login', 'logout', 'failed_login'])
                     ->with(['user', 'company'])
                     ->recent($hours)
                     ->latest();

        if ($companyId) {
            $query->where('company_id', $companyId);
        }

        return $query->get();
    }
}
