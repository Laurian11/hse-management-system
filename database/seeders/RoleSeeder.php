<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create default permissions
        $permissions = [
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'module' => 'users', 'action' => 'view'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'module' => 'users', 'action' => 'create'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'module' => 'users', 'action' => 'edit'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'module' => 'users', 'action' => 'delete'],
            ['name' => 'users.manage', 'display_name' => 'Manage Users', 'module' => 'users', 'action' => 'manage'],
            
            // Role Management
            ['name' => 'roles.view', 'display_name' => 'View Roles', 'module' => 'roles', 'action' => 'view'],
            ['name' => 'roles.create', 'display_name' => 'Create Roles', 'module' => 'roles', 'action' => 'create'],
            ['name' => 'roles.edit', 'display_name' => 'Edit Roles', 'module' => 'roles', 'action' => 'edit'],
            ['name' => 'roles.delete', 'display_name' => 'Delete Roles', 'module' => 'roles', 'action' => 'delete'],
            ['name' => 'roles.manage', 'display_name' => 'Manage Roles', 'module' => 'roles', 'action' => 'manage'],
            
            // Department Management
            ['name' => 'departments.view', 'display_name' => 'View Departments', 'module' => 'departments', 'action' => 'view'],
            ['name' => 'departments.create', 'display_name' => 'Create Departments', 'module' => 'departments', 'action' => 'create'],
            ['name' => 'departments.edit', 'display_name' => 'Edit Departments', 'module' => 'departments', 'action' => 'edit'],
            ['name' => 'departments.delete', 'display_name' => 'Delete Departments', 'module' => 'departments', 'action' => 'delete'],
            ['name' => 'departments.manage', 'display_name' => 'Manage Departments', 'module' => 'departments', 'action' => 'manage'],
            
            // Company Management
            ['name' => 'companies.view', 'display_name' => 'View Companies', 'module' => 'companies', 'action' => 'view'],
            ['name' => 'companies.create', 'display_name' => 'Create Companies', 'module' => 'companies', 'action' => 'create'],
            ['name' => 'companies.edit', 'display_name' => 'Edit Companies', 'module' => 'companies', 'action' => 'edit'],
            ['name' => 'companies.delete', 'display_name' => 'Delete Companies', 'module' => 'companies', 'action' => 'delete'],
            ['name' => 'companies.manage', 'display_name' => 'Manage Companies', 'module' => 'companies', 'action' => 'manage'],
            
            // Activity Log Management
            ['name' => 'activity-logs.view', 'display_name' => 'View Activity Logs', 'module' => 'activity-logs', 'action' => 'view'],
            ['name' => 'activity-logs.export', 'display_name' => 'Export Activity Logs', 'module' => 'activity-logs', 'action' => 'export'],
            ['name' => 'activity-logs.cleanup', 'display_name' => 'Cleanup Activity Logs', 'module' => 'activity-logs', 'action' => 'cleanup'],
            
            // Toolbox Talks
            ['name' => 'toolbox-talks.view', 'display_name' => 'View Toolbox Talks', 'module' => 'toolbox-talks', 'action' => 'view'],
            ['name' => 'toolbox-talks.create', 'display_name' => 'Create Toolbox Talks', 'module' => 'toolbox-talks', 'action' => 'create'],
            ['name' => 'toolbox-talks.edit', 'display_name' => 'Edit Toolbox Talks', 'module' => 'toolbox-talks', 'action' => 'edit'],
            ['name' => 'toolbox-talks.delete', 'display_name' => 'Delete Toolbox Talks', 'module' => 'toolbox-talks', 'action' => 'delete'],
            ['name' => 'toolbox-talks.conduct', 'display_name' => 'Conduct Toolbox Talks', 'module' => 'toolbox-talks', 'action' => 'conduct'],
            
            // Safety Communications
            ['name' => 'safety-communications.view', 'display_name' => 'View Safety Communications', 'module' => 'safety-communications', 'action' => 'view'],
            ['name' => 'safety-communications.create', 'display_name' => 'Create Safety Communications', 'module' => 'safety-communications', 'action' => 'create'],
            ['name' => 'safety-communications.edit', 'display_name' => 'Edit Safety Communications', 'module' => 'safety-communications', 'action' => 'edit'],
            ['name' => 'safety-communications.delete', 'display_name' => 'Delete Safety Communications', 'module' => 'safety-communications', 'action' => 'delete'],
            ['name' => 'safety-communications.send', 'display_name' => 'Send Safety Communications', 'module' => 'safety-communications', 'action' => 'send'],
            
            // Incidents
            ['name' => 'incidents.view', 'display_name' => 'View Incidents', 'module' => 'incidents', 'action' => 'view'],
            ['name' => 'incidents.create', 'display_name' => 'Create Incidents', 'module' => 'incidents', 'action' => 'create'],
            ['name' => 'incidents.edit', 'display_name' => 'Edit Incidents', 'module' => 'incidents', 'action' => 'edit'],
            ['name' => 'incidents.delete', 'display_name' => 'Delete Incidents', 'module' => 'incidents', 'action' => 'delete'],
            ['name' => 'incidents.investigate', 'display_name' => 'Investigate Incidents', 'module' => 'incidents', 'action' => 'investigate'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'description' => $permission['display_name'] . ' permission',
                    'module' => $permission['module'],
                    'action' => $permission['action'],
                    'resource' => $permission['module'],
                    'is_system' => true,
                    'is_active' => true,
                ]
            );
        }

        // Create default roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'System super administrator with full access',
                'level' => 'super_admin',
                'permissions' => Permission::pluck('name')->toArray(), // All permissions
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Company administrator with management access',
                'level' => 'admin',
                'permissions' => Permission::whereNotIn('module', ['companies'])->pluck('name')->toArray(), // All except company management
            ],
            [
                'name' => 'hse_officer',
                'display_name' => 'HSE Officer',
                'description' => 'Health & Safety Officer',
                'level' => 'hse_officer',
                'permissions' => [
                    'toolbox-talks.view', 'toolbox-talks.create', 'toolbox-talks.edit', 'toolbox-talks.conduct',
                    'safety-communications.view', 'safety-communications.create', 'safety-communications.send',
                    'incidents.view', 'incidents.create', 'incidents.edit', 'incidents.investigate',
                    'departments.view', 'users.view', 'activity-logs.view',
                ],
            ],
            [
                'name' => 'supervisor',
                'display_name' => 'Supervisor',
                'description' => 'Department supervisor',
                'level' => 'supervisor',
                'permissions' => [
                    'toolbox-talks.view', 'toolbox-talks.create', 'toolbox-talks.conduct',
                    'safety-communications.view', 'incidents.view', 'incidents.create',
                    'departments.view', 'users.view',
                ],
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Regular employee',
                'level' => 'employee',
                'permissions' => [
                    'toolbox-talks.view', 'safety-communications.view', 'incidents.view', 'incidents.create',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'level' => $roleData['level'],
                    'is_system' => true,
                    'is_active' => true,
                ]
            );

            // Assign permissions to role
            $role->permissions()->sync(
                Permission::whereIn('name', $roleData['permissions'])->pluck('id')
            );
        }

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Created ' . Permission::count() . ' permissions');
        $this->command->info('Created ' . Role::count() . ' roles');
    }
}
