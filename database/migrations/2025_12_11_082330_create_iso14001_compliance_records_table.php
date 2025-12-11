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
        // Check if table already exists (may have been created as iso_14001_compliance_records)
        if (Schema::hasTable('iso14001_compliance_records') || Schema::hasTable('iso_14001_compliance_records')) {
            return;
        }
        
        Schema::create('iso14001_compliance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('clause_reference'); // e.g., "4.1", "6.1.2", "8.1"
            $table->string('requirement');
            $table->text('description')->nullable();
            $table->enum('compliance_status', ['compliant', 'non_compliant', 'partially_compliant', 'not_applicable', 'under_review'])->default('under_review');
            $table->text('evidence')->nullable(); // Evidence of compliance
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
            $table->json('attachments')->nullable(); // Array of file paths
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('clause_reference');
            $table->index('compliance_status');
            $table->index('assessment_date');
            $table->index('corrective_action_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iso14001_compliance_records');
    }
};
