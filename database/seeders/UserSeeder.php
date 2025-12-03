<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create default company if it doesn't exist
        $company = Company::firstOrCreate([
            'name' => 'HSE Management Demo',
            'description' => 'Demo company for HSE Management System',
            'license_type' => 'enterprise',
            'license_expiry' => now()->addYears(1),
            'max_users' => 1000,
            'max_departments' => 100,
            'timezone' => 'UTC',
            'currency' => 'USD',
            'language' => 'en',
            'country' => 'United States',
            'industry_type' => 'construction',
            'employee_count' => 500,
            'is_active' => true,
        ]);

        // Get or create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin'], [
            'display_name' => 'Super Administrator',
            'description' => 'System super administrator with full access',
            'level' => 'super_admin',
            'is_system' => true,
            'is_active' => true,
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Administrator',
            'description' => 'Company administrator with management access',
            'level' => 'admin',
            'is_system' => true,
            'is_active' => true,
        ]);

        $hseRole = Role::firstOrCreate(['name' => 'hse_officer'], [
            'display_name' => 'HSE Officer',
            'description' => 'Health & Safety Officer',
            'level' => 'hse_officer',
            'is_system' => true,
            'is_active' => true,
        ]);

        // Create demo users
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@hse.com',
                'password' => 'password',
                'role' => $superAdminRole,
                'is_active' => true,
            ],
            [
                'name' => 'John Administrator',
                'email' => 'john@hse.com',
                'password' => 'password',
                'role' => $adminRole,
                'is_active' => true,
            ],
            [
                'name' => 'Sarah HSE Officer',
                'email' => 'sarah@hse.com',
                'password' => 'password',
                'role' => $hseRole,
                'is_active' => true,
            ],
            [
                'name' => 'Mike Employee',
                'email' => 'mike@hse.com',
                'password' => 'password',
                'role' => $adminRole, // Give admin role for testing
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'company_id' => $company->id,
                    'role_id' => $userData['role']->id,
                    'is_active' => $userData['is_active'],
                    'email_verified_at' => now(),
                ]
            );

            // Assign role permissions
            $user->role_id = $userData['role']->id;
            $user->save();
        }

        $this->command->info('Demo users created successfully!');
        $this->command->info('Login Credentials:');
        $this->command->info('Super Admin: admin@hse.com / password');
        $this->command->info('Admin: john@hse.com / password');
        $this->command->info('HSE Officer: sarah@hse.com / password');
        $this->command->info('Employee: mike@hse.com / password');
    }
}
