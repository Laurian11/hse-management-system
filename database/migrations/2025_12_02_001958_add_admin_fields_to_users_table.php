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
        Schema::table('users', function (Blueprint $table) {
            // Role and Permission Management
            $table->foreignId('role_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->json('permissions')->nullable()->after('role_id'); // Custom permissions override
            
            // Employee Information
            $table->string('employee_id_number')->unique()->nullable()->after('email');
            $table->string('phone')->nullable()->after('employee_id_number');
            $table->string('profile_photo')->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('profile_photo');
            $table->string('nationality')->nullable()->after('date_of_birth');
            $table->string('blood_group')->nullable()->after('nationality');
            $table->text('emergency_contacts')->nullable()->after('blood_group');
            
            // Employment Details
            $table->date('date_of_hire')->nullable()->after('emergency_contacts');
            $table->string('employment_type', 20)->default('full_time')->after('date_of_hire'); // full_time, contractor, visitor
            $table->string('job_title')->nullable()->after('employment_type');
            $table->foreignId('direct_supervisor_id')->nullable()->constrained('users')->onDelete('set null')->after('job_title');
            
            // HSE Specific Data
            $table->json('hse_training_history')->nullable()->after('direct_supervisor_id');
            $table->json('competency_certificates')->nullable()->after('hse_training_history');
            $table->json('known_allergies')->nullable()->after('competency_certificates');
            $table->string('biometric_template_id')->nullable()->after('known_allergies');
            
            // Account Management
            $table->timestamp('last_login_at')->nullable()->after('biometric_template_id');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->integer('failed_login_attempts')->default(0)->after('last_login_ip');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->boolean('must_change_password')->default(false)->after('locked_until');
            $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            
            // Status and Soft Delete
            $table->boolean('is_active')->default(true)->after('password_changed_at');
            $table->timestamp('deactivated_at')->nullable()->after('is_active');
            $table->text('deactivation_reason')->nullable()->after('deactivated_at');
            $table->softDeletes()->after('deactivation_reason');
            
            // Indexes
            $table->index(['employee_id_number']);
            $table->index(['is_active']);
            $table->index(['role_id']);
            $table->index(['company_id', 'department_id']);
            $table->index(['last_login_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'role_id',
                'permissions',
                'employee_id_number',
                'phone',
                'profile_photo',
                'date_of_birth',
                'nationality',
                'blood_group',
                'emergency_contacts',
                'date_of_hire',
                'employment_type',
                'job_title',
                'direct_supervisor_id',
                'hse_training_history',
                'competency_certificates',
                'known_allergies',
                'biometric_template_id',
                'last_login_at',
                'last_login_ip',
                'failed_login_attempts',
                'locked_until',
                'must_change_password',
                'password_changed_at',
                'is_active',
                'deactivated_at',
                'deactivation_reason'
            ]);
        });
    }
};
