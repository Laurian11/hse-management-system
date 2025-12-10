<?php

namespace App\Console\Commands;

use App\Models\BiometricDevice;
use App\Services\MultiDeviceZKTecoService;
use Illuminate\Console\Command;

class TestBiometricConnection extends Command
{
    protected $signature = 'biometric:test 
                            {--ip= : Device IP address to test}
                            {--port=4370 : Port number (default: 4370 for TCP, 80 for HTTP)}
                            {--connection-type=both : Connection type: tcp, http, or both}
                            {--device-id= : Test existing device by ID}';
    
    protected $description = 'Test connection to a biometric device';

    protected $zkService;

    public function __construct()
    {
        parent::__construct();
        $this->zkService = new MultiDeviceZKTecoService();
    }

    public function handle()
    {
        $this->info('=== Testing Biometric Device Connection ===');
        $this->info('');

        $deviceId = $this->option('device-id');
        $ip = $this->option('ip');
        $port = (int) $this->option('port');
        $connectionType = $this->option('connection-type');

        // If device ID provided, use existing device
        if ($deviceId) {
            $device = BiometricDevice::find($deviceId);
            if (!$device) {
                $this->error("Device with ID {$deviceId} not found.");
                return Command::FAILURE;
            }

            $this->info("Testing device: {$device->device_name}");
            $this->info("IP: {$device->device_ip}");
            $this->info("Port: {$device->port}");
            $this->info("Connection Type: {$device->connection_type}");
            $this->info('');

            $status = $this->zkService->getDeviceStatus($device);
            $this->displayStatus($status, $device);
            
            return $status['status'] === 'online' ? Command::SUCCESS : Command::FAILURE;
        }

        // Test new IP
        if (!$ip) {
            $ip = $this->ask('Enter device IP address');
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->error("Invalid IP address: {$ip}");
            return Command::FAILURE;
        }

        $this->info("Testing connection to: {$ip}:{$port}");
        $this->info("Connection Type: {$connectionType}");
        $this->info('');

        // Create temporary device model for testing
        $tempDevice = new BiometricDevice([
            'device_ip' => $ip,
            'port' => $port,
            'connection_type' => $connectionType,
            'device_name' => 'Test Device',
        ]);

        $startTime = microtime(true);
        $connected = $this->zkService->connectToDevice($tempDevice);
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        if ($connected) {
            $this->info('✅ Connection successful!');
            $this->info('');
            $this->table(
                ['Setting', 'Value'],
                [
                    ['IP Address', $ip],
                    ['Port', $port],
                    ['Connection Type', $connectionType],
                    ['Response Time', $responseTime . ' ms'],
                    ['Status', 'Online'],
                ]
            );
            $this->info('');
            $this->info('Device is ready for use.');
            $this->info('');
            $this->info('Next steps:');
            $this->info('1. Add this device to the system: php artisan biometric:scan-network');
            $this->info('2. Or manually add via the web interface');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Connection failed!');
            $this->info('');
            $this->table(
                ['Setting', 'Value'],
                [
                    ['IP Address', $ip],
                    ['Port', $port],
                    ['Connection Type', $connectionType],
                    ['Status', 'Offline'],
                ]
            );
            $this->info('');
            $this->warn('Troubleshooting:');
            $this->warn('1. Check device is powered on');
            $this->warn('2. Verify device IP address is correct');
            $this->warn('3. Ensure device is on the same network');
            $this->warn('4. Check firewall settings (Windows Firewall may block connections)');
            $this->warn('5. Try pinging the device: ping ' . $ip);
            $this->warn('6. Try different port (80 for HTTP, 4370 for TCP)');
            $this->warn('7. Try different connection type');
            $this->info('');
            $this->info('Test ping command:');
            $this->info('  ping ' . $ip);
            $this->info('');
            $this->info('Test port connection:');
            $this->info('  telnet ' . $ip . ' ' . $port);
            return Command::FAILURE;
        }
    }

    /**
     * Display device status
     */
    private function displayStatus(array $status, BiometricDevice $device): void
    {
        $this->info('Connection Status:');
        $this->info('');

        $statusColor = $status['status'] === 'online' ? 'green' : 'red';
        $statusIcon = $status['status'] === 'online' ? '✅' : '❌';

        $this->line("{$statusIcon} Status: <fg={$statusColor}>{$status['status']}</>");
        
        if (isset($status['connection_type'])) {
            $this->line("Connection Type: {$status['connection_type']}");
        }
        
        if (isset($status['version'])) {
            $this->line("Device Version: {$status['version']}");
        }
        
        if (isset($status['device_time'])) {
            $this->line("Device Time: {$status['device_time']}");
        }
        
        if (isset($status['message'])) {
            $this->line("Message: {$status['message']}");
        }

        $this->info('');
        
        if ($status['status'] === 'online') {
            $this->info('Device is online and ready for use.');
        } else {
            $this->warn('Device is offline. Check connection settings.');
        }
    }
}

