<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('finding_type', ['non_conformance', 'observation', 'opportunity_for_improvement', 'strength'])->default('non_conformance');
            $table->enum('severity', ['critical', 'major', 'minor', 'observation'])->default('minor');
            $table->text('requirement_clause')->nullable(); // ISO clause or standard requirement
            $table->text('evidence')->nullable();
            $table->text('root_cause')->nullable();
            
            // Corrective Action
            $table->foreignId('corrective_action_id')->nullable()->constrained('capas')->onDelete('set null');
            $table->text('proposed_action')->nullable();
            $table->date('action_due_date')->nullable();
            $table->foreignId('action_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('action_completed')->default(false);
            $table->date('action_completed_date')->nullable();
            
            // Verification
            $table->boolean('verification_required')->default(true);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            
            // Status
            $table->enum('status', ['open', 'in_progress', 'closed', 'cancelled'])->default('open');
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('closed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['audit_id', 'severity']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'finding_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_findings');
    }
};
