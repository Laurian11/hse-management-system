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
        Schema::create('jsas', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            
            // Job Information
            $table->string('job_title');
            $table->text('job_description');
            $table->string('location')->nullable();
            $table->string('work_area')->nullable();
            $table->date('job_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            // Job Steps and Hazards (JSON structure)
            $table->json('job_steps')->nullable(); // Array of {step_number, description, hazards, controls}
            
            // Team Information
            $table->json('team_members')->nullable(); // Array of user IDs
            $table->text('required_qualifications')->nullable();
            $table->text('required_training')->nullable();
            
            // Equipment and Materials
            $table->json('equipment_required')->nullable();
            $table->json('materials_required')->nullable();
            $table->json('ppe_required')->nullable();
            
            // Environmental Conditions
            $table->text('weather_conditions')->nullable();
            $table->text('site_conditions')->nullable();
            $table->text('special_considerations')->nullable();
            
            // Emergency Procedures
            $table->text('emergency_contacts')->nullable();
            $table->text('emergency_procedures')->nullable();
            $table->text('first_aid_location')->nullable();
            $table->text('evacuation_route')->nullable();
            
            // Risk Assessment Summary
            $table->enum('overall_risk_level', ['low', 'medium', 'high', 'critical'])->nullable();
            $table->text('risk_summary')->nullable();
            
            // Approval and Sign-off
            $table->enum('status', [
                'draft',
                'pending_approval',
                'approved',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('draft');
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            
            // Worker Sign-offs (JSON)
            $table->json('worker_sign_offs')->nullable(); // Array of {user_id, signed_at, signature}
            
            // Links
            $table->foreignId('related_risk_assessment_id')->nullable()->constrained('risk_assessments')->onDelete('set null');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'overall_risk_level']);
            $table->index('department_id');
            $table->index('job_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jsas');
    }
};
