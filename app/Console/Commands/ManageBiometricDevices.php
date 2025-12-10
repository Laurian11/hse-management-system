<?php

namespace App\Console\Commands;

use App\Services\BiometricDeviceManager;
use App\Models\BiometricDevice;
use Illuminate\Console\Command;

class ManageBiometricDevices extends Command
{
    protected $signature = 'biometric:manage 
                            {action : Action to perform (health|sync|discover|retry|batch)}
                            {--device-id= : Specific device ID}
                            {--company-id= : Company ID for filtering}
                            {--ip-range= : IP range for discovery}
                            {--date= : Date for sync (Y-m-d)}';
    
    protected $description = 'Advanced biometric device management';

    protected $manager;

    public function __construct(BiometricDeviceManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    public function handle()
    {
        $action = $this->argument('action');

        match($action) {
            'health' => $this->checkHealth(),
            'sync' => $this->syncDevice(),
            'discover' => $this->discoverDevices(),
            'retry' => $this->processRetries(),
            'batch' => $this->batchSync(),
            default => $this->error("Unknown action: {$action}"),
        };

        return Command::SUCCESS;
    }

    protected function checkHealth(): void
    {
        $this->info('=== Device Health Check ===');
        $this->info('');

        $deviceId = $this->option('device-id');
        
        if ($deviceId) {
            $device = BiometricDevice::find($deviceId);
            if (!$device) {
                $this->error("Device not found: {$deviceId}");
                return;
            }
            
            $health = $this->manager->getDeviceHealth($device);
            $this->displayHealth($device, $health);
        } else {
            $devices = $this->getDevices();
            $devicesWithHealth = $this->manager->getDevicesWithHealth();
            
            $this->info("Found " . count($devicesWithHealth) . " device(s)");
            $this->info('');

            $tableData = [];
            foreach ($devicesWithHealth as $item) {
                $device = $item['device'];
                $health = $item['health'];
                $needsAttention = $item['needs_attention'];
                
                $tableData[] = [
                    $device->id,
                    $device->device_name,
                    $device->device_ip,
                    $health['status'],
                    $health['score'] . '/100',
                    $health['response_time'] ? $health['response_time'] . 'ms' : 'N/A',
                    $needsAttention ? '⚠️ Yes' : '✅ No',
                ];
            }

            $this->table(
                ['ID', 'Name', 'IP', 'Status', 'Score', 'Response', 'Needs Attention'],
                $tableData
            );

            // Show devices needing attention
            $needsAttention = array_filter($devicesWithHealth, fn($item) => $item['needs_attention']);
            if (!empty($needsAttention)) {
                $this->info('');
                $this->warn('Devices needing attention:');
                foreach ($needsAttention as $item) {
                    $device = $item['device'];
                    $health = $item['health'];
                    $this->warn("  - {$device->device_name} ({$device->device_ip}): " . implode(', ', $health['issues']));
                }
            }
        }
    }

    protected function syncDevice(): void
    {
        $this->info('=== Sync Device ===');
        $this->info('');

        $deviceId = $this->option('device-id');
        if (!$deviceId) {
            $deviceId = $this->ask('Enter device ID');
        }

        $device = BiometricDevice::find($deviceId);
        if (!$device) {
            $this->error("Device not found: {$deviceId}");
            return;
        }

        $date = $this->option('date') ? \Carbon\Carbon::parse($this->option('date')) : null;

        $this->info("Syncing device: {$device->device_name}");
        $this->info('');

        $result = $this->manager->syncDeviceWithRetry($device, $date);

        if ($result['success']) {
            $this->info('✅ Sync successful!');
            $this->info("Processed: {$result['processed']}");
            $this->info("New records: {$result['new_records']}");
            $this->info("Updated records: {$result['updated_records']}");
        } else {
            $this->error('❌ Sync failed');
            $this->error("Error: {$result['error']}");
            if ($result['retry_scheduled'] ?? false) {
                $this->info("Retry scheduled (attempt {$result['attempt']})");
            }
        }
    }

    protected function discoverDevices(): void
    {
        $this->info('=== Discover Devices ===');
        $this->info('');

        $ipRange = $this->option('ip-range');
        if (!$ipRange) {
            $ipRange = $this->ask('Enter IP range (e.g., 192.168.1.1-192.168.1.254)', $this->getDefaultIPRange());
        }

        $this->info("Scanning network: {$ipRange}");
        $this->info('This may take a few minutes...');
        $this->info('');

        $foundDevices = $this->manager->discoverDevices($ipRange);

        if (empty($foundDevices)) {
            $this->warn('No devices found');
            return;
        }

        $this->info("Found " . count($foundDevices) . " device(s):");
        $this->info('');

        $tableData = [];
        foreach ($foundDevices as $device) {
            $tableData[] = [
                $device['ip'],
                $device['port'],
                $device['connection_type'],
                $device['discovered_at']->format('Y-m-d H:i:s'),
            ];
        }

        $this->table(['IP', 'Port', 'Connection Type', 'Discovered At'], $tableData);

        if ($this->confirm('Would you like to auto-register these devices?', false)) {
            $companyId = $this->option('company-id') ?? $this->ask('Enter company ID');
            
            foreach ($foundDevices as $deviceInfo) {
                $device = $this->manager->autoRegisterDevice($deviceInfo, $companyId);
                if ($device) {
                    $this->info("✅ Registered: {$device->device_name} ({$device->device_ip})");
                }
            }
        }
    }

    protected function processRetries(): void
    {
        $this->info('=== Process Pending Retries ===');
        $this->info('');

        $results = $this->manager->processPendingRetries();

        $this->info("Processed: {$results['processed']} retry(ies)");
        $this->info('');

        if (!empty($results['results'])) {
            foreach ($results['results'] as $deviceName => $result) {
                if ($result['success']) {
                    $this->info("✅ {$deviceName}: Success");
                } else {
                    $this->error("❌ {$deviceName}: {$result['error']}");
                }
            }
        }
    }

    protected function batchSync(): void
    {
        $this->info('=== Batch Sync Devices ===');
        $this->info('');

        $deviceIds = null;
        if ($this->option('device-id')) {
            $deviceIds = [$this->option('device-id')];
        }

        $date = $this->option('date') ? \Carbon\Carbon::parse($this->option('date')) : null;

        $this->info('Starting batch sync...');
        $this->info('');

        $results = $this->manager->batchSyncDevices($deviceIds, $date);

        $this->info("Total devices: {$results['total']}");
        $this->info("Successful: {$results['successful']}");
        $this->info("Failed: {$results['failed']}");
        $this->info('');

        if (!empty($results['results'])) {
            $this->info('Results:');
            foreach ($results['results'] as $deviceName => $result) {
                if ($result['success']) {
                    $this->info("  ✅ {$deviceName}: {$result['new_records']} new, {$result['updated_records']} updated");
                } else {
                    $this->error("  ❌ {$deviceName}: {$result['error']}");
                }
            }
        }
    }

    protected function displayHealth(BiometricDevice $device, array $health): void
    {
        $this->info("Device: {$device->device_name}");
        $this->info("IP: {$device->device_ip}");
        $this->info('');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Status', $health['status']],
                ['Online', $health['online'] ? 'Yes' : 'No'],
                ['Health Score', $health['score'] . '/100'],
                ['Response Time', $health['response_time'] ? $health['response_time'] . 'ms' : 'N/A'],
                ['Uptime', $health['uptime_percentage'] . '%'],
                ['Sync Success Rate', $health['sync_success_rate'] . '%'],
                ['Last Connected', $health['last_connected'] ?? 'Never'],
                ['Last Sync', $health['last_sync'] ?? 'Never'],
            ]
        );

        if (!empty($health['issues'])) {
            $this->info('');
            $this->warn('Issues:');
            foreach ($health['issues'] as $issue) {
                $this->warn("  - {$issue}");
            }
        }
    }

    protected function getDevices()
    {
        $query = BiometricDevice::query();

        if ($this->option('company-id')) {
            $query->where('company_id', $this->option('company-id'));
        }

        return $query->get();
    }

    protected function getDefaultIPRange(): string
    {
        $localIP = gethostbyname(gethostname());
        
        if ($localIP && $localIP !== gethostname()) {
            $parts = explode('.', $localIP);
            if (count($parts) === 4) {
                return "{$parts[0]}.{$parts[1]}.{$parts[2]}.1-{$parts[0]}.{$parts[1]}.{$parts[2]}.254";
            }
        }

        return '192.168.1.1-192.168.1.254';
    }
}

