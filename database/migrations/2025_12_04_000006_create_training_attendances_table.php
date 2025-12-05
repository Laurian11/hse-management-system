<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('training_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('training_session_id')->constrained('training_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Attendance Details
            $table->enum('attendance_status', [
                'registered',
                'attended',
                'absent',
                'partially_attended',
                'excused'
            ])->default('registered');
            
            $table->dateTime('registered_at')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->dateTime('checked_out_at')->nullable();
            $table->integer('attendance_percentage')->nullable(); // Percentage of session attended
            
            // Registration Method
            $table->enum('registration_method', [
                'manual',
                'self_registration',
                'auto_assigned',
                'biometric',
                'system_generated'
            ])->default('manual');
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('absence_reason')->nullable();
            $table->boolean('certificate_eligible')->default(false);
            
            $table->timestamps();
            
            $table->unique(['training_session_id', 'user_id']);
            $table->index(['company_id', 'attendance_status']);
            $table->index('user_id');
            $table->index('training_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_attendances');
    }
};
