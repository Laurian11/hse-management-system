<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->foreignId('inspection_schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('inspection_checklist_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('inspection_date');
            $table->foreignId('inspected_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location');
            
            // Inspection Results
            $table->json('checklist_responses'); // Responses to checklist items
            $table->enum('overall_status', ['compliant', 'non_compliant', 'partial', 'pending'])->default('pending');
            $table->integer('total_items')->default(0);
            $table->integer('compliant_items')->default(0);
            $table->integer('non_compliant_items')->default(0);
            $table->integer('na_items')->default(0); // Not applicable
            
            // Findings
            $table->json('findings')->nullable(); // Array of findings/issues
            $table->text('observations')->nullable();
            $table->text('recommendations')->nullable();
            
            // Follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->foreignId('follow_up_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('follow_up_completed')->default(false);
            $table->date('follow_up_completed_date')->nullable();
            
            // Attachments
            $table->json('attachments')->nullable(); // File paths
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'inspection_date']);
            $table->index(['company_id', 'overall_status']);
            $table->index(['company_id', 'requires_follow_up']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
