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
        Schema::create('toolbox_talks', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->nullable();
            $table->foreignId('supervisor_id')->nullable();
            $table->foreignId('topic_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->dateTime('scheduled_date');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('location')->nullable();
            $table->enum('talk_type', ['safety', 'health', 'environment', 'incident_review', 'custom'])->default('safety');
            $table->integer('duration_minutes')->default(15);
            $table->json('materials')->nullable(); // Presentation slides, handouts, etc.
            $table->json('photos')->nullable(); // Before/after photos
            $table->json('action_items')->nullable(); // Safety actions assigned
            $table->text('supervisor_notes')->nullable();
            $table->text('key_points')->nullable();
            $table->text('regulatory_references')->nullable();
            $table->boolean('biometric_required')->default(true);
            $table->string('zk_device_id')->nullable(); // ZKTeco K40 device ID
            $table->decimal('latitude', 10, 8)->nullable(); // GPS location
            $table->decimal('longitude', 11, 8)->nullable(); // GPS location
            $table->integer('total_attendees')->default(0);
            $table->integer('present_attendees')->default(0);
            $table->decimal('attendance_rate', 5, 2)->default(0);
            $table->decimal('average_feedback_score', 3, 2)->nullable(); // 1-5 rating
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly
            $table->date('next_occurrence')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toolbox_talks');
    }
};
