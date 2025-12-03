<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get the demo company
        $company = Company::first();
        if (!$company) {
            $this->command->error('No company found. Please run UserSeeder first.');
            return;
        }

        // Create hierarchical departments
        $departments = [
            [
                'name' => 'Executive Management',
                'code' => 'EXEC',
                'description' => 'Executive management and board of directors',
                'parent_department_id' => null,
                'head_of_department_id' => null, // Will be set after users are created
                'hse_officer_id' => null,
                'location' => 'Headquarters - Floor 10',
                'risk_profile' => 'low',
                'hse_objectives' => json_encode([
                    'Ensure compliance with all HSE regulations',
                    'Promote safety culture throughout organization',
                    'Allocate resources for HSE initiatives'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Health & Safety',
                'code' => 'HSE',
                'description' => 'Health, Safety, and Environment department',
                'parent_department_id' => null,
                'head_of_department_id' => null,
                'hse_officer_id' => null,
                'location' => 'Headquarters - Floor 5',
                'risk_profile' => 'medium',
                'hse_objectives' => json_encode([
                    'Zero lost-time incidents',
                    '100% safety training compliance',
                    'Reduce environmental impact by 20%'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Main operations and production department',
                'parent_department_id' => null,
                'head_of_department_id' => null,
                'hse_officer_id' => null,
                'location' => 'Main Facility - Building A',
                'risk_profile' => 'high',
                'hse_objectives' => json_encode([
                    'Maintain zero recordable injuries',
                    'Achieve 100% PPE compliance',
                    'Conduct weekly safety inspections'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Production',
                'code' => 'PROD',
                'description' => 'Production and manufacturing operations',
                'parent_department_id' => null, // Will be set to Operations ID after creation
                'head_of_department_id' => null,
                'hse_officer_id' => null,
                'location' => 'Main Facility - Building B',
                'risk_profile' => 'high',
                'hse_objectives' => json_encode([
                    'Zero production-related injuries',
                    'Maintain equipment safety standards',
                    'Ensure proper material handling'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance',
                'code' => 'MAINT',
                'description' => 'Equipment maintenance and facilities',
                'parent_department_id' => null, // Will be set to Operations ID after creation
                'head_of_department_id' => null,
                'hse_officer_id' => null,
                'location' => 'Main Facility - Building C',
                'risk_profile' => 'high',
                'hse_objectives' => json_encode([
                    'Safe maintenance procedures',
                    'Lockout/tagout compliance',
                    'Preventive maintenance schedule'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Quality Assurance',
                'code' => 'QA',
                'description' => 'Quality control and assurance',
                'parent_department_id' => null, // Will be set to Operations ID after creation
                'head_of_department_id' => null,
                'hse_officer_id' => null,
                'location' => 'Main Facility - Building D',
                'risk_profile' => 'medium',
                'hse_objectives' => json_encode([
                    'Quality without compromising safety',
                    'Safe testing procedures',
                    'Product safety compliance'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Human resources and administration',
                'parent_department_id' => null,
                'head_of_department_id' => null,
                'hse_officer_id' => null,
                'location' => 'Headquarters - Floor 8',
                'risk_profile' => 'low',
                'hse_objectives' => json_encode([
                    'Employee safety training',
                    'Workplace safety policies',
                    'Occupational health programs'
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Finance and accounting department',
                'parent_department_id' => null,
                'head_of_department_id' => null,
                'hse_officer_id' => null,
                'location' => 'Headquarters - Floor 7',
                'risk_profile' => 'low',
                'hse_objectives' => json_encode([
                    'Budget for HSE initiatives',
                    'Insurance and risk management',
                    'Cost-benefit analysis of safety investments'
                ]),
                'is_active' => true,
            ],
        ];

        // Create departments
        $createdDepartments = [];
        foreach ($departments as $deptData) {
            $department = Department::firstOrCreate(
                ['company_id' => $company->id, 'code' => $deptData['code']],
                [
                    'name' => $deptData['name'],
                    'description' => $deptData['description'],
                    'parent_department_id' => $deptData['parent_department_id'],
                    'head_of_department_id' => $deptData['head_of_department_id'],
                    'hse_officer_id' => $deptData['hse_officer_id'],
                    'location' => $deptData['location'],
                    'risk_profile' => $deptData['risk_profile'],
                    'hse_objectives' => $deptData['hse_objectives'],
                    'is_active' => $deptData['is_active'],
                ]
            );
            $createdDepartments[$deptData['code']] = $department;
        }

        // Set up hierarchy (Production, Maintenance, QA under Operations)
        $operations = $createdDepartments['OPS'];
        $createdDepartments['PROD']->update(['parent_department_id' => $operations->id]);
        $createdDepartments['MAINT']->update(['parent_department_id' => $operations->id]);
        $createdDepartments['QA']->update(['parent_department_id' => $operations->id]);

        // Assign HODs and HSE Officers from existing users
        $admin = User::where('email', 'john@hse.com')->first();
        $hseOfficer = User::where('email', 'sarah@hse.com')->first();
        $employee = User::where('email', 'mike@hse.com')->first();

        if ($admin) {
            $createdDepartments['EXEC']->update(['head_of_department_id' => $admin->id]);
            $createdDepartments['HR']->update(['head_of_department_id' => $admin->id]);
        }

        if ($hseOfficer) {
            $createdDepartments['HSE']->update(['head_of_department_id' => $hseOfficer->id]);
            $createdDepartments['HSE']->update(['hse_officer_id' => $hseOfficer->id]);
        }

        if ($employee) {
            $createdDepartments['OPS']->update(['head_of_department_id' => $employee->id]);
            $createdDepartments['FIN']->update(['head_of_department_id' => $employee->id]);
        }

        $this->command->info('Departments created successfully!');
        $this->command->info('Created ' . Department::count() . ' departments');
        $this->command->info('Department hierarchy established');
        
        // Display department structure
        $this->command->info('Department Structure:');
        foreach ($createdDepartments as $code => $dept) {
            $parent = $dept->parent ? ' (under ' . $dept->parent->code . ')' : '';
            $this->command->info("  - {$dept->name} ({$dept->code}){$parent}");
        }
    }
}
