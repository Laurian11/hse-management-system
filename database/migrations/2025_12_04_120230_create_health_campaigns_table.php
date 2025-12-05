<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('campaign_title');
            $table->text('description')->nullable();
            $table->enum('campaign_type', ['wellness_program', 'health_screening', 'vaccination_drive', 'health_education', 'fitness_program', 'mental_health', 'other']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->json('target_audience')->nullable(); // all_employees, specific_department, specific_role, etc.
            $table->foreignId('coordinator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('objectives')->nullable();
            $table->text('activities')->nullable();
            $table->integer('target_participants')->nullable();
            $table->integer('actual_participants')->nullable();
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned');
            $table->text('outcomes')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'start_date']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_campaigns');
    }
};
