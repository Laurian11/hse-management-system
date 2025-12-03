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
        Schema::create('risk_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('risk_assessment_id')->constrained('risk_assessments')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // Review Trigger
            $table->enum('review_type', [
                'scheduled',
                'triggered_by_incident',
                'triggered_by_change',
                'triggered_by_audit',
                'triggered_by_regulation',
                'triggered_by_control_failure',
                'manual',
                'other'
            ])->default('scheduled');
            
            $table->text('trigger_description')->nullable();
            $table->foreignId('triggering_incident_id')->nullable()->constrained('incidents')->onDelete('set null');
            
            // Review Schedule
            $table->date('scheduled_date')->nullable();
            $table->date('review_date')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', [
                'scheduled',
                'in_progress',
                'completed',
                'overdue',
                'cancelled'
            ])->default('scheduled');
            
            // Review Findings
            $table->text('review_findings')->nullable();
            $table->text('changes_identified')->nullable();
            $table->text('control_effectiveness')->nullable();
            
            // Updated Risk Assessment
            $table->enum('updated_severity', ['negligible', 'minor', 'moderate', 'major', 'catastrophic'])->nullable();
            $table->enum('updated_likelihood', ['rare', 'unlikely', 'possible', 'likely', 'almost_certain'])->nullable();
            $table->integer('updated_risk_score')->nullable();
            $table->enum('updated_risk_level', ['low', 'medium', 'high', 'critical', 'extreme'])->nullable();
            
            $table->enum('risk_change', [
                'increased',
                'decreased',
                'unchanged',
                'eliminated'
            ])->nullable();
            
            $table->text('risk_change_reason')->nullable();
            
            // Next Review
            $table->date('next_review_date')->nullable();
            $table->enum('next_review_frequency', [
                'monthly',
                'quarterly',
                'semi_annually',
                'annually',
                'biannually',
                'on_change',
                'on_incident',
                'custom'
            ])->nullable();
            
            // Actions Required
            $table->boolean('requires_new_controls')->default(false);
            $table->boolean('requires_control_modification')->default(false);
            $table->text('recommended_actions')->nullable();
            
            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'review_type']);
            $table->index('risk_assessment_id');
            $table->index('scheduled_date');
            $table->index('due_date');
            $table->index('triggering_incident_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_reviews');
    }
};
