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
        Schema::create('biometric_devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            $table->string('device_serial_number')->unique();
            $table->string('device_type')->default('ZKTeco K40'); // ZKTeco K40, K50, etc.
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('location_name'); // Site A, Building B, etc.
            $table->text('location_address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Network Configuration
            $table->string('device_ip')->unique();
            $table->integer('port')->default(4370);
            $table->string('api_key')->nullable();
            $table->enum('connection_type', ['http', 'tcp', 'both'])->default('both');
            
            // Device Status
            $table->enum('status', ['active', 'inactive', 'maintenance', 'offline'])->default('active');
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->integer('sync_interval_minutes')->default(5); // How often to sync attendance
            
            // Configuration
            $table->boolean('auto_sync_enabled')->default(true);
            $table->boolean('daily_attendance_enabled')->default(true); // Track daily attendance
            $table->boolean('toolbox_attendance_enabled')->default(true); // Track toolbox talk attendance
            $table->time('work_start_time')->default('08:00:00');
            $table->time('work_end_time')->default('17:00:00');
            $table->integer('grace_period_minutes')->default(15); // Grace period for late check-in
            
            // Additional Info
            $table->text('notes')->nullable();
            $table->json('configuration')->nullable(); // Store additional device-specific config
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('device_ip');
            $table->index('status');
            $table->index('location_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_devices');
    }
};
