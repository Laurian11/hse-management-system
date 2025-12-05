<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('housekeeping_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('inspection_date');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location');
            $table->foreignId('inspected_by')->constrained('users')->onDelete('cascade');
            $table->enum('overall_rating', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->default('satisfactory');
            $table->integer('score')->nullable(); // Score out of 100
            $table->json('checklist_items')->nullable(); // Array of checklist items with status
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'follow_up_required', 'closed'])->default('scheduled');
            $table->date('follow_up_date')->nullable();
            $table->foreignId('follow_up_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('corrective_actions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'inspection_date']);
            $table->index(['company_id', 'status']);
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('housekeeping_inspections');
    }
};
