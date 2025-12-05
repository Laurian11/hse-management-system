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
        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('training_session_id')->nullable()->constrained('training_sessions')->onDelete('set null');
            $table->foreignId('training_plan_id')->nullable()->constrained('training_plans')->onDelete('set null');
            $table->foreignId('training_need_id')->nullable()->constrained('training_needs_analyses')->onDelete('set null');
            
            // Training Details
            $table->string('training_title');
            $table->text('training_description')->nullable();
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
            
            // Dates
            $table->date('training_date');
            $table->dateTime('completed_at')->nullable();
            $table->integer('duration_hours')->nullable();
            
            // Attendance
            $table->foreignId('attendance_id')->nullable()->constrained('training_attendances')->onDelete('set null');
            $table->enum('attendance_status', [
                'attended',
                'absent',
                'partially_attended',
                'excused'
            ])->nullable();
            
            // Competency
            $table->foreignId('competency_assessment_id')->nullable()->constrained('competency_assessments')->onDelete('set null');
            $table->enum('competency_status', [
                'not_assessed',
                'not_competent',
                'partially_competent',
                'competent',
                'highly_competent'
            ])->nullable();
            
            // Certification (will be added after certificates table is created)
            $table->unsignedBigInteger('certificate_id')->nullable();
            $table->boolean('certificate_issued')->default(false);
            $table->date('certificate_issue_date')->nullable();
            $table->date('certificate_expiry_date')->nullable();
            
            // Status
            $table->enum('status', [
                'in_progress',
                'completed',
                'failed',
                'incomplete'
            ])->default('in_progress');
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('feedback')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'user_id']);
            $table->index(['company_id', 'status']);
            $table->index('training_session_id');
            $table->index('certificate_expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_records');
    }
};
