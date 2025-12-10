<?php

namespace App\Services;

use App\Models\BiometricDevice;
use App\Models\DailyAttendance;
use App\Models\Employee;
use App\Models\User;
use App\Exceptions\ZKTecoException;
use App\Services\ZKTecoTCPProtocol;
use App\Services\NetworkConnectionService;
use ZKLib\ZKLib; // Installed ZKLib package
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MultiDeviceZKTecoService
{
    protected $networkService;

    public function __construct()
    {
        $this->networkService = new NetworkConnectionService();
    }

    /**
     * Connect to a specific device
     * Supports ZKTeco Software Version 9.0.1+ (HTTP API and TCP Socket)
     * Works with local network, remote network, and internet connections
     * 
     * Port 4370 = TCP Socket (not HTTP)
     * Port 80 = HTTP API
     */
    public function connectToDevice(BiometricDevice $device): bool
    {
        try {
            // Auto-detect network type if enabled
            if ($device->auto_detect_network) {
                $this->networkService->autoDetectNetwork($device);
                $device->refresh();
            }

            // Get connection IP (local or public)
            $connectionIP = $this->networkService->getConnectionIP($device);
            $timeout = $this->networkService->getConnectionTimeout($device);

            // Port 4370 is TCP, not HTTP - use socket connection directly
            if ($device->port == 4370 || $device->connection_type === 'tcp') {
                return $this->connectViaSocket($device, $connectionIP, $timeout);
            }
            
            // Try HTTP API for port 80, 8081, or when connection_type is 'http' or 'both'
            if (in_array($device->port, [80, 8081]) || $device->connection_type === 'http' || $device->connection_type === 'both') {
                $headers = [];
                if ($device->api_key) {
                    $headers['Authorization'] = 'Bearer ' . $device->api_key;
                }
                
                // Use network-aware timeout
                $timeout = $this->networkService->getConnectionTimeout($device);
                if (in_array($device->port, [8081, 8080])) {
                    $timeout = max($timeout, 15); // Ensure minimum for non-standard ports
                }
                
                // Get connection IP (local or public)
                $connectionIP = $this->networkService->getConnectionIP($device);
                
                // For HTTP, port 80 doesn't need to be specified
                $port = $device->port == 80 ? '' : ":{$device->port}";
                
                try {
                    $response = Http::timeout($timeout)
                        ->connectTimeout($timeout)
                        ->withHeaders($headers)
                        ->get("http://{$connectionIP}{$port}/api/test");
                    
                    if ($response->successful()) {
                        $device->update(['last_connected_at' => now(), 'status' => 'active']);
                        return true;
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    // Connection timeout - try alternative endpoints or fallback
                    Log::warning("HTTP connection timeout for {$device->device_name} on port {$device->port}, trying alternative methods");
                    
                    // Try alternative endpoint
                    try {
                        $response = Http::timeout($timeout)
                            ->connectTimeout($timeout)
                            ->withHeaders($headers)
                            ->get("http://{$device->device_ip}{$port}/api/status");
                        
                        if ($response->successful()) {
                            $device->update(['last_connected_at' => now(), 'status' => 'active']);
                            return true;
                        }
                    } catch (\Exception $e2) {
                        // Continue to fallback
                    }
                } catch (\Exception $e) {
                    Log::warning("HTTP connection error for {$device->device_name}: " . $e->getMessage());
                }
            }
            
            // Fallback to TCP socket if HTTP fails or connection_type is 'both'
            if ($device->connection_type === 'both' || $device->connection_type === 'tcp') {
                return $this->connectViaSocket($device);
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to connect to device {$device->device_name}: " . $e->getMessage());
            $device->update(['status' => 'offline']);
            return false;
        }
    }

    /**
     * Connect via TCP socket (fallback)
     * Tries ZKLib first, then falls back to basic socket test
     * Supports local network, remote network, and internet connections
     */
    private function connectViaSocket(BiometricDevice $device, ?string $connectionIP = null, ?int $timeout = null): bool
    {
        // Get connection IP and timeout
        $connectionIP = $connectionIP ?? $this->networkService->getConnectionIP($device);
        $timeout = $timeout ?? $this->networkService->getConnectionTimeout($device);
        
        // Check if sockets extension is available before trying ZKLib
        $socketsAvailable = function_exists('socket_create');
        
        // Try ZKLib package first (only if sockets extension is available)
        if ($socketsAvailable) {
            try {
                $zkLib = new ZKLib($connectionIP, $device->port);
                if ($zkLib->connect()) {
                    $zkLib->disconnect();
                    $device->update(['last_connected_at' => now()]);
                    return true;
                }
            } catch (\Exception $e) {
                Log::debug("ZKLib connection failed, trying socket: " . $e->getMessage());
            }
        } else {
            Log::warning("PHP sockets extension not available. Skipping ZKLib connection for device {$device->device_name}. Please enable php_sockets extension.");
        }

        // Fallback to basic socket connection test
        try {
            $socketTimeout = min($timeout, 10); // Socket timeout max 10 seconds
            $socket = @fsockopen($connectionIP, $device->port, $errno, $errstr, $socketTimeout);
            
            if (!$socket) {
                Log::warning("Socket connection failed to {$connectionIP}:{$device->port} - {$errstr}");
                return false;
            }
            
            fclose($socket);
            $device->update(['last_connected_at' => now()]);
            return true;
        } catch (\Exception $e) {
            Log::error("Socket connection exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get attendance logs from a specific device
     */
    public function getAttendanceLogs(BiometricDevice $device, string $fromDate = null, string $toDate = null): array
    {
        try {
            if (!$this->connectToDevice($device)) {
                return [];
            }

            // Port 4370 is TCP, not HTTP - use TCP protocol
            if ($device->port == 4370 || $device->connection_type === 'tcp') {
                return $this->getAttendanceLogsViaTCP($device, $fromDate, $toDate);
            }

            $params = [];
            if ($fromDate) $params['from'] = $fromDate;
            if ($toDate) $params['to'] = $toDate;

            $headers = [];
            if ($device->api_key) {
                $headers['Authorization'] = 'Bearer ' . $device->api_key;
            }

            // Get connection IP (local or public)
            $connectionIP = $this->networkService->getConnectionIP($device);
            
            // Use HTTP API only for port 80, 8081, or when connection_type is 'http' or 'both'
            $port = $device->port == 80 ? '' : ":{$device->port}";
            
            // Increase timeout for attendance sync (more data) and network type
            $timeout = $this->networkService->getConnectionTimeout($device);
            $timeout = in_array($device->port, [8081, 8080]) ? max($timeout, 30) : max($timeout, 15);
            
            try {
                $response = Http::timeout($timeout)
                    ->connectTimeout($timeout)
                    ->withHeaders($headers)
                    ->get("http://{$connectionIP}{$port}/api/attendance", $params);
                
                if ($response->successful()) {
                    return $response->json('data', []);
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("HTTP attendance sync timeout for {$device->device_name}: " . $e->getMessage());
                return [];
            } catch (\Exception $e) {
                Log::error("HTTP attendance sync error for {$device->device_name}: " . $e->getMessage());
                return [];
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error("Failed to get attendance logs from device {$device->device_name}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get attendance logs via TCP protocol
     * Tries ZKLib package first, falls back to custom implementation
     * Supports local network, remote network, and internet connections
     */
    private function getAttendanceLogsViaTCP(BiometricDevice $device, string $fromDate = null, string $toDate = null): array
    {
        // Get connection IP (local or public)
        $connectionIP = $this->networkService->getConnectionIP($device);
        
        // Check if sockets extension is available before trying ZKLib
        $socketsAvailable = function_exists('socket_create');
        
        // Try ZKLib package first (more reliable) - only if sockets extension is available
        if ($socketsAvailable) {
            try {
                $zkLib = new ZKLib($connectionIP, $device->port);
                
                if ($zkLib->connect()) {
                $attendance = $zkLib->getAttendance();
                $zkLib->disconnect();

                // Convert ZKLib format to standard format
                $logs = [];
                $from = $fromDate ? Carbon::parse($fromDate) : now()->startOfDay();
                $to = $toDate ? Carbon::parse($toDate) : now()->endOfDay();

                foreach ($attendance as $log) {
                    $timestamp = $this->parseZKLibTimestamp($log);
                    $logDate = Carbon::parse($timestamp);

                    // Filter by date range
                    if ($logDate->lt($from) || $logDate->gt($to)) {
                        continue;
                    }

                    $logs[] = [
                        'user_id' => $log['uid'] ?? null,
                        'card_number' => $log['id'] ?? null,
                        'timestamp' => $timestamp,
                        'verify_type' => $log['state'] ?? 1,
                'template_id' => $log['uid'] ?? null,
                'device_id' => $device->id,
                'device_ip' => $connectionIP,
            ];
                }

                    Log::info("Successfully retrieved attendance via ZKLib for device {$device->device_name}");
                    return $logs;
                }
            } catch (\Exception $e) {
                Log::warning("ZKLib failed for device {$device->device_name}, falling back to custom TCP: " . $e->getMessage());
            }
        } else {
            Log::warning("PHP sockets extension not available. Skipping ZKLib for device {$device->device_name}. Please enable php_sockets extension.");
        }

        // Fallback to custom TCP protocol implementation
        try {
            $tcp = new ZKTecoTCPProtocol($connectionIP, $device->port);
            
            if (!$tcp->connect()) {
                Log::error("Failed to connect to device {$device->device_name} via TCP");
                return [];
            }

            $from = $fromDate ? Carbon::parse($fromDate) : now()->startOfDay();
            $to = $toDate ? Carbon::parse($toDate) : now()->endOfDay();

            $logs = $tcp->getAttendanceLogs($from, $to);
            
            $tcp->disconnect();

            // Convert TCP logs to standard format
            return $this->convertTCPLogsToStandardFormat($logs, $device);
        } catch (\Exception $e) {
            Log::error("Failed to get attendance logs via TCP from device {$device->device_name}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse ZKLib timestamp format
     */
    private function parseZKLibTimestamp(array $log): string
    {
        if (isset($log['timestamp'])) {
            return $log['timestamp'];
        }

        if (isset($log['date']) && isset($log['time'])) {
            return $log['date'] . ' ' . $log['time'];
        }

        return now()->toDateTimeString();
    }

    /**
     * Convert TCP protocol logs to standard format
     */
    private function convertTCPLogsToStandardFormat(array $tcpLogs, BiometricDevice $device): array
    {
        $logs = [];

        foreach ($tcpLogs as $tcpLog) {
            $logs[] = [
                'user_id' => $tcpLog['user_id'] ?? null,
                'card_number' => $tcpLog['card_number'] ?? null,
                'timestamp' => $tcpLog['timestamp'] ?? null,
                'verify_type' => $tcpLog['verify_type'] ?? 1, // 1 = fingerprint
                'template_id' => $tcpLog['template_id'] ?? null,
                'device_id' => $device->id,
                'device_ip' => $device->device_ip,
            ];
        }

        return $logs;
    }

    /**
     * Sync daily attendance from a device
     */
    public function syncDailyAttendance(BiometricDevice $device, Carbon $date = null): array
    {
        if (!$device->daily_attendance_enabled) {
            return [
                'processed' => 0,
                'new_records' => 0,
                'updated_records' => 0,
                'errors' => ['Daily attendance not enabled for this device']
            ];
        }

        $date = $date ?? now();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $results = [
            'processed' => 0,
            'new_records' => 0,
            'updated_records' => 0,
            'errors' => []
        ];

        try {
            // Get attendance logs from device
            $logs = $this->getAttendanceLogs(
                $device,
                $startOfDay->format('Y-m-d H:i:s'),
                $endOfDay->format('Y-m-d H:i:s')
            );
            
            // Check if logs retrieval failed
            if ($logs === false) {
                $results['errors'][] = "Failed to retrieve attendance logs from device. Check device connection and PHP sockets extension.";
                return $results;
            }

            foreach ($logs as $log) {
                $results['processed']++;

                // Find employee by biometric template ID or employee ID number
                $employee = $this->findEmployeeByLog($log, $device->company_id);
                
                if (!$employee) {
                    $results['errors'][] = "Employee not found for log: " . json_encode($log);
                    continue;
                }

                // Determine if this is check-in or check-out
                $isCheckIn = $this->isCheckIn($log, $device, $date);
                
                if ($isCheckIn) {
                    // Create or update check-in record
                    $attendance = DailyAttendance::firstOrNew([
                        'biometric_device_id' => $device->id,
                        'company_id' => $device->company_id,
                        'employee_id' => $employee->id,
                        'user_id' => $employee->user_id,
                        'employee_id_number' => $employee->employee_id_number,
                        'attendance_date' => $date->format('Y-m-d'),
                    ]);

                    if (!$attendance->exists) {
                        $attendance->employee_name = $employee->full_name;
                        $attendance->department_id = $employee->department_id;
                        $attendance->attendance_status = 'present';
                        $results['new_records']++;
                    } else {
                        $results['updated_records']++;
                    }

                    $attendance->check_in_time = Carbon::parse($log['timestamp'])->format('H:i:s');
                    $attendance->check_in_method = 'biometric';
                    $attendance->biometric_template_id = $log['template_id'] ?? null;
                    $attendance->device_log_id = $log['id'] ?? null;
                    $attendance->check_in_latitude = $device->latitude;
                    $attendance->check_in_longitude = $device->longitude;

                    // Check if late
                    $workStart = Carbon::parse($date->format('Y-m-d') . ' ' . $device->work_start_time);
                    $checkInTime = Carbon::parse($log['timestamp']);
                    $gracePeriod = $device->grace_period_minutes;

                    if ($checkInTime->gt($workStart->copy()->addMinutes($gracePeriod))) {
                        $attendance->is_late = true;
                        $attendance->attendance_status = 'late';
                        $attendance->late_minutes = $checkInTime->diffInMinutes($workStart->copy()->addMinutes($gracePeriod));
                    }

                    $attendance->save();
                } else {
                    // Update check-out
                    $attendance = DailyAttendance::where('biometric_device_id', $device->id)
                        ->where('employee_id', $employee->id)
                        ->whereDate('attendance_date', $date->format('Y-m-d'))
                        ->first();

                    if ($attendance) {
                        $attendance->check_out_time = Carbon::parse($log['timestamp'])->format('H:i:s');
                        $attendance->check_out_method = 'biometric';
                        $attendance->check_out_latitude = $device->latitude;
                        $attendance->check_out_longitude = $device->longitude;
                        
                        // Calculate work hours
                        $attendance->calculateWorkHours();
                        $attendance->save();
                        
                        $results['updated_records']++;
                    }
                }
            }

            // Update device sync time
            $device->update(['last_sync_at' => now()]);

        } catch (\Exception $e) {
            Log::error("Error syncing daily attendance for device {$device->device_name}: " . $e->getMessage());
            $results['errors'][] = "System error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Sync daily attendance from all active devices
     */
    public function syncAllDevices(Carbon $date = null): array
    {
        $date = $date ?? now();
        $devices = BiometricDevice::where('status', 'active')
            ->where('daily_attendance_enabled', true)
            ->where('auto_sync_enabled', true)
            ->attendanceDevices() // Only attendance devices
            ->get();

        $results = [
            'devices_processed' => 0,
            'devices_successful' => 0,
            'devices_failed' => 0,
            'total_new_records' => 0,
            'total_updated_records' => 0,
            'device_results' => []
        ];

        foreach ($devices as $device) {
            $results['devices_processed']++;
            
            try {
                $deviceResult = $this->syncDailyAttendance($device, $date);
                $results['device_results'][$device->device_name] = $deviceResult;
                $results['total_new_records'] += $deviceResult['new_records'] ?? 0;
                $results['total_updated_records'] += $deviceResult['updated_records'] ?? 0;
                $results['devices_successful']++;
            } catch (\Exception $e) {
                $results['devices_failed']++;
                $results['device_results'][$device->device_name] = [
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Find employee by attendance log
     */
    private function findEmployeeByLog(array $log, int $companyId): ?Employee
    {
        // Try to find by biometric template ID first
        if (!empty($log['template_id'])) {
            $employee = Employee::where('company_id', $companyId)
                ->where('biometric_template_id', $log['template_id'])
                ->where('is_active', true)
                ->first();
            if ($employee) return $employee;
        }

        // Try to find by employee ID number
        if (!empty($log['user_id']) || !empty($log['card_number'])) {
            $employeeIdNumber = $log['user_id'] ?? $log['card_number'];
            $employee = Employee::where('company_id', $companyId)
                ->where('employee_id_number', $employeeIdNumber)
                ->where('is_active', true)
                ->first();
            if ($employee) return $employee;
        }

        return null;
    }

    /**
     * Determine if log is check-in or check-out
     */
    private function isCheckIn(array $log, BiometricDevice $device, Carbon $date): bool
    {
        // Check if we already have a check-in for this employee on this date
        $employee = $this->findEmployeeByLog($log, $device->company_id);
        if (!$employee) {
            return true; // Assume check-in if employee not found
        }

        $existingAttendance = DailyAttendance::where('biometric_device_id', $device->id)
            ->where('employee_id', $employee->id)
            ->whereDate('attendance_date', $date->format('Y-m-d'))
            ->first();

        // If no existing attendance, this is check-in
        if (!$existingAttendance) {
            return true;
        }

        // If check-in exists but no check-out, this might be check-out
        if ($existingAttendance->check_in_time && !$existingAttendance->check_out_time) {
            return false; // This is check-out
        }

        // If both exist, check the time - earlier time is check-in
        $logTime = Carbon::parse($log['timestamp']);
        $existingCheckIn = Carbon::parse($date->format('Y-m-d') . ' ' . $existingAttendance->check_in_time);
        
        return $logTime->lt($existingCheckIn);
    }

    /**
     * Get device status
     */
    public function getDeviceStatus(BiometricDevice $device): array
    {
        try {
            // Port 4370 is TCP, not HTTP - use TCP protocol
            if ($device->port == 4370 || $device->connection_type === 'tcp') {
                return $this->getDeviceStatusViaTCP($device);
            }
            
            // Try HTTP API for port 80 or when connection_type is 'http' or 'both'
            if ($device->port == 80 || $device->connection_type === 'http' || $device->connection_type === 'both') {
                $headers = [];
                if ($device->api_key) {
                    $headers['Authorization'] = 'Bearer ' . $device->api_key;
                }

                // For HTTP, port 80 doesn't need to be specified
                $port = $device->port == 80 ? '' : ":{$device->port}";
                
                // Increase timeout for non-standard ports
                $timeout = in_array($device->port, [8081, 8080]) ? 15 : 10;
                
                try {
                    $response = Http::timeout($timeout)
                        ->connectTimeout($timeout)
                        ->withHeaders($headers)
                        ->get("http://{$device->device_ip}{$port}/api/status");
                    
                    if ($response->successful()) {
                        $device->update(['last_connected_at' => now(), 'status' => 'active']);
                        return array_merge($response->json(), ['status' => 'online']);
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::warning("HTTP status check timeout for {$device->device_name}: " . $e->getMessage());
                    // Fall through to TCP fallback
                } catch (\Exception $e) {
                    Log::warning("HTTP status check error for {$device->device_name}: " . $e->getMessage());
                }
            }
            
            // Fallback to TCP if HTTP fails
            if ($device->connection_type === 'both') {
                if ($this->connectViaSocket($device)) {
                    $device->update(['last_connected_at' => now(), 'status' => 'active']);
                    return [
                        'status' => 'online',
                        'connection_type' => 'tcp',
                        'message' => 'Device connected via TCP socket (HTTP failed)'
                    ];
                }
            }
            
            $device->update(['status' => 'offline']);
            return ['status' => 'offline', 'message' => 'Device not responding'];
        } catch (\Exception $e) {
            $device->update(['status' => 'offline']);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Get device status via TCP protocol
     */
    private function getDeviceStatusViaTCP(BiometricDevice $device): array
    {
        try {
            // Get connection IP (local or public)
            $connectionIP = $this->networkService->getConnectionIP($device);
            $tcp = new ZKTecoTCPProtocol($connectionIP, $device->port);
            
            if (!$tcp->connect()) {
                $device->update(['status' => 'offline']);
                return [
                    'status' => 'offline',
                    'connection_type' => 'tcp',
                    'message' => 'TCP connection failed'
                ];
            }

            // Get device version/info
            $version = $tcp->getVersion();
            $time = $tcp->getTime();
            
            $tcp->disconnect();

            $device->update([
                'last_connected_at' => now(),
                'status' => 'active'
            ]);

            return [
                'status' => 'online',
                'connection_type' => 'tcp',
                'port' => $device->port,
                'version' => $version['version'] ?? 'Unknown',
                'device_time' => $time ? $time->format('Y-m-d H:i:s') : null,
                'message' => 'Device connected via TCP protocol'
            ];
        } catch (\Exception $e) {
            Log::error("Failed to get device status via TCP for {$device->device_name}: " . $e->getMessage());
            $device->update(['status' => 'offline']);
            return [
                'status' => 'error',
                'connection_type' => 'tcp',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Sync users to a specific device
     */
    public function syncUsersToDevice(BiometricDevice $device): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        // Get all active employees for the company
        $employees = Employee::where('company_id', $device->company_id)
            ->where('is_active', true)
            ->where('employment_status', 'active')
            ->get();

        foreach ($employees as $employee) {
            try {
                if ($this->enrollEmployee($device, $employee)) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Failed to enroll: {$employee->full_name}";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error enrolling {$employee->full_name}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Enroll employee on device
     */
    public function enrollEmployee(BiometricDevice $device, Employee $employee): bool
    {
        try {
            if (!$this->connectToDevice($device)) {
                Log::error("Cannot enroll employee {$employee->full_name}: Device connection failed");
                return false;
            }

            // Port 4370 is TCP - use ZKLib for enrollment
            if ($device->port == 4370 || $device->connection_type === 'tcp') {
                return $this->enrollEmployeeViaZKLib($device, $employee);
            }

            $headers = [];
            if ($device->api_key) {
                $headers['Authorization'] = 'Bearer ' . $device->api_key;
            }

            // Get connection IP (local or public)
            $connectionIP = $this->networkService->getConnectionIP($device);
            
            // Use HTTP API only for port 80, 8081, or when connection_type is 'http' or 'both'
            $port = $device->port == 80 ? '' : ":{$device->port}";
            $timeout = $this->networkService->getConnectionTimeout($device);
            $timeout = in_array($device->port, [8081, 8080]) ? max($timeout, 30) : max($timeout, 30);
            
            try {
                $response = Http::timeout($timeout)
                    ->connectTimeout($timeout)
                    ->withHeaders($headers)
                    ->post("http://{$connectionIP}{$port}/api/enroll", [
                        'user_id' => $employee->id,
                        'name' => $employee->full_name,
                        'card_number' => $employee->employee_id_number ?? $employee->id,
                    ]);

                if ($response->successful()) {
                    // Store enrollment info
                    $employee->update([
                        'biometric_template_id' => $response->json('template_id') ?? $employee->id,
                    ]);
                    return true;
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error("HTTP enrollment timeout for {$employee->full_name}: " . $e->getMessage());
                return false;
            } catch (\Exception $e) {
                Log::error("HTTP enrollment error for {$employee->full_name}: " . $e->getMessage());
                return false;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Failed to enroll employee {$employee->full_name} on device {$device->device_name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enroll employee via ZKLib (TCP protocol)
     */
    private function enrollEmployeeViaZKLib(BiometricDevice $device, Employee $employee): bool
    {
        try {
            // Get connection IP (local or public)
            $connectionIP = $this->networkService->getConnectionIP($device);
            $zkLib = new ZKLib($connectionIP, $device->port);
            
            if (!$zkLib->connect()) {
                Log::error("ZKLib connection failed for enrollment: {$device->device_name}");
                return false;
            }

            // Set user data using ZKLib
            // Note: Fingerprint enrollment requires physical interaction on device
            // This method sets up the user profile, fingerprint must be enrolled separately
            $result = $zkLib->setUser(
                $employee->id,                          // User ID
                $employee->full_name,                   // Name
                '',                                     // Password (empty)
                0,                                      // Privilege (0 = user, 14 = admin)
                $employee->employee_id_number ?? (string)$employee->id  // Card number
            );

            $zkLib->disconnect();

            if ($result) {
                // Store enrollment reference
                $employee->update([
                    'biometric_template_id' => (string)$employee->id, // Use employee ID as template reference
                ]);
                
                Log::info("Successfully enrolled employee {$employee->full_name} via ZKLib on device {$device->device_name}");
                return true;
            }

            Log::warning("ZKLib setUser returned false for employee {$employee->full_name}");
            return false;
        } catch (\Exception $e) {
            Log::error("ZKLib enrollment failed for {$employee->full_name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if employee is enrolled on device
     */
    public function checkEnrollmentStatus(BiometricDevice $device, Employee $employee): array
    {
        try {
            if (!$this->connectToDevice($device)) {
                return ['enrolled' => false, 'status' => 'device_offline'];
            }

            // Port 4370 is TCP, not HTTP - cannot use HTTP API
            if ($device->port == 4370 || $device->connection_type === 'tcp') {
                // For TCP connections, check if employee has biometric_template_id as indicator
                return [
                    'enrolled' => !empty($employee->biometric_template_id),
                    'status' => !empty($employee->biometric_template_id) ? 'enrolled' : 'not_enrolled',
                    'template_id' => $employee->biometric_template_id,
                    'note' => 'TCP connection - status based on template ID'
                ];
            }

            $headers = [];
            if ($device->api_key) {
                $headers['Authorization'] = 'Bearer ' . $device->api_key;
            }

            // Get connection IP (local or public)
            $connectionIP = $this->networkService->getConnectionIP($device);
            
            $port = $device->port == 80 ? '' : ":{$device->port}";
            $timeout = $this->networkService->getConnectionTimeout($device);
            
            $response = Http::timeout($timeout)
                ->connectTimeout($timeout)
                ->withHeaders($headers)
                ->get("http://{$connectionIP}{$port}/api/users/{$employee->id}");

            if ($response->successful()) {
                $userData = $response->json();
                return [
                    'enrolled' => true,
                    'status' => 'enrolled',
                    'template_id' => $userData['template_id'] ?? null,
                    'enrolled_at' => $userData['enrolled_at'] ?? null,
                ];
            }

            return ['enrolled' => false, 'status' => 'not_enrolled'];
        } catch (\Exception $e) {
            Log::error("Failed to check enrollment status for {$employee->full_name}: " . $e->getMessage());
            return ['enrolled' => false, 'status' => 'error'];
        }
    }

    /**
     * Remove employee from device
     */
    public function removeEmployee(BiometricDevice $device, Employee $employee): bool
    {
        try {
            if (!$this->connectToDevice($device)) {
                return false;
            }

            // Port 4370 is TCP, not HTTP - cannot use HTTP API
            if ($device->port == 4370 || $device->connection_type === 'tcp') {
                Log::warning("HTTP API removal not available for TCP connection on port 4370. Device: {$device->device_name}");
                // Clear biometric template ID as fallback
                $employee->update(['biometric_template_id' => null]);
                return true; // Return true as we've cleared the local reference
            }

            $headers = [];
            if ($device->api_key) {
                $headers['Authorization'] = 'Bearer ' . $device->api_key;
            }

            // Get connection IP (local or public)
            $connectionIP = $this->networkService->getConnectionIP($device);
            
            $port = $device->port == 80 ? '' : ":{$device->port}";
            $timeout = $this->networkService->getConnectionTimeout($device);
            
            $response = Http::timeout($timeout)
                ->connectTimeout($timeout)
                ->withHeaders($headers)
                ->delete("http://{$connectionIP}{$port}/api/users/{$employee->id}");

            if ($response->successful()) {
                // Clear biometric template ID
                $employee->update(['biometric_template_id' => null]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Failed to remove employee {$employee->full_name} from device {$device->device_name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Process attendance for a specific toolbox talk
     */
    public function processToolboxTalkAttendance(BiometricDevice $device, \App\Models\ToolboxTalk $toolboxTalk): array
    {
        if (!$toolboxTalk->biometric_required) {
            return ['message' => 'Biometric not required for this talk'];
        }

        if (!$device->toolbox_attendance_enabled) {
            return ['message' => 'Toolbox attendance not enabled for this device'];
        }

        $results = [
            'processed' => 0,
            'new_attendances' => 0,
            'errors' => []
        ];

        try {
            // Get attendance logs for the talk timeframe
            $startTime = $toolboxTalk->start_time->copy()->subMinutes(30); // 30 min before
            $endTime = $toolboxTalk->end_time ?? $toolboxTalk->start_time->copy()->addMinutes($toolboxTalk->duration_minutes ?? 15);

            $logs = $this->getAttendanceLogs(
                $device,
                $startTime->format('Y-m-d H:i:s'),
                $endTime->format('Y-m-d H:i:s')
            );

            foreach ($logs as $log) {
                $results['processed']++;

                // Find employee by biometric template ID or employee ID number
                $employee = $this->findEmployeeByLog($log, $device->company_id);
                
                if (!$employee) {
                    $results['errors'][] = "Employee not found for log: " . json_encode($log);
                    continue;
                }

                // Check if attendance already recorded
                $existing = \App\Models\ToolboxTalkAttendance::where('toolbox_talk_id', $toolboxTalk->id)
                    ->where('employee_id', $employee->id)
                    ->first();

                if ($existing) {
                    continue; // Already processed
                }

                // Determine attendance status based on check-in time
                $checkInTime = Carbon::parse($log['timestamp']);
                $talkStartTime = $toolboxTalk->start_time;
                $gracePeriodMinutes = 5; // 5 minute grace period
                
                $attendanceStatus = 'present';
                if ($checkInTime->gt($talkStartTime->copy()->addMinutes($gracePeriodMinutes))) {
                    $attendanceStatus = 'late';
                }

                // Create attendance record
                $attendance = \App\Models\ToolboxTalkAttendance::create([
                    'toolbox_talk_id' => $toolboxTalk->id,
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->full_name,
                    'employee_id_number' => $employee->employee_id_number,
                    'department' => $employee->department?->name,
                    'attendance_status' => $attendanceStatus,
                    'check_in_time' => $log['timestamp'],
                    'check_in_method' => 'biometric',
                    'biometric_template_id' => $log['template_id'] ?? null,
                    'device_id' => $device->device_ip,
                    'check_in_latitude' => $toolboxTalk->latitude,
                    'check_in_longitude' => $toolboxTalk->longitude,
                    'is_supervisor' => $employee->user?->role && $employee->user->role->name === 'supervisor',
                    'is_presenter' => $toolboxTalk->supervisor_id === $employee->user_id,
                ]);

                $results['new_attendances']++;

                Log::info("Biometric attendance recorded for {$employee->full_name} in toolbox talk {$toolboxTalk->reference_number}");
            }

            // Update toolbox talk statistics
            $toolboxTalk->total_attendees = $toolboxTalk->attendances()->count();
            $toolboxTalk->present_attendees = $toolboxTalk->attendances()->where('attendance_status', 'present')->count();
            if (method_exists($toolboxTalk, 'calculateAttendanceRate')) {
                $toolboxTalk->calculateAttendanceRate();
            }

        } catch (\Exception $e) {
            Log::error("Error processing toolbox talk attendance: " . $e->getMessage());
            $results['errors'][] = "System error: " . $e->getMessage();
        }

        return $results;
    }
}

