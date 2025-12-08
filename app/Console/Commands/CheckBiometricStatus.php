<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckBiometricStatus extends Command
{
    protected $signature = 'biometric:check {employee_id}';
    
    protected $description = 'Check biometric registration status of an employee';

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
            return Command::FAILURE;
        }

        $this->info('=== Biometric Registration Status ===');
        $this->info('');
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Employee ID', $user->employee_id_number ?: 'N/A'],
                ['Email', $user->email ?: 'N/A'],
                ['Company', $user->company ? $user->company->name : 'N/A'],
                ['Status', $user->is_active ? 'Active' : 'Inactive'],
                ['Registered', $user->biometric_template_id ? '✅ Yes' : '❌ No'],
                ['Template ID', $user->biometric_template_id ?: 'Not registered'],
            ]
        );

        if ($user->biometric_template_id) {
            $this->info('');
            $this->info('✅ Employee is registered for biometric attendance');
        } else {
            $this->info('');
            $this->warn('⚠️  Employee is NOT registered');
            $this->info('To register, run: php artisan biometric:enroll ' . $user->id);
        }

        return Command::SUCCESS;
    }
}

