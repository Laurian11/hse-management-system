<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ergonomic_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('workstation_location')->nullable();
            $table->foreignId('assessed_employee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('job_title')->nullable();
            $table->text('task_description')->nullable();
            $table->date('assessment_date');
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('risk_factors')->nullable(); // posture, repetition, force, duration, etc.
            $table->enum('risk_level', ['low', 'medium', 'high', 'very_high'])->default('low');
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('control_measures')->nullable();
            $table->date('review_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'reviewed'])->default('pending');
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'assessment_date']);
            $table->index(['company_id', 'risk_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ergonomic_assessments');
    }
};
