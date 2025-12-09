<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemKPI extends Model
{
    protected $table = 'system_kpis';
    
    protected $fillable = [
        'recorded_date',
        'period_type',
        'total_users',
        'active_users',
        'new_users',
        'inactive_users',
        'users_logged_in_today',
        'users_logged_in_this_week',
        'users_logged_in_this_month',
        'user_activity_rate',
        'total_companies',
        'active_companies',
        'new_companies',
        'inactive_companies',
        'total_incidents',
        'total_risk_assessments',
        'total_trainings',
        'total_audits',
        'total_toolbox_talks',
        'total_documents',
        'total_activities_today',
        'total_activities_this_week',
        'total_activities_this_month',
        'total_activities_this_year',
        'average_activities_per_user',
        'average_response_time',
        'system_uptime',
        'error_count',
        'api_requests_today',
        'api_requests_this_week',
        'api_requests_this_month',
        'total_storage_used',
        'total_storage_available',
        'storage_utilization',
        'total_files',
        'total_documents_uploaded',
        'total_records',
        'database_size',
        'table_count',
        'failed_login_attempts',
        'locked_accounts',
        'password_resets',
        'suspicious_activities',
        'notifications_sent_today',
        'notifications_sent_this_week',
        'notifications_sent_this_month',
        'emails_sent_today',
        'emails_sent_this_week',
        'emails_sent_this_month',
        'email_delivery_rate',
        'system_health_score',
        'system_status',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'user_activity_rate' => 'decimal:2',
        'average_activities_per_user' => 'decimal:2',
        'average_response_time' => 'decimal:2',
        'system_uptime' => 'decimal:2',
        'storage_utilization' => 'decimal:2',
        'email_delivery_rate' => 'decimal:2',
        'system_health_score' => 'decimal:2',
        'total_storage_used' => 'integer',
        'total_storage_available' => 'integer',
        'database_size' => 'integer',
    ];

    public function getSystemStatusAttribute($value)
    {
        if (!$value && $this->system_health_score) {
            return $this->calculateSystemStatus($this->system_health_score);
        }
        return $value;
    }

    protected function calculateSystemStatus($score)
    {
        if ($score >= 90) return 'healthy';
        if ($score >= 70) return 'warning';
        return 'critical';
    }
}
