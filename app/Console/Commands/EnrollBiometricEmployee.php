<?php

namespace App\Console\Commands;

use App\Services\ZKTecoService;
use App\Models\User;
use Illuminate\Console\Command;

class EnrollBiometricEmployee extends Command
{
    protected $signature = 'biometric:enroll {employee_id} {--force : Re-enroll even if already registered}';
    
    protected $description = 'Enroll a single employee for biometric attendance';

    public function handle()
    {
        $employeeId = $this->argument('employee_id');
        
        // Try to find by ID first, then by employee_id_number
        $user = User::find($employeeId);
        
        if (!$user) {
            $user = User::where('employee_id_number', $employeeId)->first();
        }

        if (!$user) {
            $this->error("Employee not found: {$employeeId}");
            $this->warn('Try using employee ID or employee ID number (e.g., EMP-001)');
            return Command::FAILURE;
        }

        $this->info('=== Biometric Employee Registration ===');
        $this->info('');
        $companyName = $user->company ? $user->company->name : 'N/A';
        $this->info("Employee: {$user->name}");
        $this->info("Employee ID: {$user->employee_id_number}");
        $this->info("Company: {$companyName}");
        $this->info('');

        // Check if already registered
        if ($user->biometric_template_id && !$this->option('force')) {
            $this->warn("⚠️  Employee is already registered!");
            $this->info("Template ID: {$user->biometric_template_id}");
            if (!$this->confirm('Re-enroll anyway?', false)) {
                $this->info('Enrollment cancelled.');
                return Command::SUCCESS;
            }
        }

        // Test device connection
        $zkService = new ZKTecoService();
        $connectionTest = $zkService->testConnection();

        if (!$connectionTest['connected']) {
            $this->error('❌ Cannot connect to biometric device!');
            $this->error('Error: ' . ($connectionTest['error'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        $this->info('✅ Device connected');
        $this->info('Device IP: ' . $connectionTest['device_ip']);
        $this->info('');
        $this->info('📋 Instructions:');
        $this->info('1. Device is ready for enrollment');
        $this->info('2. Employee should place finger on scanner');
        $this->info('3. Wait for device confirmation');
        $this->info('');

        if (!$this->confirm('Ready to start enrollment?', true)) {
            $this->info('Enrollment cancelled.');
            return Command::SUCCESS;
        }

        $this->info('');
        $this->info('Enrolling employee...');

        try {
            if ($zkService->enrollFingerprint($user)) {
                $user->refresh();
                
                $templateId = $user->biometric_template_id ?? 'N/A';
                $this->info('');
                $this->info('✅ Enrollment successful!');
                $this->info("Template ID: {$templateId}");
                $this->info('');
                $this->info('Employee can now use biometric check-in.');
                
                return Command::SUCCESS;
            } else {
                $this->error('');
                $this->error('❌ Enrollment failed!');
                $this->warn('Possible reasons:');
                $this->warn('1. Device not in enrollment mode');
                $this->warn('2. Fingerprint not captured properly');
                $this->warn('3. Device storage full');
                $this->warn('4. Network connection issue');
                
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('');
            $this->error('❌ Error during enrollment: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

