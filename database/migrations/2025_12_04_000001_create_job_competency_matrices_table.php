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
        Schema::create('job_competency_matrices', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('job_title');
            $table->string('job_role_code')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->json('required_competencies'); // Array of competency requirements
            $table->json('mandatory_trainings'); // Array of mandatory training IDs
            $table->json('optional_trainings')->nullable(); // Array of optional training IDs
            $table->json('certification_requirements')->nullable(); // Array of required certifications
            $table->integer('experience_requirement_months')->nullable();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'job_title']);
            $table->index(['company_id', 'status']);
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_competency_matrices');
    }
};
