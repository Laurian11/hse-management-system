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
        Schema::create('toolbox_talk_topics', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['safety', 'health', 'environment', 'incident_review', 'emergency', 'equipment', 'procedural', 'custom'])->default('safety');
            $table->enum('subcategory', ['equipment_safety', 'hazard_recognition', 'emergency_procedures', 'ergonomics', 'waste_management', 'incident_learnings', 'ppe', 'lockout_tagout', 'chemical_safety', 'electrical_safety', 'fall_protection', 'fire_safety', 'first_aid', 'wellness', 'mental_health', 'other'])->nullable();
            $table->enum('difficulty_level', ['basic', 'intermediate', 'advanced'])->default('basic');
            $table->integer('estimated_duration_minutes')->default(15);
            $table->json('presentation_content')->nullable(); // Slides, talking points
            $table->json('discussion_questions')->nullable(); // Guided questions for engagement
            $table->json('quiz_questions')->nullable(); // Quick knowledge check questions
            $table->json('required_materials')->nullable(); // PPE samples, equipment, handouts
            $table->json('learning_objectives')->nullable(); // What participants should learn
            $table->text('key_talking_points')->nullable();
            $table->text('real_world_examples')->nullable();
            $table->json('related_incidents')->nullable(); // Links to relevant past incidents
            $table->text('regulatory_references')->nullable(); // OSHA, local regulations
            $table->json('department_relevance')->nullable(); // Which departments this applies to
            $table->enum('seasonal_relevance', ['all_year', 'summer', 'winter', 'monsoon', 'extreme_heat', 'extreme_cold'])->default('all_year');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_mandatory')->default(false);
            $table->integer('usage_count')->default(0); // Track how many times used
            $table->decimal('average_feedback_score', 3, 2)->nullable(); // 1-5 rating
            $table->integer('effectiveness_rating')->nullable(); // 1-5 effectiveness
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toolbox_talk_topics');
    }
};
