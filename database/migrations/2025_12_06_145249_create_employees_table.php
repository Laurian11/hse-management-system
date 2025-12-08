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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Link to user if they have system access
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('direct_supervisor_id')->nullable()->constrained('employees')->onDelete('set null');
            
            // Basic Information
            $table->string('employee_id_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('profile_photo')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('blood_group')->nullable();
            $table->json('emergency_contacts')->nullable();
            
            // Employment Details
            $table->date('date_of_hire')->nullable();
            $table->date('date_of_termination')->nullable();
            $table->string('employment_type', 20)->default('full_time'); // full_time, contractor, visitor, part_time
            $table->string('employment_status', 20)->default('active'); // active, terminated, on_leave, suspended
            $table->string('job_title')->nullable();
            $table->string('job_role_code')->nullable();
            $table->foreignId('job_competency_matrix_id')->nullable()->constrained()->onDelete('set null');
            
            // HSE Specific Data
            $table->json('hse_training_history')->nullable();
            $table->json('competency_certificates')->nullable();
            $table->json('known_allergies')->nullable();
            $table->string('biometric_template_id')->nullable();
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('deactivated_at')->nullable();
            $table->text('deactivation_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('employee_id_number');
            $table->index('email');
            $table->index('employment_status');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
