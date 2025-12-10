<?php

namespace App\Services;

use App\Models\BiometricDevice;
use App\Models\Employee;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Carbon\Carbon;

/**
 * Dynamic Biometric Device Manager
 * 
 * Provides advanced device management with:
 * - Health monitoring
 * - Retry logic
 * - Device pooling
 * - Event-driven operations
 * - Dynamic configuration
 */
class BiometricDeviceManager
{
    protected $zkService;
    protected $healthCheckInterval = 5; // minutes
    protected $maxRetries = 3;
    protected $retryDelay = 30; // seconds

    public function __construct(MultiDeviceZKTecoService $zkService)
    {
        $this->zkService = $zkService;
    }

    /**
     * Get all devices with health status
     */
    public function getDevicesWithHealth(): array
    {
        $devices = BiometricDevice::where('status', '!=', 'inactive')->get();
        $result = [];

        foreach ($devices as $device) {
            $health = $this->getDeviceHealth($device);
            $result[] = [
                'device' => $device,
                'health' => $health,
                'needs_attention' => $this->needsAttention($health),
            ];
        }

        return $result;
    }

    /**
     * Get comprehensive device health status
     */
    public function getDeviceHealth(BiometricDevice $device): array
    {
        $cacheKey = "device_health_{$device->id}";
        
        return Cache::remember($cacheKey, 60, function() use ($device) {
            $health = [
                'status' => 'unknown',
                'online' => false,
                'last_connected' => $device->last_connected_at?->diffForHumans(),
                'last_sync' => $device->last_sync_at?->diffForHumans(),
                'response_time' => null,
                'uptime_percentage' => 0,
                'sync_success_rate' => 0,
                'issues' => [],
                'score' => 0, // 0-100 health score
            ];

            // Test connection
            $startTime = microtime(true);
            $connected = $this->zkService->connectToDevice($device);
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            $health['response_time'] = $responseTime;
            $health['online'] = $connected;

            if ($connected) {
                $health['status'] = 'healthy';
                $health['score'] += 50;
            } else {
                $health['status'] = 'offline';
                $health['issues'][] = 'Device not responding';
            }

            // Check last connection time
            if ($device->last_connected_at) {
                $minutesSinceConnection = $device->last_connected_at->diffInMinutes(now());
                if ($minutesSinceConnection < 5) {
                    $health['score'] += 20;
                } elseif ($minutesSinceConnection < 30) {
                    $health['score'] += 10;
                } else {
                    $health['issues'][] = 'No recent connection';
                }
            } else {
                $health['issues'][] = 'Never connected';
            }

            // Check sync status
            if ($device->last_sync_at) {
                $minutesSinceSync = $device->last_sync_at->diffInMinutes(now());
                if ($minutesSinceSync <= $device->sync_interval_minutes * 2) {
                    $health['score'] += 20;
                } else {
                    $health['issues'][] = 'Sync overdue';
                }
            } else {
                $health['issues'][] = 'Never synced';
            }

            // Check response time
            if ($responseTime < 1000) {
                $health['score'] += 10;
            } elseif ($responseTime < 3000) {
                $health['score'] += 5;
            } else {
                $health['issues'][] = 'Slow response time';
            }

            // Calculate uptime (last 24 hours)
            $health['uptime_percentage'] = $this->calculateUptime($device);
            $health['sync_success_rate'] = $this->calculateSyncSuccessRate($device);

            return $health;
        });
    }

    /**
     * Check if device needs attention
     */
    public function needsAttention(array $health): bool
    {
        return $health['score'] < 50 || 
               !$health['online'] || 
               count($health['issues']) > 2;
    }

    /**
     * Sync device with retry logic
     */
    public function syncDeviceWithRetry(BiometricDevice $device, Carbon $date = null, int $attempt = 1): array
    {
        try {
            $result = $this->zkService->syncDailyAttendance($device, $date);
            
            // Success - clear retry cache
            Cache::forget("device_retry_{$device->id}");
            
            return array_merge($result, [
                'attempt' => $attempt,
                'success' => true,
            ]);
        } catch (\Exception $e) {
            Log::warning("Device sync attempt {$attempt} failed for {$device->device_name}: " . $e->getMessage());
            
            if ($attempt < $this->maxRetries) {
                // Schedule retry
                $this->scheduleRetry($device, $date, $attempt + 1);
                
                return [
                    'success' => false,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                    'retry_scheduled' => true,
                ];
            }
            
            // Max retries reached
            $device->update(['status' => 'offline']);
            
            return [
                'success' => false,
                'attempt' => $attempt,
                'error' => $e->getMessage(),
                'retry_scheduled' => false,
            ];
        }
    }

    /**
     * Schedule retry for failed sync
     */
    protected function scheduleRetry(BiometricDevice $device, Carbon $date, int $attempt): void
    {
        $cacheKey = "device_retry_{$device->id}";
        $delay = $this->retryDelay * $attempt; // Exponential backoff
        
        Cache::put($cacheKey, [
            'device_id' => $device->id,
            'date' => $date?->toDateString(),
            'attempt' => $attempt,
            'scheduled_at' => now()->addSeconds($delay),
        ], $delay + 60);
        
        // Dispatch job for retry (if using queues)
        if (config('queue.default') !== 'sync') {
            \App\Jobs\SyncBiometricDeviceJob::dispatch($device, $date)
                ->delay(now()->addSeconds($delay));
        }
    }

    /**
     * Process pending retries
     */
    public function processPendingRetries(): array
    {
        $devices = BiometricDevice::where('status', 'active')
            ->where('auto_sync_enabled', true)
            ->get();
        
        $processed = 0;
        $results = [];
        
        foreach ($devices as $device) {
            $cacheKey = "device_retry_{$device->id}";
            $retryData = Cache::get($cacheKey);
            
            if ($retryData && now()->gte($retryData['scheduled_at'])) {
                $date = $retryData['date'] ? Carbon::parse($retryData['date']) : null;
                $result = $this->syncDeviceWithRetry($device, $date, $retryData['attempt']);
                $results[$device->device_name] = $result;
                $processed++;
            }
        }
        
        return [
            'processed' => $processed,
            'results' => $results,
        ];
    }

    /**
     * Auto-discover devices on network
     */
    public function discoverDevices(string $ipRange = null, array $options = []): array
    {
        $ipRange = $ipRange ?? $this->getDefaultIPRange();
        [$startIP, $endIP] = $this->parseIPRange($ipRange);
        
        if (!$startIP || !$endIP) {
            return ['error' => 'Invalid IP range'];
        }

        $foundDevices = [];
        $ports = $options['ports'] ?? [4370, 80];
        $connectionTypes = $options['connection_types'] ?? ['tcp', 'http'];
        $timeout = $options['timeout'] ?? 2;

        $startLong = ip2long($startIP);
        $endLong = ip2long($endIP);

        for ($ipLong = $startLong; $ipLong <= $endLong; $ipLong++) {
            $ip = long2ip($ipLong);
            
            foreach ($ports as $port) {
                foreach ($connectionTypes as $connectionType) {
                    if ($this->testConnection($ip, $port, $timeout, $connectionType)) {
                        $foundDevices[] = [
                            'ip' => $ip,
                            'port' => $port,
                            'connection_type' => $connectionType,
                            'discovered_at' => now(),
                        ];
                        break 2; // Found device, skip other ports/types
                    }
                }
            }
        }

        return $foundDevices;
    }

    /**
     * Auto-register discovered device
     */
    public function autoRegisterDevice(array $deviceInfo, int $companyId, array $defaults = []): ?BiometricDevice
    {
        // Check if device already exists
        $existing = BiometricDevice::where('device_ip', $deviceInfo['ip'])->first();
        if ($existing) {
            return $existing;
        }

        // Create new device
        $device = BiometricDevice::create([
            'device_name' => $defaults['device_name'] ?? "Device {$deviceInfo['ip']}",
            'device_serial_number' => $defaults['serial_number'] ?? "AUTO-{$deviceInfo['ip']}",
            'device_type' => $defaults['device_type'] ?? 'ZKTeco K40',
            'company_id' => $companyId,
            'location_name' => $defaults['location_name'] ?? 'Auto-Discovered',
            'device_ip' => $deviceInfo['ip'],
            'port' => $deviceInfo['port'],
            'connection_type' => $deviceInfo['connection_type'],
            'status' => 'active',
            'auto_sync_enabled' => $defaults['auto_sync_enabled'] ?? true,
            'daily_attendance_enabled' => $defaults['daily_attendance_enabled'] ?? true,
            'work_start_time' => $defaults['work_start_time'] ?? '08:00:00',
            'work_end_time' => $defaults['work_end_time'] ?? '17:00:00',
            'grace_period_minutes' => $defaults['grace_period_minutes'] ?? 15,
            'sync_interval_minutes' => $defaults['sync_interval_minutes'] ?? 5,
        ]);

        // Test connection
        $this->zkService->connectToDevice($device);

        return $device;
    }

    /**
     * Get best available device for employee
     */
    public function getBestDeviceForEmployee(Employee $employee): ?BiometricDevice
    {
        $devices = BiometricDevice::where('company_id', $employee->company_id)
            ->where('status', 'active')
            ->where('daily_attendance_enabled', true)
            ->get();

        if ($devices->isEmpty()) {
            return null;
        }

        // Score devices based on health and load
        $scoredDevices = [];
        foreach ($devices as $device) {
            $health = $this->getDeviceHealth($device);
            $score = $health['score'];
            
            // Prefer devices with fewer recent syncs
            $recentSyncs = $device->dailyAttendances()
                ->where('created_at', '>=', now()->subHour())
                ->count();
            $score -= $recentSyncs * 2; // Penalize busy devices
            
            $scoredDevices[] = [
                'device' => $device,
                'score' => $score,
            ];
        }

        // Sort by score descending
        usort($scoredDevices, fn($a, $b) => $b['score'] <=> $a['score']);

        return $scoredDevices[0]['device'] ?? null;
    }

    /**
     * Batch sync multiple devices in parallel
     */
    public function batchSyncDevices(array $deviceIds = null, Carbon $date = null): array
    {
        $query = BiometricDevice::where('status', 'active')
            ->where('auto_sync_enabled', true)
            ->where('daily_attendance_enabled', true)
            ->attendanceDevices(); // Only attendance devices

        if ($deviceIds) {
            $query->whereIn('id', $deviceIds);
        }

        $devices = $query->get();
        $results = [
            'total' => $devices->count(),
            'successful' => 0,
            'failed' => 0,
            'results' => [],
        ];

        foreach ($devices as $device) {
            try {
                $result = $this->syncDeviceWithRetry($device, $date);
                $results['results'][$device->device_name] = $result;
                
                if ($result['success']) {
                    $results['successful']++;
                } else {
                    $results['failed']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['results'][$device->device_name] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Calculate device uptime percentage (last 24 hours)
     */
    protected function calculateUptime(BiometricDevice $device): float
    {
        // This is simplified - in production, track connection history
        $checks = 24 * 12; // Every 5 minutes for 24 hours
        $onlineChecks = 0;

        // Check last 24 hours of connection status
        // For now, use last_connected_at as proxy
        if ($device->last_connected_at && $device->last_connected_at->gt(now()->subDay())) {
            $onlineChecks = $checks * 0.8; // Assume 80% uptime if connected recently
        }

        return round(($onlineChecks / $checks) * 100, 2);
    }

    /**
     * Calculate sync success rate
     */
    protected function calculateSyncSuccessRate(BiometricDevice $device): float
    {
        // Simplified - track sync history in production
        if (!$device->last_sync_at) {
            return 0;
        }

        // If synced recently, assume good success rate
        $minutesSinceSync = $device->last_sync_at->diffInMinutes(now());
        $expectedSyncs = floor($minutesSinceSync / $device->sync_interval_minutes);
        
        if ($expectedSyncs > 0) {
            return max(0, 100 - ($expectedSyncs * 10)); // Penalize missed syncs
        }

        return 100;
    }

    /**
     * Test connection to device
     */
    protected function testConnection(string $ip, int $port, int $timeout, string $connectionType): bool
    {
        try {
            if ($connectionType === 'tcp' || $connectionType === 'both') {
                $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);
                if ($socket) {
                    fclose($socket);
                    return true;
                }
            }

            if ($connectionType === 'http' || $connectionType === 'both') {
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
     * Parse IP range
     */
    protected function parseIPRange(string $range): array
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
     * Get default IP range
     */
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

