<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BiometricDevice;
use App\Models\DailyAttendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ZKTecoBridgeController extends Controller
{
    /**
     * Receive sync data from local bridge
     */
    public function receiveSyncData(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'device' => 'required|array',
            'device.name' => 'required|string',
            'device.serial' => 'required|string',
            'device.ip' => 'required|ip',
            'type' => 'required|in:attendance,users,photos',
            'data' => 'required|array',
            'bridge_id' => 'required|string',
            'timestamp' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify bridge authentication
        if (!$this->verifyBridge($request->bridge_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized bridge'
            ], 401);
        }

        $deviceData = $request->device;
        $syncType = $request->type;
        $syncData = $request->data;

        // Find or create device
        $device = BiometricDevice::updateOrCreate(
            ['device_ip' => $deviceData['ip']],
            [
                'device_name' => $deviceData['name'],
                'location' => $deviceData['area'] ?? 'Unknown',
                'status' => 'active',
                'last_connected_at' => now(),
            ]
        );

        // Process based on sync type
        switch ($syncType) {
            case 'attendance':
                $result = $this->processAttendance($device, $syncData);
                break;

            case 'users':
                $result = $this->processUsers($device, $syncData);
                break;

            default:
                $result = ['success' => true, 'message' => 'Sync type not processed'];
        }

        // Update device stats
        $device->update(['last_sync_at' => now()]);

        // Log sync
        Log::info("Bridge sync received", [
            'bridge' => $request->bridge_id,
            'device' => $device->device_name,
            'type' => $syncType,
            'count' => count($syncData)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sync processed successfully',
            'device' => $device->device_name,
            'processed' => $result['processed'] ?? 0,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Process attendance data
     */
    private function processAttendance($device, $logs)
    {
        $processed = 0;

        foreach ($logs as $log) {
            try {
                // Map device user ID to employee
                $employee = $this->mapToEmployee($device->id, $log['user_id']);

                // Determine check type
                $checkType = $this->determineCheckType($employee, $log);

                // Parse timestamp
                $checkTime = Carbon::parse($log['timestamp']);

                // Create attendance record
                DailyAttendance::updateOrCreate([
                    'biometric_device_id' => $device->id,
                    'employee_id_number' => $log['user_id'],
                    'attendance_date' => $checkTime->toDateString(),
                    'check_in_time' => $checkType === 'check_in' ? $checkTime->toTimeString() : null,
                    'check_out_time' => $checkType === 'check_out' ? $checkTime->toTimeString() : null,
                ], [
                    'company_id' => $device->company_id,
                    'employee_id' => $employee?->id,
                    'user_id' => $employee?->user_id,
                    'department_id' => $employee?->department_id,
                    'employee_name' => $employee ? "{$employee->first_name} {$employee->last_name}" : "User {$log['user_id']}",
                    'check_in_method' => $checkType === 'check_in' ? 'biometric' : null,
                    'check_out_method' => $checkType === 'check_out' ? 'biometric' : null,
                    'biometric_template_id' => $log['biometric_template_id'] ?? null,
                    'device_log_id' => $log['device_log_id'] ?? null,
                    'attendance_status' => $this->calculateAttendanceStatus($checkTime, $device),
                    'is_late' => $this->isLate($checkTime, $device),
                    'late_minutes' => $this->calculateLateMinutes($checkTime, $device),
                ]);

                $processed++;

            } catch (\Exception $e) {
                Log::error("Failed to process attendance log: " . $e->getMessage(), [
                    'log' => $log,
                    'error' => $e->getTraceAsString()
                ]);
            }
        }

        return ['processed' => $processed];
    }

    /**
     * Map device user ID to employee
     */
    private function mapToEmployee($deviceId, $deviceUserId)
    {
        // Check cache first
        $cacheKey = "device_{$deviceId}_user_{$deviceUserId}";

        if (Cache::has($cacheKey)) {
            $employeeId = Cache::get($cacheKey);
            return Employee::find($employeeId);
        }

        // Try to find mapping - check employee_id_number or biometric mappings
        $employee = Employee::where('employee_id_number', $deviceUserId)
            ->orWhere(function($query) use ($deviceId, $deviceUserId) {
                $query->whereJsonContains('biometric_mappings->' . $deviceId, $deviceUserId);
            })
            ->first();

        if ($employee) {
            Cache::put($cacheKey, $employee->id, 3600); // Cache for 1 hour
        }

        return $employee;
    }

    /**
     * Determine check type (check-in/check-out)
     */
    private function determineCheckType($employee, $log)
    {
        if (!$employee) {
            return 'check_in'; // Default to check_in for unmapped users
        }

        // Get last check for this employee today
        $checkTime = Carbon::parse($log['timestamp']);
        $lastCheck = DailyAttendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $checkTime->toDateString())
            ->orderBy('check_in_time', 'desc')
            ->first();

        if (!$lastCheck || !$lastCheck->check_in_time) {
            return 'check_in';
        }

        // If last check was check_in and no check_out, this is check_out
        if ($lastCheck->check_in_time && !$lastCheck->check_out_time) {
            return 'check_out';
        }

        // If last check had both check_in and check_out, this is a new check_in
        if ($lastCheck->check_in_time && $lastCheck->check_out_time) {
            return 'check_in';
        }

        // Default to check_in
        return 'check_in';
    }

    /**
     * Calculate attendance status
     */
    private function calculateAttendanceStatus($checkTime, $device)
    {
        // Basic logic - can be enhanced
        return 'present';
    }

    /**
     * Check if late
     */
    private function isLate($checkTime, $device)
    {
        $workStart = Carbon::parse($device->work_start_time);
        $checkTimeOnly = $checkTime->copy()->setTime($checkTime->hour, $checkTime->minute, $checkTime->second);
        
        return $checkTimeOnly->gt($workStart->copy()->addMinutes($device->grace_period_minutes));
    }

    /**
     * Calculate late minutes
     */
    private function calculateLateMinutes($checkTime, $device)
    {
        if (!$this->isLate($checkTime, $device)) {
            return 0;
        }

        $workStart = Carbon::parse($device->work_start_time);
        $checkTimeOnly = $checkTime->copy()->setTime($checkTime->hour, $checkTime->minute, $checkTime->second);
        
        return $workStart->copy()->addMinutes($device->grace_period_minutes)->diffInMinutes($checkTimeOnly);
    }

    /**
     * Process users data
     */
    private function processUsers($device, $users)
    {
        $processed = 0;

        foreach ($users as $userData) {
            try {
                // Map device users to employees
                // This would typically update employee biometric mappings
                $processed++;
            } catch (\Exception $e) {
                Log::error("Failed to process user: " . $e->getMessage());
            }
        }

        return ['processed' => $processed];
    }

    /**
     * Bridge heartbeat
     */
    public function heartbeat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bridge_id' => 'required|string',
            'status' => 'required|string',
            'devices' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false], 400);
        }

        // Update bridge status in cache
        Cache::put("bridge_{$request->bridge_id}", [
            'status' => $request->status,
            'last_seen' => now(),
            'devices' => $request->devices
        ], 300); // 5 minutes cache

        return response()->json(['success' => true]);
    }

    /**
     * Verify bridge authentication
     */
    private function verifyBridge($bridgeId)
    {
        $validBridges = [
            'hesu_bridge_001' => env('BRIDGE_KEY_001'),
            'hesu_bridge_002' => env('BRIDGE_KEY_002')
        ];

        $bridgeKey = request()->header('X-Bridge-Key');
        
        return isset($validBridges[$bridgeId]) && 
               $bridgeKey === $validBridges[$bridgeId];
    }

    /**
     * Get bridge status
     */
    public function getBridgeStatus()
    {
        $bridges = [
            'hesu_bridge_001' => Cache::get('bridge_hesu_bridge_001', ['status' => 'offline'])
        ];

        return response()->json([
            'success' => true,
            'bridges' => $bridges
        ]);
    }
}
