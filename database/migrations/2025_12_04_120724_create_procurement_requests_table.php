<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('procurement_requests')) {
            return;
        }
        
        Schema::create('procurement_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->enum('item_category', ['safety_equipment', 'ppe', 'tools', 'materials', 'services', 'other'])->default('safety_equipment');
            $table->integer('quantity');
            $table->string('unit')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->string('currency')->default('TZS');
            $table->text('justification')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->date('required_date')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'purchased', 'received', 'cancelled'])->default('draft');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('review_date')->nullable();
            $table->text('review_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('approval_date')->nullable();
            $table->text('approval_notes')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->decimal('purchase_cost', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('received_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'requested_by']);
            $table->index(['company_id', 'required_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_requests');
    }
};
