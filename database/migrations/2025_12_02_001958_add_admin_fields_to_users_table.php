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
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('role_id'); // Custom permissions override
            }
            
            // Employee Information
            if (!Schema::hasColumn('users', 'employee_id_number')) {
                $table->string('employee_id_number')->unique()->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('employee_id_number');
            }
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('profile_photo');
            }
            if (!Schema::hasColumn('users', 'nationality')) {
                $table->string('nationality')->nullable()->after('date_of_birth');
            }
            if (!Schema::hasColumn('users', 'blood_group')) {
                $table->string('blood_group')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('users', 'emergency_contacts')) {
                $table->text('emergency_contacts')->nullable()->after('blood_group');
            }
            
            // Employment Details
            if (!Schema::hasColumn('users', 'date_of_hire')) {
                $table->date('date_of_hire')->nullable()->after('emergency_contacts');
            }
            if (!Schema::hasColumn('users', 'employment_type')) {
                $table->string('employment_type', 20)->default('full_time')->after('date_of_hire'); // full_time, contractor, visitor
            }
            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable()->after('employment_type');
            }
            if (!Schema::hasColumn('users', 'direct_supervisor_id')) {
                $table->foreignId('direct_supervisor_id')->nullable()->constrained('users')->onDelete('set null')->after('job_title');
            }
            
            // HSE Specific Data
            if (!Schema::hasColumn('users', 'hse_training_history')) {
                $table->json('hse_training_history')->nullable()->after('direct_supervisor_id');
            }
            if (!Schema::hasColumn('users', 'competency_certificates')) {
                $table->json('competency_certificates')->nullable()->after('hse_training_history');
            }
            if (!Schema::hasColumn('users', 'known_allergies')) {
                $table->json('known_allergies')->nullable()->after('competency_certificates');
            }
            if (!Schema::hasColumn('users', 'biometric_template_id')) {
                $table->string('biometric_template_id')->nullable()->after('known_allergies');
            }
            
            // Account Management
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('biometric_template_id');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->integer('failed_login_attempts')->default(0)->after('last_login_ip');
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            }
            if (!Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('locked_until');
            }
            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            }
            
            // Status and Soft Delete
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('password_changed_at');
            }
            if (!Schema::hasColumn('users', 'deactivated_at')) {
                $table->timestamp('deactivated_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'deactivation_reason')) {
                $table->text('deactivation_reason')->nullable()->after('deactivated_at');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('deactivation_reason');
            }
        });
        
        // Add indexes separately to avoid issues if columns don't exist
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'employee_id_number') && !$this->indexExists('users', 'users_employee_id_number_index')) {
                $table->index(['employee_id_number']);
            }
            if (Schema::hasColumn('users', 'is_active') && !$this->indexExists('users', 'users_is_active_index')) {
                $table->index(['is_active']);
            }
            if (Schema::hasColumn('users', 'role_id') && !$this->indexExists('users', 'users_role_id_index')) {
                $table->index(['role_id']);
            }
            // Note: company_id and department_id indexes will be added in later migrations
            if (Schema::hasColumn('users', 'last_login_at') && !$this->indexExists('users', 'users_last_login_at_index')) {
                $table->index(['last_login_at']);
            }
        });
    }
    
    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();
        
        if ($driver === 'mysql') {
            $result = $connection->select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return count($result) > 0;
        }
        
        return false;
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
