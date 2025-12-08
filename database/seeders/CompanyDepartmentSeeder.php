<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanyDepartmentSeeder extends Seeder
{
    /**
     * Seed construction, ICD, and transportation companies with departments
     */
    public function run(): void
    {
        // Get or create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Administrator',
            'description' => 'Company administrator',
            'level' => 'admin',
            'is_system' => true,
            'is_active' => true,
        ]);

        // 1. Construction Company
        $constructionCompany = Company::firstOrCreate(
            ['name' => 'Tanzania Construction Ltd'],
            [
                'description' => 'Leading construction company in Tanzania specializing in infrastructure and building projects',
                'license_type' => 'enterprise',
                'license_expiry' => now()->addYears(2),
                'max_users' => 500,
                'max_departments' => 50,
                'timezone' => 'Africa/Dar_es_Salaam',
                'currency' => 'TZS',
                'language' => 'en',
                'country' => 'Tanzania',
                'industry_type' => 'construction',
                'employee_count' => 350,
                'is_active' => true,
                'phone' => '+255 22 123 4567',
                'email' => 'info@tanzaniaconstruction.co.tz',
                'website' => 'www.tanzaniaconstruction.co.tz',
                'address' => 'Plot 123, Nyerere Road',
                'city' => 'Dar es Salaam',
                'state' => 'Dar es Salaam',
                'postal_code' => '11101',
            ]
        );

        // Construction Company Departments
        $constructionDepartments = [
            ['name' => 'Project Management', 'code' => 'PM', 'description' => 'Oversees all construction projects'],
            ['name' => 'Site Operations', 'code' => 'SO', 'description' => 'On-site construction activities'],
            ['name' => 'Safety & Health', 'code' => 'SH', 'description' => 'HSE management and compliance'],
            ['name' => 'Engineering', 'code' => 'ENG', 'description' => 'Civil and structural engineering'],
            ['name' => 'Procurement', 'code' => 'PROC', 'description' => 'Materials and equipment procurement'],
            ['name' => 'Quality Control', 'code' => 'QC', 'description' => 'Quality assurance and testing'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Employee management and recruitment'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Financial management and accounting'],
            ['name' => 'Maintenance', 'code' => 'MAINT', 'description' => 'Equipment and facility maintenance'],
            ['name' => 'Logistics', 'code' => 'LOG', 'description' => 'Transportation and material handling'],
        ];

        foreach ($constructionDepartments as $dept) {
            Department::firstOrCreate(
                [
                    'company_id' => $constructionCompany->id,
                    'name' => $dept['name'],
                ],
                [
                    'code' => $dept['code'],
                    'description' => $dept['description'],
                    'is_active' => true,
                ]
            );
        }

        // Create admin user for construction company
        User::firstOrCreate(
            ['email' => 'admin@tanzaniaconstruction.co.tz'],
            [
                'name' => 'Construction Admin',
                'password' => Hash::make('password'),
                'company_id' => $constructionCompany->id,
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("✓ Construction Company created: {$constructionCompany->name}");

        // 2. ICD (Industrial Control/Construction) Company
        $icdCompany = Company::firstOrCreate(
            ['name' => 'ICD Industrial Services Ltd'],
            [
                'description' => 'Industrial control and construction services specializing in automation and industrial projects',
                'license_type' => 'enterprise',
                'license_expiry' => now()->addYears(2),
                'max_users' => 300,
                'max_departments' => 30,
                'timezone' => 'Africa/Dar_es_Salaam',
                'currency' => 'TZS',
                'language' => 'en',
                'country' => 'Tanzania',
                'industry_type' => 'industrial',
                'employee_count' => 200,
                'is_active' => true,
                'phone' => '+255 22 234 5678',
                'email' => 'info@icdindustrial.co.tz',
                'website' => 'www.icdindustrial.co.tz',
                'address' => 'Plot 456, Industrial Area',
                'city' => 'Dar es Salaam',
                'state' => 'Dar es Salaam',
                'postal_code' => '11102',
            ]
        );

        // ICD Company Departments
        $icdDepartments = [
            ['name' => 'Industrial Control', 'code' => 'IC', 'description' => 'Control systems and automation'],
            ['name' => 'Project Engineering', 'code' => 'PE', 'description' => 'Engineering and design'],
            ['name' => 'Safety & Compliance', 'code' => 'SC', 'description' => 'HSE and regulatory compliance'],
            ['name' => 'Operations', 'code' => 'OPS', 'description' => 'Day-to-day operations'],
            ['name' => 'Maintenance', 'code' => 'MAINT', 'description' => 'Equipment maintenance'],
            ['name' => 'Procurement', 'code' => 'PROC', 'description' => 'Materials and supplies'],
            ['name' => 'Quality Assurance', 'code' => 'QA', 'description' => 'Quality control and testing'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'HR management'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Financial operations'],
            ['name' => 'IT & Systems', 'code' => 'IT', 'description' => 'IT infrastructure and systems'],
        ];

        foreach ($icdDepartments as $dept) {
            Department::firstOrCreate(
                [
                    'company_id' => $icdCompany->id,
                    'name' => $dept['name'],
                ],
                [
                    'code' => $dept['code'],
                    'description' => $dept['description'],
                    'is_active' => true,
                ]
            );
        }

        // Create admin user for ICD company
        User::firstOrCreate(
            ['email' => 'admin@icdindustrial.co.tz'],
            [
                'name' => 'ICD Admin',
                'password' => Hash::make('password'),
                'company_id' => $icdCompany->id,
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("✓ ICD Company created: {$icdCompany->name}");

        // 3. Transportation Company
        $transportationCompany = Company::firstOrCreate(
            ['name' => 'Tanzania Transport Services Ltd'],
            [
                'description' => 'Comprehensive transportation and logistics services across Tanzania',
                'license_type' => 'enterprise',
                'license_expiry' => now()->addYears(2),
                'max_users' => 400,
                'max_departments' => 40,
                'timezone' => 'Africa/Dar_es_Salaam',
                'currency' => 'TZS',
                'language' => 'en',
                'country' => 'Tanzania',
                'industry_type' => 'transportation',
                'employee_count' => 280,
                'is_active' => true,
                'phone' => '+255 22 345 6789',
                'email' => 'info@tanzaniatransport.co.tz',
                'website' => 'www.tanzaniatransport.co.tz',
                'address' => 'Plot 789, Transport Hub',
                'city' => 'Dar es Salaam',
                'state' => 'Dar es Salaam',
                'postal_code' => '11103',
            ]
        );

        // Transportation Company Departments
        $transportationDepartments = [
            ['name' => 'Fleet Management', 'code' => 'FM', 'description' => 'Vehicle fleet operations'],
            ['name' => 'Logistics', 'code' => 'LOG', 'description' => 'Cargo and freight management'],
            ['name' => 'Safety & Compliance', 'code' => 'SC', 'description' => 'Transport safety and compliance'],
            ['name' => 'Operations', 'code' => 'OPS', 'description' => 'Daily transport operations'],
            ['name' => 'Maintenance', 'code' => 'MAINT', 'description' => 'Vehicle maintenance and repairs'],
            ['name' => 'Dispatch', 'code' => 'DISP', 'description' => 'Dispatch and routing'],
            ['name' => 'Customer Service', 'code' => 'CS', 'description' => 'Client relations and support'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'HR management'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Financial management'],
            ['name' => 'Warehouse', 'code' => 'WH', 'description' => 'Warehouse and storage operations'],
        ];

        foreach ($transportationDepartments as $dept) {
            Department::firstOrCreate(
                [
                    'company_id' => $transportationCompany->id,
                    'name' => $dept['name'],
                ],
                [
                    'code' => $dept['code'],
                    'description' => $dept['description'],
                    'is_active' => true,
                ]
            );
        }

        // Create admin user for transportation company
        User::firstOrCreate(
            ['email' => 'admin@tanzaniatransport.co.tz'],
            [
                'name' => 'Transport Admin',
                'password' => Hash::make('password'),
                'company_id' => $transportationCompany->id,
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("✓ Transportation Company created: {$transportationCompany->name}");

        $this->command->info("\n=== Seeding Complete ===");
        $this->command->info("Companies created: 3");
        $this->command->info("Total departments: " . (count($constructionDepartments) + count($icdDepartments) + count($transportationDepartments)));
        $this->command->info("\nLogin Credentials:");
        $this->command->info("Construction: admin@tanzaniaconstruction.co.tz / password");
        $this->command->info("ICD: admin@icdindustrial.co.tz / password");
        $this->command->info("Transportation: admin@tanzaniatransport.co.tz / password");
    }
}

