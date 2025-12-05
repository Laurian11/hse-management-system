<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evacuation_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location'); // Building, floor, area
            $table->enum('plan_type', ['building', 'floor', 'area', 'site', 'general'])->default('building');
            
            // Evacuation Routes
            $table->json('assembly_points')->nullable(); // Array of assembly points
            $table->json('evacuation_routes')->nullable(); // Array of routes with descriptions
            $table->json('emergency_exits')->nullable(); // Array of emergency exits
            $table->json('hazard_zones')->nullable(); // Areas to avoid during evacuation
            
            // Procedures
            $table->text('evacuation_procedures')->nullable();
            $table->text('accountability_procedures')->nullable();
            $table->text('special_needs_procedures')->nullable(); // For disabled persons
            $table->json('roles_and_responsibilities')->nullable();
            
            // Equipment & Resources
            $table->json('required_equipment')->nullable();
            $table->json('communication_methods')->nullable();
            
            // Diagrams/Maps
            $table->json('diagrams')->nullable(); // File paths to evacuation diagrams
            $table->json('maps')->nullable(); // File paths to maps
            
            // Review & Update
            $table->date('last_review_date')->nullable();
            $table->date('next_review_date')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('review_notes')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'location']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evacuation_plans');
    }
};
