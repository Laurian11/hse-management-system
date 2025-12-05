<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_material_gap_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('analysis_date');
            $table->string('material_category')->nullable();
            $table->string('required_material')->nullable();
            $table->text('description')->nullable();
            $table->integer('required_quantity')->nullable();
            $table->integer('available_quantity')->nullable();
            $table->integer('gap_quantity')->nullable(); // Calculated: required - available
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('impact')->nullable();
            $table->text('recommendations')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('analyzed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('procurement_requested')->default(false);
            $table->foreignId('related_procurement_request_id')->nullable()->constrained('procurement_requests')->onDelete('set null');
            $table->enum('status', ['identified', 'procurement_requested', 'procured', 'resolved', 'closed'])->default('identified');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'analysis_date']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_material_gap_analyses');
    }
};
