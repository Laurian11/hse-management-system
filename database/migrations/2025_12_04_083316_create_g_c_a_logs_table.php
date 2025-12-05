<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gca_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->foreignId('work_permit_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // GCLA Details
            $table->enum('gcla_type', ['pre_work', 'during_work', 'post_work', 'continuous'])->default('pre_work');
            $table->dateTime('check_date');
            $table->string('location');
            $table->text('description');
            
            // Checklist Items
            $table->json('checklist_items')->nullable(); // Array of checked items
            $table->json('findings')->nullable(); // Any findings or issues
            $table->enum('compliance_status', ['compliant', 'non_compliant', 'partial'])->default('compliant');
            
            // Actions
            $table->text('corrective_actions')->nullable();
            $table->foreignId('action_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('action_due_date')->nullable();
            $table->boolean('action_completed')->default(false);
            $table->dateTime('action_completed_at')->nullable();
            
            // Verification
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'work_permit_id']);
            $table->index(['company_id', 'check_date']);
            $table->index(['company_id', 'compliance_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gca_logs');
    }
};
