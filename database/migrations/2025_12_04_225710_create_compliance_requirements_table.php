<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('requirement_title');
            $table->text('description')->nullable();
            $table->enum('regulatory_body', ['gcla', 'osha', 'nemc', 'tbs', 'iso', 'other'])->default('gcla');
            $table->string('regulation_code')->nullable(); // e.g., "OSHA 1910.120"
            $table->enum('requirement_type', ['legal', 'standard', 'certification', 'permit', 'license', 'other'])->default('legal');
            $table->enum('category', ['safety', 'health', 'environmental', 'quality', 'training', 'documentation', 'other'])->default('safety');
            $table->date('effective_date')->nullable();
            $table->date('compliance_due_date')->nullable();
            $table->enum('compliance_status', ['compliant', 'non_compliant', 'partially_compliant', 'under_review', 'not_applicable'])->default('under_review');
            $table->foreignId('responsible_person_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->text('compliance_evidence')->nullable();
            $table->text('non_compliance_issues')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->date('last_review_date')->nullable();
            $table->date('next_review_date')->nullable();
            $table->integer('review_frequency_months')->default(12);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'regulatory_body']);
            $table->index(['company_id', 'compliance_status']);
            $table->index(['company_id', 'compliance_due_date']);
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_requirements');
    }
};
