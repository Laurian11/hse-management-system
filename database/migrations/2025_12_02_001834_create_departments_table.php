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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->string('name', 100);
            $table->string('code', 20)->nullable();
            $table->text('description')->nullable();
            $table->foreignId('head_of_department_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('hse_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('location')->nullable();
            $table->json('risk_profile')->nullable(); // Department-specific risk factors
            $table->json('hse_objectives')->nullable(); // Department HSE goals
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['company_id', 'name']);
            $table->index(['company_id', 'parent_department_id']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
