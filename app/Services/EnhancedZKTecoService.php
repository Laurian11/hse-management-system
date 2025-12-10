<?php

namespace App\Services;

use App\Models\BiometricDevice;
use App\Models\DailyAttendance;
use App\Models\Employee;
use ZKLib\ZKLib;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Enhanced ZKTeco Service using ZKLib package
 * 
 * This service integrates the installed ZKLib package with your existing
 * biometric device system for improved reliability and features.
 */
class EnhancedZKTecoService
{
    protected $zkLib;
    protected $useZkLib = true; // Toggle to use ZKLib or custom implementation

    /**
     * Connect to device using ZKLib
     */
    public function connectToDevice(BiometricDevice $device): bool
    {
        try {
            if ($this->useZkLib && ($device->port == 4370 || $device->connection_type === 'tcp')) {
                return $this->connectViaZKLib($device);
            }

            // Fallback to existing service for HTTP or other protocols
            $existingService = new MultiDeviceZKTecoService();
            return $existingService->connectToDevice($device);
        } catch (\Exception $e) {
            Log::error("Enhanced ZKTeco connection failed for {$device->device_name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Connect using ZKLib package
     */
    protected function connectViaZKLib(BiometricDevice $device): bool
    {
        try {
            $this->zkLib = new ZKLib($device->device_ip, $device->port);
            $connected = $this->zkLib->connect();

            if ($connected) {
                $device->update([
                    'last_connected_at' => now(),
                    'status' => 'active'
                ]);
            } else {
                $device->update(['status' => 'offline']);
            }

            return $connected;
        } catch (\Exception $e) {
            Log::error("ZKLib connection failed: " . $e->getMessage());
            $device->update(['status' => 'offline']);
            return false;
        }
    }

    /**
     * Get attendance logs using ZKLib
     */
    public function getAttendanceLogs(BiometricDevice $device, string $fromDate = null, string $toDate = null): array
    {
        try {
            if (!$this->connectToDevice($device)) {
                return [];
            }

            // Use ZKLib for TCP connections
            if ($this->useZkLib && ($device->port == 4370 || $device->connection_type === 'tcp')) {
                return $this->getAttendanceLogsViaZKLib($device, $fromDate, $toDate);
            }

            // Fallback to existing service
            $existingService = new MultiDeviceZKTecoService();
            return $existingService->getAttendanceLogs($device, $fromDate, $toDate);
        } catch (\Exception $e) {
            Log::error("Failed to get attendance logs from {$device->device_name}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get attendance logs via ZKLib
     */
    protected function getAttendanceLogsViaZKLib(BiometricDevice $device, string $fromDate = null, string $toDate = null): array
    {
        try {
            if (!$this->zkLib || !$this->zkLib->connect()) {
                $this->connectViaZKLib($device);
            }

            $attendance = $this->zkLib->getAttendance();
            
            // Convert ZKLib format to standard format
            $logs = [];
            foreach ($attendance as $log) {
                $timestamp = $this->parseZKLibTimestamp($log);
                
                // Filter by date range if provided
                if ($fromDate || $toDate) {
                    $logDate = Carbon::parse($timestamp);
                    if ($fromDate && $logDate->lt(Carbon::parse($fromDate))) {
                        continue;
                    }
                    if ($toDate && $logDate->gt(Carbon::parse($toDate))) {
                        continue;
                    }
                }

                $logs[] = [
                    'user_id' => $log['uid'] ?? null,
                    'card_number' => $log['id'] ?? null,
                    'timestamp' => $timestamp,
                    'verify_type' => $log['state'] ?? 1,
                    'template_id' => $log['uid'] ?? null,
                    'device_id' => $device->id,
                    'device_ip' => $device->device_ip,
                ];
            }

            return $logs;
        } catch (\Exception $e) {
            Log::error("ZKLib getAttendance failed: " . $e->getMessage());
            return [];
        } finally {
            if ($this->zkLib) {
                $this->zkLib->disconnect();
            }
        }
    }

    /**
     * Parse ZKLib timestamp format
     */
    protected function parseZKLibTimestamp(array $log): string
    {
        // ZKLib returns timestamp in format: YYYY-MM-DD HH:MM:SS
        if (isset($log['timestamp'])) {
            return $log['timestamp'];
        }

        // Alternative format handling
        if (isset($log['date']) && isset($log['time'])) {
            return $log['date'] . ' ' . $log['time'];
        }

        return now()->toDateTimeString();
    }

    /**
     * Get device version using ZKLib
     */
    public function getDeviceVersion(BiometricDevice $device): ?string
    {
        try {
            if ($this->connectViaZKLib($device)) {
                $version = $this->zkLib->version();
                $this->zkLib->disconnect();
                return $version;
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Failed to get device version: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get device time using ZKLib
     */
    public function getDeviceTime(BiometricDevice $device): ?Carbon
    {
        try {
            if ($this->connectViaZKLib($device)) {
                $time = $this->zkLib->getTime();
                $this->zkLib->disconnect();
                
                if ($time) {
                    return Carbon::parse($time);
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Failed to get device time: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Set device time using ZKLib
     */
    public function setDeviceTime(BiometricDevice $device, Carbon $time = null): bool
    {
        try {
            $time = $time ?? now();
            
            if ($this->connectViaZKLib($device)) {
                $result = $this->zkLib->setTime($time->format('Y-m-d H:i:s'));
                $this->zkLib->disconnect();
                return $result;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to set device time: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enroll employee using ZKLib
     */
    public function enrollEmployee(BiometricDevice $device, Employee $employee): bool
    {
        try {
            if ($this->connectViaZKLib($device)) {
                // Set user data
                $result = $this->zkLib->setUser(
                    $employee->id,
                    $employee->full_name,
                    '', // password
                    0,  // privilege (0 = user)
                    $employee->employee_id_number ?? (string)$employee->id
                );

                if ($result) {
                    // Note: Fingerprint enrollment requires physical interaction
                    // This just sets up the user, fingerprint must be enrolled on device
                    $employee->update([
                        'biometric_template_id' => $employee->id
                    ]);
                }

                $this->zkLib->disconnect();
                return $result;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to enroll employee {$employee->full_name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove employee from device using ZKLib
     */
    public function removeEmployee(BiometricDevice $device, Employee $employee): bool
    {
        try {
            if ($this->connectViaZKLib($device)) {
                $result = $this->zkLib->removeUser($employee->id);
                $this->zkLib->disconnect();

                if ($result) {
                    $employee->update(['biometric_template_id' => null]);
                }

                return $result;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to remove employee {$employee->full_name}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear attendance logs on device
     */
    public function clearAttendance(BiometricDevice $device): bool
    {
        try {
            if ($this->connectViaZKLib($device)) {
                $result = $this->zkLib->clearAttendance();
                $this->zkLib->disconnect();
                return $result;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to clear attendance: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get device status with detailed information
     */
    public function getDeviceStatus(BiometricDevice $device): array
    {
        try {
            $status = [
                'status' => 'offline',
                'connection_type' => 'tcp',
                'version' => null,
                'device_time' => null,
                'message' => 'Device not responding'
            ];

            if ($this->connectViaZKLib($device)) {
                $status['status'] = 'online';
                
                try {
                    $status['version'] = $this->zkLib->version();
                } catch (\Exception $e) {
                    // Version not available
                }

                try {
                    $time = $this->zkLib->getTime();
                    $status['device_time'] = $time ? Carbon::parse($time)->format('Y-m-d H:i:s') : null;
                } catch (\Exception $e) {
                    // Time not available
                }

                $status['message'] = 'Device connected via ZKLib';
                $this->zkLib->disconnect();
            }

            return $status;
        } catch (\Exception $e) {
            Log::error("Failed to get device status: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Enable or disable device
     */
    public function setDeviceEnabled(BiometricDevice $device, bool $enabled): bool
    {
        try {
            if ($this->connectViaZKLib($device)) {
                $result = $enabled 
                    ? $this->zkLib->enableDevice()
                    : $this->zkLib->disableDevice();
                
                $this->zkLib->disconnect();
                return $result;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to set device enabled state: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Restart device
     */
    public function restartDevice(BiometricDevice $device): bool
    {
        try {
            if ($this->connectViaZKLib($device)) {
                $result = $this->zkLib->restart();
                $this->zkLib->disconnect();
                return $result;
            }
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to restart device: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle ZKLib usage
     */
    public function setUseZkLib(bool $use): void
    {
        $this->useZkLib = $use;
    }
}

