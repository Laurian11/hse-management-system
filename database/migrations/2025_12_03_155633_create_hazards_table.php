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
        Schema::create('hazards', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            
            // Hazard Identification
            $table->string('title');
            $table->text('description');
            $table->enum('hazard_category', [
                'physical',
                'chemical',
                'biological',
                'ergonomic',
                'psychosocial',
                'mechanical',
                'electrical',
                'fire',
                'environmental',
                'other'
            ])->default('other');
            
            // Location and Context
            $table->string('location')->nullable();
            $table->string('process_or_activity')->nullable();
            $table->string('asset_or_equipment')->nullable();
            $table->enum('hazard_source', [
                'routine_activity',
                'non_routine_activity',
                'maintenance',
                'change_introduction',
                'emergency_situation',
                'contractor_work',
                'other'
            ])->nullable();
            
            // Identification Method
            $table->enum('identification_method', [
                'hazid_checklist',
                'what_if_analysis',
                'hazop',
                'job_observation',
                'incident_analysis',
                'audit_finding',
                'employee_report',
                'other'
            ])->nullable();
            
            // Who is at Risk
            $table->json('at_risk_personnel')->nullable(); // Array of role/department IDs
            $table->text('exposure_description')->nullable();
            
            // Status
            $table->enum('status', ['identified', 'assessed', 'controlled', 'closed', 'archived'])->default('identified');
            $table->boolean('is_active')->default(true);
            
            // Links to other modules
            $table->foreignId('related_incident_id')->nullable()->constrained('incidents')->onDelete('set null');
            // Note: related_risk_assessment_id will be added after risk_assessments table is created
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'hazard_category']);
            $table->index('department_id');
            $table->index('related_incident_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hazards');
    }
};
