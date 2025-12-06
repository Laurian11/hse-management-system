<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->enum('resource_type', ['water', 'fuel', 'electricity', 'gas', 'steam', 'other']);
            $table->date('reading_date');
            $table->string('meter_location')->nullable();
            $table->string('meter_number')->nullable();
            $table->decimal('previous_reading', 12, 2)->nullable();
            $table->decimal('current_reading', 12, 2);
            $table->decimal('consumption', 12, 2)->nullable(); // Calculated: current - previous
            $table->string('unit')->nullable(); // kWh, liters, cubic_meters, etc.
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('currency')->default('TZS');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'resource_type', 'reading_date']);
            $table->index(['company_id', 'reading_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_usage_records');
    }
};
