<?php

namespace App\Console\Commands;

use App\Services\ZKTecoService;
use Illuminate\Console\Command;

class TestBiometricDevice extends Command
{
    protected $signature = 'biometric:test-device';
    
    protected $description = 'Test connection to biometric device';

    public function handle()
    {
        $this->info('=== Testing Biometric Device Connection ===');
        $this->info('');

        $zkService = new ZKTecoService();
        $result = $zkService->testConnection();

        if ($result['connected']) {
            $this->info('✅ Device connected successfully!');
            $this->info('');
            $this->table(
                ['Setting', 'Value'],
                [
                    ['Device IP', $result['device_ip']],
                    ['Response Time', ($result['response_time'] ?? 'N/A') . 'ms'],
                    ['Status Code', $result['status_code'] ?? 'N/A'],
                    ['Status', 'Online'],
                ]
            );
            $this->info('');
            $this->info('Device is ready for enrollment.');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Device connection failed!');
            $this->info('');
            $this->table(
                ['Setting', 'Value'],
                [
                    ['Device IP', $result['device_ip']],
                    ['Status', 'Offline'],
                    ['Error', $result['error'] ?? 'Unknown error'],
                ]
            );
            $this->info('');
            $this->warn('Troubleshooting:');
            $this->warn('1. Check device is powered on');
            $this->warn('2. Verify device IP in .env file');
            $this->warn('3. Ensure device is on same network');
            $this->warn('4. Check firewall settings');
            $this->warn('5. Try pinging device: ping ' . $result['device_ip']);
            return Command::FAILURE;
        }
    }
}

