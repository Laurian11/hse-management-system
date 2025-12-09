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
        Schema::create('five_s_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('audit_date');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('area')->nullable(); // Area/zone being audited
            $table->foreignId('audited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('team_leader_id')->nullable()->constrained('users')->onDelete('set null');
            
            // 5S Scores (0-100 scale)
            $table->decimal('sort_score', 5, 2)->default(0);
            $table->decimal('set_score', 5, 2)->default(0);
            $table->decimal('shine_score', 5, 2)->default(0);
            $table->decimal('standardize_score', 5, 2)->default(0);
            $table->decimal('sustain_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            
            // Overall Rating
            $table->enum('overall_rating', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->nullable();
            
            // Findings (text fields)
            $table->text('sort_findings')->nullable();
            $table->text('set_findings')->nullable();
            $table->text('shine_findings')->nullable();
            $table->text('standardize_findings')->nullable();
            $table->text('sustain_findings')->nullable();
            
            // Summary
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('improvement_actions')->nullable();
            
            // Scheduling
            $table->date('next_audit_date')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            
            // Notes
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('department_id');
            $table->index('audit_date');
            $table->index('status');
            $table->index('overall_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('five_s_audits');
    }
};
