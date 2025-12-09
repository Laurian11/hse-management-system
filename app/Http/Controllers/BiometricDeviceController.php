<?php

namespace App\Http\Controllers;

use App\Models\BiometricDevice;
use App\Models\Company;
use App\Services\MultiDeviceZKTecoService;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BiometricDeviceController extends Controller
{
    use UsesCompanyGroup;

    protected $zkService;

    public function __construct()
    {
        $this->zkService = new MultiDeviceZKTecoService();
    }

    /**
     * Display a listing of devices
     */
    public function index(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
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
        $request->validate([
            'device_name' => 'required|string|max:255',
            'device_serial_number' => 'required|string|unique:biometric_devices,device_serial_number',
            'device_type' => 'required|string',
            'company_id' => 'required|exists:companies,id',
            'location_name' => 'required|string|max:255',
            'location_address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_ip' => 'required|ip|unique:biometric_devices,device_ip',
            'port' => 'required|integer|min:1|max:65535',
            'api_key' => 'nullable|string',
            'connection_type' => 'required|in:http,tcp,both',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'grace_period_minutes' => 'required|integer|min:0|max:60',
        ]);

        $device = BiometricDevice::create([
            'device_name' => $request->device_name,
            'device_serial_number' => $request->device_serial_number,
            'device_type' => $request->device_type,
            'company_id' => $request->company_id,
            'location_name' => $request->location_name,
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'device_ip' => $request->device_ip,
            'port' => $request->port,
            'api_key' => $request->api_key,
            'connection_type' => $request->connection_type,
            'work_start_time' => $request->work_start_time,
            'work_end_time' => $request->work_end_time,
            'grace_period_minutes' => $request->grace_period_minutes,
            'auto_sync_enabled' => $request->boolean('auto_sync_enabled', true),
            'daily_attendance_enabled' => $request->boolean('daily_attendance_enabled', true),
            'toolbox_attendance_enabled' => $request->boolean('toolbox_attendance_enabled', true),
            'sync_interval_minutes' => $request->sync_interval_minutes ?? 5,
            'status' => $request->status ?? 'active',
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

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
        
        if (!in_array($biometricDevice->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $biometricDevice->load(['company', 'creator', 'updater']);
        
        // Get device status
        $deviceStatus = $this->zkService->getDeviceStatus($biometricDevice);
        
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
            'company_id' => 'required|exists:companies,id',
            'location_name' => 'required|string|max:255',
            'location_address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_ip' => 'required|ip|unique:biometric_devices,device_ip,' . $biometricDevice->id,
            'port' => 'required|integer|min:1|max:65535',
            'api_key' => 'nullable|string',
            'connection_type' => 'required|in:http,tcp,both',
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'grace_period_minutes' => 'required|integer|min:0|max:60',
        ]);

        $biometricDevice->update([
            'device_name' => $request->device_name,
            'device_serial_number' => $request->device_serial_number,
            'device_type' => $request->device_type,
            'company_id' => $request->company_id,
            'location_name' => $request->location_name,
            'location_address' => $request->location_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'device_ip' => $request->device_ip,
            'port' => $request->port,
            'api_key' => $request->api_key,
            'connection_type' => $request->connection_type,
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
        
        $status = $this->zkService->getDeviceStatus($biometricDevice);
        
        return response()->json($status);
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
        
        $results = $this->zkService->syncUsersToDevice($biometricDevice);
        
        return back()->with('success', "Synced {$results['success']} employees. Failed: {$results['failed']}");
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
