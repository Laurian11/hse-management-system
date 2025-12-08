<?php

namespace App\Console\Commands;

use App\Services\ZKTecoService;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Console\Command;

class EnrollBiometricAll extends Command
{
    protected $signature = 'biometric:enroll-all 
                            {--company= : Company ID to enroll employees from}
                            {--department= : Department ID to enroll employees from}
                            {--force : Re-enroll even if already registered}';
    
    protected $description = 'Enroll all active employees for biometric attendance';

    public function handle()
    {
        $this->info('=== Biometric Employee Registration ===');
        $this->info('');

        // Test device connection first
        $zkService = new ZKTecoService();
        $connectionTest = $zkService->testConnection();

        if (!$connectionTest['connected']) {
            $this->error('❌ Cannot connect to biometric device!');
            $this->error('Error: ' . ($connectionTest['error'] ?? 'Unknown error'));
            $this->warn('');
            $this->warn('Please check:');
            $this->warn('1. Device is powered on and connected to network');
            $this->warn('2. Device IP is correct in .env file');
            $this->warn('3. Device is on same network as server');
            return Command::FAILURE;
        }

        $this->info('✅ Device connected successfully');
        $this->info('Device IP: ' . $connectionTest['device_ip']);
        $this->info('Response Time: ' . ($connectionTest['response_time'] ?? 'N/A') . 'ms');
        $this->info('');

        // Get employees to enroll
        $query = User::where('is_active', true);

        if ($this->option('company')) {
            $company = Company::find($this->option('company'));
            if (!$company) {
                $this->error('Company not found!');
                return Command::FAILURE;
            }
            $query->where('company_id', $company->id);
            $this->info("Filtering by company: {$company->name}");
        }

        if ($this->option('department')) {
            $department = Department::find($this->option('department'));
            if (!$department) {
                $this->error('Department not found!');
                return Command::FAILURE;
            }
            $query->whereHas('employee', function($q) use ($department) {
                $q->where('department_id', $department->id);
            });
            $this->info("Filtering by department: {$department->name}");
        }

        $employees = $query->get();

        if ($employees->isEmpty()) {
            $this->warn('No employees found to enroll.');
            return Command::SUCCESS;
        }

        $this->info("Found {$employees->count()} employee(s) to enroll");
        $this->info('');

        if (!$this->option('force')) {
            // Filter out already registered employees
            $unregistered = $employees->filter(function($user) {
                return empty($user->biometric_template_id);
            });

            if ($unregistered->isEmpty()) {
                $this->info('✅ All employees are already registered!');
                $this->info('Use --force to re-enroll all employees.');
                return Command::SUCCESS;
            }

            $unregisteredCount = $unregistered->count();
            $registeredCount = $employees->count() - $unregisteredCount;
            $this->info("{$unregisteredCount} employee(s) need registration");
            $this->info("{$registeredCount} already registered");
            $this->info('');

            if (!$this->confirm('Continue with enrollment?', true)) {
                $this->info('Enrollment cancelled.');
                return Command::SUCCESS;
            }

            $employees = $unregistered;
        }

        $this->info('');
        $this->info('Starting enrollment process...');
        $this->info('Please ensure employees are ready to scan their fingerprints.');
        $this->info('');

        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        $progressBar = $this->output->createProgressBar($employees->count());
        $progressBar->start();

        foreach ($employees as $employee) {
            // Skip if already registered and not forcing
            if (!$this->option('force') && $employee->biometric_template_id) {
                $results['skipped']++;
                $progressBar->advance();
                continue;
            }

            try {
                if ($zkService->enrollFingerprint($employee)) {
                    $results['success']++;
                    $this->line("\n✅ {$employee->name} ({$employee->employee_id_number})");
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Failed to enroll: {$employee->name}";
                    $this->line("\n❌ {$employee->name} ({$employee->employee_id_number})");
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error enrolling {$employee->name}: " . $e->getMessage();
                $this->line("\n❌ {$employee->name} - Error: " . $e->getMessage());
            }

            $progressBar->advance();
            
            // Small delay to avoid overwhelming device
            usleep(500000); // 0.5 seconds
        }

        $progressBar->finish();
        $this->info('');
        $this->info('');

        // Summary
        $this->info('=== Enrollment Summary ===');
        $this->info("✅ Success: {$results['success']}");
        $this->info("❌ Failed: {$results['failed']}");
        if ($results['skipped'] > 0) {
            $this->info("⏭️  Skipped: {$results['skipped']}");
        }
        $this->info('');

        if (!empty($results['errors'])) {
            $this->warn('Errors:');
            foreach ($results['errors'] as $error) {
                $this->warn("  - {$error}");
            }
            $this->info('');
        }

        if ($results['success'] > 0) {
            $this->info('✅ Enrollment completed successfully!');
            $this->info('Employees can now use biometric check-in for toolbox talks.');
        }

        return Command::SUCCESS;
    }
}

