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
        Schema::create('training_materials', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Material Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('material_type', [
                'presentation',
                'manual',
                'video',
                'handout',
                'checklist',
                'assessment',
                'simulation',
                'e_learning_module',
                'other'
            ]);
            
            // Content
            $table->string('file_path')->nullable(); // For uploaded files
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable(); // in bytes
            $table->string('external_url')->nullable(); // For external resources
            $table->text('content')->nullable(); // For text-based materials
            
            // Metadata
            $table->string('version')->default('1.0');
            $table->foreignId('created_by')->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('tags')->nullable(); // For categorization
            $table->json('applicable_training_types')->nullable(); // Which training types can use this
            
            // Status
            $table->enum('status', ['draft', 'active', 'archived', 'under_review'])->default('draft');
            $table->boolean('is_standard')->default(false); // Standard material vs custom
            $table->boolean('is_public')->default(true); // Available to all vs restricted
            
            // Review
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->date('next_review_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'material_type']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_materials');
    }
};
