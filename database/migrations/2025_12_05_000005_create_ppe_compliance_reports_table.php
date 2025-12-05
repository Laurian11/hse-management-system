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
        Schema::create('ppe_compliance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Report Details
            $table->string('report_type'); // usage, expiry, inspection, compliance, etc.
            $table->date('report_period_start');
            $table->date('report_period_end');
            $table->enum('scope', ['company', 'department', 'user', 'item'])->default('company');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('ppe_item_id')->nullable()->constrained('ppe_items')->onDelete('set null');
            
            // Metrics & Statistics
            $table->json('metrics')->nullable(); // Compliance rate, usage stats, etc.
            $table->integer('total_issuances')->default(0);
            $table->integer('active_issuances')->default(0);
            $table->integer('expired_issuances')->default(0);
            $table->integer('overdue_inspections')->default(0);
            $table->integer('non_compliant_count')->default(0);
            $table->decimal('compliance_rate', 5, 2)->default(0); // Percentage
            $table->decimal('usage_rate', 5, 2)->default(0); // Percentage
            
            // Findings
            $table->text('summary')->nullable();
            $table->json('findings')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('action_items')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'generated', 'reviewed', 'archived'])->default('draft');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'report_type']);
            $table->index(['company_id', 'scope']);
            $table->index('report_period_start');
            $table->index('report_period_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_compliance_reports');
    }
};

