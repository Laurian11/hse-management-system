<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->enum('audit_type', ['internal', 'external', 'certification', 'regulatory', 'supplier'])->default('internal');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('scope', ['full', 'partial', 'focused'])->default('full');
            $table->text('scope_description')->nullable();
            
            // Schedule
            $table->date('planned_start_date');
            $table->date('planned_end_date');
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            
            // Team
            $table->foreignId('lead_auditor_id')->constrained('users')->onDelete('cascade');
            $table->json('audit_team')->nullable(); // Array of user IDs
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            
            // Standards/Requirements
            $table->json('applicable_standards')->nullable(); // ISO 45001, ISO 14001, etc.
            $table->json('audit_criteria')->nullable();
            
            // Status
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled', 'postponed'])->default('planned');
            $table->enum('result', ['compliant', 'non_compliant', 'partial', 'pending'])->nullable();
            
            // Findings Summary
            $table->integer('total_findings')->default(0);
            $table->integer('critical_findings')->default(0);
            $table->integer('major_findings')->default(0);
            $table->integer('minor_findings')->default(0);
            $table->integer('observations')->default(0);
            
            // Report
            $table->text('executive_summary')->nullable();
            $table->text('conclusion')->nullable();
            $table->json('attachments')->nullable();
            
            // Follow-up
            $table->date('follow_up_date')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->boolean('follow_up_completed')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'audit_type']);
            $table->index(['company_id', 'planned_start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
