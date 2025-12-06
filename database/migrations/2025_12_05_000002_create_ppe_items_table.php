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
        Schema::create('ppe_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('name');
            $table->string('category'); // Helmet, Safety Glasses, Gloves, Boots, etc.
            $table->string('type')->nullable(); // Sub-category
            $table->text('description')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model_number')->nullable();
            $table->string('sku')->nullable();
            
            // Inventory Management
            $table->integer('total_quantity')->default(0);
            $table->integer('available_quantity')->default(0);
            $table->integer('issued_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('minimum_stock_level')->default(0);
            $table->integer('reorder_quantity')->default(0);
            
            // Specifications
            $table->json('specifications')->nullable(); // Size, color, material, etc.
            $table->json('standards_compliance')->nullable(); // ANSI, CE, ISO standards
            $table->string('unit_of_measure')->default('piece'); // piece, pair, set, etc.
            
            // Expiry & Replacement
            $table->boolean('has_expiry')->default(false);
            $table->integer('expiry_days')->nullable(); // Days from issuance
            $table->integer('replacement_alert_days')->default(30); // Alert before expiry
            $table->boolean('requires_inspection')->default(true);
            $table->integer('inspection_frequency_days')->default(90); // Inspection frequency
            
            // Supplier & Procurement
            $table->foreignId('supplier_id')->nullable()->constrained('ppe_suppliers')->onDelete('set null');
            $table->decimal('unit_cost', 15, 2)->nullable();
            $table->string('currency', 3)->default('TZS');
            $table->date('last_purchase_date')->nullable();
            $table->integer('last_purchase_quantity')->nullable();
            
            // Location & Storage
            $table->string('storage_location')->nullable();
            $table->string('warehouse')->nullable();
            $table->text('storage_conditions')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'category']);
            $table->index(['company_id', 'status']);
            $table->index('supplier_id');
            $table->index('has_expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ppe_items');
    }
};

