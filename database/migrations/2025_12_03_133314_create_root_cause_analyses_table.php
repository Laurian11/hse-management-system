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
        Schema::create('root_cause_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->foreignId('investigation_id')->nullable()->constrained('incident_investigations')->onDelete('set null');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Analysis Type
            $table->enum('analysis_type', ['5_whys', 'fishbone', 'taproot', 'fault_tree', 'custom'])->default('5_whys');
            
            // 5 Whys Analysis
            $table->text('why_1')->nullable();
            $table->text('why_2')->nullable();
            $table->text('why_3')->nullable();
            $table->text('why_4')->nullable();
            $table->text('why_5')->nullable();
            $table->text('root_cause')->nullable(); // Final root cause identified
            
            // Fishbone/Ishikawa Categories
            $table->text('human_factors')->nullable();
            $table->text('organizational_factors')->nullable();
            $table->text('technical_factors')->nullable();
            $table->text('environmental_factors')->nullable();
            $table->text('procedural_factors')->nullable();
            $table->text('equipment_factors')->nullable();
            
            // Comprehensive Analysis
            $table->text('direct_cause')->nullable();
            $table->text('contributing_causes')->nullable();
            $table->text('root_causes')->nullable(); // Can be multiple
            $table->text('systemic_failures')->nullable();
            
            // Analysis Details
            $table->json('causal_factors')->nullable(); // Array of causal factors
            $table->json('barriers_failed')->nullable(); // Safety barriers that failed
            $table->text('prevention_possible')->nullable();
            $table->text('lessons_learned')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'in_progress', 'completed', 'reviewed'])->default('draft');
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['incident_id', 'status']);
            $table->index(['company_id', 'analysis_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('root_cause_analyses');
    }
};
