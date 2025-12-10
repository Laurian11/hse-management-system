<?php

namespace App\Http\Controllers;

use App\Models\BiometricDevice;
use App\Models\Company;
use App\Services\MultiDeviceZKTecoService;
use App\Services\EnhancedZKTecoService;
use App\Services\NetworkConnectionService;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiometricDeviceController extends Controller
{
    use UsesCompanyGroup;

    protected $zkService;
    protected $enhancedService;

    public function __construct()
    {
        $this->zkService = new MultiDeviceZKTecoService();
        // EnhancedZKTecoService is not used in index, so we'll lazy load it when needed
        // $this->enhancedService = new EnhancedZKTecoService();
    }
    
    /**
     * Get enhanced service instance (lazy loading)
     */
    protected function getEnhancedService()
    {
        if (!$this->enhancedService) {
            $this->enhancedService = new EnhancedZKTecoService();
        }
        return $this->enhancedService;
    }

    /**
     * Display a listing of devices
     */
    public function index(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        // Handle empty company group IDs - return empty result set
        if (empty($companyGroupIds)) {
            $devices = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $companies = collect([]);
            return view('biometric-devices.index', compact('devices', 'companies'));
        }
        
        $query = BiometricDevice::whereIn('company_id', $companyGroupIds)
            ->with(['company', 'creator']);
        
        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('location')) {
            $query->where('location_name', 'like', "%{$request->location}%");
        }
        
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        if ($request->filled('device_category')) {
            $query->where('device_category', $request->device_category);
        }
        
        $devices = $query->orderBy('location_name')->paginate(15);
        
        $companies = Company::whereIn('id', $companyGroupIds)->get();
        
        return view('biometric-devices.index', compact('devices', 'companies'));
    }

    /**
     * Show the form for creating a new device
     */
    public function create()
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        $companies = Company::whereIn('id', $companyGroupIds)->get();
        
        return view('biometric-devices.create', compact('companies'));
    }

    /**
     * Store a newly created device
     */
    public function store(Request $request)
    {
        // Check for existing devices before validation
        $existingBySerial = BiometricDevice::where('device_serial_number', $request->device_serial_number)->first();
        $existingByIP = BiometricDevice::where('device_ip', $request->device_ip)->first();

        $request->validate([
            'device_name' => 'required|string|max:255',
            'device_serial_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($existingBySerial) {
                    if ($existingBySerial) {
                        $fail("The serial number '{$value}' is already registered to device: {$existingBySerial->device_name} (ID: {$existingBySerial->id}). Please use a different serial number or edit the existing device.");
                    }
                },
            ],
            'device_type' => 'required|string',
            'device_category' => 'required|in:attendance,toolbox_training,both',
            'device_purpose' => 'nullable|string|max:500',
            'company_id' => 'required|exists:companies,id',
            'location_name' => 'required|string|max:255',
            'location_address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_ip' => [
                'required',
                'ip',
                function ($attribute, $value, $fail) use ($existingByIP) {
                    if ($existingByIP) {
                        $fail("The IP address '{$value}' is already registered to device: {$existingByIP->device_name} (ID: {$existingByIP->id}). Please use a different IP address or edit the existing device.");
                    }
                },
            ],
            'public_ip' => 'nullable|ip',
            'port' => 'required|integer|min:1|max:65535',
            'api_key' => 'nullable|string',
            'connection_type' => 'required|in:http,tcp,both',
            'network_type' => 'nullable|in:local,remote,internet',
            'requires_vpn' => 'nullable|boolean',
            'connection_timeout' => 'nullable|integer|min:5|max:120',
            'auto_detect_network' => 'nullable|boolean',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'grace_period_minutes' => 'required|integer|min:0|max:60',
        ], [
            'device_serial_number.required' => 'The device serial number is required.',
            'device_ip.required' => 'The device IP address is required.',
            'device_ip.ip' => 'Please enter a valid IP address.',
        ]);

        // Auto-detect network type if not provided
        $networkService = new \App\Services\NetworkConnectionService();
        $networkType = $request->network_type ?? $networkService->detectNetworkType($request->device_ip);
        
        // Get recommended settings for network type
        $recommendedSettings = $networkService->getRecommendedSettings($networkType);

        $device = BiometricDevice::create([
            'device_name' => $request->device_name,
            'device_serial_number' => $request->device_serial_number,
            'device_type' => $request->device_type,
            'device_category' => $request->device_category ?? 'attendance',
            'device_purpose' => $request->device_purpose,
            'company_id' => $request->company_id,
            'location_name' => $request->location_name,
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'device_ip' => $request->device_ip,
            'public_ip' => $request->public_ip,
            'port' => $request->port,
            'api_key' => $request->api_key,
            'connection_type' => $request->connection_type,
            'network_type' => $networkType,
            'requires_vpn' => $request->boolean('requires_vpn', $recommendedSettings['requires_vpn'] ?? false),
            'connection_timeout' => $request->connection_timeout ?? $recommendedSettings['connection_timeout'] ?? 10,
            'auto_detect_network' => $request->boolean('auto_detect_network', $recommendedSettings['auto_detect_network'] ?? true),
            'work_start_time' => $request->work_start_time,
            'work_end_time' => $request->work_end_time,
            'grace_period_minutes' => $request->grace_period_minutes,
            'auto_sync_enabled' => $request->boolean('auto_sync_enabled', true),
            // Auto-configure based on device category
            'daily_attendance_enabled' => $request->boolean('daily_attendance_enabled', in_array($request->device_category, ['attendance', 'both'])),
            'toolbox_attendance_enabled' => $request->boolean('toolbox_attendance_enabled', in_array($request->device_category, ['toolbox_training', 'both'])),
            'sync_interval_minutes' => $request->sync_interval_minutes ?? 5,
            'status' => $request->status ?? 'active',
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Auto-detect network configuration
        if ($device->auto_detect_network) {
            $networkService->autoDetectNetwork($device);
            $device->refresh();
        }

        // Test connection
        $connectionStatus = $this->zkService->connectToDevice($device);
        
        if ($connectionStatus) {
            $device->update(['status' => 'active', 'last_connected_at' => now()]);
        } else {
            $device->update(['status' => 'offline']);
        }

        return redirect()
            ->route('biometric-devices.show', $device)
            ->with('success', 'Biometric device created successfully! ' . ($connectionStatus ? 'Device is online.' : 'Could not connect to device.'));
    }

    /**
     * Display the specified device
     */
    public function show(BiometricDevice $biometricDevice)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        // Handle empty company group IDs
        if (empty($companyGroupIds)) {
            abort(403, 'Unauthorized: No company access');
        }
        
        // Check if device has a company_id
        if (!$biometricDevice->company_id) {
            abort(404, 'Device company not found');
        }
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        // Load relationships with null safety
        $biometricDevice->load(['company', 'creator', 'updater']);
        
        // Get device status with error handling
        $deviceStatus = [
            'connected' => false,
            'status' => 'unknown',
            'message' => 'Status check not available'
        ];
        
        try {
            $deviceStatus = $this->zkService->getDeviceStatus($biometricDevice);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Failed to get device status for device {$biometricDevice->id}: " . $e->getMessage());
            $deviceStatus = [
                'connected' => false,
                'status' => 'error',
                'message' => 'Unable to check device status: ' . $e->getMessage()
            ];
        }
        
        // Get today's attendance stats (with caching)
        $cacheKey = "device_{$biometricDevice->id}_stats_" . today()->format('Y-m-d');
        $stats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($biometricDevice) {
            $todayAttendance = $biometricDevice->dailyAttendances()
                ->whereDate('attendance_date', today())
                ->get();
            
            return [
                'total_today' => $todayAttendance->count(),
                'present_today' => $todayAttendance->where('attendance_status', 'present')->count(),
                'late_today' => $todayAttendance->where('is_late', true)->count(),
                'absent_today' => $todayAttendance->where('attendance_status', 'absent')->count(),
            ];
        });
        
        return view('biometric-devices.show', compact('biometricDevice', 'deviceStatus', 'stats'));
    }

    /**
     * Show the form for editing the device
     */
    public function edit(BiometricDevice $biometricDevice)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $companies = Company::whereIn('id', $companyGroupIds)->get();
        
        return view('biometric-devices.edit', compact('biometricDevice', 'companies'));
    }

    /**
     * Update the device
     */
    public function update(Request $request, BiometricDevice $biometricDevice)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'device_name' => 'required|string|max:255',
            'device_serial_number' => 'required|string|unique:biometric_devices,device_serial_number,' . $biometricDevice->id,
            'device_type' => 'required|string',
            'device_category' => 'required|in:attendance,toolbox_training,both',
            'device_purpose' => 'nullable|string|max:500',
            'company_id' => 'required|exists:companies,id',
            'location_name' => 'required|string|max:255',
            'location_address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_ip' => 'required|ip|unique:biometric_devices,device_ip,' . $biometricDevice->id,
            'public_ip' => 'nullable|ip',
            'port' => 'required|integer|min:1|max:65535',
            'api_key' => 'nullable|string',
            'connection_type' => 'required|in:http,tcp,both',
            'network_type' => 'nullable|in:local,remote,internet',
            'requires_vpn' => 'nullable|boolean',
            'connection_timeout' => 'nullable|integer|min:5|max:120',
            'auto_detect_network' => 'nullable|boolean',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'grace_period_minutes' => 'required|integer|min:0|max:60',
        ]);

        // Auto-detect network type if changed or auto-detect enabled
        $networkService = new \App\Services\NetworkConnectionService();
        $networkType = $request->network_type;
        
        if (!$networkType || $biometricDevice->auto_detect_network) {
            $networkType = $networkService->detectNetworkType($request->device_ip);
        }

        $biometricDevice->update([
            'device_name' => $request->device_name,
            'device_serial_number' => $request->device_serial_number,
            'device_type' => $request->device_type,
            'device_category' => $request->device_category ?? 'attendance',
            'device_purpose' => $request->device_purpose,
            'company_id' => $request->company_id,
            'location_name' => $request->location_name,
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'device_ip' => $request->device_ip,
            'public_ip' => $request->public_ip,
            'port' => $request->port,
            'api_key' => $request->api_key,
            'connection_type' => $request->connection_type,
            'network_type' => $networkType,
            'requires_vpn' => $request->boolean('requires_vpn'),
            'connection_timeout' => $request->connection_timeout ?? $biometricDevice->connection_timeout ?? 10,
            'auto_detect_network' => $request->boolean('auto_detect_network', true),
            'work_start_time' => $request->work_start_time,
            'work_end_time' => $request->work_end_time,
            'grace_period_minutes' => $request->grace_period_minutes,
            'auto_sync_enabled' => $request->boolean('auto_sync_enabled'),
            'daily_attendance_enabled' => $request->boolean('daily_attendance_enabled'),
            'toolbox_attendance_enabled' => $request->boolean('toolbox_attendance_enabled'),
            'sync_interval_minutes' => $request->sync_interval_minutes ?? 5,
            'status' => $request->status,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Auto-detect network configuration if enabled
        if ($biometricDevice->auto_detect_network) {
            $networkService->autoDetectNetwork($biometricDevice);
        }

        return redirect()
            ->route('biometric-devices.show', $biometricDevice)
            ->with('success', 'Device updated successfully!');
    }

    /**
     * Remove the device
     */
    public function destroy(BiometricDevice $biometricDevice)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $biometricDevice->delete();

        return redirect()
            ->route('biometric-devices.index')
            ->with('success', 'Device deleted successfully!');
    }

    /**
     * Test device connection
     */
    public function testConnection(BiometricDevice $biometricDevice)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        // Try enhanced service first (uses ZKLib), fallback to standard service
        try {
            $status = $this->getEnhancedService()->getDeviceStatus($biometricDevice);
        } catch (\Exception $e) {
            // Fallback to standard service
        $status = $this->zkService->getDeviceStatus($biometricDevice);
        }
        
        return response()->json($status);
    }

    /**
     * Get device information (version, time, etc.)
     */
    public function getDeviceInfo(BiometricDevice $biometricDevice)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }

        $info = [
            'device_id' => $biometricDevice->id,
            'device_name' => $biometricDevice->device_name,
            'device_ip' => $biometricDevice->device_ip,
            'port' => $biometricDevice->port,
            'connection_type' => $biometricDevice->connection_type,
        ];

        // Get enhanced information using ZKLib
        try {
            $version = $this->getEnhancedService()->getDeviceVersion($biometricDevice);
            $deviceTime = $this->getEnhancedService()->getDeviceTime($biometricDevice);
            $status = $this->getEnhancedService()->getDeviceStatus($biometricDevice);

            $info['version'] = $version;
            $info['device_time'] = $deviceTime ? $deviceTime->format('Y-m-d H:i:s') : null;
            $info['status'] = $status;
            $info['source'] = 'ZKLib';
        } catch (\Exception $e) {
            $info['error'] = $e->getMessage();
            $info['source'] = 'fallback';
        }

        return response()->json($info);
    }

    /**
     * Sync device time with server
     */
    public function syncDeviceTime(BiometricDevice $biometricDevice)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }

        try {
            $result = $this->getEnhancedService()->setDeviceTime($biometricDevice, now());
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Device time synchronized successfully',
                    'server_time' => now()->format('Y-m-d H:i:s'),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to synchronize device time',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync users to device
     */
    public function syncUsers(BiometricDevice $biometricDevice)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        try {
            // Test connection first
            if (!$this->zkService->connectToDevice($biometricDevice)) {
                return back()->with('error', 'Cannot sync users: Device connection failed. Please check device settings and network connectivity.');
        }
        
        $results = $this->zkService->syncUsersToDevice($biometricDevice);
        
            $message = "Synced {$results['success']} employees. Failed: {$results['failed']}";
            
            if ($results['failed'] > 0 && !empty($results['errors'])) {
                $errorCount = min(3, count($results['errors']));
                $message .= "\nErrors: " . implode(', ', array_slice($results['errors'], 0, $errorCount));
                if (count($results['errors']) > $errorCount) {
                    $message .= ' and ' . (count($results['errors']) - $errorCount) . ' more...';
                }
            }
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Sync users error for device {$biometricDevice->id}: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error("Stack trace: " . $e->getTraceAsString());
            
            return back()->with('error', 'Failed to sync users: ' . $e->getMessage() . '. Please check logs for details.');
        }
    }

    /**
     * Sync daily attendance from device
     */
    public function syncAttendance(BiometricDevice $biometricDevice, Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $date = $request->filled('date') ? \Carbon\Carbon::parse($request->date) : now();
        
        $results = $this->zkService->syncDailyAttendance($biometricDevice, $date);
        
        return back()->with('success', "Processed {$results['processed']} logs. New: {$results['new_records']}, Updated: {$results['updated_records']}");
    }

    /**
     * Show employee enrollment page
     */
    public function enrollment(BiometricDevice $biometricDevice)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $employees = \App\Models\Employee::where('company_id', $biometricDevice->company_id)
            ->where('is_active', true)
            ->with(['department', 'user'])
            ->orderBy('first_name')
            ->get();
        
        // Check enrollment status for each employee
        $enrollmentStatus = [];
        foreach ($employees as $employee) {
            $enrollmentStatus[$employee->id] = $this->zkService->checkEnrollmentStatus($biometricDevice, $employee);
        }
        
        return view('biometric-devices.enrollment', compact('biometricDevice', 'employees', 'enrollmentStatus'));
    }

    /**
     * Enroll single employee
     */
    public function enrollEmployee(BiometricDevice $biometricDevice, Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);
        
        $employee = \App\Models\Employee::findOrFail($request->employee_id);
        
        if ($employee->company_id != $biometricDevice->company_id) {
            abort(403, 'Employee does not belong to device company');
        }
        
        $result = $this->zkService->enrollEmployee($biometricDevice, $employee);
        
        if ($result) {
            return back()->with('success', "Employee {$employee->full_name} enrolled successfully!");
        } else {
            return back()->with('error', "Failed to enroll employee {$employee->full_name}. Please check device connection.");
        }
    }

    /**
     * Bulk enroll employees
     */
    public function bulkEnroll(BiometricDevice $biometricDevice, Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);
        
        $employees = \App\Models\Employee::whereIn('id', $request->employee_ids)
            ->where('company_id', $biometricDevice->company_id)
            ->get();
        
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($employees as $employee) {
            if ($this->zkService->enrollEmployee($biometricDevice, $employee)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = $employee->full_name;
            }
        }
        
        $message = "Enrolled {$results['success']} employees. Failed: {$results['failed']}";
        if ($results['failed'] > 0) {
            $message .= " (" . implode(', ', array_slice($results['errors'], 0, 5)) . ")";
        }
        
        return back()->with('success', $message);
    }

    /**
     * Remove employee from device
     */
    public function removeEmployee(BiometricDevice $biometricDevice, Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);
        
        $employee = \App\Models\Employee::findOrFail($request->employee_id);
        
        $result = $this->zkService->removeEmployee($biometricDevice, $employee);
        
        if ($result) {
            return back()->with('success', "Employee {$employee->full_name} removed from device successfully!");
        } else {
            return back()->with('error', "Failed to remove employee {$employee->full_name}.");
        }
    }
}
