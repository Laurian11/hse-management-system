<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_response_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('team_type', ['fire_warden', 'first_aid', 'evacuation', 'search_rescue', 'hazmat', 'security', 'medical', 'general'])->default('general');
            $table->text('description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->json('team_members')->nullable(); // Array of user IDs with roles
            $table->foreignId('team_leader_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deputy_leader_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Training
            $table->date('last_training_date')->nullable();
            $table->date('next_training_date')->nullable();
            $table->text('training_requirements')->nullable();
            $table->json('certifications')->nullable(); // Required certifications
            
            // Availability
            $table->boolean('is_24_7')->default(false);
            $table->json('availability_schedule')->nullable(); // Schedule if not 24/7
            $table->json('contact_information')->nullable();
            
            // Equipment
            $table->json('assigned_equipment')->nullable(); // Equipment IDs
            $table->text('equipment_requirements')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'team_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_response_teams');
    }
};
