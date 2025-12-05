<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fire_drills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->date('drill_date');
            $table->time('drill_time');
            $table->string('location');
            $table->enum('drill_type', ['announced', 'unannounced', 'partial', 'full'])->default('announced');
            $table->text('objectives')->nullable();
            $table->text('scenario')->nullable();
            
            // Participants
            $table->integer('total_participants')->default(0);
            $table->integer('expected_participants')->default(0);
            $table->json('participants')->nullable(); // List of participants
            
            // Results
            $table->time('evacuation_time')->nullable(); // Time taken to evacuate
            $table->enum('overall_result', ['excellent', 'good', 'satisfactory', 'needs_improvement', 'poor'])->nullable();
            $table->text('observations')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('recommendations')->nullable();
            
            // Conducted by
            $table->foreignId('conducted_by')->constrained('users')->onDelete('cascade');
            $table->json('observers')->nullable(); // Additional observers
            
            // Follow-up
            $table->boolean('requires_follow_up')->default(false);
            $table->text('follow_up_actions')->nullable();
            $table->date('follow_up_due_date')->nullable();
            $table->boolean('follow_up_completed')->default(false);
            
            // Attachments
            $table->json('attachments')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'drill_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fire_drills');
    }
};
