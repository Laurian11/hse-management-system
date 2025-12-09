<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biometric_device_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Employee Information
            $table->string('employee_id_number');
            $table->string('employee_name');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            
            // Attendance Details
            $table->date('attendance_date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->enum('attendance_status', ['present', 'absent', 'late', 'half_day', 'on_leave', 'sick_leave'])->default('present');
            $table->enum('check_in_method', ['biometric', 'manual', 'mobile', 'api'])->default('biometric');
            $table->enum('check_out_method', ['biometric', 'manual', 'mobile', 'api'])->nullable();
            
            // Biometric Data
            $table->string('biometric_template_id')->nullable();
            $table->string('device_log_id')->nullable(); // ID from device logs
            
            // Location Data
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            
            // Work Hours Calculation
            $table->integer('total_work_minutes')->nullable(); // Calculated work hours in minutes
            $table->integer('overtime_minutes')->default(0);
            $table->integer('late_minutes')->default(0);
            $table->integer('early_departure_minutes')->default(0);
            
            // Status Flags
            $table->boolean('is_late')->default(false);
            $table->boolean('is_early_departure')->default(false);
            $table->boolean('is_overtime')->default(false);
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_manual_entry')->default(false);
            
            // Additional Information
            $table->text('check_in_notes')->nullable();
            $table->text('check_out_notes')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('biometric_device_id');
            $table->index('company_id');
            $table->index('employee_id');
            $table->index('user_id');
            $table->index('attendance_date');
            $table->index('employee_id_number');
            $table->index(['company_id', 'attendance_date']);
            $table->index(['employee_id', 'attendance_date']);
            $table->unique(['biometric_device_id', 'employee_id_number', 'attendance_date', 'check_in_time'], 'unique_daily_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_attendance');
    }
};
