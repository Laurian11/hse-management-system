<?php

namespace App\Services;

use App\Models\BiometricDevice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Network Connection Service
 * 
 * Handles connections to biometric devices via:
 * - Local network (same subnet)
 * - Remote network (different subnet, same organization)
 * - Internet (public IP, VPN, port forwarding)
 */
class NetworkConnectionService
{
    /**
     * Detect network type for an IP address
     */
    public function detectNetworkType(string $ip): string
    {
        // Check if IP is private (local network)
        if ($this->isPrivateIP($ip)) {
            return 'local';
        }

        // Check if IP is public (internet)
        if ($this->isPublicIP($ip)) {
            return 'internet';
        }

        // Default to local
        return 'local';
    }

    /**
     * Check if IP is on local network
     */
    public function isLocalNetwork(string $ip): bool
    {
        return $this->isPrivateIP($ip) && $this->isSameSubnet($ip);
    }

    /**
     * Check if IP is private (RFC 1918)
     */
    public function isPrivateIP(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        // Private IP ranges
        $privateRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            '127.0.0.0/8',
        ];

        foreach ($privateRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is public (internet)
     */
    public function isPublicIP(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        return !$this->isPrivateIP($ip) && !$this->isLocalhost($ip);
    }

    /**
     * Check if IP is localhost
     */
    public function isLocalhost(string $ip): bool
    {
        return in_array($ip, ['127.0.0.1', '::1', 'localhost']);
    }

    /**
     * Check if IP is in same subnet as server
     */
    public function isSameSubnet(string $ip): bool
    {
        try {
            $serverIP = $this->getServerIP();
            if (!$serverIP) {
                return false;
            }

            $serverSubnet = $this->getSubnet($serverIP);
            $deviceSubnet = $this->getSubnet($ip);

            return $serverSubnet === $deviceSubnet;
        } catch (\Exception $e) {
            Log::warning("Failed to check subnet: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get server's local IP address
     */
    public function getServerIP(): ?string
    {
        $cacheKey = 'server_local_ip';
        
        return Cache::remember($cacheKey, 3600, function() {
            // Try multiple methods to get local IP
            $methods = [
                function() {
                    return gethostbyname(gethostname());
                },
                function() {
                    $ip = shell_exec("hostname -I | awk '{print $1}'");
                    return $ip ? trim($ip) : null;
                },
                function() {
                    // Windows method
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        $ip = shell_exec("ipconfig | findstr /i \"IPv4\"");
                        if (preg_match('/\d+\.\d+\.\d+\.\d+/', $ip, $matches)) {
                            return $matches[0];
                        }
                    }
                    return null;
                },
            ];

            foreach ($methods as $method) {
                try {
                    $ip = $method();
                    if ($ip && filter_var($ip, FILTER_VALIDATE_IP) && $ip !== '127.0.0.1') {
                        return $ip;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return null;
        });
    }

    /**
     * Get subnet from IP (assumes /24)
     */
    protected function getSubnet(string $ip): string
    {
        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            return "{$parts[0]}.{$parts[1]}.{$parts[2]}.0/24";
        }
        return '';
    }

    /**
     * Check if IP is in CIDR range
     */
    protected function ipInRange(string $ip, string $range): bool
    {
        list($subnet, $mask) = explode('/', $range);
        
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - (int)$mask);
        
        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }

    /**
     * Get connection IP for device (local or public)
     */
    public function getConnectionIP(BiometricDevice $device): string
    {
        // If device has public IP and network type is internet/remote, use public IP
        if ($device->network_type === 'internet' || $device->network_type === 'remote') {
            if ($device->public_ip) {
                return $device->public_ip;
            }
        }

        // Default to device IP
        return $device->device_ip;
    }

    /**
     * Get connection timeout based on network type
     */
    public function getConnectionTimeout(BiometricDevice $device): int
    {
        // Use device-specific timeout if set
        if ($device->connection_timeout) {
            return $device->connection_timeout;
        }

        // Default timeouts based on network type
        $networkType = $device->network_type ?? 'local';
        switch ($networkType) {
            case 'local':
                return 10;
            case 'remote':
                return 20;
            case 'internet':
                return 30;
            default:
                return 15;
        }
    }

    /**
     * Test network connectivity
     */
    public function testNetworkConnectivity(string $ip, int $port, int $timeout = 10): array
    {
        $result = [
            'reachable' => false,
            'port_open' => false,
            'response_time' => null,
            'network_type' => $this->detectNetworkType($ip),
            'is_local' => $this->isLocalNetwork($ip),
            'error' => null,
        ];

        $startTime = microtime(true);

        // Test 1: Ping
        try {
            $pingResult = $this->ping($ip);
            $result['reachable'] = $pingResult;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        // Test 2: Port connectivity
        try {
            $socket = @fsockopen($ip, $port, $errno, $errstr, min($timeout, 5));
            if ($socket) {
                $result['port_open'] = true;
                fclose($socket);
            }
        } catch (\Exception $e) {
            // Port not open
        }

        $result['response_time'] = round((microtime(true) - $startTime) * 1000, 2);

        return $result;
    }

    /**
     * Ping an IP address
     */
    protected function ping(string $ip): bool
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $command = $isWindows 
            ? "ping -n 2 -w 1000 {$ip}" 
            : "ping -c 2 -W 1 {$ip}";
        
        exec($command, $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Auto-detect and update device network configuration
     */
    public function autoDetectNetwork(BiometricDevice $device): void
    {
        if (!$device->auto_detect_network) {
            return;
        }

        $networkType = $this->detectNetworkType($device->device_ip);
        $isLocal = $this->isLocalNetwork($device->device_ip);

        $updates = [
            'network_type' => $networkType,
            'last_network_check' => now(),
        ];

        // If device IP is private but we can't reach it, might need public IP
        if ($networkType === 'local' && !$isLocal) {
            // Keep as local but note it might need VPN or port forwarding
            Log::info("Device {$device->device_name} is on private network but not same subnet");
        }

        // If public IP exists, use it for internet connections
        if ($networkType === 'internet' && !$device->public_ip) {
            // Suggest using public IP
            Log::info("Device {$device->device_name} has public IP but public_ip field not set");
        }

        $device->update($updates);
    }

    /**
     * Get recommended connection settings for network type
     */
    public function getRecommendedSettings(string $networkType): array
    {
        switch ($networkType) {
            case 'local':
                return [
                    'connection_timeout' => 10,
                    'requires_vpn' => false,
                    'auto_detect_network' => true,
                ];
            case 'remote':
                return [
                    'connection_timeout' => 20,
                    'requires_vpn' => true,
                    'auto_detect_network' => true,
                ];
            case 'internet':
                return [
                    'connection_timeout' => 30,
                    'requires_vpn' => false, // May use port forwarding instead
                    'auto_detect_network' => true,
                ];
            default:
                return [
                    'connection_timeout' => 15,
                    'requires_vpn' => false,
                    'auto_detect_network' => true,
                ];
        }
    }
}

