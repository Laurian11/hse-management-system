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
        Schema::create('toolbox_talk_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toolbox_talk_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable();
            $table->string('employee_name')->nullable();
            $table->string('employee_id_number')->nullable();
            $table->string('department')->nullable();
            $table->enum('attendance_status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();
            $table->enum('check_in_method', ['biometric', 'manual', 'mobile_app', 'video_conference'])->default('biometric');
            $table->string('biometric_template_id')->nullable(); // ZKTeco template ID
            $table->string('device_id')->nullable(); // K40 device used
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->text('absence_reason')->nullable();
            $table->boolean('is_supervisor')->default(false);
            $table->boolean('is_presenter')->default(false);
            $table->json('digital_signature')->nullable(); // Base64 signature data
            $table->string('signature_ip_address')->nullable();
            $table->dateTime('signature_timestamp')->nullable();
            $table->text('participation_notes')->nullable(); // Supervisor notes on engagement
            $table->integer('engagement_score')->nullable(); // 1-5 participation rating
            $table->json('feedback_responses')->nullable(); // Feedback given during talk
            $table->json('assigned_actions')->nullable(); // Safety actions assigned to employee
            $table->boolean('action_acknowledged')->default(false);
            $table->dateTime('action_acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toolbox_talk_attendances');
    }
};
