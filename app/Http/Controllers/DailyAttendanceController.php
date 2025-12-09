<?php

namespace App\Http\Controllers;

use App\Models\DailyAttendance;
use App\Models\BiometricDevice;
use App\Models\Employee;
use App\Traits\UsesCompanyGroup;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyAttendanceController extends Controller
{
    use UsesCompanyGroup;

    /**
     * Display a listing of daily attendance
     */
    public function index(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $query = DailyAttendance::whereIn('company_id', $companyGroupIds)
            ->with(['biometricDevice', 'employee', 'department', 'company']);
        
        // Filters
        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        } else {
            $query->whereDate('attendance_date', today());
        }
        
        if ($request->filled('device_id')) {
            $query->where('biometric_device_id', $request->device_id);
        }
        
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->filled('status')) {
            $query->where('attendance_status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }
        
        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('check_in_time', 'desc')
            ->paginate(20);
        
        // Get devices and employees for filters
        $devices = BiometricDevice::whereIn('company_id', $companyGroupIds)->active()->get();
        $employees = Employee::whereIn('company_id', $companyGroupIds)->active()->get();
        
        // Statistics (with caching)
        $selectedDate = $request->filled('date') ? $request->date : today()->format('Y-m-d');
        $cacheKey = "attendance_stats_{$selectedDate}_" . md5(implode(',', $companyGroupIds));
        $stats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($companyGroupIds, $selectedDate) {
            return [
                'total' => DailyAttendance::whereIn('company_id', $companyGroupIds)
                    ->whereDate('attendance_date', $selectedDate)
                    ->count(),
                'present' => DailyAttendance::whereIn('company_id', $companyGroupIds)
                    ->whereDate('attendance_date', $selectedDate)
                    ->where('attendance_status', 'present')
                    ->count(),
                'late' => DailyAttendance::whereIn('company_id', $companyGroupIds)
                    ->whereDate('attendance_date', $selectedDate)
                    ->where('is_late', true)
                    ->count(),
                'absent' => DailyAttendance::whereIn('company_id', $companyGroupIds)
                    ->whereDate('attendance_date', $selectedDate)
                    ->where('attendance_status', 'absent')
                    ->count(),
            ];
        });
        
        return view('daily-attendance.index', compact('attendances', 'devices', 'employees', 'stats', 'selectedDate'));
    }

    /**
     * Show attendance dashboard
     */
    public function dashboard(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $date = $request->filled('date') ? Carbon::parse($request->date) : today();
        
        // Today's statistics
        $todayStats = [
            'total_employees' => Employee::whereIn('company_id', $companyGroupIds)->active()->count(),
            'present' => DailyAttendance::whereIn('company_id', $companyGroupIds)
                ->whereDate('attendance_date', $date)
                ->where('attendance_status', 'present')
                ->count(),
            'late' => DailyAttendance::whereIn('company_id', $companyGroupIds)
                ->whereDate('attendance_date', $date)
                ->where('is_late', true)
                ->count(),
            'absent' => DailyAttendance::whereIn('company_id', $companyGroupIds)
                ->whereDate('attendance_date', $date)
                ->where('attendance_status', 'absent')
                ->count(),
        ];
        
        // Device-wise statistics
        $devices = BiometricDevice::whereIn('company_id', $companyGroupIds)
            ->where('daily_attendance_enabled', true)
            ->get();
        
        $deviceStats = $devices->map(function($device) use ($date) {
            $attendances = DailyAttendance::where('biometric_device_id', $device->id)
                ->whereDate('attendance_date', $date)
                ->get();
            
            return [
                'device' => $device,
                'total' => $attendances->count(),
                'present' => $attendances->where('attendance_status', 'present')->count(),
                'late' => $attendances->where('is_late', true)->count(),
            ];
        });
        
        // Recent attendance
        $recentAttendance = DailyAttendance::whereIn('company_id', $companyGroupIds)
            ->with(['biometricDevice', 'employee', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('daily-attendance.dashboard', compact('todayStats', 'deviceStats', 'recentAttendance', 'date'));
    }

    /**
     * Show the specified attendance record
     */
    public function show(DailyAttendance $dailyAttendance)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($dailyAttendance->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $dailyAttendance->load(['biometricDevice', 'employee', 'department', 'company', 'approver']);
        
        return view('daily-attendance.show', compact('dailyAttendance'));
    }

    /**
     * Approve attendance record
     */
    public function approve(DailyAttendance $dailyAttendance)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($dailyAttendance->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $dailyAttendance->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return back()->with('success', 'Attendance record approved successfully!');
    }

    /**
     * Reject attendance record
     */
    public function reject(DailyAttendance $dailyAttendance, Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        if (!in_array($dailyAttendance->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $dailyAttendance->update([
            'approved_by' => null,
            'approved_at' => null,
            'remarks' => 'Rejected: ' . $request->rejection_reason,
        ]);
        
        return back()->with('success', 'Attendance record rejected.');
    }

    /**
     * Manual check-in
     */
    public function manualCheckIn(Request $request)
    {
        $request->validate([
            'biometric_device_id' => 'required|exists:biometric_devices,id',
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);
        
        $device = BiometricDevice::findOrFail($request->biometric_device_id);
        $employee = Employee::findOrFail($request->employee_id);
        
        $companyGroupIds = $this->getCompanyGroupIds();
        if (!in_array($device->company_id, $companyGroupIds) || !in_array($employee->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $attendance = DailyAttendance::firstOrNew([
            'biometric_device_id' => $device->id,
            'company_id' => $device->company_id,
            'employee_id' => $employee->id,
            'user_id' => $employee->user_id,
            'employee_id_number' => $employee->employee_id_number,
            'attendance_date' => $request->attendance_date,
        ]);
        
        $attendance->employee_name = $employee->full_name;
        $attendance->department_id = $employee->department_id;
        $attendance->check_in_time = $request->check_in_time;
        $attendance->check_in_method = 'manual';
        $attendance->attendance_status = 'present';
        $attendance->check_in_notes = $request->notes;
        $attendance->is_manual_entry = true;
        $attendance->save();
        
        return back()->with('success', 'Manual check-in recorded successfully!');
    }

    /**
     * Manual check-out
     */
    public function manualCheckOut(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:daily_attendance,id',
            'check_out_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);
        
        $attendance = DailyAttendance::findOrFail($request->attendance_id);
        
        $companyGroupIds = $this->getCompanyGroupIds();
        if (!in_array($attendance->company_id, $companyGroupIds)) {
            abort(403, 'Unauthorized');
        }
        
        $attendance->check_out_time = $request->check_out_time;
        $attendance->check_out_method = 'manual';
        $attendance->check_out_notes = $request->notes;
        $attendance->calculateWorkHours();
        $attendance->save();
        
        return back()->with('success', 'Manual check-out recorded successfully!');
    }

    /**
     * Export attendance to Excel
     */
    public function exportExcel(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $query = DailyAttendance::whereIn('company_id', $companyGroupIds)
            ->with(['biometricDevice', 'employee', 'department']);
        
        // Apply same filters as index
        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }
        
        if ($request->filled('device_id')) {
            $query->where('biometric_device_id', $request->device_id);
        }
        
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }
        
        $attendances = $query->orderBy('attendance_date', 'desc')->get();
        
        $filename = 'daily_attendance_export_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Date',
                'Employee ID',
                'Employee Name',
                'Department',
                'Device/Location',
                'Check-In Time',
                'Check-Out Time',
                'Status',
                'Total Hours',
                'Late Minutes',
                'Overtime Minutes'
            ]);
            
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->attendance_date->format('Y-m-d'),
                    $attendance->employee_id_number,
                    $attendance->employee_name,
                    $attendance->department->name ?? 'N/A',
                    $attendance->biometricDevice->location_name ?? 'N/A',
                    $attendance->check_in_time ? Carbon::parse($attendance->check_in_time)->format('H:i:s') : 'N/A',
                    $attendance->check_out_time ? Carbon::parse($attendance->check_out_time)->format('H:i:s') : 'N/A',
                    ucfirst($attendance->attendance_status),
                    $attendance->total_work_hours,
                    $attendance->late_minutes,
                    $attendance->overtime_minutes,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export attendance to PDF
     */
    public function exportPdf(Request $request)
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        $query = DailyAttendance::whereIn('company_id', $companyGroupIds)
            ->with(['biometricDevice', 'employee', 'department']);
        
        // Apply same filters as index
        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }
        
        if ($request->filled('device_id')) {
            $query->where('biometric_device_id', $request->device_id);
        }
        
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('attendance_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('attendance_date', '<=', $request->date_to);
        }
        
        $attendances = $query->orderBy('attendance_date', 'desc')->get();
        
        $pdf = Pdf::loadView('daily-attendance.exports.pdf', [
            'attendances' => $attendances,
            'date' => $request->filled('date') ? $request->date : today()->format('Y-m-d'),
        ]);
        
        return $pdf->download('daily_attendance_' . date('Y-m-d_His') . '.pdf');
    }
}
