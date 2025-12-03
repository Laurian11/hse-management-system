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
        Schema::create('incident_investigations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('investigator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Investigation Details
            $table->dateTime('investigation_started_at')->nullable();
            $table->dateTime('investigation_completed_at')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'overdue'])->default('pending');
            
            // Investigation Facts
            $table->text('what_happened')->nullable();
            $table->text('when_occurred')->nullable();
            $table->text('where_occurred')->nullable();
            $table->text('who_involved')->nullable();
            $table->text('how_occurred')->nullable();
            $table->text('immediate_causes')->nullable();
            $table->text('contributing_factors')->nullable();
            
            // Witness Information
            $table->json('witnesses')->nullable(); // Array of witness details
            $table->json('witness_statements')->nullable(); // Array of statements
            
            // Conditions at Time
            $table->text('environmental_conditions')->nullable();
            $table->text('equipment_conditions')->nullable();
            $table->text('procedures_followed')->nullable();
            $table->text('training_received')->nullable();
            
            // Investigation Team
            $table->json('investigation_team')->nullable(); // Array of team member IDs
            
            // Findings
            $table->text('key_findings')->nullable();
            $table->text('evidence_collected')->nullable();
            $table->text('interviews_conducted')->nullable();
            
            // Notes
            $table->text('investigator_notes')->nullable();
            $table->text('recommendations')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['incident_id', 'status']);
            $table->index(['company_id', 'status']);
            $table->index('investigator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_investigations');
    }
};
