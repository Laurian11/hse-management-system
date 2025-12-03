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
        Schema::create('control_measures', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('risk_assessment_id')->nullable()->constrained('risk_assessments')->onDelete('cascade');
            $table->foreignId('hazard_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('jsa_id')->nullable()->constrained('jsas')->onDelete('cascade');
            $table->foreignId('incident_id')->nullable()->constrained('incidents')->onDelete('set null'); // Link from incident CAPA
            
            // Control Details
            $table->string('title');
            $table->text('description');
            
            // Hierarchy of Controls
            $table->enum('control_type', [
                'elimination',
                'substitution',
                'engineering',
                'administrative',
                'ppe',
                'combination'
            ])->default('administrative');
            
            $table->enum('effectiveness_level', [
                'low',
                'medium',
                'high',
                'very_high'
            ])->nullable();
            
            // Implementation
            $table->enum('status', [
                'planned',
                'in_progress',
                'implemented',
                'verified',
                'ineffective',
                'closed',
                'cancelled'
            ])->default('planned');
            
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('responsible_party')->nullable()->constrained('users')->onDelete('set null');
            $table->date('target_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->date('verification_date')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Cost and Resources
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->nullable();
            $table->text('resources_required')->nullable();
            
            // Effectiveness
            $table->text('verification_method')->nullable();
            $table->text('verification_results')->nullable();
            $table->boolean('is_effective')->nullable();
            $table->text('effectiveness_notes')->nullable();
            
            // Maintenance
            $table->enum('maintenance_frequency', [
                'daily',
                'weekly',
                'monthly',
                'quarterly',
                'semi_annually',
                'annually',
                'as_needed',
                'none'
            ])->nullable();
            
            $table->text('maintenance_requirements')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            
            // Links to CAPA
            $table->foreignId('related_capa_id')->nullable()->constrained('capas')->onDelete('set null');
            
            // Priority
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'control_type']);
            $table->index('risk_assessment_id');
            $table->index('hazard_id');
            $table->index('assigned_to');
            $table->index('target_completion_date');
            $table->index('related_capa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_measures');
    }
};
