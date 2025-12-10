<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZKLib\ZKLib;

class TestZKTecoPackages extends Command
{
    protected $signature = 'zkteco:test-packages 
                            {--ip= : Device IP address}
                            {--port=4370 : Port number}';
    
    protected $description = 'Test installed ZKTeco packages';

    public function handle()
    {
        $this->info('=== Testing ZKTeco Packages ===');
        $this->info('');

        // Check if packages are installed
        $this->info('Checking installed packages...');
        
        if (class_exists('ZKLib\ZKLib')) {
            $this->info('✅ wnasich/php_zklib is installed');
        } else {
            $this->error('❌ wnasich/php_zklib not found');
            return Command::FAILURE;
        }

        if (class_exists('Rats\Zkteco\Zkteco')) {
            $this->info('✅ rats/zkteco is installed');
        } else {
            $this->warn('⚠️  rats/zkteco class not found (may use different namespace)');
        }

        $this->info('');

        // Test connection if IP provided
        $ip = $this->option('ip');
        if ($ip) {
            $port = (int) $this->option('port');
            
            $this->info("Testing connection to {$ip}:{$port}...");
            $this->info('');

            try {
                $zk = new ZKLib($ip, $port);
                
                if ($zk->connect()) {
                    $this->info('✅ Connection successful!');
                    $this->info('');

                    // Get device version
                    try {
                        $version = $zk->version();
                        $this->info("Device Version: {$version}");
                    } catch (\Exception $e) {
                        $this->warn("Could not get version: " . $e->getMessage());
                    }

                    // Get device time
                    try {
                        $time = $zk->getTime();
                        $this->info("Device Time: {$time}");
                    } catch (\Exception $e) {
                        $this->warn("Could not get time: " . $e->getMessage());
                    }

                    // Get attendance count
                    try {
                        $attendance = $zk->getAttendance();
                        $this->info("Attendance Records: " . count($attendance));
                    } catch (\Exception $e) {
                        $this->warn("Could not get attendance: " . $e->getMessage());
                    }

                    $zk->disconnect();
                    $this->info('');
                    $this->info('✅ Package is working correctly!');
                } else {
                    $this->error('❌ Connection failed');
                    $this->info('');
                    $this->warn('Troubleshooting:');
                    $this->warn('1. Check device is powered on');
                    $this->warn('2. Verify IP address is correct');
                    $this->warn('3. Ensure device is on same network');
                    $this->warn('4. Check firewall settings');
                    return Command::FAILURE;
                }
            } catch (\Exception $e) {
                $this->error('❌ Error: ' . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $this->info('Packages are installed and ready to use!');
            $this->info('');
            $this->info('To test connection, run:');
            $this->info('  php artisan zkteco:test-packages --ip=192.168.1.100');
        }

        return Command::SUCCESS;
    }
}

