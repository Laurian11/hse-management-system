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
        Schema::create('training_needs_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Trigger Source Information
            $table->enum('trigger_source', [
                'risk_assessment',
                'incident_rca',
                'new_hire',
                'job_role_change',
                'audit_finding',
                'legal_register',
                'competency_gap',
                'certificate_expiry',
                'manual'
            ]);
            
            $table->foreignId('triggered_by_risk_assessment_id')->nullable()->constrained('risk_assessments')->onDelete('set null');
            $table->foreignId('triggered_by_control_measure_id')->nullable()->constrained('control_measures')->onDelete('set null');
            $table->foreignId('triggered_by_incident_id')->nullable()->constrained('incidents')->onDelete('set null');
            $table->foreignId('triggered_by_rca_id')->nullable()->constrained('root_cause_analyses')->onDelete('set null');
            $table->foreignId('triggered_by_capa_id')->nullable()->constrained('capas')->onDelete('set null');
            $table->foreignId('triggered_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('triggered_by_job_matrix_id')->nullable();
            
            // Training Need Details
            $table->string('training_title');
            $table->text('training_description');
            $table->text('gap_analysis')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('training_type', [
                'classroom',
                'e_learning',
                'on_job_training',
                'workshop',
                'simulation',
                'refresher',
                'certification',
                'combination'
            ])->default('classroom');
            
            // Target Audience
            $table->json('target_departments')->nullable(); // Array of department IDs
            $table->json('target_job_roles')->nullable(); // Array of job role codes
            $table->json('target_user_ids')->nullable(); // Specific user IDs
            $table->integer('estimated_participants')->nullable();
            
            // Regulatory/Compliance
            $table->boolean('is_mandatory')->default(false);
            $table->boolean('is_regulatory')->default(false);
            $table->string('regulatory_reference')->nullable();
            $table->date('regulatory_deadline')->nullable();
            
            // Status and Workflow
            $table->enum('status', [
                'identified',
                'validated',
                'planned',
                'in_progress',
                'completed',
                'cancelled',
                'on_hold'
            ])->default('identified');
            
            $table->foreignId('created_by')->constrained('users')->onDelete('set null');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->text('validation_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'trigger_source']);
            $table->index(['company_id', 'priority']);
            $table->index('triggered_by_risk_assessment_id');
            $table->index('triggered_by_incident_id');
            $table->index('triggered_by_capa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_needs_analyses');
    }
};
