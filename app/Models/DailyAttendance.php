<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class DailyAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daily_attendance';

    protected $fillable = [
        'biometric_device_id',
        'company_id',
        'employee_id',
        'user_id',
        'employee_id_number',
        'employee_name',
        'department_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'attendance_status',
        'check_in_method',
        'check_out_method',
        'biometric_template_id',
        'device_log_id',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'total_work_minutes',
        'overtime_minutes',
        'late_minutes',
        'early_departure_minutes',
        'is_late',
        'is_early_departure',
        'is_overtime',
        'is_absent',
        'is_manual_entry',
        'check_in_notes',
        'check_out_notes',
        'remarks',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
        'total_work_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'late_minutes' => 'integer',
        'early_departure_minutes' => 'integer',
        'is_late' => 'boolean',
        'is_early_departure' => 'boolean',
        'is_overtime' => 'boolean',
        'is_absent' => 'boolean',
        'is_manual_entry' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function biometricDevice(): BelongsTo
    {
        return $this->belongsTo(BiometricDevice::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scopes
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    public function scopePresent($query)
    {
        return $query->where('attendance_status', 'present')
                    ->whereNotNull('check_in_time');
    }

    public function scopeAbsent($query)
    {
        return $query->where('attendance_status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    public function scopeForDevice($query, $deviceId)
    {
        return $query->where('biometric_device_id', $deviceId);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Helper Methods
     */
    public function calculateWorkHours(): void
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return;
        }

        $checkIn = Carbon::parse($this->attendance_date . ' ' . $this->check_in_time);
        $checkOut = Carbon::parse($this->attendance_date . ' ' . $this->check_out_time);
        
        $totalMinutes = $checkOut->diffInMinutes($checkIn);
        $this->total_work_minutes = $totalMinutes;

        // Get device work hours
        $device = $this->biometricDevice;
        if ($device) {
            $workStart = Carbon::parse($this->attendance_date . ' ' . $device->work_start_time);
            $workEnd = Carbon::parse($this->attendance_date . ' ' . $device->work_end_time);
            $expectedWorkMinutes = $workEnd->diffInMinutes($workStart);

            // Calculate overtime
            if ($totalMinutes > $expectedWorkMinutes) {
                $this->overtime_minutes = $totalMinutes - $expectedWorkMinutes;
                $this->is_overtime = true;
            }

            // Calculate late minutes
            $gracePeriod = $device->grace_period_minutes;
            if ($checkIn->gt($workStart->copy()->addMinutes($gracePeriod))) {
                $this->late_minutes = $checkIn->diffInMinutes($workStart->copy()->addMinutes($gracePeriod));
                $this->is_late = true;
            }

            // Calculate early departure
            if ($checkOut->lt($workEnd)) {
                $this->early_departure_minutes = $workEnd->diffInMinutes($checkOut);
                $this->is_early_departure = true;
            }
        }

        $this->save();
    }

    public function getTotalWorkHoursAttribute(): float
    {
        if (!$this->total_work_minutes) {
            return 0;
        }
        return round($this->total_work_minutes / 60, 2);
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'present' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Present</span>',
            'absent' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Absent</span>',
            'late' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Late</span>',
            'half_day' => '<span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Half Day</span>',
            'on_leave' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">On Leave</span>',
            'sick_leave' => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Sick Leave</span>',
        ];

        return $badges[$this->attendance_status] ?? '';
    }
}
