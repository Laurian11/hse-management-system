<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emission_monitoring_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->enum('monitoring_type', ['air_emission', 'water_effluent', 'noise', 'vibration', 'odor', 'other']);
            $table->string('source')->nullable(); // stack, discharge_point, equipment, etc.
            $table->string('location')->nullable();
            $table->date('monitoring_date');
            $table->time('monitoring_time')->nullable();
            $table->string('parameter')->nullable(); // CO2, SO2, NOx, pH, BOD, etc.
            $table->decimal('measured_value', 10, 4)->nullable();
            $table->string('unit')->nullable(); // ppm, mg/L, dB, etc.
            $table->decimal('permissible_limit', 10, 4)->nullable();
            $table->enum('compliance_status', ['compliant', 'non_compliant', 'marginal'])->default('compliant');
            $table->text('weather_conditions')->nullable();
            $table->text('operating_conditions')->nullable();
            $table->foreignId('monitored_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('verification_date')->nullable();
            $table->text('corrective_action')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'monitoring_date']);
            $table->index(['company_id', 'compliance_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emission_monitoring_records');
    }
};
