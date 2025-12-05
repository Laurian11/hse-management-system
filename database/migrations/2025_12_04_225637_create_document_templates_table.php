<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('template_type', ['form', 'report', 'checklist', 'procedure', 'policy', 'other'])->default('form');
            $table->enum('category', ['safety', 'health', 'environmental', 'quality', 'compliance', 'training', 'emergency', 'other'])->default('safety');
            $table->text('template_content')->nullable(); // HTML or structured content
            $table->json('fields')->nullable(); // Form fields definition
            $table->string('file_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'template_type']);
            $table->index(['company_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
