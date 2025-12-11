<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('equipment_name');
            $table->string('equipment_type')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('certification_type')->nullable(); // calibration, inspection, test, etc.
            $table->string('certificate_number')->nullable();
            $table->date('certification_date');
            $table->date('expiry_date')->nullable();
            $table->date('next_due_date')->nullable();
            // Note: Foreign key will be added after suppliers table is created
            $table->unsignedBigInteger('certified_by')->nullable();
            $table->string('certifier_name')->nullable();
            $table->text('certification_details')->nullable();
            $table->enum('status', ['valid', 'expired', 'pending', 'rejected'])->default('valid');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'expiry_date']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'next_due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_certifications');
    }
};
