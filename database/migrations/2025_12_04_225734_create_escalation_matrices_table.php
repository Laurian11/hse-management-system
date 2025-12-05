<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalation_matrices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('event_type', ['incident', 'non_compliance', 'overdue_action', 'expiring_item', 'audit_finding', 'custom'])->default('custom');
            $table->enum('severity_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->integer('days_overdue')->default(0); // Days overdue before escalation
            $table->json('escalation_levels')->nullable(); // Array of escalation steps
            // Each level: {level: 1, days: 3, notify: [user_ids], roles: [role_names], actions: ['email', 'sms']}
            $table->foreignId('default_assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'event_type']);
            $table->index(['company_id', 'severity_level']);
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_matrices');
    }
};
