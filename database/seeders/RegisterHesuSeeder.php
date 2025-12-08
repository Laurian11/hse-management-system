<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class RegisterHesuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Registering Hesu Investment Ltd ===');
        $this->command->info('');

        // Step 1: Ensure super_admin role exists
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
        $this->command->info('✓ Super Admin role ready');

        // Step 2: Create or get company
        $company = Company::firstOrCreate(
            ['name' => 'Hesu Investment Ltd'],
            [
                'description' => 'Hesu Investment Limited',
                'email' => 'admin@hesu.co.tz',
                'license_type' => 'enterprise',
                'license_expiry' => now()->addYears(1),
                'max_users' => 500,
                'max_departments' => 50,
                'timezone' => 'Africa/Dar_es_Salaam',
                'currency' => 'TZS',
                'language' => 'en',
                'country' => 'Tanzania',
                'industry_type' => 'investment',
                'employee_count' => 100,
                'is_active' => true,
            ]
        );
        $this->command->info("✓ Company created/verified: {$company->name} (ID: {$company->id})");

        // Step 3: Create IT Department
        $department = Department::firstOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'IT',
            ],
            [
                'code' => 'IT',
                'description' => 'Information Technology Department',
                'is_active' => true,
            ]
        );
        $this->command->info("✓ Department created/verified: {$department->name} (ID: {$department->id})");

        // Step 4: Create Employee
        $employee = Employee::firstOrCreate(
            [
                'company_id' => $company->id,
                'email' => 'laurianlawrence@hesu.co.tz',
            ],
            [
                'department_id' => $department->id,
                'employee_id_number' => 'HESU-' . str_pad(1, 4, '0', STR_PAD_LEFT),
                'first_name' => 'Laurian',
                'last_name' => 'Lawrence',
                'phone' => '+255 700 000 000',
                'date_of_hire' => now(),
                'employment_type' => 'full_time',
                'employment_status' => 'active',
                'job_title' => 'IT Manager / Super Administrator',
                'is_active' => true,
            ]
        );
        $this->command->info("✓ Employee created/verified: {$employee->first_name} {$employee->last_name} (ID: {$employee->id})");

        // Step 5: Create User Account with Super Admin role
        $user = User::firstOrCreate(
            ['email' => 'laurianlawrence@hesu.co.tz'],
            [
                'name' => 'Laurian Lawrence',
                'password' => Hash::make('Password'),
                'company_id' => $company->id,
                'role_id' => $superAdminRole->id,
                'employee_id_number' => $employee->employee_id_number,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Update user if it already existed
        if ($user->wasRecentlyCreated === false) {
            $user->update([
                'name' => 'Laurian Lawrence',
                'password' => Hash::make('Password'),
                'company_id' => $company->id,
                'role_id' => $superAdminRole->id,
                'employee_id_number' => $employee->employee_id_number,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $this->command->info("✓ User updated: {$user->name}");
        } else {
            $this->command->info("✓ User created: {$user->name}");
        }

        // Link employee to user (Employee has user_id, not the other way around)
        $employee->update(['user_id' => $user->id]);

        $this->command->info('');
        $this->command->info('=== Registration Complete ===');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('Email: laurianlawrence@hesu.co.tz');
        $this->command->info('Password: Password');
        $this->command->info('Role: Super Admin (Full Access)');
        $this->command->info('');
        $this->command->info('Company: Hesu Investment Ltd');
        $this->command->info('Department: IT');
        $this->command->info('Employee: Laurian Lawrence');
    }
}

