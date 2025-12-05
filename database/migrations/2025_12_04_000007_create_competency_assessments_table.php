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
        Schema::create('competency_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('training_session_id')->nullable()->constrained('training_sessions')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assessor_id')->constrained('users')->onDelete('cascade');
            
            // Assessment Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('assessment_type', [
                'knowledge_test',
                'practical_evaluation',
                'observation',
                'simulation',
                'portfolio_review',
                'combination'
            ]);
            
            // Assessment Results
            $table->enum('competency_status', [
                'not_assessed',
                'not_competent',
                'partially_competent',
                'competent',
                'highly_competent'
            ])->default('not_assessed');
            
            $table->integer('score_percentage')->nullable();
            $table->integer('passing_score')->default(70);
            $table->boolean('passed')->default(false);
            
            // Assessment Data
            $table->json('knowledge_test_results')->nullable(); // For quiz/test results
            $table->json('practical_evaluation_results')->nullable(); // For practical assessment
            $table->json('observation_checklist')->nullable(); // For observation-based assessment
            $table->text('assessor_notes')->nullable();
            $table->text('recommendations')->nullable();
            
            // Dates
            $table->dateTime('assessment_date');
            $table->dateTime('completed_at')->nullable();
            
            // Status
            $table->enum('status', [
                'scheduled',
                'in_progress',
                'completed',
                'cancelled'
            ])->default('scheduled');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'competency_status']);
            $table->index(['company_id', 'status']);
            $table->index('user_id');
            $table->index('assessor_id');
            $table->index('training_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competency_assessments');
    }
};
