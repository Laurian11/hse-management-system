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
        Schema::create('ppe_issuances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->foreignId('ppe_item_id')->constrained('ppe_items')->onDelete('cascade');
            $table->foreignId('issued_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            
            // Issuance Details
            $table->enum('transaction_type', ['issuance', 'return', 'replacement', 'exchange'])->default('issuance');
            $table->integer('quantity')->default(1);
            $table->date('issue_date');
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->enum('return_condition', ['good', 'fair', 'poor', 'damaged', 'lost'])->nullable();
            $table->text('return_notes')->nullable();
            
            // Expiry & Replacement
            $table->date('expiry_date')->nullable();
            $table->date('replacement_due_date')->nullable();
            $table->boolean('replacement_alert_sent')->default(false);
            $table->date('replacement_alert_sent_at')->nullable();
            
            // Condition & Inspection
            $table->enum('initial_condition', ['new', 'good', 'fair', 'poor'])->default('new');
            $table->text('condition_notes')->nullable();
            $table->boolean('requires_inspection')->default(true);
            $table->date('next_inspection_date')->nullable();
            $table->date('last_inspection_date')->nullable();
            
            // Status
            $table->enum('status', ['active', 'returned', 'expired', 'replaced', 'lost', 'damaged'])->default('active');
            $table->text('notes')->nullable();
            $table->text('reason')->nullable(); // Reason for issuance
            
            // Serial Numbers / Batch Numbers
            $table->json('serial_numbers')->nullable();
            $table->string('batch_number')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'ppe_item_id']);
            $table->index(['company_id', 'issued_to']);
            $table->index(['company_id', 'status']);
            $table->index('expiry_date');
            $table->index('replacement_due_date');
            $table->index('next_inspection_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_issuances');
    }
};

