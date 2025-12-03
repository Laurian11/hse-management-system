<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'level',
        'default_permissions',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'default_permissions' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Helper Methods
    public function hasPermission($permissionName): bool
    {
        if ($this->default_permissions && in_array($permissionName, $this->default_permissions)) {
            return true;
        }

        return $this->permissions()->where('name', $permissionName)->exists();
    }

    public function givePermission($permissionName): self
    {
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission && !$this->hasPermission($permissionName)) {
            $this->permissions()->attach($permission->id);
        }

        return $this;
    }

    public function revokePermission($permissionName): self
    {
        $permission = Permission::where('name', $permissionName)->first();
        if ($permission) {
            $this->permissions()->detach($permission->id);
        }

        return $this;
    }

    public function syncPermissions(array $permissions): self
    {
        $permissionIds = [];
        
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $perm = Permission::where('name', $permission)->first();
                if ($perm) {
                    $permissionIds[] = $perm->id;
                }
            } elseif (is_numeric($permission)) {
                $permissionIds[] = $permission;
            }
        }

        $this->permissions()->sync($permissionIds);

        return $this;
    }

    public function getPermissionNames(): array
    {
        $names = $this->default_permissions ?? [];
        
        foreach ($this->permissions as $permission) {
            $names[] = $permission->name;
        }

        return array_unique($names);
    }

    public function getLevelLabel(): string
    {
        $labels = [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'hse_officer' => 'HSE Officer',
            'hod' => 'Head of Department',
            'employee' => 'Employee',
        ];

        return $labels[$this->level] ?? ucfirst($this->level);
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_system && $this->users()->count() === 0;
    }

    public function getPermissionCount(): int
    {
        $count = count($this->default_permissions ?? []);
        $count += $this->permissions()->count();
        
        return $count;
    }

    public function getBadge(): string
    {
        $colors = [
            'super_admin' => 'bg-red-100 text-red-800',
            'admin' => 'bg-blue-100 text-blue-800',
            'hse_officer' => 'bg-green-100 text-green-800',
            'hod' => 'bg-purple-100 text-purple-800',
            'employee' => 'bg-gray-100 text-gray-800',
        ];

        $color = $colors[$this->level] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . 
               $this->getLevelLabel() . '</span>';
    }

    // Static Methods
    public static function getLevels(): array
    {
        return [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'hse_officer' => 'HSE Officer',
            'hod' => 'Head of Department',
            'employee' => 'Employee',
        ];
    }

    public static function createDefaultRoles()
    {
        $levels = self::getLevels();
        
        foreach ($levels as $level => $displayName) {
            self::firstOrCreate([
                'name' => $level,
                'display_name' => $displayName,
                'level' => $level,
                'is_system' => true,
                'is_active' => true,
            ], [
                'description' => "Default {$displayName} role",
            ]);
        }
    }
}
