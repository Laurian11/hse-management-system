<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_sustainability_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->enum('record_type', ['recycling', 'waste_segregation', 'sustainability_initiative', 'energy_tracking', 'other'])->default('recycling');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('record_date');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->string('unit')->nullable(); // kg, liters, tons, etc.
            $table->enum('waste_category', ['hazardous', 'non_hazardous', 'recyclable', 'organic', 'electronic', 'other'])->nullable();
            $table->enum('disposal_method', ['recycling', 'landfill', 'incineration', 'composting', 'reuse', 'treatment', 'other'])->nullable();
            $table->decimal('carbon_equivalent', 10, 2)->nullable(); // CO2 equivalent in kg
            $table->decimal('energy_saved', 10, 2)->nullable(); // kWh saved
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'record_type']);
            $table->index(['company_id', 'record_date']);
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_sustainability_records');
    }
};
