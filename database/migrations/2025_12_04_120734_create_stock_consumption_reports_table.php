<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_consumption_reports')) {
            return;
        }
        
        Schema::create('stock_consumption_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('item_name');
            $table->string('item_category')->nullable();
            $table->string('item_code')->nullable();
            $table->decimal('opening_stock', 10, 2)->nullable();
            $table->decimal('received_quantity', 10, 2)->default(0);
            $table->decimal('consumed_quantity', 10, 2)->default(0);
            $table->decimal('closing_stock', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->date('report_period_start');
            $table->date('report_period_end');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->text('consumption_details')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'report_period_start', 'report_period_end']);
            $table->index(['company_id', 'item_category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_consumption_reports');
    }
};
