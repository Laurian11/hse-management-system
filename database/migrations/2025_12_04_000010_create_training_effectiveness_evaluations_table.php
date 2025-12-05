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
        Schema::create('training_effectiveness_evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('training_plan_id')->nullable()->constrained('training_plans')->onDelete('set null');
            $table->foreignId('training_session_id')->nullable()->constrained('training_sessions')->onDelete('set null');
            
            // Evaluation Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('evaluation_level', [
                'level_1', // Reaction (immediate feedback)
                'level_2', // Learning (knowledge/skill assessment)
                'level_3', // Behavior (on-the-job application)
                'level_4'  // Results (business impact)
            ]);
            
            // Level 1: Reaction
            $table->json('reaction_scores')->nullable(); // Satisfaction ratings
            $table->text('reaction_feedback')->nullable();
            
            // Level 2: Learning
            $table->decimal('average_knowledge_score', 5, 2)->nullable();
            $table->decimal('average_skill_score', 5, 2)->nullable();
            $table->integer('pass_rate_percentage')->nullable();
            
            // Level 3: Behavior
            $table->json('behavior_observations')->nullable(); // Supervisor observations
            $table->integer('behavior_improvement_percentage')->nullable();
            $table->date('behavior_evaluation_date')->nullable();
            
            // Level 4: Results
            $table->json('kpi_improvements')->nullable(); // Key performance indicators
            $table->integer('incident_reduction_percentage')->nullable();
            $table->integer('audit_score_improvement')->nullable();
            $table->decimal('cost_savings', 15, 2)->nullable();
            $table->text('business_impact_notes')->nullable();
            
            // Overall Assessment
            $table->enum('overall_effectiveness', [
                'not_effective',
                'somewhat_effective',
                'effective',
                'highly_effective'
            ])->nullable();
            
            $table->text('effectiveness_notes')->nullable();
            $table->text('recommendations')->nullable();
            
            // Evaluation Period
            $table->date('evaluation_start_date');
            $table->date('evaluation_end_date')->nullable();
            $table->date('evaluation_date');
            
            // Evaluator
            $table->foreignId('evaluated_by')->constrained('users')->onDelete('cascade');
            $table->json('evaluation_team')->nullable(); // Array of user IDs involved
            
            // Status
            $table->enum('status', [
                'draft',
                'in_progress',
                'completed',
                'reviewed'
            ])->default('draft');
            
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'evaluation_level']);
            $table->index(['company_id', 'status']);
            $table->index('training_plan_id');
            $table->index('training_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_effectiveness_evaluations');
    }
};
