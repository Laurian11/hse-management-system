<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class CleanupHESUSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Cleaning up HESU INVESTMENT LIMITED and LaurianLawrence ===');
        $this->command->info('');

        // Find HESU company with various search patterns
        $hesuCompany = Company::where(function($q) {
            $q->where('name', 'LIKE', '%HESU%')
              ->orWhere('name', 'LIKE', '%Hesu%')
              ->orWhere('name', 'LIKE', '%hesu%')
              ->orWhere('name', 'LIKE', '%INVESTMENT%')
              ->orWhere('name', 'LIKE', '%Investment%');
        })->first();

        if ($hesuCompany) {
            $this->command->info("✓ Found company: {$hesuCompany->name} (ID: {$hesuCompany->id})");

            // Get related data counts
            $departmentsCount = $hesuCompany->departments()->count();
            $employeesCount = $hesuCompany->employees()->count();
            $usersCount = $hesuCompany->users()->count();

            $this->command->info("  - Departments: {$departmentsCount}");
            $this->command->info("  - Employees: {$employeesCount}");
            $this->command->info("  - Users: {$usersCount}");

            // Delete related data first (cascade should handle most, but let's be explicit)
            if ($departmentsCount > 0) {
                $this->command->info("  Deleting departments...");
                $hesuCompany->departments()->delete();
            }

            if ($employeesCount > 0) {
                $this->command->info("  Deleting employees...");
                $hesuCompany->employees()->delete();
            }

            if ($usersCount > 0) {
                $this->command->info("  Deleting users...");
                $hesuCompany->users()->delete();
            }

            // Delete the company
            $this->command->info("  Deleting company...");
            $hesuCompany->delete();

            $this->command->info("✓ HESU company and all related data deleted");
        } else {
            $this->command->warn("✗ HESU company not found in database");
        }

        $this->command->info('');

        // Find LaurianLawrence user with various search patterns
        $laurianUser = User::where(function($q) {
            $q->where('name', 'LIKE', '%Laurian%')
              ->orWhere('name', 'LIKE', '%Lawrence%')
              ->orWhere('name', 'LIKE', '%LaurianLawrence%')
              ->orWhere('name', 'LIKE', '%Laurian Lawrence%')
              ->orWhere('email', 'LIKE', '%Laurian%')
              ->orWhere('email', 'LIKE', '%laurian%')
              ->orWhere('email', 'LIKE', '%Lawrence%')
              ->orWhere('email', 'LIKE', '%lawrence%');
        })->first();

        if ($laurianUser) {
            $this->command->info("✓ Found user: {$laurianUser->name} ({$laurianUser->email}) (ID: {$laurianUser->id})");

            // Check if user has employee record
            $employee = Employee::where('user_id', $laurianUser->id)->first();
            if ($employee) {
                $this->command->info("  Found linked employee (ID: {$employee->id})");
                // Update employee to remove user link
                $employee->update(['user_id' => null]);
                $this->command->info("  Unlinked employee from user");
            }

            // Delete the user
            $this->command->info("  Deleting user...");
            $laurianUser->delete();

            $this->command->info("✓ LaurianLawrence user deleted");
        } else {
            $this->command->warn("✗ LaurianLawrence user not found in database");
        }

        $this->command->info('');

        // Also check for any employees with similar names
        $laurianEmployees = Employee::where(function($q) {
            $q->where('first_name', 'LIKE', '%Laurian%')
              ->orWhere('last_name', 'LIKE', '%Lawrence%')
              ->orWhere('first_name', 'LIKE', '%Lawrence%')
              ->orWhere('last_name', 'LIKE', '%Laurian%')
              ->orWhere('email', 'LIKE', '%Laurian%')
              ->orWhere('email', 'LIKE', '%laurian%')
              ->orWhere('email', 'LIKE', '%Lawrence%')
              ->orWhere('email', 'LIKE', '%lawrence%');
        })->get();

        if ($laurianEmployees->count() > 0) {
            $this->command->info("✓ Found {$laurianEmployees->count()} employee(s) with similar names:");
            foreach ($laurianEmployees as $emp) {
                $this->command->info("  - {$emp->first_name} {$emp->last_name} (ID: {$emp->id}, Email: {$emp->email})");
            }
            $this->command->info("  Deleting these employees...");
            foreach ($laurianEmployees as $emp) {
                $emp->delete();
            }
            $this->command->info("✓ Laurian-related employees deleted");
        } else {
            $this->command->info("✓ No Laurian-related employees found");
        }

        $this->command->info('');
        $this->command->info('=== Cleanup Summary ===');
        $this->command->info('All cleanup operations completed!');
        $this->command->info('');
        $this->command->info('Note: If items were not found, they may have already been deleted or never existed.');
        $this->command->info('You can run this seeder anytime to ensure cleanup.');
    }
}
