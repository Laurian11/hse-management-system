<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carbon_footprint_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('record_date');
            $table->enum('source_type', ['energy', 'transportation', 'waste', 'water', 'materials', 'other'])->default('energy');
            $table->string('source_name'); // e.g., "Electricity", "Vehicle Fleet", "Waste Disposal"
            $table->decimal('consumption', 10, 2); // Consumption amount
            $table->string('consumption_unit'); // kWh, liters, kg, km, etc.
            $table->decimal('emission_factor', 10, 4); // Emission factor (CO2 per unit)
            $table->decimal('carbon_equivalent', 10, 2); // Calculated CO2 equivalent in kg
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'source_type']);
            $table->index(['company_id', 'record_date']);
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carbon_footprint_records');
    }
};
