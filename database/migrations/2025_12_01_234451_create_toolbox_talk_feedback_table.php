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
        Schema::create('toolbox_talk_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toolbox_talk_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable();
            $table->string('employee_name')->nullable();
            $table->enum('feedback_type', ['quick_rating', 'detailed_survey', 'suggestion', 'complaint'])->default('quick_rating');
            $table->integer('overall_rating')->nullable(); // 1-5 stars
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable();
            $table->text('most_valuable_point')->nullable();
            $table->text('improvement_suggestion')->nullable();
            $table->integer('presenter_effectiveness')->nullable(); // 1-5 rating
            $table->integer('topic_relevance')->nullable(); // 1-5 rating
            $table->integer('content_clarity')->nullable(); // 1-5 rating
            $table->integer('engagement_level')->nullable(); // 1-5 rating
            $table->integer('timing_appropriateness')->nullable(); // 1-5 rating
            $table->integer('material_quality')->nullable(); // 1-5 rating
            $table->text('specific_comments')->nullable();
            $table->json('topic_requests')->nullable(); // Future topic suggestions
            $table->enum('format_preference', ['presentation_only', 'discussion_heavy', 'hands_on', 'video_based'])->nullable();
            $table->text('location_feedback')->nullable();
            $table->text('time_preference')->nullable();
            $table->boolean('would_recommend')->nullable();
            $table->text('additional_topics')->nullable();
            $table->enum('response_method', ['mobile_app', 'paper_form', 'email_survey', 'tablet_kiosk'])->default('mobile_app');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toolbox_talk_feedback');
    }
};
