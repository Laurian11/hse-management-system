<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('non_conformance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->foreignId('inspection_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['critical', 'major', 'minor', 'observation'])->default('minor');
            $table->enum('status', ['open', 'investigating', 'corrective_action', 'closed', 'cancelled'])->default('open');
            $table->date('identified_date');
            $table->foreignId('identified_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location');
            
            // Root Cause
            $table->text('root_cause')->nullable();
            $table->text('immediate_action')->nullable();
            
            // Corrective Action
            $table->foreignId('corrective_action_id')->nullable()->constrained('capas')->onDelete('set null');
            $table->text('corrective_action_plan')->nullable();
            $table->date('corrective_action_due_date')->nullable();
            $table->foreignId('corrective_action_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('corrective_action_completed')->default(false);
            $table->date('corrective_action_completed_date')->nullable();
            
            // Verification
            $table->boolean('verification_required')->default(true);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            // Closure
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('closed_at')->nullable();
            $table->text('closure_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'severity']);
            $table->index(['company_id', 'identified_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('non_conformance_reports');
    }
};
