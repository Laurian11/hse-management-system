<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hse_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('document_type', ['policy', 'procedure', 'form', 'template', 'manual', 'guideline', 'standard', 'other'])->default('procedure');
            $table->enum('category', ['safety', 'health', 'environmental', 'quality', 'compliance', 'training', 'emergency', 'other'])->default('safety');
            $table->string('document_code')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('approval_date')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('review_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['draft', 'under_review', 'approved', 'active', 'superseded', 'archived', 'cancelled'])->default('draft');
            $table->enum('access_level', ['public', 'restricted', 'confidential', 'classified'])->default('restricted');
            $table->json('access_permissions')->nullable(); // User IDs or role IDs with access
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable(); // in bytes
            $table->integer('retention_years')->default(7);
            $table->date('archival_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'document_type']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'category']);
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hse_documents');
    }
};
