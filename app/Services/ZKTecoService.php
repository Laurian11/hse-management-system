<?php

namespace App\Services;

use App\Models\ToolboxTalk;
use App\Models\ToolboxTalkAttendance;
use App\Models\User;
use App\Exceptions\ZKTecoException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ZKTecoService
{
    private string $deviceIp;
    private int $port;
    private ?string $apiKey;

    public function __construct()
    {
        $this->deviceIp = config('services.zkteco.device_ip', '192.168.1.201');
        $this->port = config('services.zkteco.port', 4370);
        $this->apiKey = config('services.zkteco.api_key') ?: null;
    }

    /**
     * Connect to ZKTeco K40 device
     * 
     * @throws ZKTecoException
     */
    public function connect(): bool
    {
        try {
            // ZKTeco K40 typically uses TCP socket on port 4370
            // For HTTP API, some models support REST API on port 80
            // Try HTTP first, fallback to socket if needed
            
            $headers = [];
            if ($this->apiKey) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }
            
            $response = Http::timeout(5)
                ->withHeaders($headers)
                ->get("http://{$this->deviceIp}/api/test");
            
            if ($response->successful()) {
                return true;
            }
            
            // If HTTP fails, try socket connection
            return $this->connectViaSocket();
        } catch (\Exception $e) {
            Log::error('ZKTeco connection failed: ' . $e->getMessage());
            throw new ZKTecoException('Failed to connect to ZKTeco device: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Connect via TCP socket (fallback method)
     */
    private function connectViaSocket(): bool
    {
        try {
            $socket = @fsockopen($this->deviceIp, $this->port, $errno, $errstr, 5);
            
            if (!$socket) {
                throw new ZKTecoException("Socket connection failed: $errstr ($errno)");
            }
            
            fclose($socket);
            return true;
        } catch (\Exception $e) {
            throw new ZKTecoException('Socket connection failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get all users from the device
     * 
     * @throws ZKTecoException
     */
    public function getUsers(): array
    {
        try {
            $headers = [];
            if ($this->apiKey) {
                $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }
            
            $response = Http::timeout(10)
                ->withHeaders($headers)
                ->get("http://{$this->deviceIp}/api/users");
            
            if ($response->successful()) {
                return $response->json('data', []);
            }
            
            throw new ZKTecoException('Failed to retrieve users from device. Status: ' . $response->status());
        } catch (ZKTecoException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to get users from ZKTeco: ' . $e->getMessage());
            throw new ZKTecoException('Failed to get users: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get attendance logs from the device
     */
    public function getAttendanceLogs(string $fromDate = null, string $toDate = null): array
    {
        try {
            $params = [];
            if ($fromDate) $params['from'] = $fromDate;
            if ($toDate) $params['to'] = $toDate;

            $response = Http::timeout(15)->get("http://{$this->deviceIp}/api/attendance", $params);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return [];
        } catch (\Exception $e) {
            Log::error('Failed to get attendance logs from ZKTeco: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Enroll user fingerprint on device
     */
    public function enrollFingerprint(User $user): bool
    {
        try {
            $response = Http::timeout(30)->post("http://{$this->deviceIp}/api/enroll", [
                'user_id' => $user->id,
                'name' => $user->name,
                'card_number' => $user->employee_id_number,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to enroll fingerprint: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user from device
     */
    public function deleteUser(User $user): bool
    {
        try {
            $response = Http::timeout(10)->delete("http://{$this->deviceIp}/api/users/{$user->id}");
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to delete user from ZKTeco: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync users to device
     */
    public function syncUsers(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $users = User::where('company_id', auth()->user()->company_id)
                    ->where('is_active', true)
                    ->get();

        foreach ($users as $user) {
            try {
                if ($this->enrollFingerprint($user)) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Failed to enroll user: {$user->name}";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error enrolling {$user->name}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Process attendance for a specific toolbox talk
     */
    public function processToolboxTalkAttendance(ToolboxTalk $toolboxTalk): array
    {
        if (!$toolboxTalk->biometric_required) {
            return ['message' => 'Biometric not required for this talk'];
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
                $startTime->format('Y-m-d H:i:s'),
                $endTime->format('Y-m-d H:i:s')
            );

            foreach ($logs as $log) {
                $results['processed']++;

                // Find user by employee ID or template ID
                $user = $this->findUserByLog($log);
                
                if (!$user) {
                    $results['errors'][] = "User not found for log: " . json_encode($log);
                    continue;
                }

                // Check if attendance already recorded
                $existing = ToolboxTalkAttendance::where('toolbox_talk_id', $toolboxTalk->id)
                                                  ->where('employee_id', $user->id)
                                                  ->first();

                if ($existing) {
                    continue; // Already processed
                }

                // Determine attendance status based on check-in time
                $checkInTime = \Carbon\Carbon::parse($log['timestamp']);
                $talkStartTime = $toolboxTalk->start_time;
                $gracePeriodMinutes = 5; // 5 minute grace period
                
                $attendanceStatus = 'present';
                if ($checkInTime->gt($talkStartTime->copy()->addMinutes($gracePeriodMinutes))) {
                    $attendanceStatus = 'late';
                }

                // Create attendance record
                $attendance = ToolboxTalkAttendance::create([
                    'toolbox_talk_id' => $toolboxTalk->id,
                    'employee_id' => $user->id,
                    'employee_name' => $user->name,
                    'employee_id_number' => $user->employee_id_number,
                    'department' => $user->department?->name,
                    'attendance_status' => $attendanceStatus,
                    'check_in_time' => $log['timestamp'],
                    'check_in_method' => 'biometric',
                    'biometric_template_id' => $log['template_id'] ?? null,
                    'device_id' => $this->deviceIp,
                    'check_in_latitude' => $toolboxTalk->latitude,
                    'check_in_longitude' => $toolboxTalk->longitude,
                    'is_supervisor' => $user->role && $user->role->name === 'supervisor',
                    'is_presenter' => $toolboxTalk->supervisor_id === $user->id,
                ]);

                $results['new_attendances']++;

                Log::info("Biometric attendance recorded for {$user->name} in toolbox talk {$toolboxTalk->reference_number}");
            }

            // Update toolbox talk statistics
            $toolboxTalk->total_attendees = $toolboxTalk->attendances()->count();
            $toolboxTalk->present_attendees = $toolboxTalk->attendances()->present()->count();
            $toolboxTalk->calculateAttendanceRate();

        } catch (\Exception $e) {
            Log::error("Error processing toolbox talk attendance: " . $e->getMessage());
            $results['errors'][] = "System error: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Find user by attendance log
     */
    private function findUserByLog(array $log): ?User
    {
        // Try to find by template ID first
        if (!empty($log['template_id'])) {
            $user = User::where('biometric_template_id', $log['template_id'])->first();
            if ($user) return $user;
        }

        // Try to find by employee ID number
        if (!empty($log['user_id'])) {
            $user = User::where('employee_id_number', $log['user_id'])->first();
            if ($user) return $user;
        }

        // Try to find by card number
        if (!empty($log['card_number'])) {
            $user = User::where('employee_id_number', $log['card_number'])->first();
            if ($user) return $user;
        }

        return null;
    }

    /**
     * Get device status
     */
    public function getDeviceStatus(): array
    {
        try {
            $response = Http::timeout(5)->get("http://{$this->deviceIp}/api/status");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return [
                'status' => 'offline',
                'message' => 'Device not responding'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Clear attendance logs from device
     */
    public function clearAttendanceLogs(): bool
    {
        try {
            $response = Http::timeout(10)->delete("http://{$this->deviceIp}/api/attendance");
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to clear attendance logs: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get device information
     */
    public function getDeviceInfo(): array
    {
        try {
            $response = Http::timeout(5)->get("http://{$this->deviceIp}/api/info");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return [
                'device_type' => 'ZKTeco K40',
                'ip' => $this->deviceIp,
                'status' => 'unknown'
            ];
        } catch (\Exception $e) {
            return [
                'device_type' => 'ZKTeco K40',
                'ip' => $this->deviceIp,
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Test device connectivity
     */
    public function testConnection(): array
    {
        $startTime = microtime(true);
        
        try {
            $response = Http::timeout(5)->get("http://{$this->deviceIp}/api/test");
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return [
                'connected' => $response->successful(),
                'response_time' => $responseTime,
                'device_ip' => $this->deviceIp,
                'status_code' => $response->status(),
                'message' => $response->successful() ? 'Connected successfully' : 'Connection failed'
            ];
        } catch (\Exception $e) {
            return [
                'connected' => false,
                'response_time' => null,
                'device_ip' => $this->deviceIp,
                'error' => $e->getMessage(),
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }
}
