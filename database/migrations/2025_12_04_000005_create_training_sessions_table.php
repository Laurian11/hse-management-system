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
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('training_plan_id')->constrained('training_plans')->onDelete('cascade');
            
            // Session Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('session_type', [
                'classroom',
                'e_learning',
                'on_job_training',
                'workshop',
                'simulation',
                'refresher',
                'certification',
                'combination'
            ]);
            
            // Scheduling
            $table->dateTime('scheduled_start');
            $table->dateTime('scheduled_end');
            $table->dateTime('actual_start')->nullable();
            $table->dateTime('actual_end')->nullable();
            $table->integer('duration_minutes')->nullable();
            
            // Location
            $table->string('location_name')->nullable();
            $table->text('location_address')->nullable();
            $table->string('room_number')->nullable();
            $table->string('virtual_meeting_link')->nullable();
            
            // Instructor
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('external_instructor_name')->nullable();
            $table->json('co_instructors')->nullable(); // Array of user IDs
            
            // Capacity
            $table->integer('max_participants')->nullable();
            $table->integer('min_participants')->default(1);
            $table->integer('registered_participants')->default(0);
            
            // Materials
            $table->json('assigned_materials')->nullable(); // Array of material IDs
            
            // Status
            $table->enum('status', [
                'scheduled',
                'in_progress',
                'completed',
                'cancelled',
                'postponed'
            ])->default('scheduled');
            
            $table->text('cancellation_reason')->nullable();
            $table->dateTime('postponed_to')->nullable();
            
            // Completion
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index('training_plan_id');
            $table->index('instructor_id');
            $table->index('scheduled_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
