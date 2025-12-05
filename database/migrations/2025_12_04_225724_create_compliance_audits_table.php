<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('audit_title');
            $table->text('description')->nullable();
            $table->enum('audit_type', ['internal', 'external', 'certification', 'regulatory', 'surveillance', 'other'])->default('internal');
            $table->enum('standard', ['iso_45001', 'iso_14001', 'iso_9001', 'osha', 'gcla', 'nemc', 'custom', 'other'])->nullable();
            $table->date('audit_date');
            $table->date('audit_end_date')->nullable();
            $table->foreignId('auditor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('external_auditor_name')->nullable();
            $table->string('auditor_organization')->nullable();
            $table->json('audit_scope')->nullable(); // Areas/departments audited
            $table->enum('audit_status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->integer('total_findings')->default(0);
            $table->integer('major_non_conformances')->default(0);
            $table->integer('minor_non_conformances')->default(0);
            $table->integer('observations')->default(0);
            $table->integer('positive_findings')->default(0);
            $table->enum('overall_result', ['compliant', 'non_compliant', 'conditionally_compliant', 'pending'])->default('pending');
            $table->text('summary')->nullable();
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('corrective_action_due_date')->nullable();
            $table->date('follow_up_audit_date')->nullable();
            $table->text('audit_report_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'audit_type']);
            $table->index(['company_id', 'audit_date']);
            $table->index(['company_id', 'audit_status']);
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_audits');
    }
};
