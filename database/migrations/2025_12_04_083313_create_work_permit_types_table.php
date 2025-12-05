<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_permit_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Hot Work, Confined Space, Electrical, Excavation, etc.
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->json('required_precautions')->nullable(); // List of required safety measures
            $table->json('required_equipment')->nullable(); // Required safety equipment
            $table->integer('default_validity_hours')->default(24); // Default permit validity
            $table->integer('max_validity_hours')->default(168); // Maximum validity (7 days)
            $table->boolean('requires_risk_assessment')->default(true);
            $table->boolean('requires_jsa')->default(false);
            $table->boolean('requires_gas_test')->default(false);
            $table->boolean('requires_fire_watch')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('approval_levels')->default(1); // Number of approval levels required
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_types');
    }
};
