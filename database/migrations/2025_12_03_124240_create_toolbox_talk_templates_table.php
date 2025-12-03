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
        Schema::create('toolbox_talk_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('title')->nullable();
            $table->text('description_content')->nullable();
            $table->foreignId('topic_id')->nullable();
            $table->enum('talk_type', ['safety', 'health', 'environment', 'incident_review', 'custom'])->default('safety');
            $table->integer('duration_minutes')->default(15);
            $table->text('key_points')->nullable();
            $table->text('regulatory_references')->nullable();
            $table->json('materials')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toolbox_talk_templates');
    }
};
