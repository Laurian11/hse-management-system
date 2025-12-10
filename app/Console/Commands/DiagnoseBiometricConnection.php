<?php

namespace App\Console\Commands;

use App\Models\BiometricDevice;
use App\Services\MultiDeviceZKTecoService;
use App\Services\EnhancedZKTecoService;
use ZKLib\ZKLib;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DiagnoseBiometricConnection extends Command
{
    protected $signature = 'biometric:diagnose 
                            {--device-id= : Device ID to diagnose}
                            {--ip= : Device IP address}
                            {--port= : Port number}';
    
    protected $description = 'Diagnose biometric device connection issues';

    public function handle()
    {
        $this->info('=== Biometric Device Connection Diagnostics ===');
        $this->info('');

        $deviceId = $this->option('device-id');
        $ip = $this->option('ip');
        $port = (int) ($this->option('port') ?? 4370);

        if ($deviceId) {
            $device = BiometricDevice::find($deviceId);
            if (!$device) {
                $this->error("Device not found: {$deviceId}");
                return Command::FAILURE;
            }
            $ip = $device->device_ip;
            $port = $device->port;
        } elseif (!$ip) {
            $this->error('Please provide --device-id or --ip');
            return Command::FAILURE;
        }

        $this->info("Diagnosing: {$ip}:{$port}");
        $this->info('');

        // Test 1: Ping
        $this->info('1. Testing network connectivity (ping)...');
        $pingResult = $this->testPing($ip);
        $this->displayResult($pingResult['success'], $pingResult['message']);
        $this->info('');

        // Test 2: Port connectivity
        $this->info("2. Testing port {$port} connectivity...");
        $portResult = $this->testPort($ip, $port);
        $this->displayResult($portResult['success'], $portResult['message']);
        $this->info('');

        // Test 3: HTTP API (if port is HTTP)
        if (in_array($port, [80, 8080, 8081])) {
            $this->info("3. Testing HTTP API on port {$port}...");
            $httpResult = $this->testHttpApi($ip, $port);
            $this->displayResult($httpResult['success'], $httpResult['message']);
            if (!$httpResult['success'] && isset($httpResult['error'])) {
                $this->warn("   Error: {$httpResult['error']}");
            }
            $this->info('');
        }

        // Test 4: TCP/ZKLib connection (if port is TCP)
        if ($port == 4370 || !in_array($port, [80, 8080, 8081])) {
            $this->info("4. Testing TCP/ZKLib connection on port {$port}...");
            $tcpResult = $this->testTcpConnection($ip, $port);
            $this->displayResult($tcpResult['success'], $tcpResult['message']);
            if (!$tcpResult['success'] && isset($tcpResult['error'])) {
                $this->warn("   Error: {$tcpResult['error']}");
            }
            $this->info('');
        }

        // Test 5: Try alternative ports
        $this->info('5. Testing alternative ports...');
        $alternativePorts = [4370, 80, 8080, 8081];
        $alternativePorts = array_diff($alternativePorts, [$port]);
        
        $foundPorts = [];
        foreach ($alternativePorts as $altPort) {
            if ($this->testPort($ip, $altPort)['success']) {
                $foundPorts[] = $altPort;
            }
        }
        
        if (!empty($foundPorts)) {
            $this->info("   ✅ Found open ports: " . implode(', ', $foundPorts));
        } else {
            $this->warn("   ⚠️  No alternative ports found open");
        }
        $this->info('');

        // Summary
        $this->info('=== Summary ===');
        $this->info('');
        
        if ($pingResult['success'] && ($portResult['success'] || !empty($foundPorts))) {
            $this->info('✅ Device is reachable on network');
            $this->info('');
            $this->info('Recommendations:');
            
            if (!$portResult['success'] && !empty($foundPorts)) {
                $this->warn("   - Try using port: " . $foundPorts[0]);
                $this->warn("   - Update device configuration with correct port");
            }
            
            if ($port == 8081 || $port == 8080) {
                $this->info("   - Port {$port} is non-standard, ensure device supports HTTP API");
                $this->info("   - Try TCP connection on port 4370 if available");
            }
            
            if ($port == 4370) {
                $this->info("   - Port 4370 uses TCP protocol, not HTTP");
                $this->info("   - Ensure ZKLib package is working correctly");
            }
        } else {
            $this->error('❌ Device connection issues detected');
            $this->info('');
            $this->warn('Troubleshooting steps:');
            $this->warn('1. Verify device is powered on');
            $this->warn('2. Check device IP address is correct');
            $this->warn('3. Ensure device is on same network');
            $this->warn('4. Check firewall settings (Windows Firewall may block)');
            $this->warn('5. Verify device network configuration');
        }

        return Command::SUCCESS;
    }

    protected function testPing(string $ip): array
    {
        $pingCommand = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' 
            ? "ping -n 2 {$ip}" 
            : "ping -c 2 {$ip}";
        
        exec($pingCommand, $output, $returnCode);
        
        return [
            'success' => $returnCode === 0,
            'message' => $returnCode === 0 ? 'Device is reachable' : 'Device not reachable (ping failed)'
        ];
    }

    protected function testPort(string $ip, int $port): array
    {
        $socket = @fsockopen($ip, $port, $errno, $errstr, 3);
        
        if ($socket) {
            fclose($socket);
            return [
                'success' => true,
                'message' => "Port {$port} is open and accessible"
            ];
        }
        
        return [
            'success' => false,
            'message' => "Port {$port} is closed or blocked (Error: {$errstr})"
        ];
    }

    protected function testHttpApi(string $ip, int $port): array
    {
        $portStr = $port == 80 ? '' : ":{$port}";
        $timeout = in_array($port, [8081, 8080]) ? 15 : 10;
        
        // Try multiple endpoints
        $endpoints = ['/api/test', '/api/status', '/api/info', '/'];
        
        foreach ($endpoints as $endpoint) {
            try {
                $response = Http::timeout($timeout)
                    ->connectTimeout($timeout)
                    ->get("http://{$ip}{$portStr}{$endpoint}");
                
                if ($response->successful() || $response->status() == 401) {
                    return [
                        'success' => true,
                        'message' => "HTTP API responding on endpoint: {$endpoint}"
                    ];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Timeout - try next endpoint
                continue;
            } catch (\Exception $e) {
                // Other error - try next endpoint
                continue;
            }
        }
        
        return [
            'success' => false,
            'message' => "HTTP API not responding (timeout after {$timeout}s)",
            'error' => 'Connection timed out - device may not support HTTP API on this port'
        ];
    }

    protected function testTcpConnection(string $ip, int $port): array
    {
        try {
            $zk = new ZKLib($ip, $port);
            $connected = $zk->connect();
            
            if ($connected) {
                $zk->disconnect();
                return [
                    'success' => true,
                    'message' => "TCP/ZKLib connection successful"
                ];
            }
            
            return [
                'success' => false,
                'message' => "TCP/ZKLib connection failed",
                'error' => 'Device did not respond to ZKLib protocol'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "TCP/ZKLib connection error",
                'error' => $e->getMessage()
            ];
        }
    }

    protected function displayResult(bool $success, string $message): void
    {
        if ($success) {
            $this->info("   ✅ {$message}");
        } else {
            $this->error("   ❌ {$message}");
        }
    }
}

