<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spill_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('incident_date');
            $table->time('incident_time')->nullable();
            $table->string('location');
            $table->enum('spill_type', ['chemical', 'oil', 'fuel', 'hazardous_waste', 'water', 'other']);
            $table->text('substance_description')->nullable();
            $table->decimal('estimated_volume', 10, 2)->nullable();
            $table->string('volume_unit')->nullable(); // liters, gallons, kg, etc.
            $table->enum('severity', ['minor', 'moderate', 'major', 'catastrophic'])->default('minor');
            $table->text('cause')->nullable();
            $table->text('immediate_response')->nullable();
            $table->text('containment_measures')->nullable();
            $table->text('cleanup_procedures')->nullable();
            $table->enum('status', ['reported', 'contained', 'cleaned_up', 'investigating', 'closed'])->default('reported');
            $table->boolean('environmental_impact')->default(false);
            $table->text('environmental_impact_description')->nullable();
            $table->boolean('regulatory_notification_required')->default(false);
            $table->text('regulatory_notification_details')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('investigated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->text('preventive_measures')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'incident_date']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spill_incidents');
    }
};
