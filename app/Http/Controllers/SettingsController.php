<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BiometricDevice;
use App\Models\DailyAttendance;
use App\Models\Employee;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Traits\UsesCompanyGroup;

class SettingsController extends Controller
{
    use UsesCompanyGroup;

    /**
     * Display settings dashboard
     */
    public function index()
    {
        $companyGroupIds = $this->getCompanyGroupIds();
        
        // Statistics for settings modules
        $stats = [
            'biometric_devices' => BiometricDevice::whereIn('company_id', $companyGroupIds)->count(),
            'active_devices' => BiometricDevice::whereIn('company_id', $companyGroupIds)->where('status', 'active')->count(),
            'total_attendance_today' => DailyAttendance::whereIn('company_id', $companyGroupIds)
                ->whereDate('attendance_date', today())
                ->count(),
            'notification_rules' => class_exists(\App\Models\NotificationRule::class) 
                ? \App\Models\NotificationRule::whereIn('company_id', $companyGroupIds)->count() : 0,
            'escalation_matrices' => class_exists(\App\Models\EscalationMatrix::class)
                ? \App\Models\EscalationMatrix::whereIn('company_id', $companyGroupIds)->count() : 0,
            'total_users' => User::whereIn('company_id', $companyGroupIds)->count(),
            'total_employees' => Employee::whereIn('company_id', $companyGroupIds)->count(),
            'total_companies' => Company::whereIn('id', $companyGroupIds)->count(),
            'total_departments' => Department::whereIn('company_id', $companyGroupIds)->count(),
        ];
        
        return view('settings.index', compact('stats'));
    }
}
