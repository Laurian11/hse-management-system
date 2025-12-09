<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Permission;

class AssignSuperAdminPermissions extends Command
{
    protected $signature = 'roles:assign-super-admin-permissions';
    protected $description = 'Assign all permissions to super_admin role';

    public function handle()
    {
        $this->info('Assigning all permissions to super_admin role...');
        
        $superAdminRole = Role::where('name', 'super_admin')->first();
        
        if (!$superAdminRole) {
            $this->error('Super admin role not found!');
            return 1;
        }
        
        $allPermissions = Permission::active()->get();
        
        if ($allPermissions->isEmpty()) {
            $this->warn('No permissions found in the system.');
            $this->info('You may need to run permission seeders first.');
            return 0;
        }
        
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));
        
        $this->info("✓ Assigned {$allPermissions->count()} permissions to super_admin role");
        $this->info("  Role: {$superAdminRole->display_name} (ID: {$superAdminRole->id})");
        
        return 0;
    }
}
