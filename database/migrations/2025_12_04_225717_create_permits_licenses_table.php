<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permits_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('permit_license_number')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['permit', 'license', 'certification', 'registration', 'other'])->default('permit');
            $table->enum('category', ['environmental', 'safety', 'health', 'operational', 'legal', 'other'])->default('operational');
            $table->string('issuing_authority'); // e.g., "GCLA", "NEMC", "Local Government"
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->date('renewal_due_date')->nullable(); // Usually 30-60 days before expiry
            $table->enum('status', ['active', 'expired', 'pending_renewal', 'suspended', 'revoked', 'cancelled'])->default('active');
            $table->foreignId('responsible_person_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('file_path')->nullable();
            $table->text('conditions')->nullable();
            $table->text('renewal_requirements')->nullable();
            $table->date('last_renewal_date')->nullable();
            $table->decimal('renewal_fee', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'expiry_date']);
            $table->index('permit_license_number');
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permits_licenses');
    }
};
