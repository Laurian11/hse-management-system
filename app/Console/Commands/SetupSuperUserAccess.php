<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class SetupSuperUserAccess extends Command
{
    protected $signature = 'users:setup-superuser {--email=}';
    protected $description = 'Create all permissions and assign to super_admin role, optionally assign to specific user';

    public function handle()
    {
        $this->info('Setting up superuser access...');
        $this->newLine();

        // Step 1: Create all default permissions
        $this->info('Step 1: Creating default permissions...');
        Permission::createDefaultPermissions();
        $permissionCount = Permission::count();
        $this->info("✓ Created {$permissionCount} permissions");
        $this->newLine();

        // Step 2: Get or create super_admin role
        $this->info('Step 2: Setting up super_admin role...');
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'System super administrator with full access',
                'level' => 'super_admin',
                'is_system' => true,
                'is_active' => true,
            ]
        );
        $this->info("✓ Super admin role ready (ID: {$superAdminRole->id})");
        $this->newLine();

        // Step 3: Assign all permissions to super_admin role
        $this->info('Step 3: Assigning all permissions to super_admin role...');
        $allPermissions = Permission::active()->get();
        $superAdminRole->permissions()->sync($allPermissions->pluck('id'));
        $this->info("✓ Assigned {$allPermissions->count()} permissions to super_admin role");
        $this->newLine();

        // Step 4: Optionally assign super_admin role to specific user
        if ($email = $this->option('email')) {
            $this->info("Step 4: Assigning super_admin role to user: {$email}");
            $user = User::where('email', $email)->first();
            
            if ($user) {
                $user->update(['role_id' => $superAdminRole->id]);
                $this->info("✓ User {$user->name} (ID: {$user->id}) now has super_admin role");
                
                // Also give user all permissions directly (overrides role)
                $user->syncPermissions($allPermissions->pluck('name')->toArray());
                $this->info("✓ User {$user->name} has all {$allPermissions->count()} permissions assigned directly");
            } else {
                $this->warn("✗ User with email {$email} not found");
            }
            $this->newLine();
        }

        // Step 5: Show summary
        $this->info('=== Setup Complete ===');
        $this->info("Total Permissions: {$permissionCount}");
        $this->info("Super Admin Role: {$superAdminRole->display_name}");
        $this->info("Permissions Assigned: {$allPermissions->count()}");
        
        if ($email && isset($user)) {
            $this->info("User Updated: {$user->name} ({$user->email})");
        }
        
        $this->newLine();
        $this->info('Superuser access is now fully configured!');
        
        return 0;
    }
}

