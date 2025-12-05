<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('5s_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('audit_date');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('area'); // Area/workstation being audited
            $table->foreignId('audited_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('team_leader_id')->nullable()->constrained('users')->onDelete('set null');
            
            // 5S Scores (0-100 each)
            $table->integer('sort_score')->default(0); // Seiri - Sort
            $table->integer('set_score')->default(0); // Seiton - Set in order
            $table->integer('shine_score')->default(0); // Seiso - Shine
            $table->integer('standardize_score')->default(0); // Seiketsu - Standardize
            $table->integer('sustain_score')->default(0); // Shitsuke - Sustain
            
            $table->integer('total_score')->default(0); // Average of all 5S scores
            $table->enum('overall_rating', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->default('satisfactory');
            
            // Detailed assessments
            $table->text('sort_findings')->nullable();
            $table->text('set_findings')->nullable();
            $table->text('shine_findings')->nullable();
            $table->text('standardize_findings')->nullable();
            $table->text('sustain_findings')->nullable();
            
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('improvement_actions')->nullable();
            $table->date('next_audit_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'follow_up_required', 'closed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'audit_date']);
            $table->index(['company_id', 'department_id']);
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('5s_audits');
    }
};
