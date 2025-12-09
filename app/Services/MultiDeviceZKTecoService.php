<?php

namespace App\Services;

use App\Models\BiometricDevice;
use App\Models\DailyAttendance;
use App\Models\Employee;
use App\Models\User;
use App\Exceptions\ZKTecoException;
use App\Services\ZKTecoTCPProtocol;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class MultiDeviceZKTecoService
{
    /**
     * Connect to a specific device
     * Supports ZKTeco Software Version 9.0.1+ (HTTP API and TCP Socket)
     * 
     * Port 4370 = TCP Socket (not HTTP)
     * Port 80 = HTTP API
     */
    public function connectToDevice(BiometricDevice $device): bool
    {
        try {
            // Port 4370 is TCP, not HTTP - use socket connection directly
            if ($device->port == 4370 || $device->connection_type === 'tcp') {
                return $this->connectViaSocket($device);
            }
            
            // Try HTTP API for port 80 or when connection_type is 'http' or 'both'
            if ($device->port == 80 || $device->connection_type === 'http' || $device->connection_type === 'both') {
                $headers = [];
                if ($device->api_key) {
                    $headers['Authorization'] = 'Bearer ' . $device->api_key;
                }
                
                // For HTTP, port 80 doesn't need to be specified
                $port = $device->port == 80 ? '' : ":{$device->port}";
                $response = Http::timeout(5)
                    ->withHeaders($headers)
                    ->get("http://{$device->device_ip}{$port}/api/test");
                
                if ($response->successful()) {
                    $device->update(['last_connected_at' => now(), 'status' => 'active']);
                    return true;
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
     */
    private function connectViaSocket(BiometricDevice $device): bool
    {
        try {
            $socket = @fsockopen($device->device_ip, $device->port, $errno, $errstr, 5);
            
            if (!$socket) {
                return false;
            }
            
            fclose($socket);
            $device->update(['last_connected_at' => now()]);
            return true;
        } catch (\Exception $e) {
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

            // Use HTTP API only for port 80 or when connection_type is 'http' or 'both'
            $port = $device->port == 80 ? '' : ":{$device->port}";
            $response = Http::timeout(15)
                ->withHeaders($headers)
                ->get("http://{$device->device_ip}{$port}/api/attendance", $params);
            
            if ($response->successful()) {
                return $response->json('data', []);
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error("Failed to get attendance logs from device {$device->device_name}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get attendance logs via TCP protocol
     */
    private function getAttendanceLogsViaTCP(BiometricDevice $device, string $fromDate = null, string $toDate = null): array
    {
        try {
            $tcp = new ZKTecoTCPProtocol($device->device_ip, $device->port);
            
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
            return ['message' => 'Daily attendance not enabled for this device'];
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
                $response = Http::timeout(5)
                    ->withHeaders($headers)
                    ->get("http://{$device->device_ip}{$port}/api/status");
                
                if ($response->successful()) {
                    $device->update(['last_connected_at' => now(), 'status' => 'active']);
                    return array_merge($response->json(), ['status' => 'online']);
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
            $tcp = new ZKTecoTCPProtocol($device->device_ip, $device->port);
            
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
                return false;
            }

            // Port 4370 is TCP, not HTTP - cannot use HTTP API for enrollment
            if ($device->port == 4370 || $device->connection_type === 'tcp') {
                Log::warning("HTTP API enrollment not available for TCP connection on port 4370. Device: {$device->device_name}");
                // TODO: Implement TCP-based enrollment protocol if needed
                // For now, return false as enrollment requires device-specific protocol
                return false;
            }

            $headers = [];
            if ($device->api_key) {
                $headers['Authorization'] = 'Bearer ' . $device->api_key;
            }

            // Use HTTP API only for port 80 or when connection_type is 'http' or 'both'
            $port = $device->port == 80 ? '' : ":{$device->port}";
            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->post("http://{$device->device_ip}{$port}/api/enroll", [
                    'user_id' => $employee->id,
                    'name' => $employee->full_name,
                    'card_number' => $employee->employee_id_number ?? $employee->id,
                ]);

            if ($response->successful()) {
                // Store enrollment info
                $employee->update([
                    'biometric_template_id' => $response->json('template_id'),
                ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Failed to enroll employee {$employee->full_name} on device {$device->device_name}: " . $e->getMessage());
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

            $port = $device->port == 80 ? '' : ":{$device->port}";
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->get("http://{$device->device_ip}{$port}/api/users/{$employee->id}");

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

            $port = $device->port == 80 ? '' : ":{$device->port}";
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->delete("http://{$device->device_ip}{$port}/api/users/{$employee->id}");

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
}

