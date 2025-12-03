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
        Schema::create('incident_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            
            // File Information
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // mime type
            $table->string('file_extension')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            
            // Categorization
            $table->enum('category', [
                'photo',
                'video',
                'document',
                'witness_statement',
                'interview_recording',
                'policy_manual',
                'training_record',
                'equipment_manual',
                'other'
            ])->default('other');
            
            $table->text('description')->nullable();
            $table->text('tags')->nullable(); // Comma-separated tags
            
            // Metadata
            $table->text('metadata')->nullable(); // JSON for additional metadata
            $table->boolean('is_evidence')->default(false);
            $table->boolean('is_confidential')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['incident_id', 'category']);
            $table->index(['company_id', 'category']);
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_attachments');
    }
};
