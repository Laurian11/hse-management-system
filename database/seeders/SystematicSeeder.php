<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystematicSeeder extends Seeder
{
    /**
     * Systematically seed Companies, Departments, Employees, and Users
     */
    public function run(): void
    {
        $this->command->info('=== Starting Systematic Seeding ===');
        $this->command->info('');

        // Step 1: Create Roles
        $this->command->info('Step 1: Creating Roles...');
        $roles = $this->createRoles();
        $this->command->info('✓ Roles created');
        $this->command->info('');

        // Step 2: Create Companies
        $this->command->info('Step 2: Creating Companies...');
        $companies = $this->createCompanies();
        $this->command->info('✓ ' . count($companies) . ' companies created');
        $this->command->info('');

        // Step 3: Create Departments for each Company
        $this->command->info('Step 3: Creating Departments...');
        $departmentsByCompany = $this->createDepartments($companies);
        $totalDepartments = array_sum(array_map('count', $departmentsByCompany));
        $this->command->info('✓ ' . $totalDepartments . ' departments created');
        $this->command->info('');

        // Step 4: Create Employees for each Department
        $this->command->info('Step 4: Creating Employees...');
        $employeesByCompany = $this->createEmployees($companies, $departmentsByCompany);
        $totalEmployees = array_sum(array_map('count', $employeesByCompany));
        $this->command->info('✓ ' . $totalEmployees . ' employees created');
        $this->command->info('');

        // Step 5: Create User Accounts for some Employees
        $this->command->info('Step 5: Creating User Accounts...');
        $usersCreated = $this->createUserAccounts($employeesByCompany, $roles);
        $this->command->info('✓ ' . $usersCreated . ' user accounts created');
        $this->command->info('');

        // Step 6: Assign Department Heads
        $this->command->info('Step 6: Assigning Department Heads...');
        $this->assignDepartmentHeads($departmentsByCompany, $employeesByCompany);
        $this->command->info('✓ Department heads assigned');
        $this->command->info('');

        // Summary
        $this->command->info('=== Seeding Complete ===');
        $this->command->info('Companies: ' . Company::count());
        $this->command->info('Departments: ' . Department::count());
        $this->command->info('Employees: ' . Employee::count());
        $this->command->info('Users: ' . User::count());
        $this->command->info('Employees with User Access: ' . Employee::withUserAccess()->count());
        $this->command->info('Employees without User Access: ' . Employee::withoutUserAccess()->count());
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('Super Admin: admin@hse.com / password');
        foreach ($companies as $company) {
            $admin = User::where('company_id', $company->id)->whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->first();
            if ($admin) {
                $this->command->info("{$company->name}: {$admin->email} / password");
            }
        }
    }

    private function createRoles(): array
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'System super administrator with full access',
                'level' => 'super_admin',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Company administrator with management access',
                'level' => 'admin',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'hse_officer',
                'display_name' => 'HSE Officer',
                'description' => 'Health, Safety, and Environment officer',
                'level' => 'hse_officer',
                'is_system' => true,
                'is_active' => true,
            ],
            [
                'name' => 'supervisor',
                'display_name' => 'Supervisor',
                'description' => 'Department supervisor',
                'level' => 'supervisor',
                'is_system' => false,
                'is_active' => true,
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Regular employee',
                'level' => 'employee',
                'is_system' => false,
                'is_active' => true,
            ],
        ];

        $createdRoles = [];
        foreach ($roles as $roleData) {
            $createdRoles[$roleData['name']] = Role::firstOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        return $createdRoles;
    }

    private function createCompanies(): array
    {
        $companiesData = [
            [
                'name' => 'Tanzania Construction Ltd',
                'email' => 'admin@tanzaniaconstruction.co.tz',
                'industry_type' => 'construction',
                'employee_count' => 350,
            ],
            [
                'name' => 'ICD Industrial Services Ltd',
                'email' => 'admin@icdindustrial.co.tz',
                'industry_type' => 'industrial',
                'employee_count' => 200,
            ],
            [
                'name' => 'Tanzania Transport Services Ltd',
                'email' => 'admin@tanzaniatransport.co.tz',
                'industry_type' => 'transportation',
                'employee_count' => 280,
            ],
        ];

        $companies = [];
        foreach ($companiesData as $data) {
            $company = Company::firstOrCreate(
                ['name' => $data['name']],
                [
                    'description' => "Demo company for {$data['industry_type']} industry",
                    'license_type' => 'enterprise',
                    'license_expiry' => now()->addYears(1),
                    'max_users' => 500,
                    'max_departments' => 50,
                    'timezone' => 'Africa/Dar_es_Salaam',
                    'currency' => 'TZS',
                    'language' => 'en',
                    'country' => 'Tanzania',
                    'industry_type' => $data['industry_type'],
                    'employee_count' => $data['employee_count'],
                    'is_active' => true,
                ]
            );
            $companies[$data['name']] = $company;
        }

        return $companies;
    }

    private function createDepartments(array $companies): array
    {
        $departmentsConfig = [
            'Tanzania Construction Ltd' => [
                ['name' => 'Project Management', 'code' => 'PM'],
                ['name' => 'Site Operations', 'code' => 'SO'],
                ['name' => 'Safety & Health', 'code' => 'SH'],
                ['name' => 'Engineering', 'code' => 'ENG'],
                ['name' => 'Procurement', 'code' => 'PROC'],
                ['name' => 'Quality Control', 'code' => 'QC'],
                ['name' => 'Human Resources', 'code' => 'HR'],
                ['name' => 'Finance', 'code' => 'FIN'],
                ['name' => 'Maintenance', 'code' => 'MAINT'],
                ['name' => 'Logistics', 'code' => 'LOG'],
            ],
            'ICD Industrial Services Ltd' => [
                ['name' => 'Industrial Control', 'code' => 'IC'],
                ['name' => 'Project Engineering', 'code' => 'PE'],
                ['name' => 'Safety & Compliance', 'code' => 'SC'],
                ['name' => 'Operations', 'code' => 'OPS'],
                ['name' => 'Maintenance', 'code' => 'MAINT'],
                ['name' => 'Procurement', 'code' => 'PROC'],
                ['name' => 'Quality Assurance', 'code' => 'QA'],
                ['name' => 'Human Resources', 'code' => 'HR'],
                ['name' => 'Finance', 'code' => 'FIN'],
                ['name' => 'IT & Systems', 'code' => 'IT'],
            ],
            'Tanzania Transport Services Ltd' => [
                ['name' => 'Fleet Management', 'code' => 'FM'],
                ['name' => 'Logistics', 'code' => 'LOG'],
                ['name' => 'Safety & Compliance', 'code' => 'SC'],
                ['name' => 'Operations', 'code' => 'OPS'],
                ['name' => 'Maintenance', 'code' => 'MAINT'],
                ['name' => 'Dispatch', 'code' => 'DISP'],
                ['name' => 'Customer Service', 'code' => 'CS'],
                ['name' => 'Human Resources', 'code' => 'HR'],
                ['name' => 'Finance', 'code' => 'FIN'],
                ['name' => 'Warehouse', 'code' => 'WH'],
            ],
        ];

        $departmentsByCompany = [];
        foreach ($companies as $companyName => $company) {
            $departments = [];
            foreach ($departmentsConfig[$companyName] ?? [] as $deptData) {
                $department = Department::firstOrCreate(
                    ['company_id' => $company->id, 'code' => $deptData['code']],
                    [
                        'name' => $deptData['name'],
                        'description' => "{$deptData['name']} department for {$company->name}",
                        'location' => 'Various',
                        'risk_profile' => 'medium',
                        'is_active' => true,
                    ]
                );
                $departments[] = $department;
            }
            $departmentsByCompany[$companyName] = $departments;
        }

        return $departmentsByCompany;
    }

    private function createEmployees(array $companies, array $departmentsByCompany): array
    {
        $employeesPerDept = [
            'Tanzania Construction Ltd' => [
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
            'ICD Industrial Services Ltd' => [
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
            'Tanzania Transport Services Ltd' => [
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
            'male' => ['John', 'Michael', 'David', 'James', 'Robert', 'William', 'Richard', 'Joseph', 'Thomas', 'Charles', 'Daniel', 'Matthew', 'Anthony', 'Mark', 'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua', 'Kenneth', 'Kevin', 'Brian', 'George', 'Timothy', 'Ronald', 'Jason', 'Edward', 'Jeffrey', 'Ryan', 'Jacob'],
            'female' => ['Mary', 'Patricia', 'Jennifer', 'Linda', 'Elizabeth', 'Barbara', 'Susan', 'Jessica', 'Sarah', 'Karen', 'Nancy', 'Lisa', 'Betty', 'Margaret', 'Sandra', 'Ashley', 'Kimberly', 'Emily', 'Donna', 'Michelle', 'Dorothy', 'Carol', 'Amanda', 'Melissa', 'Deborah', 'Stephanie', 'Rebecca', 'Sharon', 'Laura', 'Cynthia'],
        ];

        $surnames = ['Mwakitalu', 'Mkoma', 'Mwalimu', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege', 'Mwakalukwa', 'Kisanga', 'Mwakasege'];

        $jobTitles = [
            'Project Management' => ['Project Manager', 'Assistant Project Manager', 'Project Coordinator', 'Site Engineer'],
            'Site Operations' => ['Site Manager', 'Foreman', 'Site Supervisor', 'Construction Worker', 'Operator'],
            'Safety & Health' => ['HSE Manager', 'Safety Officer', 'Safety Coordinator', 'Health Officer'],
            'Engineering' => ['Senior Engineer', 'Engineer', 'Junior Engineer', 'Design Engineer'],
            'Procurement' => ['Procurement Manager', 'Procurement Officer', 'Buyer'],
            'Quality Control' => ['QC Manager', 'QC Inspector', 'Quality Engineer'],
            'Human Resources' => ['HR Manager', 'HR Officer', 'HR Assistant'],
            'Finance' => ['Finance Manager', 'Accountant', 'Finance Officer'],
            'Maintenance' => ['Maintenance Manager', 'Maintenance Technician', 'Mechanic'],
            'Logistics' => ['Logistics Manager', 'Logistics Coordinator', 'Warehouse Supervisor'],
            'Industrial Control' => ['Control Engineer', 'Automation Technician', 'Systems Engineer'],
            'Project Engineering' => ['Project Engineer', 'Design Engineer', 'Process Engineer'],
            'Safety & Compliance' => ['Compliance Officer', 'Safety Inspector', 'Regulatory Officer'],
            'Operations' => ['Operations Manager', 'Operations Supervisor', 'Operations Officer'],
            'Quality Assurance' => ['QA Manager', 'QA Engineer', 'QA Inspector'],
            'IT & Systems' => ['IT Manager', 'System Administrator', 'IT Support'],
            'Fleet Management' => ['Fleet Manager', 'Fleet Supervisor', 'Fleet Coordinator'],
            'Dispatch' => ['Dispatch Manager', 'Dispatcher', 'Route Planner'],
            'Customer Service' => ['Customer Service Manager', 'Customer Service Representative'],
            'Warehouse' => ['Warehouse Manager', 'Warehouse Supervisor', 'Storekeeper'],
        ];

        $employeesByCompany = [];
        $employeeCounter = 1;

        foreach ($companies as $companyName => $company) {
            $employees = [];
            $departments = $departmentsByCompany[$companyName] ?? [];
            $companyPrefix = strtoupper(substr($companyName, 0, 3));

            foreach ($departments as $department) {
                $deptName = $department->name;
                $employeeCount = $employeesPerDept[$companyName][$deptName] ?? 5;
                $deptJobTitles = $jobTitles[$deptName] ?? ['Employee', 'Officer'];

                for ($i = 0; $i < $employeeCount; $i++) {
                    $gender = rand(0, 1) ? 'male' : 'female';
                    $firstName = $names[$gender][array_rand($names[$gender])];
                    $lastName = $surnames[array_rand($surnames)];
                    
                    $employeeId = $companyPrefix . '-' . str_pad($employeeCounter, 4, '0', STR_PAD_LEFT);
                    $email = strtolower(str_replace(' ', '.', $firstName . '.' . $lastName)) . '@' . strtolower(str_replace([' Ltd', ' '], ['', ''], $company->name)) . '.co.tz';

                    $jobTitle = $deptJobTitles[array_rand($deptJobTitles)];
                    if ($i === 0) {
                        $jobTitle = str_replace(['Officer', 'Assistant', 'Technician'], ['Manager', 'Supervisor', 'Lead Technician'], $jobTitle);
                    }

                    $employee = Employee::create([
                        'company_id' => $company->id,
                        'department_id' => $department->id,
                        'employee_id_number' => $employeeId,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'phone' => '+255 ' . rand(700, 799) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                        'date_of_hire' => now()->subMonths(rand(1, 24)),
                        'employment_type' => 'full_time',
                        'employment_status' => 'active',
                        'job_title' => $jobTitle,
                        'is_active' => true,
                    ]);

                    $employees[] = $employee;
                    $employeeCounter++;
                }
            }

            $employeesByCompany[$companyName] = $employees;
        }

        return $employeesByCompany;
    }

    private function createUserAccounts(array $employeesByCompany, array $roles): int
    {
        $usersCreated = 0;

        // Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@hse.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'company_id' => null,
                'role_id' => $roles['super_admin']->id,
                'employee_id_number' => 'SYS-0001',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $usersCreated++;

        foreach ($employeesByCompany as $companyName => $employees) {
            $company = Company::where('name', $companyName)->first();
            if (!$company) continue;

            // Create company admin (first employee in first department)
            if (!empty($employees)) {
                $adminEmployee = $employees[0];
                $adminUser = User::firstOrCreate(
                    ['email' => $adminEmployee->email],
                    [
                        'name' => $adminEmployee->full_name,
                        'password' => Hash::make('password'),
                        'company_id' => $company->id,
                        'role_id' => $roles['admin']->id,
                        'department_id' => $adminEmployee->department_id,
                        'employee_id_number' => $adminEmployee->employee_id_number,
                        'phone' => $adminEmployee->phone,
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );
                $adminEmployee->update(['user_id' => $adminUser->id]);
                $usersCreated++;
            }

            // Create user accounts for 30% of employees (random selection)
            $employeesToCreateUsers = $employees;
            shuffle($employeesToCreateUsers);
            $countToCreate = (int) ceil(count($employeesToCreateUsers) * 0.3);

            for ($i = 0; $i < $countToCreate && $i < count($employeesToCreateUsers); $i++) {
                $employee = $employeesToCreateUsers[$i];
                
                // Skip if already has user account
                if ($employee->user_id) continue;

                // Skip if email already exists in users table
                if ($employee->email && User::where('email', $employee->email)->exists()) {
                    continue;
                }

                // Determine role based on department
                $role = $roles['employee'];
                if (stripos($employee->department->name, 'Safety') !== false || 
                    stripos($employee->department->name, 'Health') !== false) {
                    $role = $roles['hse_officer'];
                } elseif ($i < 5) { // First few employees are supervisors
                    $role = $roles['supervisor'];
                }

                // Use employee email or generate one if not available
                $userEmail = $employee->email;
                if (!$userEmail || User::where('email', $userEmail)->exists()) {
                    $userEmail = $employee->employee_id_number . '@' . strtolower(str_replace([' Ltd', ' '], ['', ''], $company->name)) . '.co.tz';
                }

                $user = User::firstOrCreate(
                    ['email' => $userEmail],
                    [
                        'name' => $employee->full_name,
                        'password' => Hash::make('password'),
                        'company_id' => $company->id,
                        'role_id' => $role->id,
                        'department_id' => $employee->department_id,
                        'employee_id_number' => $employee->employee_id_number,
                        'phone' => $employee->phone,
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );

                $employee->update(['user_id' => $user->id]);
                $usersCreated++;
            }
        }

        return $usersCreated;
    }

    private function assignDepartmentHeads(array $departmentsByCompany, array $employeesByCompany): void
    {
        foreach ($departmentsByCompany as $companyName => $departments) {
            $employees = $employeesByCompany[$companyName] ?? [];
            
            foreach ($departments as $department) {
                // Find first employee in this department who has a user account
                $deptEmployee = collect($employees)->first(function($emp) use ($department) {
                    return $emp->department_id === $department->id && $emp->user_id;
                });

                // If no employee with user account, find any employee
                if (!$deptEmployee) {
                    $deptEmployee = collect($employees)->first(function($emp) use ($department) {
                        return $emp->department_id === $department->id;
                    });
                }

                if ($deptEmployee && $deptEmployee->user_id) {
                    // Department head_of_department_id references users table
                    $department->update(['head_of_department_id' => $deptEmployee->user_id]);
                }
            }
        }
    }
}

