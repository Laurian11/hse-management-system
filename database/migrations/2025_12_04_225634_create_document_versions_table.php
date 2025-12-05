<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hse_document_id')->constrained('hse_documents')->onDelete('cascade');
            $table->string('version_number'); // e.g., "1.0", "2.1"
            $table->text('change_summary')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('review_date')->nullable();
            $table->date('approval_date')->nullable();
            $table->enum('status', ['draft', 'under_review', 'approved', 'rejected'])->default('draft');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['hse_document_id', 'is_current']);
            $table->index('version_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_versions');
    }
};
