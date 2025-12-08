<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Seed employees for Construction, ICD, and Transportation companies
     */
    public function run(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $hseRole = Role::where('name', 'hse_officer')->first();
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor'], [
            'display_name' => 'Supervisor',
            'description' => 'Department supervisor',
            'level' => 'supervisor',
            'is_system' => false,
            'is_active' => true,
        ]);
        $employeeRole = Role::firstOrCreate(['name' => 'employee'], [
            'display_name' => 'Employee',
            'description' => 'Regular employee',
            'level' => 'employee',
            'is_system' => false,
            'is_active' => true,
        ]);

        // 1. Construction Company Employees
        $constructionCompany = Company::where('name', 'Tanzania Construction Ltd')->first();
        if ($constructionCompany) {
            $this->seedCompanyEmployees($constructionCompany, 'construction', $adminRole, $hseRole, $supervisorRole, $employeeRole);
        }

        // 2. ICD Company Employees
        $icdCompany = Company::where('name', 'ICD Industrial Services Ltd')->first();
        if ($icdCompany) {
            $this->seedCompanyEmployees($icdCompany, 'icd', $adminRole, $hseRole, $supervisorRole, $employeeRole);
        }

        // 3. Transportation Company Employees
        $transportationCompany = Company::where('name', 'Tanzania Transport Services Ltd')->first();
        if ($transportationCompany) {
            $this->seedCompanyEmployees($transportationCompany, 'transportation', $adminRole, $hseRole, $supervisorRole, $employeeRole);
        }

        $this->command->info("\n=== Employee Seeding Complete ===");
        $this->command->info("Total employees created: " . User::whereNotNull('employee_id_number')->count());
    }

    private function seedCompanyEmployees($company, $companyType, $adminRole, $hseRole, $supervisorRole, $employeeRole)
    {
        $departments = Department::where('company_id', $company->id)->get();
        $employeeCounter = 1;

        // Define employees per department based on company type
        $employeesPerDept = [
            'construction' => [
                'Project Management' => 8,
                'Site Operations' => 45,
                'Safety & Health' => 12,
                'Engineering' => 25,
                'Procurement' => 8,
                'Quality Control' => 15,
                'Human Resources' => 6,
                'Finance' => 8,
                'Maintenance' => 20,
                'Logistics' => 10,
            ],
            'icd' => [
                'Industrial Control' => 30,
                'Project Engineering' => 20,
                'Safety & Compliance' => 10,
                'Operations' => 35,
                'Maintenance' => 18,
                'Procurement' => 6,
                'Quality Assurance' => 12,
                'Human Resources' => 5,
                'Finance' => 7,
                'IT & Systems' => 8,
            ],
            'transportation' => [
                'Fleet Management' => 25,
                'Logistics' => 40,
                'Safety & Compliance' => 10,
                'Operations' => 50,
                'Maintenance' => 30,
                'Dispatch' => 15,
                'Customer Service' => 12,
                'Human Resources' => 5,
                'Finance' => 8,
                'Warehouse' => 20,
            ],
        ];

        $names = [
            'male' => ['John', 'Michael', 'David', 'James', 'Robert', 'William', 'Richard', 'Joseph', 'Thomas', 'Charles', 'Daniel', 'Matthew', 'Anthony', 'Mark', 'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua', 'Kenneth', 'Kevin', 'Brian', 'George', 'Timothy', 'Ronald', 'Jason', 'Edward', 'Jeffrey', 'Ryan', 'Jacob', 'Gary', 'Nicholas', 'Eric', 'Jonathan', 'Stephen', 'Larry', 'Justin', 'Scott', 'Brandon', 'Benjamin', 'Frank', 'Samuel', 'Raymond', 'Patrick', 'Alexander', 'Jack', 'Dennis', 'Jerry', 'Tyler', 'Aaron'],
            'female' => ['Mary', 'Patricia', 'Jennifer', 'Linda', 'Elizabeth', 'Barbara', 'Susan', 'Jessica', 'Sarah', 'Karen', 'Nancy', 'Lisa', 'Betty', 'Margaret', 'Sandra', 'Ashley', 'Kimberly', 'Emily', 'Donna', 'Michelle', 'Dorothy', 'Carol', 'Amanda', 'Melissa', 'Deborah', 'Stephanie', 'Rebecca', 'Sharon', 'Laura', 'Cynthia', 'Kathleen', 'Amy', 'Angela', 'Shirley', 'Anna', 'Brenda', 'Pamela', 'Emma', 'Nicole', 'Helen', 'Samantha', 'Katherine', 'Christine', 'Debra', 'Rachel', 'Carolyn', 'Janet', 'Virginia', 'Maria', 'Heather'],
        ];

        $surnames = ['Mwakitalu', 'Mkoma', 'Mwalimu', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Mwakasege', 'Kisanga', 'Mwakalukwa', 'Mwakasege', 'Mwakalukwa', 'Mwakasege', 'Kisanga', 'Mwakalukwa', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Mwakasege', 'Kisanga', 'Mwakalukwa', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Mwakasege', 'Kisanga', 'Mwakalukwa', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Mwakasege', 'Kisanga', 'Mwakalukwa', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Mwakasege', 'Kisanga', 'Mwakalukwa', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege', 'Mwakalukwa'];

        $jobTitles = [
            'Project Management' => ['Project Manager', 'Assistant Project Manager', 'Project Coordinator', 'Site Engineer', 'Project Planner'],
            'Site Operations' => ['Site Manager', 'Foreman', 'Site Supervisor', 'Construction Worker', 'Laborer', 'Operator', 'Technician'],
            'Safety & Health' => ['HSE Manager', 'Safety Officer', 'Safety Coordinator', 'Health Officer', 'Environmental Officer'],
            'Engineering' => ['Senior Engineer', 'Engineer', 'Junior Engineer', 'Design Engineer', 'Structural Engineer'],
            'Procurement' => ['Procurement Manager', 'Procurement Officer', 'Buyer', 'Procurement Assistant'],
            'Quality Control' => ['QC Manager', 'QC Inspector', 'Quality Engineer', 'QA Officer'],
            'Human Resources' => ['HR Manager', 'HR Officer', 'HR Assistant', 'Recruiter'],
            'Finance' => ['Finance Manager', 'Accountant', 'Finance Officer', 'Accounts Assistant'],
            'Maintenance' => ['Maintenance Manager', 'Maintenance Technician', 'Mechanic', 'Electrician'],
            'Logistics' => ['Logistics Manager', 'Logistics Coordinator', 'Warehouse Supervisor', 'Storekeeper'],
            'Industrial Control' => ['Control Engineer', 'Automation Technician', 'Systems Engineer', 'PLC Programmer'],
            'Project Engineering' => ['Project Engineer', 'Design Engineer', 'Process Engineer', 'Engineering Technician'],
            'Safety & Compliance' => ['Compliance Officer', 'Safety Inspector', 'Regulatory Officer'],
            'Operations' => ['Operations Manager', 'Operations Supervisor', 'Operations Officer', 'Operator'],
            'Quality Assurance' => ['QA Manager', 'QA Engineer', 'QA Inspector', 'Quality Analyst'],
            'IT & Systems' => ['IT Manager', 'System Administrator', 'IT Support', 'Network Engineer'],
            'Fleet Management' => ['Fleet Manager', 'Fleet Supervisor', 'Fleet Coordinator', 'Vehicle Inspector'],
            'Dispatch' => ['Dispatch Manager', 'Dispatcher', 'Route Planner', 'Dispatch Coordinator'],
            'Customer Service' => ['Customer Service Manager', 'Customer Service Representative', 'Client Relations Officer'],
            'Warehouse' => ['Warehouse Manager', 'Warehouse Supervisor', 'Storekeeper', 'Inventory Clerk'],
        ];

        foreach ($departments as $department) {
            $deptName = $department->name;
            $employeeCount = $employeesPerDept[$companyType][$deptName] ?? 5;
            $deptJobTitles = $jobTitles[$deptName] ?? ['Employee', 'Officer', 'Assistant'];

            // Assign first employee as department head
            $isFirst = true;
            $deptHead = null;

            for ($i = 0; $i < $employeeCount; $i++) {
                $gender = rand(0, 1) ? 'male' : 'female';
                $firstName = $names[$gender][array_rand($names[$gender])];
                $lastName = $surnames[array_rand($surnames)];
                $fullName = $firstName . ' ' . $lastName;
                
                $employeeId = strtoupper(substr($companyType, 0, 3)) . '-' . str_pad($employeeCounter, 4, '0', STR_PAD_LEFT);
                $email = strtolower(str_replace(' ', '.', $firstName . '.' . $lastName)) . '@' . strtolower(str_replace(' ', '', $company->name)) . '.co.tz';
                
                // Clean email
                $email = str_replace([' Ltd', ' '], ['', ''], $email);

                // Determine role
                if ($isFirst && in_array($deptName, ['Safety & Health', 'Safety & Compliance'])) {
                    $role = $hseRole;
                } elseif ($isFirst) {
                    $role = $supervisorRole;
                } else {
                    $role = $employeeRole;
                }

                $jobTitle = $deptJobTitles[array_rand($deptJobTitles)];
                if ($isFirst) {
                    $jobTitle = str_replace(['Officer', 'Assistant', 'Technician'], ['Manager', 'Supervisor', 'Lead Technician'], $jobTitle);
                }

                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $fullName,
                        'password' => Hash::make('password'),
                        'company_id' => $company->id,
                        'department_id' => $department->id,
                        'role_id' => $role->id,
                        'employee_id_number' => $employeeId,
                        'job_title' => $jobTitle,
                        'employment_type' => 'full_time',
                        'phone' => '+255 ' . rand(700, 799) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                        'is_active' => true,
                        'email_verified_at' => now(),
                        'date_of_hire' => now()->subMonths(rand(1, 24)),
                    ]
                );

                if ($isFirst) {
                    $deptHead = $user;
                    $isFirst = false;
                }

                $employeeCounter++;
            }

            // Update department with head
            if ($deptHead) {
                $department->update(['head_of_department_id' => $deptHead->id]);
            }
        }

        $this->command->info("✓ Seeded employees for {$company->name}");
    }
}

