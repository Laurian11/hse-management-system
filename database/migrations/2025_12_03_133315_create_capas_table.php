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
        Schema::create('capas', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->foreignId('root_cause_analysis_id')->nullable()->constrained('root_cause_analyses')->onDelete('set null');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // CAPA Details
            $table->enum('action_type', ['corrective', 'preventive', 'both'])->default('corrective');
            $table->string('title');
            $table->text('description');
            $table->text('root_cause_addressed')->nullable(); // Which root cause this addresses
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            
            // Priority and Resources
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('required_resources')->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->text('budget_approved')->nullable();
            
            // Timeline
            $table->dateTime('due_date')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            
            // Status Workflow
            $table->enum('status', ['pending', 'in_progress', 'under_review', 'verified', 'closed', 'overdue'])->default('pending');
            
            // Implementation Details
            $table->text('implementation_plan')->nullable();
            $table->text('progress_notes')->nullable();
            $table->text('challenges_encountered')->nullable();
            $table->text('effectiveness_measures')->nullable();
            
            // Verification
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('verification_notes')->nullable();
            $table->enum('effectiveness', ['effective', 'partially_effective', 'ineffective', 'not_yet_measured'])->nullable();
            $table->text('effectiveness_evidence')->nullable();
            
            // Closure
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('closure_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['incident_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capas');
    }
};
