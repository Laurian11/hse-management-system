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
        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('hazard_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            
            // Assessment Details
            $table->string('title');
            $table->text('description');
            $table->enum('assessment_type', [
                'general',
                'process',
                'task',
                'equipment',
                'chemical',
                'workplace',
                'environmental',
                'other'
            ])->default('general');
            
            // Risk Matrix Scoring
            $table->enum('severity', ['negligible', 'minor', 'moderate', 'major', 'catastrophic'])->nullable();
            $table->enum('likelihood', ['rare', 'unlikely', 'possible', 'likely', 'almost_certain'])->nullable();
            $table->integer('severity_score')->nullable(); // 1-5
            $table->integer('likelihood_score')->nullable(); // 1-5
            $table->integer('risk_score')->nullable(); // Calculated: severity * likelihood
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical', 'extreme'])->nullable();
            
            // Existing Controls
            $table->text('existing_controls')->nullable();
            $table->enum('existing_controls_effectiveness', ['none', 'poor', 'adequate', 'good', 'excellent'])->nullable();
            
            // Residual Risk (after controls)
            $table->enum('residual_severity', ['negligible', 'minor', 'moderate', 'major', 'catastrophic'])->nullable();
            $table->enum('residual_likelihood', ['rare', 'unlikely', 'possible', 'likely', 'almost_certain'])->nullable();
            $table->integer('residual_risk_score')->nullable();
            $table->enum('residual_risk_level', ['low', 'medium', 'high', 'critical', 'extreme'])->nullable();
            
            // ALARP Assessment
            $table->boolean('is_alarp')->default(false); // As Low As Reasonably Practicable
            $table->text('alarp_justification')->nullable();
            
            // Status and Workflow
            $table->enum('status', [
                'draft',
                'under_review',
                'approved',
                'implementation',
                'monitoring',
                'closed',
                'archived'
            ])->default('draft');
            
            $table->date('assessment_date')->nullable();
            $table->date('next_review_date')->nullable();
            $table->enum('review_frequency', [
                'monthly',
                'quarterly',
                'semi_annually',
                'annually',
                'biannually',
                'on_change',
                'on_incident',
                'custom'
            ])->nullable();
            
            // Links to other modules
            $table->foreignId('related_incident_id')->nullable()->constrained('incidents')->onDelete('set null');
            $table->foreignId('related_jsa_id')->nullable()->constrained('jsas')->onDelete('set null');
            
            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'risk_level']);
            $table->index(['company_id', 'residual_risk_level']);
            $table->index('department_id');
            $table->index('next_review_date');
            $table->index('related_incident_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_assessments');
    }
};
