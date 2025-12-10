<?php

namespace App\Console\Commands;

use App\Services\MultiDeviceZKTecoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScanBiometricDevices extends Command
{
    protected $signature = 'biometric:scan-network 
                            {--ip-range= : IP range to scan (e.g., 192.168.1.1-192.168.1.254)}
                            {--port= : Port to scan (default: auto-scan common ports: 4370, 80, 8081)}
                            {--timeout=2 : Connection timeout in seconds}
                            {--connection-type=both : Connection type: tcp, http, or both}
                            {--auto : Auto-detect network and scan all common ports}';
    
    protected $description = 'Scan local network for biometric devices (auto-detects network and ports)';

    protected $zkService;

    public function __construct()
    {
        parent::__construct();
        $this->zkService = new MultiDeviceZKTecoService();
    }

    public function handle()
    {
        $this->info('=== Scanning Network for Biometric Devices ===');
        $this->info('');

        // Auto-detect network if --auto flag is set or no IP range provided
        $autoMode = $this->option('auto') || !$this->option('ip-range');
        
        // Get IP range
        $ipRange = $this->option('ip-range');
        if (!$ipRange) {
            $defaultRange = $this->getDefaultIPRange();
            if ($autoMode) {
                $ipRange = $defaultRange;
                $this->info("Auto-detected network range: {$ipRange}");
            } else {
                $ipRange = $this->ask('Enter IP range to scan (e.g., 192.168.1.1-192.168.1.254)', $defaultRange);
            }
        }

        $timeout = (int) $this->option('timeout');
        $connectionType = $this->option('connection-type');
        
        // Determine ports to scan
        $portsToScan = [];
        if ($this->option('port')) {
            $portsToScan = [(int) $this->option('port')];
        } else {
            // Auto-scan common biometric device ports
            $portsToScan = [4370, 80, 8081, 8080];
            $this->info("Auto-scanning common ports: " . implode(', ', $portsToScan));
        }

        $this->info("Scanning IP range: {$ipRange}");
        $this->info("Ports: " . implode(', ', $portsToScan));
        $this->info("Connection Type: {$connectionType}");
        $this->info("Timeout: {$timeout}s");
        $this->info('');

        // Parse IP range
        [$startIP, $endIP] = $this->parseIPRange($ipRange);
        if (!$startIP || !$endIP) {
            $this->error('Invalid IP range format. Use: 192.168.1.1-192.168.1.254');
            return Command::FAILURE;
        }

        $this->info("Scanning from {$startIP} to {$endIP}...");
        $this->info('This may take a few minutes...');
        $this->info('');

        $foundDevices = [];
        $totalIPs = $this->ipToLong($endIP) - $this->ipToLong($startIP) + 1;
        $totalScans = $totalIPs * count($portsToScan);
        $progressBar = $this->output->createProgressBar($totalScans);
        $progressBar->start();

        // Scan IPs
        $startLong = $this->ipToLong($startIP);
        $endLong = $this->ipToLong($endIP);

        for ($ipLong = $startLong; $ipLong <= $endLong; $ipLong++) {
            $ip = $this->longToIP($ipLong);
            
            // Try each port
            foreach ($portsToScan as $port) {
                if ($this->testConnection($ip, $port, $timeout, $connectionType)) {
                    // Check if we already found this device on a different port
                    $existing = array_filter($foundDevices, function($d) use ($ip) {
                        return $d['ip'] === $ip;
                    });
                    
                    if (empty($existing)) {
                        $foundDevices[] = [
                            'ip' => $ip,
                            'port' => $port,
                            'connection_type' => $connectionType,
                        ];
                    }
                    break; // Found device on this IP, skip other ports
                }
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->info('');
        $this->info('');

        // Display results
        if (empty($foundDevices)) {
            $this->warn('No biometric devices found on the network.');
            $this->info('');
            $this->info('Troubleshooting tips:');
            $this->info('1. Verify devices are powered on');
            $this->info('2. Check if devices are on the same network');
            $this->info('3. Try scanning port 80 if using HTTP API');
            $this->info('4. Check firewall settings');
            $this->info('5. Verify IP range is correct');
            return Command::SUCCESS;
        }

        $this->info("Found " . count($foundDevices) . " device(s):");
        $this->info('');

        $tableData = [];
        foreach ($foundDevices as $device) {
            $tableData[] = [
                $device['ip'],
                $device['port'],
                $device['connection_type'],
                '✅ Online'
            ];
        }

        $this->table(['IP Address', 'Port', 'Connection Type', 'Status'], $tableData);
        $this->info('');

        // Ask if user wants to add devices
        if ($this->confirm('Would you like to add these devices to the system?', true)) {
            foreach ($foundDevices as $device) {
                $this->addDevice($device);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Test connection to a specific IP and port
     */
    private function testConnection(string $ip, int $port, int $timeout, string $connectionType): bool
    {
        try {
            if ($connectionType === 'tcp' || $connectionType === 'both') {
                // Test TCP connection
                $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);
                if ($socket) {
                    fclose($socket);
                    return true;
                }
            }

            if ($connectionType === 'http' || $connectionType === 'both') {
                // Test HTTP connection
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://{$ip}:{$port}/api/test");
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                
                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($result !== false && ($httpCode == 200 || $httpCode == 401)) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Parse IP range string
     */
    private function parseIPRange(string $range): array
    {
        if (strpos($range, '-') === false) {
            return [null, null];
        }

        [$start, $end] = explode('-', $range, 2);
        $start = trim($start);
        $end = trim($end);

        if (!filter_var($start, FILTER_VALIDATE_IP) || !filter_var($end, FILTER_VALIDATE_IP)) {
            return [null, null];
        }

        return [$start, $end];
    }

    /**
     * Convert IP to long integer
     */
    private function ipToLong(string $ip): int
    {
        return ip2long($ip);
    }

    /**
     * Convert long integer to IP
     */
    private function longToIP(int $long): string
    {
        return long2ip($long);
    }

    /**
     * Get default IP range based on local network
     */
    private function getDefaultIPRange(): string
    {
        // Try to detect local IP
        $localIP = gethostbyname(gethostname());
        
        if ($localIP && $localIP !== gethostname()) {
            $parts = explode('.', $localIP);
            if (count($parts) === 4) {
                return "{$parts[0]}.{$parts[1]}.{$parts[2]}.1-{$parts[0]}.{$parts[1]}.{$parts[2]}.254";
            }
        }

        return '192.168.1.1-192.168.1.254';
    }

    /**
     * Add device to system
     */
    private function addDevice(array $device): void
    {
        $this->info("Adding device: {$device['ip']}");

        $deviceName = $this->ask("Device name for {$device['ip']}", "Device {$device['ip']}");
        $serialNumber = $this->ask("Serial number", "SN-" . str_replace('.', '-', $device['ip']));
        $deviceType = $this->choice("Device type", ['ZKTeco K40', 'ZKTeco K50', 'ZKTeco K60', 'Other'], 0);
        $locationName = $this->ask("Location name", "Main Office");

        // Get company ID
        $companies = \App\Models\Company::all();
        if ($companies->isEmpty()) {
            $this->warn("No companies found. Please create a company first.");
            return;
        }

        $companyChoices = $companies->pluck('name', 'id')->toArray();
        $companyId = $this->choice("Select company", $companyChoices, 0);

        try {
            \App\Models\BiometricDevice::create([
                'device_name' => $deviceName,
                'device_serial_number' => $serialNumber,
                'device_type' => $deviceType,
                'company_id' => $companyId,
                'location_name' => $locationName,
                'device_ip' => $device['ip'],
                'port' => $device['port'],
                'connection_type' => $device['connection_type'],
                'status' => 'active',
                'auto_sync_enabled' => true,
                'daily_attendance_enabled' => true,
                'toolbox_attendance_enabled' => true,
                'work_start_time' => '08:00:00',
                'work_end_time' => '17:00:00',
                'grace_period_minutes' => 15,
                'sync_interval_minutes' => 5,
                'created_by' => 1, // Default admin user
            ]);

            $this->info("✅ Device {$device['ip']} added successfully!");
        } catch (\Exception $e) {
            $this->error("Failed to add device {$device['ip']}: " . $e->getMessage());
        }
    }
}

