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
        Schema::create('training_plans', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('training_need_id')->constrained('training_needs_analyses')->onDelete('cascade');
            
            // Plan Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('training_type', [
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
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->integer('duration_days')->nullable();
            $table->enum('delivery_method', [
                'internal',
                'external',
                'mixed'
            ])->default('internal');
            
            // Resources
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('external_instructor_name')->nullable();
            $table->string('external_instructor_qualifications')->nullable();
            $table->foreignId('training_location_id')->nullable(); // Can link to a locations table if exists
            $table->string('location_name')->nullable();
            $table->text('location_address')->nullable();
            
            // Materials
            $table->json('training_materials')->nullable(); // Array of material IDs
            $table->text('additional_resources')->nullable();
            
            // Budget
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->nullable();
            $table->boolean('budget_approved')->default(false);
            $table->foreignId('budget_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('budget_approved_at')->nullable();
            
            // Status
            $table->enum('status', [
                'draft',
                'approved',
                'scheduled',
                'in_progress',
                'completed',
                'cancelled',
                'on_hold'
            ])->default('draft');
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index('training_need_id');
            $table->index('instructor_id');
            $table->index('planned_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_plans');
    }
};
