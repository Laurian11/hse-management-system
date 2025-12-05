<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iso_14001_compliance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('clause_reference')->nullable(); // ISO 14001 clause number
            $table->string('requirement')->nullable();
            $table->text('description')->nullable();
            $table->enum('compliance_status', ['compliant', 'non_compliant', 'partially_compliant', 'not_applicable'])->default('compliant');
            $table->text('evidence')->nullable();
            $table->date('assessment_date');
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('findings')->nullable();
            $table->text('corrective_action')->nullable();
            $table->date('corrective_action_due_date')->nullable();
            $table->foreignId('corrective_action_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('corrective_action_completed')->default(false);
            $table->date('corrective_action_completed_date')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('verification_date')->nullable();
            $table->text('verification_notes')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'compliance_status']);
            $table->index(['company_id', 'assessment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iso_14001_compliance_records');
    }
};
