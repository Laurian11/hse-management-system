<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_kpis', function (Blueprint $table) {
            $table->id();
            $table->date('recorded_date');
            $table->string('period_type', 20)->default('daily'); // daily, weekly, monthly, yearly
            
            // User Metrics
            $table->integer('total_users')->default(0);
            $table->integer('active_users')->default(0);
            $table->integer('new_users')->default(0);
            $table->integer('inactive_users')->default(0);
            $table->integer('users_logged_in_today')->default(0);
            $table->integer('users_logged_in_this_week')->default(0);
            $table->integer('users_logged_in_this_month')->default(0);
            $table->decimal('user_activity_rate', 5, 2)->default(0);
            
            // Company Metrics
            $table->integer('total_companies')->default(0);
            $table->integer('active_companies')->default(0);
            $table->integer('new_companies')->default(0);
            $table->integer('inactive_companies')->default(0);
            
            // System Usage Metrics
            $table->integer('total_incidents')->default(0);
            $table->integer('total_risk_assessments')->default(0);
            $table->integer('total_trainings')->default(0);
            $table->integer('total_audits')->default(0);
            $table->integer('total_toolbox_talks')->default(0);
            $table->integer('total_documents')->default(0);
            
            // Activity Metrics
            $table->integer('total_activities_today')->default(0);
            $table->integer('total_activities_this_week')->default(0);
            $table->integer('total_activities_this_month')->default(0);
            $table->integer('total_activities_this_year')->default(0);
            $table->decimal('average_activities_per_user', 8, 2)->default(0);
            
            // Performance Metrics
            $table->decimal('average_response_time', 8, 2)->nullable(); // milliseconds
            $table->decimal('system_uptime', 5, 2)->default(100); // percentage
            $table->integer('error_count')->default(0);
            $table->integer('api_requests_today')->default(0);
            $table->integer('api_requests_this_week')->default(0);
            $table->integer('api_requests_this_month')->default(0);
            
            // Storage Metrics
            $table->bigInteger('total_storage_used')->default(0); // bytes
            $table->bigInteger('total_storage_available')->default(0); // bytes
            $table->decimal('storage_utilization', 5, 2)->default(0); // percentage
            $table->integer('total_files')->default(0);
            $table->integer('total_documents_uploaded')->default(0);
            
            // Database Metrics
            $table->integer('total_records')->default(0);
            $table->bigInteger('database_size')->default(0); // bytes
            $table->integer('table_count')->default(0);
            
            // Security Metrics
            $table->integer('failed_login_attempts')->default(0);
            $table->integer('locked_accounts')->default(0);
            $table->integer('password_resets')->default(0);
            $table->integer('suspicious_activities')->default(0);
            
            // Notification Metrics
            $table->integer('notifications_sent_today')->default(0);
            $table->integer('notifications_sent_this_week')->default(0);
            $table->integer('notifications_sent_this_month')->default(0);
            $table->integer('emails_sent_today')->default(0);
            $table->integer('emails_sent_this_week')->default(0);
            $table->integer('emails_sent_this_month')->default(0);
            $table->decimal('email_delivery_rate', 5, 2)->default(100);
            
            // Overall System Health
            $table->decimal('system_health_score', 5, 2)->default(100); // 0-100
            $table->string('system_status', 20)->default('healthy'); // healthy, warning, critical
            
            $table->timestamps();
            
            $table->unique(['recorded_date', 'period_type']);
            $table->index(['recorded_date', 'period_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_kpis');
    }
};
