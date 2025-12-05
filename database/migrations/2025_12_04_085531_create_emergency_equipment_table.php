<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->enum('equipment_type', ['fire_extinguisher', 'fire_alarm', 'sprinkler', 'emergency_lighting', 'first_aid_kit', 'eyewash_station', 'safety_shower', 'emergency_exit_sign', 'other'])->default('fire_extinguisher');
            $table->string('name');
            $table->string('model_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('location');
            $table->text('description')->nullable();
            
            // Status
            $table->enum('status', ['operational', 'out_of_service', 'maintenance', 'expired', 'missing'])->default('operational');
            $table->date('installation_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('last_inspection_date')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->integer('inspection_frequency_days')->nullable(); // Days between inspections
            
            // Inspection Details
            $table->foreignId('last_inspected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('inspection_notes')->nullable();
            $table->enum('inspection_result', ['pass', 'fail', 'needs_attention'])->nullable();
            
            // Maintenance
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('maintenance_notes')->nullable();
            
            // Supplier/Service Provider
            $table->string('supplier_name')->nullable();
            $table->string('supplier_contact')->nullable();
            
            // Compliance
            $table->json('compliance_standards')->nullable(); // NFPA, OSHA, etc.
            $table->text('compliance_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'equipment_type']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'next_inspection_date']);
            $table->index(['company_id', 'expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_equipment');
    }
};
