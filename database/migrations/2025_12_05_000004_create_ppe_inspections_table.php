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
        Schema::create('ppe_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->foreignId('ppe_issuance_id')->constrained('ppe_issuances')->onDelete('cascade');
            $table->foreignId('ppe_item_id')->constrained('ppe_items')->onDelete('cascade');
            $table->foreignId('inspected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // PPE owner
            
            // Inspection Details
            $table->date('inspection_date');
            $table->enum('inspection_type', ['scheduled', 'pre_use', 'post_use', 'damage_report', 'random'])->default('scheduled');
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor', 'unsafe', 'damaged'])->default('good');
            
            // Inspection Checklist (JSON)
            $table->json('inspection_checklist')->nullable(); // Visual inspection, functionality, etc.
            $table->text('findings')->nullable();
            $table->text('defects')->nullable();
            $table->json('defect_photos')->nullable(); // Array of photo paths
            
            // Action Taken
            $table->enum('action_taken', ['approved', 'repair', 'replace', 'dispose', 'quarantine'])->default('approved');
            $table->text('action_notes')->nullable();
            $table->date('next_inspection_date')->nullable();
            
            // Compliance
            $table->boolean('is_compliant')->default(true);
            $table->text('non_compliance_reason')->nullable();
            $table->json('compliance_issues')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'ppe_item_id']);
            $table->index(['company_id', 'user_id']);
            $table->index('inspection_date');
            $table->index('next_inspection_date');
            $table->index('is_compliant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_inspections');
    }
};

